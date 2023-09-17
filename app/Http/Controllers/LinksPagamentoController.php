<?php

namespace App\Http\Controllers;

use App\Helpers\API\Financeiro\Financeiro;
use App\Helpers\API\RDStation\Services\RDLinkPagamentoCriadoService;
use App\Helpers\Utils;
use App\Http\Util\LogEvent;
use App\Http\Util\Logger;
use App\Http\Util\LogPriority;
use App\LinkPagamento;
use App\Models\Clientes;
use App\Models\Cobrancas;
use App\Models\Notas;
use Carbon\Carbon;
use Composer\Package\Link;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Entrust;

class LinksPagamentoController extends AppBaseController
{
    public function index(Request $request)
    {
        $linksPagamento = LinkPagamento::orderBy('id', 'DESC')->get();

        return view('links_pagamento.index', compact('linksPagamento'));
    }

    public function edit($id)
    {

        if(!Entrust::hasRole(['ADMINISTRADOR', 'FINANCEIRO'])) {
            return self::notAllowed();
        }

        $linkPagamento = LinkPagamento::where('id', $id)
            ->first();
        if (!$linkPagamento) return; //criar msg de erro.

        $clientes = Clientes::where('id', $linkPagamento->id_cliente)
            ->get();

        if (!$clientes) return; //criar msg de erro.
        $data = [
            'linkPagamento' => $linkPagamento,
            'clientes' => $clientes
        ];
        return view('links_pagamento.edit', $data);
    }

    public function create(Request $request)
    {
        $clientes = Clientes::where('ativo', 1)
                            //->where('forma_pagamento', 'cartao')
                    ->get();

        $data = [
            'clientes' => $clientes
        ];
        return view('links_pagamento.create', $data);
    }

    public function update(Request $request, $id)
    {

        if(!Entrust::hasRole(['ADMINISTRADOR', 'FINANCEIRO'])) {
            return self::notAllowed();
        }

        $v = Validator::make($request->all(), [
            'valor' => 'required|min:1|numeric',
            'parcelas' => 'required|min:1|max:12|numeric',
            'descricao' => 'required',
            'expires_at' => 'required|date_format:d/m/Y|after:today'
        ]);

        if ($v->fails()) {
            $messages = join("\n", $v->getMessageBag()->all());
            self::setError($messages, 'Oops.');

            return back()
                ->withErrors($v)
                ->withInput();
        }

        $link = LinkPagamento::where('id', $id)->first();

        if (!$link) {
            $messages = 'Link não encontrado.';
            self::setError($messages, 'Oops.');

            return back()
                ->withErrors($v)
                ->withInput();
        }

        $link->valor = $request->valor;
        $link->parcelas = $request->parcelas;
        $link->descricao = $request->descricao;
        $link->expires_at = $request->expires_at;

        $link->save();

        $rd = new RDLinkPagamentoCriadoService();
        $rd->process($link);

        self::setSuccess('O link de pagamento foi editado com sucesso.');

        return redirect(route('links-pagamento.index'));

    }
    public function store(Request $request)
    {
        $v = Validator::make($request->all(), [
            'id_cliente' => 'required|exists:clientes,id',
            'valor' => 'required|min:1|numeric',
            'parcelas' => 'required|min:1|max:12|numeric',
            'tags' => 'required|array',
            'descricao' => 'required',
            'expires_at' => 'required|date_format:d/m/Y|after:today'
        ]);

        if ($v->fails()) {
            $messages = join("\n", $v->getMessageBag()->all());
            self::setError($messages, 'Oops.');

            return back()
                ->withErrors($v)
                ->withInput();
        }
        $input = $request->all();

        $input['hash'] = md5(Carbon::now()->format('dmYhis') . $input['id_cliente']);
        $input['status'] = LinkPagamento::STATUS_ABERTO;
        $input['tags'] = join(';', $input['tags']);

        $link = LinkPagamento::create($input);

        try {
            $rd = new RDLinkPagamentoCriadoService();
            $rd->process($link);
        } catch (\Exception $e) {
            $exception = "{$e->getMessage()}\n{$e->getFile()}:{$e->getLine()}";
            Logger::log(
                LogEvent::WARNING,
                'links-pagamento',
                LogPriority::LOW,
                'Houve uma falha na tentativa de comunicar o cliente via RDStation: ' . $exception
            );
        }

        self::setSuccess('O link foi criado com sucesso.');

        return redirect(route('links-pagamento.index'));
    }

