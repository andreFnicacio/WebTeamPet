<?php
/**
 * Created by PhpStorm.
 * User: Breno
 * Date: 30/04/2021
 * Time: 12:54
 */

namespace App\DAO;

use App\LinkPagamento;
use App\Models\Notas;
use App\Models\Pets;
use App\Models\PetsPlanos;
use App\Models\Renovacao;
use App\Repositories\RenovacaoRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RenewalDAO
{
    const STATUS_INITIAL = 'INITIAL';
    const STATUS_OPEN = 'OPEN';
    const STATUS_PROCESSING = 'PROCESSING';
    const STATUS_CANCELING = 'CANCELING';
    const STATUS_CLOSED = 'CLOSED';
    const STATUS_COMPLETE = 'COMPLETE';

    /**
     * @property array|null
     */
    public $pet = null;
    /**
     * @property array|null
     */
    public $tutor = null;

    public $data_inicio_contrato = null;
    public $regime = null;
    public $modalidade = null;

    /**
     * @property array|null
     */
    public $plano = null;

    public $mes_reajuste = null;
    public $mes_reajuste_numerico = null;

    /**
     * @property array|null
     */
    public $detailed = null;

    /**
     * @property array|null
     */
    public $calculed = null;

    /**
     * @property array|null
     */
    public $renovacao = null;

    public $status = null;

    public $anual = null;
    public $mensal = null;

    public $processing = false;
    public $processingMessage = null;

    public $request = [];

    public function __construct(Pets $pet)
    {
        $contrato = $pet->petsPlanosAtual()->first();
        if($contrato) {
            $plano = $contrato->plano;

            $this->plano = [
                'link' => route('planos.edit', $plano->id),
                'nome' => $plano->nome_plano,
                'valor' => $contrato->valor_momento,
                //'object' => $plano
            ];

            $this->data_inicio_contrato = $contrato->data_inicio_contrato->format('d/m/Y');
        } else {
            $this->plano = [
                'link' => '#',
                'nome' => 'PET SEM PLANO.',
                'valor' => ' - ',
                //'object' => null
            ];

            $this->data_inicio_contrato = ' - ';
        }

        $this->regime = $pet->regime;
        $this->modalidade = $pet->participativo ? 'PARTICIPATIVO' : 'INTEGRAL';
        $this->anual = $pet->isAnual();
        $this->mensal = !$pet->isAnual();

        $this->tutor = [
            'link' => route('clientes.edit', ['id' => $pet->cliente->id]),
            'nome' => $pet->cliente->nome_cliente,
            'id' => $pet->cliente->id
            //'object' => $pet->cliente
        ];

        $this->pet = [
            'link' => route('pets.edit', ['id' => $pet->id]),
            'nome' => $pet->nome_pet,
            'id' => $pet->id
            //'object' => $pet
        ];

        $mesExtenso = Carbon::createFromFormat('m/d', $pet->mes_reajuste . "/01")->formatLocalized('%B');
        $mesExtenso = utf8_encode($mesExtenso);
        $this->mes_reajuste = mb_convert_case($mesExtenso, MB_CASE_TITLE, 'UTF-8');
        $this->mes_reajuste_numerico = sprintf("%02d", $pet->mes_reajuste);

        $this->detailed = null;
        $this->calculed = [
            'valor_mensal_original' => null,
            'desconto' => null,
            'parcelas' => 1,
            'total_anual' => null,
            'total_mensal' => null
        ];

        $renovacao = Renovacao::where('id_pet', $pet->id)
                              ->where('status', '<>', Renovacao::STATUS_CANCELADO)
                              ->whereYear('created_at', Carbon::now()->year)->first();

        $this->renovacao = [
            'renovado' => $renovacao ? true : false,
            'object' => $renovacao,
        ];

        $this->status = self::STATUS_INITIAL;

        if($renovacao) {
            $this->status = self::getRenewalStatus($renovacao->status);
        }
    }

    /**
     * @param Request $request
     * @return Renovacao|mixed|void
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public static function newFromRequest(Request $request)
    {
        $input = $request->all();
        /**
         * @var Pets $pet
         */
        $pet   = Pets::find($input['id_pet']);
        /**
         * @var PetsPlanos $contrato
         */
        $contrato = $pet->petsPlanosAtual()->first();

        $input['id_cliente']      = $pet->cliente->id;
        $input['competencia_mes'] = sprintf("%02d", $contrato->data_inicio_contrato->format('m'));
        $input['competencia_ano'] = Carbon::now()->format('Y');
        $input['status']          = Renovacao::STATUS_NOVO;

        /**
         * Valores sem desconto, do plano original, sem reajuste.
         */
        $valorMensal              = $input['valor_original'];
        $valorAnual               = $valorMensal * 12;
        $expires_at = null;
        if($input['regime'] === Pets::REGIME_MENSAL) {
            $input['valor_bruto'] = $input['valor_original'] = $valorMensal;
        } else {
            $input['valor_bruto'] = $input['valor_original'] = $valorAnual;
            $expires_at           = $contrato->data_inicio_contrato->year(now()->year);
        }
        //Valor reajustado sem desconto
        $input['valor_bruto'] *= 1+($input['reajuste']/100);
        //Valor reajustado com desconto
        $input['valor'] = $input['valor_bruto'] * (1 - $input['desconto']/100);

        try {
            $linkPagamento = self::createLinkPagamento($request, $input, $expires_at);
        } catch (\Exception $e) {
            return abort(500, $e->getMessage());
        }

        $input['id_link_pagamento'] = $linkPagamento->id;
        $input['valor']             = number_format($input['valor'], 2, '.', '');
        $input['valor_bruto']       = number_format($input['valor_bruto'], 2, '.', '');
        $input['id_plano']          = $pet->plano()->id;

        /**
         * @var Renovacao $renovacao
         */
        $renovacaoRepository = new RenovacaoRepository(app());
        $renovacao = $renovacaoRepository->create($input);

        $renovacao->link->update([
            'callback_url' => route('api.renovacao.callback', ['id' => $renovacao->id])
        ]);

        return $renovacao;
    }

    public static function skipFromRequest(Request $request)
    {
        $input = $request->all();
        /**
         * @var Pets $pet
         */
        $pet   = Pets::find($input['id_pet']);
        /**
         * @var PetsPlanos $contrato
         */
        $contrato = $pet->petsPlanosAtual()->first();

        $input['id_cliente']      = $pet->cliente->id;
        $input['competencia_mes'] = sprintf("%02d", $contrato->data_inicio_contrato->format('m'));
        $input['competencia_ano'] = Carbon::now()->format('Y');
        $now = Carbon::now();
        Notas::create([
            'user_id' => 1,
            'cliente_id' => $pet->cliente->id,
            'corpo' => "Pet {$pet->nome_pet} não teve reajuste no ano de {$now->year}. Ou seja, foi renovado sem acréscimos na mensalidade/anuidade."
        ]);

        $input['status'] = Renovacao::STATUS_NAO_OPTANTE;
        $input['id_plano'] = $pet->plano()->id;

        /**
         * @var Renovacao $renovacao
         */
        /**
         * @var Renovacao $renovacao
         */
        $renovacaoRepository = new RenovacaoRepository(app());
        $renovacao = $renovacaoRepository->create($input);

        return $renovacao;
    }

    /**
     * @param Request $request
     * @param array $sent
     * @param Carbon|null $expires_at
     * @return mixed
     * @throws \Exception
     */
    public static function createLinkPagamento(Request $request, $sent = [], $expires_at = null)
    {
        $input = $request->all();
        $pet   = Pets::find($input['id_pet']);

        $input['tags']            = ['renovacao','link-pagamento'];
        $input['descricao']       = 'Pagamento da renovação do plano do pet ' .  $pet->nome_pet;
        $input['expires_at']      = $expires_at ? $expires_at->format('d/m/Y') : Carbon::today()->addMonth()->format('d/m/Y');

        $input = array_merge($input, $sent);

        return LinkPagamento::createForRenovacao($input);
    }

    public static function getRenewalStatus($status) {
        $statusProcessamento = [
            Renovacao::STATUS_NOVO,
            Renovacao::STATUS_EM_NEGOCIACAO,
            Renovacao::STATUS_ATUALIZADO,
            Renovacao::STATUS_AGENDADO
        ];

        $statusConcluido = [
            Renovacao::STATUS_CONVERTIDO,
            Renovacao::STATUS_NAO_OPTANTE,
            Renovacao::STATUS_PAGO
        ];

        if(in_array($status, $statusProcessamento)) {
            return self::STATUS_PROCESSING;
        } else if (in_array($status, $statusConcluido)) {
            return self::STATUS_COMPLETE;
        }

        return null;
    }
}