<?php


namespace App\Http\Controllers\Superlogica;


use App\Helpers\API\Superlogica\V2\CreditCardRequiredException;
use App\Helpers\API\Superlogica\V2\Domain\Models\CreditCard;
use App\Helpers\API\Superlogica\V2\Exceptions\IdDidNotMatchAnyCustomer;
use App\Helpers\API\Superlogica\V2\Exceptions\InvalidCallException;
use App\Helpers\API\Superlogica\V2\Exceptions\InvalidChargeInvalidationReason;
use App\Helpers\API\Superlogica\V2\InactiveCustomerFound;
use App\Helpers\API\Superlogica\V2\OverdueCustomerFound;
use App\Helpers\API\Superlogica\V2\Signature;
use App\Helpers\Utils;
use App\Http\Util\Logger;
use App\Models\Clientes;
use App\Models\LPTCodigosPromocionais;
use App\Models\Pets;
use App\Models\PetsPlanos;
use App\Models\Planos;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LifepetParaTodosController extends \App\Http\Controllers\LifepetParaTodosController
{
    const NÃO_FOI_POSSÍVEL_GARANTIR_A_AUTENTICIDADE_DA_REQUISIÇÃO = 'Não foi possível garantir a autenticidade da requisição.';
    const ENTRE_EM_CONTATO_COM_NOSSO_ATENDIMENTO_PARA_ADQUIRIR_UM_NOVO_PLANO = 'Entre em contato com nosso atendimento para adquirir um novo plano.';

    /**
     * @param Request $request
     * @return Factory|View
     */
    public function pagamento(Request $request)
    {
        $_SESSION['fingerprint_session'] = sha1(rand());

        $params = [];
        $params['nome'] = $nome = $request->get('nome');
        $params['email'] = $email = $request->get('email');
        $params['celular'] = $celular = $request->get('celular');
        $params['regime'] = $regime = strtoupper($request->get('regime', 'MENSAL'));
        $params['pets'] = $pets = intval($request->get('pets', 1));
        $id_plano = intval($request->get('plano', self::PLANO_PADRAO));
        $params['plano'] = $plano = Planos::find($id_plano);
        $params['preco'] = self::obterPrecoPlano($plano, $pets, $regime);
        $params['parcelas'] = self::obterParcelasPlano($plano, $pets, $regime);
        $params['cupom'] = $cupom = $request->get('cupom', null);
        $params['racas'] = \App\Models\Raca::orderBy('tipo')->orderBy('nome', 'ASC')->get();
        return view('lpt_assinaturas.superlogica.pagar', $params);
    }

    /**
     */
    public function assinar(Request $request): array
    {
        //Validar dados básicos da requisição
        $logger = new Logger('ecommerce');
        $formaPagamento = $request->get('forma_pagamento', Clientes::FORMA_PAGAMENTO_CARTAO);
        $input = $this->validateInput($request);
        $requestData = $request->all();

        $recaptchaOk = $this->checkRecaptcha($request, $logger);
        if (!$recaptchaOk) {
            return self::errorMessage(self::NÃO_FOI_POSSÍVEL_GARANTIR_A_AUTENTICIDADE_DA_REQUISIÇÃO);
        }

        DB::beginTransaction();
        try {
            /**
             * @var Clientes|null $cliente
             */
            $cliente = $this->findOrCreateCliente($input);
            //Cria cadastro do pet (inativo) no ERP
            $signatures = $this->createPetSignatures($request, $cliente, $input, $formaPagamento);
        } catch (InactiveCustomerFound|OverdueCustomerFound $e) {
            DB::rollBack();

            $logger->register(\App\Http\Util\LogEvent::ERROR, \App\Http\Util\LogPriority::HIGH,
                "Não foi possível realizar a inclusão do pet no sistema. O cliente já possui um cadastro inativo ou com pagamento pendente em nosso sistema: \nOs dados recebidos foram: \n{$dadosCliente}"
            );
            return self::errorMessage(self::ENTRE_EM_CONTATO_COM_NOSSO_ATENDIMENTO_PARA_ADQUIRIR_UM_NOVO_PLANO);
        } catch (Exception $e) {
            //Adicionar erro em log
            $messages = $e->getMessage();
            $requestData['exception'] = [
                'exception' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ];
            DB::rollBack();

            $requestData = self::formatClientDataToLog($requestData, true);
            $logger->register(\App\Http\Util\LogEvent::ERROR, \App\Http\Util\LogPriority::HIGH,
                "Não foi possível realizar a inclusão do pet no sistema: $messages \nOs dados recebidos foram: $requestData"
            );

            return self::errorMessage('Encontramos um erro inesperado ao tentar completar a sua assinatura.');
        }
        DB::commit();

        $requestData = self::formatClientDataToLog($requestData, true);
        $logger->register(\App\Http\Util\LogEvent::NOTICE, \App\Http\Util\LogPriority::HIGH,
            "O cadastro do cliente foi gerado com sucesso. Aguardando confirmação de pagamento.\nOs dados recebidos foram: \n{$requestData}"
        );


        //Email de aguarde
        if(!empty($signatures)) {
            //TODO: Enviar email da RD

        }
        //Enviar para tela de aguarde
        return self::successMessage('Estamos processando seu pagamento. Assim que tudo estiver pronto lhe enviaremos um e-mail de confirmação.');
    }

    /**
     * @param Request $request
     * @return array
     */
    private function validateInput(Request $request): array
    {
        $origin = request()->headers->get('origin');
        $allowed = [
            'http://lifepet.com.br',
            'https://lifepet.com.br',
            'http://app.lifepet.com.br',
            'https://app.lifepet.com.br',
            'http://manager.lifepet',
            'http://staging.lifepet.com.br',
            'https://staging.lifepet.com.br'
        ];
        if (is_null($origin) || !in_array($origin, $allowed)) {
            abort(403, 'Não autorizado.');
        }

        header('Access-Control-Allow-Origin: https://lifepet.com.br');
        /**
         * ETAPA DE INICIALIZAÇÃO E OBTENÇÃO/ORGANIZAÇÃO DE DADOS
         */
        $tags = [];
        $logger = new Logger('ecommerce');

        $v = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'id_plano' => 'required',
            'name' => 'required',
            'data_nascimento' => 'required',
            'sexo' => 'required',
            'email' => 'required|email',
            'celular' => 'required',
            'cpf' => 'required',
            'country' => 'required',
            'state' => 'required',
            'city' => 'required',
            'neighbourhood' => 'required',
            'street' => 'required',
            'brand' => 'sometimes|required',
            'card_number' => 'sometimes|required',
            'expires_in' => 'sometimes|required',
            'cvv' => 'sometimes|required',
            'regime' => 'required',
            'recaptcha' => 'required',
            'pets' => 'required|array|min:1',
            'pets.*.nome_pet' => 'required|string',
            'pets.*.data_nascimento' => 'required|string',
            'pets.*.sexo' => 'required|string',
            'pets.*.tipo' => 'required|string',
            'pets.*.raca' => 'required|string',
        ]);

        $input = $request->all();
        $input['origin'] = $origin;

        if ($v->fails()) {
            $messages = join("\n", $v->getMessageBag()->all());
            //Redirect back with errors
            abort(422, $messages);
        }

        return $input;
    }

    /**
     * @param array $input
     * @return Clientes
     * @throws InactiveCustomerFound
     * @throws OverdueCustomerFound
     */
    public function findOrCreateCliente(array $input): Clientes
    {
        $cliente = Clientes::cpf($input['cpf'])->first();
        if ($cliente) {
            //Verificar se o cliente está inativo:
            if (!$cliente->ativo) {
                throw new InactiveCustomerFound();
            }

            //Verificar se o cliente está inadimplente
            if ($cliente->getStatusPagamentoAttribute() !== Clientes::PAGAMENTO_EM_DIA) {
                throw new OverdueCustomerFound();
            }
        } else {
            $cliente = new Clientes();
            $cliente->fill([
                'nome_cliente' => $input['name'],
                'data_nascimento' => Carbon::createFromFormat(Utils::BRAZILIAN_DATE, $input['data_nascimento'])->format(Utils::BRAZILIAN_DATE),
                'sexo' => $input['sexo'],
                'email' => $input['email'],
                'celular' => Utils::numberOnly($input['celular']),
                'cpf' => Utils::numberOnly($input['cpf']),
                'rua' => $input['street'],
                'numero_endereco' => $input['address_number'],
                'bairro' => $input['neighbourhood'],
                'cidade' => $input['city'],
                'estado' => $input['state'],
                'cep' => $input['cep'],
                'ativo' => 0,
                'dia_vencimento' => Carbon::now()->day,
                'forma_pagamento' => strtolower($input['payment_type']),
                //Observações
                'observacoes' => 'Cadastro automático gerado à partir do e-commerce. Iniciará de forma inativa até a confirmação do primeiro pagamento.'
            ]);
            $cliente->save();
        }
        return $cliente;
    }

    /**
     * @param Request $request
     * @param Clientes|null $cliente
     * @param array $input
     * @param $formaPagamento
     * @return array
     * @throws CreditCardRequiredException
     * @throws IdDidNotMatchAnyCustomer
     * @throws InvalidCallException
     * @throws InvalidChargeInvalidationReason
     */
    public function createPetSignatures(Request $request, ?Clientes $cliente, array $input, $formaPagamento): array
    {
        $petsFromRequest = $request->get('pets', []);
        $signatures = [];
        foreach ($petsFromRequest as $pfr) {
            $signature = [];
            $pet = new Pets();
            $pet = $pet->fill([
                'nome_pet' => $pfr['nome_pet'],
                'tipo' => $pfr['tipo'],
                'sexo' => $pfr['sexo'],
                'data_nascimento' => Carbon::createFromFormat(Utils::BRAZILIAN_DATE, $pfr['data_nascimento']),
                'id_cliente' => $cliente->id,
                'regime' => $input['regime'],
                'mes_reajuste' => Carbon::now()->month,
                'participativo' => 0,
                'vencimento' => Carbon::now()->day,
                'id_raca' => (int)$pfr['raca'],
                'ativo' => 0
            ]);
            $pet->save();
            $signature['pet'] = $pet;

            //Criar vínculo de assinatura local
            $assinatura = new PetsPlanos();
            $assinatura->fill([
                'id_pet' => $pet->id,
                'id_plano' => $input['id_plano'],
                'data_inicio_contrato' => Carbon::now()->format(Utils::BRAZILIAN_DATE),
                'id_vendedor' => 1,
                'status' => 'P',
                'transicao' => PetsPlanos::TRANSICAO__NOVA_COMPRA,
                'valor_momento' => $this->getPrice($input['id_plano'], $input['regime'], count($petsFromRequest)) / count($petsFromRequest)
            ]);

            $assinatura->save();
            $pet->id_pets_planos = $assinatura->id;
            $pet->save();

            $signature['assinatura'] = $assinatura;

            //Define dados do cartão de crédito
            $creditCard = null;
            if ($formaPagamento === Clientes::FORMA_PAGAMENTO_CARTAO) {
                $validity = explode('/', $input['expires_in']);
                $input['valid_month'] = $validity[0];
                $input['valid_year'] = $validity[1];

                $creditCard = new CreditCard($input['card_number'], $input['valid_month'], $input['valid_year'], $input['cvv'], $input['holder'], $input['brand']);
            }

            //Criar assinatura no superlógica para todos os pets
            $superlogicaSignatureService = new Signature();

            $signature['superlogica'] = $superlogicaSignatureService->sign($pet, true, $creditCard);
            $signatures[] = $signature;
        }

        return $signatures;
    }

    public static function formatClientDataToLog($input, $json = true)
    {
        $data = $input;

        if(isset($data['card_number'])) {
            $data['card_number'] = substr($data['card_number'], 0, 4) . str_repeat('*', 12) . substr($data['card_number'], -4);
        }

        $data['ccv'] = '***';

        if($json) {
            return json_encode($data);
        }

        return $data;
    }

    /**
     * @param Request $request
     * @param Logger $logger
     * @return bool
     */
    private function checkRecaptcha(Request $request, Logger $logger): bool
    {
        if(env('APP_ENV') === 'local' || env('APP_ENV') === 'staging') {
            return true;
        }

        if (!self::googleCaptcha($request->get('recaptcha'))) {
            $dadosRequisicao = json_encode([
                '_REQUEST' => self::formatClientDataToLog($_REQUEST),
                '_SERVER' => Utils::getServerInfoToLog($_SERVER)
            ]);

            $logger->register(\App\Http\Util\LogEvent::WARNING, \App\Http\Util\LogPriority::MEDIUM,
                "Uma tentativa de compra foi barrada após ser identificada como ação fraudulenta. Dados de requisição: \n{$dadosRequisicao}"
            );

            return false;
        }

        return true;
    }

    /**
     * @param $message
     * @return array
     */
    private static function errorMessage($message)
    {
        return [
            'erro' => 'Erro.',
            'message' => $message,
            'status' => false
        ];
    }

    /**
     * @param $message
     * @param array $additionalData
     * @return array|bool[]
     */
    private static function successMessage($message, array $additionalData = [])
    {
        return array_merge([
            'message' => $message,
            'status' => true,
        ], $additionalData);
    }

    public function getPrice($idPlano, $regime = 'MENSAL', $quantidade = 1, $idCupom = null)
    {
        $plano = Planos::find($idPlano);

        $priceToPay = \App\Http\Controllers\LifepetParaTodosController::obterPrecoPlano($plano, $quantidade, $regime);

        if(!$priceToPay) {
            $prices = \App\Http\Controllers\API\AssinaturasAPIController::getPrice($idPlano);
            $priceToPay = $prices[$quantidade];
        }
        $logger = new Logger('ecommerce');

        //Aplicar códigos de desconto.
        if($idCupom) {
            /**
             * @var LPTCodigosPromocionais $cupom
             */
            $cupom = LPTCodigosPromocionais::find($idCupom);
            if($cupom && $cupom->regimeAplicavel($regime)) {
                $logger->register(\App\Http\Util\LogEvent::NOTICE, \App\Http\Util\LogPriority::HIGH,
                    "O cupom {$cupom->codigo} acaba de ser utilizado."
                );

                $priceToPay = $cupom->aplicar($priceToPay);
            }
        }

        return $priceToPay;
    }
}