    public function show(Request $request, $id)
    {
        $link = LinkPagamento::find($id);

        if(!$link) {
            abort(404, 'Link de pagamento não encontrado.');
        }

        return view('links_pagamento.show', [
            'linkPagamento' => $link
        ]);
    }

    public function formPagamento($hash)
    {
        $today = Carbon::today();
        $link = LinkPagamento::where('hash', $hash)
                             ->where('expires_at', '>=', $today)
                             ->where('status', LinkPagamento::STATUS_ABERTO)->first();

        if(!$link) {
            abort(404, 'Link de pagamento não encontrado. O link pode ter expirado ou já foi pago.');
        }

        return view('links_pagamento.pagar', [
            'linkPagamento' => $link
        ]);
    }

    public function pagar(Request $request, $hash)
    {
        $today = Carbon::today();
        /**
         * @var LinkPagamento $link
         */
        $link = LinkPagamento::where('hash', $hash)->where('expires_at', '>=', $today)->first();

        if(!$link) {
            abort(404, 'Link de pagamento não encontrado. O link pode ter expirado.');
        }

        if($link->status == LinkPagamento::STATUS_PAGO) {
            return redirect(route('links-pagamento.sucesso'));
        }

        $parcelas = $link->parcelas;

        $input = $request->all();
        $input['card_number'] = str_replace(' ', '', $input['card_number']);

        $v = Validator::make($input, [
            'card_number' => 'required|numeric',
            'brand' => 'required',
            'holder' => 'required',
            'expires_in' => 'required',
            'ccv' => 'required',
            'parcelas' => "numeric|min:1|max:$parcelas"
        ]);

        if ($v->fails()) {
            $messages = join("\n", $v->getMessageBag()->all());
            self::setError($messages, 'Oops.');

            return back()
                ->withErrors($v)
                ->withInput();
        }

        $finance = new Financeiro();
        $logger = new Logger('links-pagamento');

        if(!$link->cliente->id_externo) {
            self::setError("O seu cadastro ainda não está vinculado com o nosso sistema financeiro. Por favor, entre em contato com nosso atendimento.", 'Oops.');
            $logger->register(LogEvent::ERROR, LogPriority::MEDIUM, "O cliente {$link->cliente->nome_cliente} não pôde realizar o pagamento pois o cadastro do ERP não possui sincronia com o SF.", $link->id, 'links_pagamento');
            return back()
                ->withInput();
        }

        $customer = $finance->customerByRefcode($link->cliente->id_externo);

        if(!$customer) {
            self::setError("Encontramos um erro ao buscar seu cadastro no nosso sistema financeiro. Contate nossa equipe de atendimento.", 'Oops.');
            $logger->register(LogEvent::ERROR, LogPriority::MEDIUM, "O cliente {$link->cliente->nome_cliente} não pôde realizar o pagamento pois não foi possível encontrar o cadastro no SF com o REFCODE informado.",$link->id, 'links_pagamento');
            return back()
                ->withInput();
        }

        //Adds credit card
        $card = null;
        try {
            $card = $finance->addCreditCard($customer, $input);
        } catch (\Exception $e) {
            self::setError("Não foi possível utilizar o cartão informado para a compra. Encontramos um erro ao tentar cadastrá-lo. Por favor entre em contato com nosso atendimento.", 'Oops.');
            $logger->register(LogEvent::ERROR, LogPriority::MEDIUM, "O cliente {$link->cliente->nome_cliente} não pôde realizar o pagamento pois não foi possível cadastrar os dados de cartão informados.\nExceção: {$e->getMessage()}", $link->id, 'links_pagamento');
            return back()
                ->withInput();
        }

        if(!$card) {
            self::setError("Não foi possível utilizar o cartão informado para a compra. Encontramos um erro ao tentar cadastrá-lo. Por favor entre em contato com nosso atendimento.", 'Oops.');
            $logger->register(LogEvent::ERROR, LogPriority::MEDIUM, "O cliente {$link->cliente->nome_cliente} não pôde realizar o pagamento pois não foi possível cadastrar os dados de cartão informados.\nExceção: {$e->getMessage()}", $link->id, 'links_pagamento');
            return back()
                ->withInput();
        }

        $priceToPay = number_format($link->valor, 2);
        $installments = $request->get('parcelas');

        if($priceToPay < 1) {
            self::setError("Não foi possível concluir a compra. Encontramos um erro no valor do pagamento. Por favor entre em contato com nosso atendimento.", 'Oops.');
            $logger->register(LogEvent::ERROR, LogPriority::MEDIUM, "O cliente {$link->cliente->nome_cliente} não pôde realizar o pagamento pois houve uma tentativa de pagar um valor inferior a R\$ 1,00.", $link->id, 'links_pagamento');
            return back()
                ->withInput();
        }

        //Tentativa de pagamento.
        try {
            $payment = $finance->pay([
                'amount' => $priceToPay,
                'customer_id' => $customer->id,
                'due_date' => Carbon::now()->format('Y-m-d'),
                'installments' => $installments,
                'type' => 'creditcard',
                'card_id' => $card->id,
//                'fingerprint_ip' => $request->ip(),
//                'fingerprint_session' => $request->get('fingerprint_session'),
                'tags' => join(';', ['link-pagamento', "lp:{$link->id}", "{$link->hash}"])
            ]);
        } catch (\Exception $e) {
            $exception = "{$e->getMessage()}\n{$e->getFile()}:{$e->getLine()}";
            $logger->register(LogEvent::ERROR, LogPriority::HIGH,
                "Houve um erro ao tentar processar o pagamento do cliente no SF.\nIdentificação: {$link->cliente->nome_cliente}.\nExceção: {$exception}",
                $link->id, 'links_pagamento'
            );
            self::setError("Não foi possível concluir a compra. Encontramos um erro ao processar o pagamento. Por favor entre em contato com nosso atendimento.\n" . $e->getMessage(), 'Oops.');

            return back()
                ->withInput();
        }

        if($payment->status == 'AVAILABLE') {
            self::setSuccess("Você está quase lá! Recebemos seus dados e estamos processando seu pagamento.");

            $logger->register(LogEvent::NOTICE, LogPriority::HIGH,
                "O pagamento do cliente foi confirmado.\n Identificação: {$link->cliente->nome_cliente}",
                $link->id, 'links_pagamento'
            );

            //Cobrancas::cobrancaAutomatica($link->cliente, $link->valor, "Link de pagamento #{$link->id}", null, null, $payment->id);

            $valorMonetario = Utils::money($link->valor);
            $link->cliente->addNota("Pagamento do link #{$link->id} feito com sucesso. Descrição: {$link->descricao}. Valor: $valorMonetario em {$link->parcelas}x");

            $link->status = LinkPagamento::STATUS_PAGO;
            $link->update();
            $link->dispatch();

            try {
                $rd = new \App\Helpers\API\RDStation\Services\RDLinkPagamentoPagoService();
                $rd->process($link);
            } catch (\Exception $e) {
                $exception = "{$e->getMessage()}\n{$e->getFile()}:{$e->getLine()}";
                Logger::log(
                    LogEvent::WARNING,
                    'links-pagamento',
                    LogPriority::LOW,
                    'Houve uma falha na tentativa de comunicar o cliente via RDStation: ' . $exception
                );
            }

            $mensagem = "Você está quase lá! Recebemos seus dados e estamos processando seu pagamento.";
            $whatsapp = new \App\Helpers\API\SimpleChat\Message($link->cliente->nome_cliente, $mensagem, $link->cliente->celular);

            try {
                $whatsapp->send();
            } catch (\Exception $e) {
                Logger::log(
                    LogEvent::WARNING,
                    'links-pagamento',
                    LogPriority::LOW,
                    'Houve uma falha na tentativa de comunicar o cliente via Whatsapp. '
                );
            }

            return redirect(route('links-pagamento.sucesso', $link->id));
        } else {
            //Create email trigger - Failure
//            $rd = new \App\Helpers\API\RDStation\Services\RDCompraParaTodosFalhaPagamentoService();
//            $rd->process($compraRapida);
            self::setError("Não foi possível concluir a compra. Encontramos um erro ao processar o pagamento. Por favor entre em contato com nosso atendimento.", 'Oops.');

            //Enviar um email notificando problema na compra. (Thiago, Atendimento, Alexandre)
            $logger->register(LogEvent::WARNING, LogPriority::HIGH,
                "O pagamento do cliente NÃO foi confirmado.\nIdentificação: {$link->cliente->nome_cliente}.",
                $link->id, 'links_pagamento'
            );

            return back();
        }
    }

    public function sucesso()
    {
        return view('links_pagamento.sucesso');
    }
}
