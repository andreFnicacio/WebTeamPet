<?php

namespace App\Http\Controllers;

use App\Helpers\API\MailChimp\MailChimp;
use App\Helpers\API\Superlogica\Invoice;
use App\Http\Requests\CreateClientesRequest;
use App\Http\Util\LogEvent;
use App\Http\Util\Logger;
use App\Http\Util\LogPriority;
use App\Http\Util\Superlogica\Client;
use App\Http\Util\Superlogica\Plans;
use App\Models\Clientes;
use App\Models\Pets;
use App\Models\PetsPlanos;
use App\Models\Planos;
use App\Models\Cobrancas;
use App\Models\Pagamentos;

use App\Repositories\ClientesRepository;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Entrust;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Helpers\API\Financeiro\Financeiro;
use Illuminate\Support\Facades\Log;

use App\Helpers\API\RDStation\Services\{RDSendBoletoInsideSalesService,RDSendCreditCardConfirmInsideSalesService};

class ComercialController extends AppBaseController
{
    /** @var  ClientesRepository */
    private $clientesRepository;

    const UPLOAD_TO = 'clientes/';

    private $juros = 0;
    private $multa = 0;

    public function __construct(ClientesRepository $clientesRepo)
    {
        $this->clientesRepository = $clientesRepo;
        //Log::useDailyFiles(storage_path().'/logs/rd-station/inside-sales/vendas.log');
    }

    public static function notAllowed($message = '"Você não está habilitado a utilizar a ferramenta de Inside Sales!\n Entre em contato com o responsável pelo comercial."', $type = null) {
        return view('403', [
            'message' => $message
        ]);
    }

    /**
     * Mostra o formulário de cadastro do Inside Sales.
     *
     * @return Response
     */
    public function create()
    {
        $vendedor = $this::loggedVendedor();

        if (!$vendedor || !$vendedor->canUseInsideSales()) {
            return self::notAllowed();
        }

        return view('comercial.inside_sales.inside_sales', [
            'cliente' => (new \App\Models\Clientes),
            'checklistProposta' => self::$checklistProposta,
            'checklistDoencasProposta' => self::$checklistDoencasProposta,
            'ufs' => self::$ufs,
            'vendedor' => $vendedor,
        ]);
    }

    /**
     * Efetua o cadastro enviado pelo formulário de Inside Sales
     *
     * @param CreateClientesRequest $request
     * @return void
     */
    public function inside_sales_cadastro(CreateClientesRequest $request)
    {
        if (!Entrust::can('inside_sales_cadastro')) {
            return self::notAllowed();
        }

        $input = $request->all();

        /**
         * Passos para cadastro de cliente pelo inside sales:
         * --------------------------------------------------
         * ERP:
         * > Passo 1 - Cadastrar cliente
         * > Passo 2 - Cadastrar usuário e vincular ao cliente
         * > Passo 3 - Cadastrar pet e vincular aos planos
         * 
         * Financeiro
         * > Passo 4 - Cadastrar cliente no Financeiro 
         * > Passo 5 - Cadastrar assinaturas dos pets no Financeiro
         * > Passo 6 - Pagar a adesão
         * --------------------------------------------------
         */

        try {

            list($input, $cliente, $error) = $this->cadastrarVendaERP($input);
            $finCliente = $this->cadastrarVendaFinanceiro($cliente, $input);
           
            return $cliente;
        } catch (\Exception $ex) {
            return response($ex->getMessage(), 500);
        }
    }


    /**
     * Cadastra os dados enviados no ERP
     *
     * @param array $input
     * @return void
     */
    public function cadastrarVendaERP(array $input) {
        
        try {
            // Cadastra o cliente
            list($input, $cliente, $error) = $this->cadastrarCliente($input);
            if ($error) {
                throw $error;
            }

            // Cria o usuário e vincula ao cliente
            $areaClienteController = (new AreaClienteController);
            $areaClienteController::$emailBoasVindasInsideSales = true;
            $areaClienteController->doCreateUser($cliente->id);

            // Cadastra os pets e vincula aos planos escolhidos
            $this->cadastrarPets($input, $cliente);

            return array($input, $cliente, false);
        } catch (\Throwable $e) {
            throw new \Exception('Falha ao cadastrar o cliente no sistema interno: ' . $e->getMessage());
        }
       
    }

    /**
     * Cadastra os dados enviados no Financeiro
     *
     * @param Clientes $cliente
     * @param array $input
     * @return void
     */
    public function cadastrarVendaFinanceiro(Clientes $cliente, array $input) {

        try {

            // Cadastra o cliente
            try {
                $finCliente = $this->cadastrarClienteFinanceiro($cliente);
               
            } catch(\Exception $e) {
                throw new \Exception(
                    "Erro na criação do cliente: " . $this->getExceptionMessage($e->getMessage())
                );
            }

            // Cria as assinaturas dos pets
            try {
                $this->cadastrarAssinaturaFinanceiro($cliente);
            } catch(\Exception $e) {
                throw new \Exception(
                    "Erro na criação das assinaturas: " . $this->getExceptionMessage($e->getMessage())
                );
            }

    
            if(!isset($input['cliente']['pagamento']['forma'])) {
                throw new \Exception('É necessário informar a forma de pagamento');
            }

            // Paga a adesão
            if ($input['cliente']['pagamento']['forma'] != 'Boleto') {
                $this->pagarPorCartao($finCliente, $cliente, $input);
            } else {
                $this->pagarPorBoleto($finCliente, $cliente, $input);
            }

        } catch (\Throwable $e) {
            throw new \Exception('Falha ao cadastrar ao cadastrar os dados do cliente no sistema financeiro - ' . $e->getMessage());
        }
        

    }

    /**
     * Cadastra o cliente enviado via formulário Inside Sales
     *
     * @param array $input
     * @return void
     */
    public function cadastrarCliente(array $input)
    {
        try {

            // TALVEZ POR CONFUSÃO FOI ADICIONADO MAIS UM CAMPO DE VENCIMENTO NO BANCO (dia_vencimento)
            // Sendo assim, manteremos as duas colunas pra não dar problema
            $input['cliente']['dados']['vencimento'] = $input['cliente']['dados']['dia_vencimento'];
            
            // Por precaução, pegamos a informação do 1° index do array de pets
            // Anteriormente utilizava o index 0 diretamente, mas nem sempre 
            // exista. Ex.: Quando o vendedor apaga os pets pra recadastrar.
            $primeiroPetIndex = 0;
            $keys = array_keys($input['pets']);
            if(count($keys) > 0) {
                $primeiroPetIndex = $keys[0];
            }

            $input['cliente']['dados']['vencimento'] = Carbon::createFromFormat('d/m/Y', $input['pets'][$primeiroPetIndex]['plano']['data_inicio_contrato'])->day;
            
            // Verifica se o cliente já foi cadastrado anteriormente
            $cliente = Clientes::where('email', $input['cliente']['dados']['email'])->whereRaw('DATE(created_at)', date('Y-m-d'))->first();
    
            if(isset($cliente)) {
                $cliente->update(array_merge($input['cliente']['dados'], $input['cliente']['endereco']));
            } else {
                $cliente = (new Clientes)->create(array_merge($input['cliente']['dados'], $input['cliente']['endereco']));
            }
        
            if (empty($input['numero_contrato'])) {
                $cliente->numero_contrato = $cliente->id;
            }

            
            

            $input['versao'] = 'v1';
            $input['aceite'] = false;
            $input['data_proposta'] = Carbon::today()->format('Y-m-d');
            
            unset($input['token']);
            unset($input['cep']);
            

            $dadosProposta = $input;
            unset($dadosProposta['cliente']['cartao']);

            $cliente->dados_proposta = json_encode([$dadosProposta]);
            $cliente->ativo = 0;
            $cliente->update();

            return array($input, $cliente, false);
        } catch (\Exception $exception) {
            return array($input, null, $exception);
        }
    }

    
    /**
     * Cadastra os pets enviados via form Inside Sales
     *
     * @param array $input
     * @param Clientes $cliente
     * @return void
     */
    protected function cadastrarPets(array $input, Clientes $cliente)
    {
        foreach ($input['pets'] as $dadosPet) {

            $pet = $dadosPet['pet'];

            $plano = (new Planos)->find($dadosPet['plano']['id_plano']);

            // Trata formatos
            $valor_plano = str_replace(',', '.', str_replace('.', '', $dadosPet['plano']['valor_plano']));
            $valor_plano = number_format($valor_plano, 2, '.', '');
            $valor_adesao = str_replace(',', '.', str_replace('.', '', $dadosPet['plano']['valor_adesao']));
            $valor_adesao = number_format($valor_adesao, 2, '.', '');
            $mes_reajuste = explode('/', $dadosPet['plano']['data_inicio_contrato']);
            $mes_reajuste = abs($mes_reajuste[1]);

            $dados = null;
            $dados = [
                'nome_pet' => $pet["nome_pet"],
                'tipo' => $pet["tipo"],
                'sexo' => $pet["sexo"],
                'id_raca' => $pet["id_raca"],
                'data_nascimento' => $pet["data_nascimento"],
                'contem_doenca_pre_existente' => $pet["contem_doenca_pre_existente"],
                'doencas_pre_existentes' => $pet["doencas_pre_existentes"],
                'observacoes' => $pet["observacoes"],
                'id_cliente' => $cliente->id,
                'ativo' => 0,
                'familiar' => $dadosPet['plano']['familiar'],
                'participativo' => $dadosPet['plano']['participativo'],
                'regime' => $dadosPet['plano']['regime'],
                'mes_reajuste' => $mes_reajuste,
                'valor' => $valor_plano,
                'numero_microchip' => '0',
                'vencimento' => $input['cliente']['dados']['dia_vencimento']
            ];

            // Verifica se o pet já foi cadastrado
            $newPet = Pets::where('id_cliente', $cliente->id)
                ->where('nome_pet', $pet['nome_pet'])
                ->where('tipo', $pet["tipo"])
                ->where('sexo', $pet['sexo'])
                ->where('id_raca', $pet['id_raca'])
                ->whereRaw('DATE(created_at)', date('Y-m-d'))
                ->first();

            if(isset($newPet)) {
                $newPet->update($dados);
            } else {
                $newPet = (new Pets)->create($dados);
            }
            
           
            $petPlanoDados = null;
            $petPlanoDados = [
                'id_pet' => $newPet->id,
                'id_plano' => $plano->id,
                'data_inicio_contrato' => $dadosPet['plano']['data_inicio_contrato'],
                'id_vendedor' => $dadosPet['plano']['id_vendedor'],
                'status' => 'P',
                'valor_momento' => $valor_plano,
                'adesao' => $valor_adesao,
            ];

            $petPlano = PetsPlanos::where('id_pet', $newPet->id)
                ->first();

            if(isset($petPlano)) {
                $petPlano->update($petPlanoDados);
            } else {
                $petPlano = (new PetsPlanos)->create($petPlanoDados);
            }

            $newPet->id_pets_planos = $petPlano->id;
            $newPet->save();
        }
    }

    /**
     * Cadastra o cliente no Sistema Financeiro
     *
     * @param Clientes $cliente
     * @return void
     */
    public function cadastrarClienteFinanceiro(Clientes $cliente) {
 
        $financeiro = new Financeiro();

        $form = [
            'name' => $cliente->nome_cliente,
            'email' => $cliente->email,
            'birthdate' => $cliente->data_nascimento->format('Y-m-d'),
            'cpf_cnpj' => $cliente->cpf,
            'status' => 'A',
            'due_day' => $cliente->dia_vencimento,
            'payment_type' => $cliente->forma_pagamento != 'boleto' ? 'creditcard' : 'boleto'
        ];

        if(isset($cliente->id_externo) && $cliente->id_externo > 0) {
            $response = $financeiro->post("/customer/{$cliente->id_externo}", $form);
        } else {

            $mergedForm = array_merge($form, [
                'address[0][zipcode]' => $cliente->cep,
                'address[0][address1]' => $cliente->rua,
                'address[0][number]' => $cliente->numero_endereco,
                'address[0][address2]' => $cliente->bairro,
                'address[0][city]' => $cliente->cidade,
                'address[0][country]' => 'Brasil',
                'address[0][state]' => $cliente->estado
            ]);

            $response = $financeiro->post('/customer', $mergedForm);
            $cliente->id_externo = $response->id;
            $cliente->save();
        }
        

        return $response;
    }

    /**
     * Cria as assinaturas dos pets do cliente no sistema financeiro
     *
     * @param Clientes $cliente
     * @return void
     */
    public function cadastrarAssinaturaFinanceiro(Clientes $cliente) {
        $pets = $cliente->pets;

        foreach ($pets as $pet) {

            $subscriptions = [];


            $plano = $pet->plano();
            $identificador = 'PLANO - '.$pet->nome_pet;
            $precoAdesao = $pet->petsPlanosAtual()->first()->adesao;

            $financeiro = new Financeiro();

            $interval = 'M';

            if ($pet->regime != "MENSAL") {
                $interval = 'A';   
            }

            $dados = [
                'customer_id' => $cliente->id_externo,
                'status' => 'A',
                'due_day' => $cliente->dia_vencimento,
                'price' => number_format($pet->valor, 2, ',', '.'),
                'membership_fee' => number_format($precoAdesao, 2, ',', '.'),
                'payment_type' => $cliente->forma_pagamento != 'boleto' ? 'creditcard' : 'boleto',
                'ref_code' => $pet->id,
                'product_id' => $plano->id,
                'name' => 'PLANO - '.$pet->nome_pet,
                'interval' => $interval,
                'start_at' => (new Carbon())->format('Y-m-d')
            ];

            $subscriptions[] = $dados;

            try {

                if(isset($pet->id_externo)) {
                    $response = $financeiro->post("/payment-subscription/{$pet->id_externo}", $dados);
                } else {
                    $response = $financeiro->post('/payment-subscription', $dados);
                    $pet->id_externo = $response->id;
                    $pet->save();
                }
                
            }
            catch (\Exception $e){
                
                $response = $e->getMessage();
                return [
                    "signed" => false,
                    "response" => $this->getExceptionMessage($response),
                    "dados" => $subscriptions
                ];
            
            }
        }
        
       
        return [
            "signed" => true,
            "response" => $response
        ];
            
    }
    
    /**
     * Faz o processo de cadastrar o cartão e criar uma transação
     *
     * @param \stdClass $finCliente
     * @param array $input
     * @return void
     */
    public function pagarPorCartao(\stdClass $finCliente, Clientes $cliente, array $input) {

        // Adiciona o cartão de crédito
        try {
            $this->salvarCartaoCreditoFinanceiro($finCliente, $input);
        }
        catch(\Exception $e) {
            throw new \Exception(
                "Erro ao armazenar os dados do cartão de crédito: ". $this->getExceptionMessage($e->getMessage())
            );
        }

        if($input['cliente']['pagamento']['gerar_cobranca'] == 'sim') {
            // Cria uma transação no cartão de crédito
            try {
                $pagamento = $this->criarTransacaoCartaoFinanceiro($finCliente, $input);

                $cobrancaId = $this->cadastrarCobranca($finCliente, $cliente, $input, $pagamento->id);

                $this->cadastrarPagamento($pagamento->amount_paid, $pagamento->paid_at, 2, $cobrancaId, $pagamento->id);

                $rdSendBoletoInsideSales = new RDSendCreditCardConfirmInsideSalesService($pagamento->amount_paid, $pagamento->due_date, $cliente);
                $rdSendBoletoInsideSales->process();
                
            } catch(\Exception $e) {
                (new Logger('ecommerce', 'clientes'))->register(
                    LogEvent::ERROR,
                    LogPriority::HIGH,
                    "Não foi possível realizar o pagamento do cliente ID: {$finCliente->id} (SF) via Inside Sales."
                );

                throw new \Exception(
                    "Erro ao criar uma transação com o cartão de crédito: " . $this->getExceptionMessage($e->getMessage())
                );
            }

        // Caso seja marcado para não gerar cobrança, apenas envia o e-mail para o cliente
        } else {

            $valor = str_replace(',', '.', str_replace('.', '', $input['cliente']['pagamento']['valor']));
      
            $rdSendBoletoInsideSales = new RDSendCreditCardConfirmInsideSalesService($valor, date('Y-m-d'), $cliente);
            $rdSendBoletoInsideSales->process();
        }
    }

    /**
     * Faz o processo de criar o boleto e enviar por e-mail para o cliente
     *
     * @param \stdClass $finCliente
     * @param array $input
     * @return void
     */
    public function pagarPorBoleto(\stdClass $finCliente, Clientes $cliente, array $input) {
        try {
            $boleto = $this->gerarBoletoFinanceiro($finCliente, $input);
            $this->cadastrarCobranca($finCliente, $cliente, $input, $boleto->payment_id, $boleto->hash);

            $rdSendBoletoInsideSales = new RDSendBoletoInsideSalesService($boleto, $cliente);
            $rdSendBoletoInsideSales->process();
        }
        catch(\Exception $e) {
            Log::info($e);
            throw new \Exception(
                "Erro ao gerar o boleto de pagamento: " . $this->getExceptionMessage($e->getMessage())
            );
        }
    }
    
    /**
     * Adiciona o cartão de crédito e vincula ao cliente
     *
     * @param \stdClass $finCliente
     * @param array $input
     * @return void
     */
    public function salvarCartaoCreditoFinanceiro(\stdClass $finCliente, array $input){
        
        if(!isset($input['cliente']['cartao']["numero_cartao"])) {
            throw new \Exception('É necessário informar o número do cartão');
        }

        if(!isset($input['cliente']['cartao']["nome_cartao"])) {
            throw new \Exception('É necessário informar o nome no cartão');
        }

        if(!isset($input['cliente']['cartao']["validade"])) {
            throw new \Exception('É necessário informar a validade do cartão');
        }

        if(!isset($input['cliente']['cartao']["cvv"])) {
            throw new \Exception('É necessário informar o CVV do cartão');
        }

        $numero = preg_replace('/\s+/', '', $input['cliente']['cartao']["numero_cartao"]);
        $mes = explode('/', $input['cliente']['cartao']["validade"])[0];
        $ano = explode('/', $input['cliente']['cartao']["validade"])[1];
        $cvv = $input['cliente']['cartao']["cvv"];

        $numero = preg_replace('/[^0-9]/', '', $numero);
        $bandeira = $this->getBandeiraPorNumeroCartao($numero);

        $cartao = [
            'brand' => $bandeira,
            'number' => $numero,
            'ccv' => $cvv,
            'holder' => $input['cliente']['cartao']['nome_cartao'],
            'valid' => sprintf('%s/%s',$mes,$ano),
            'hash' => $finCliente->hash
        ];
       
        $financeiro = new Financeiro();
        
        $finClienteResp = $financeiro->get('/customer/'.$finCliente->id);
        
        if(isset($finClienteResp, $finClienteResp->data, $finClienteResp->data->creditCard)) {   
            foreach($finClienteResp->data->creditCard as $card) {
       
                if(
                    $card->holder == $input['cliente']['cartao']['nome_cartao'] &&
                    $card->expireIn == sprintf('%s/%s',$mes,$ano) &&
                    $card->brand == $this->getBandeiraFinanceiroFormato($bandeira) &&
                    $card->number === substr($numero, -4)
                ) {
                    return $card;
                }
            }
        }

        return $financeiro->post('/customer/card/'.$finCliente->id, $cartao);
        
        
        
    }
    
    /**
     * Cria uma transação com o cartão de crédito no sistema financeiro
     *
     * @param \stdClass $finCliente
     * @param array $input
     * @return void
     */
    public function criarTransacaoCartaoFinanceiro(\stdClass $finCliente, array $input){

        if(!isset($finCliente->id)) {
            throw new \Exception('Não foi possível encontrar o cliente. Favor entrar em contato com o suporte técnico');
        }
        
        if(!isset($input['cliente']['pagamento']['valor'])) {
            throw new \Exception('É necessário informar o valor da transação');
        }

        $valor = str_replace(',', '.', str_replace('.', '', $input['cliente']['pagamento']['valor']));
        $valor = number_format($valor, 2, '.', '');

        if($valor < 1) {
            throw new \Exception('O valor precisa ser no mínimo R$1,00');
        }

        $parcelas = 1;
        if(isset($input['cliente']['cartao']['parcelas'])) {
            $parcelas = $input['cliente']['cartao']['parcelas'];
            if($parcelas > 12) {
                $parcelas = 12;
            }
        }



        $financeiro = new Financeiro();
        $session = $financeiro->fingerprint();
        $data = [
            'due_date' => date('Y-m-d'),
            'amount' => $input['cliente']['pagamento']['valor'],
            'customer_id' => $finCliente->id,
            'type' => 'creditcard',
            'fingerprint_ip' => request()->ip(),
            'fingerprint_session' => $session,
            'installments' => $parcelas,
            'tag' => join(';', ['e-commerce', 'inside-sales'])
        ];

        $pagamentos = $financeiro->get('/customer/' . $finCliente->id . '/payments');
        
        if(!empty($pagamentos->data ?? null)) {
            throw new \Exception('Já foi gerado uma cobrança para este cliente. Em caso de dúvidas, entre em contato com o suporte técnico');
        }

        $fin = $financeiro->post('/payment/transaction', $data);

        return $fin;
    }

    
    /**
     * Verifica qual é a bandeira do cartão de crédito enviado
     *
     * @param string $number
     * @return void
     */
    public function getBandeiraPorNumeroCartao(string $number) {
        $brands = array(
            'visa'       => '/^4\d{12}(\d{3})?$/',
            'mastercard' => '/^(5[1-5]\d{4}|677189)\d{10}$/',
            'diners'     => '/^3(0[0-5]|[68]\d)\d{11}$/',
            'discover'   => '/^6(?:011|5[0-9]{2})[0-9]{12}$/',
            'elo'        => '/^((((636368)|(438935)|(504175)|(451416)|(636297))\d{0,10})|((5067)|(4576)|(4011))\d{0,12})$/',
            'amex'       => '/^3[47]\d{13}$/',
            'jcb'        => '/^(?:2131|1800|35\d{3})\d{11}$/',
            'aura'       => '/^(5078\d{2})(\d{2})(\d{11})$/',
            'hipercard'  => '/^(606282\d{10}(\d{3})?)|(3841\d{15})$/',
            'maestro'    => '/^(?:5[0678]\d\d|6304|6390|67\d\d)\d{8,15}$/',
        );
       
        
        foreach ( $brands as $brand => $regex ) {
            if ( preg_match( $regex, $number ) ) {
                return $brand;
            }
        }
    }

    public function getBandeiraFinanceiroFormato($bandeiraNome) {

        switch($bandeiraNome) {
            case 'visa':
                return 'Visa';
            case 'mastercard':
                return 'Master';
            case 'diners':
                return 'Diners';
            case 'discover':
                return 'Discover';
            case 'elo':
                return 'Elo';
            case 'amex':
                return 'Amex';
            case 'jcb':
                return 'JCB';
            case 'aura':
                return 'Aura';
            case 'hipercard':
                return 'Hipercard';
            default:
                return $bandeiraNome;
        }
        
   
        
    }

    /**
     * Cadastra o boleto no sistema financeiro
     *
     * @param \stdClass $finCliente
     * @param array $input
     * @return void
     */
    public function gerarBoletoFinanceiro(\stdClass $finCliente, array $input){

        if(!isset($finCliente->id)) {
            throw new \Exception('Não foi possível encontrar o cliente. Favor entrar em contato com o suporte técnico');
        }

        if(!isset($input['cliente']['pagamento']['valor'])) {
            throw new \Exception('É necessário informar o valor do boleto');
        }

        $valor = str_replace(',', '.', str_replace('.', '', $input['cliente']['pagamento']['valor']));
        $valor = number_format($valor, 2, '.', '');

        if($valor < 1) {
            throw new \Exception('O valor precisa ser no mínimo R$1,00');
        }

        if(!isset($input['cliente']['boleto']['vencimento'])) {
            throw new \Exception('É necessário informar a data de vencimento do boleto');
        }

        $dados = [
            'amount' => $input['cliente']['pagamento']['valor'],
            'due_date' => Carbon::createFromFormat('d/m/Y',$input['cliente']['boleto']['vencimento'])->format('Y-m-d'),
            'reference' => date('m/Y'),
            'customer_id' => $finCliente->id,
            'obs' => 'Venda realizada via INSIDE SALES',
            'juros' => 0,
            'multa' => 0,
            'status' => 'PENDING',
            'status_code' => 1,
            'instrucao1' => '',
            'instrucao2' => '',
            'instrucao3' => '',
            'instrucao4' => '',
            'instrucao5' => '',
        ];

        $financeiro = new Financeiro();

        $pagamentos = $financeiro->get('/customer/' . $finCliente->id . '/payments');
        
        if(!empty($pagamentos->data ?? null)) {
            throw new \Exception('Já foi gerado uma cobrança para este cliente. Em caso de dúvidas, entre em contato com o suporte técnico');
        }
        return $financeiro->post('/boleto', $dados);
    }

    /**
     * Cadastra um histórico de cobrança no ERP
     *
     * @param \StdClass $finCliente
     * @param array $input
     * @param integer $finPagamentoId
     * @param string $finBoletoHash
     * @return void
     */
    public function cadastrarCobranca(\StdClass $finCliente, Clientes $cliente, array $input, int $finPagamentoId, string $finBoletoHash = null) {

        $valor = str_replace(',', '.', str_replace('.', '', $input['cliente']['pagamento']['valor']));
        $valor = number_format($valor, 2, '.', '');

        $novaCobranca = new Cobrancas();
        
        $novaCobranca->fill([
            'id_cliente' => $cliente->id,
            'competencia' => date('Y-m'),
            'valor_original' => $valor,
            'data_vencimento' => ($input['cliente']['pagamento']['forma'] != 'Boleto' ? date('Y-m-d') : Carbon::createFromFormat('d/m/Y',$input['cliente']['boleto']['vencimento'])->format('Y-m-d') ),
            'status' => 1,
            'complemento' => 'Primeiro pagamento Inside Sales',
            'id_financeiro' => $finPagamentoId,
            'hash_boleto' => $finBoletoHash ?? null
        ]);
        
        $novaCobranca->save();

        return $novaCobranca->id;
    }

    /**
     * Cadastra um pagamento
     *
     * @param float $valor
     * @param string $dataPagamento Y-m-d
     * @param integer $formaPagamento - 0: Boleto, 1: Débito, 2: Crédito
     * @param integer $cobrancaId
     * @param integer $finPagamentoId
     * @return void
     */
    public function cadastrarPagamento(float $valorPago, string $dataPagamento, int $formaPagamento, int $cobrancaId, int $finPagamentoId) {

        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$dataPagamento)) {
            throw new \Exception('É necessário que a data de pagamento esteja no formato YYYY-mm-dd');
        }

        if(!in_array($formaPagamento, [0,1,2])) {
            throw new \Exception('É necessário que a forma de pagamento seja de 0 a 2 (0: Boleto, 1: Débito, 2: Crédito)');
        }
        
        $novoPagamento = new Pagamentos();
        $novoPagamento->fill([
            'id_cobranca' => $cobrancaId,
            'data_pagamento' => $dataPagamento,
            'forma_pagamento' => $formaPagamento,
            'valor_pago' => $valorPago,
            'complemento' => 'Primeiro pagamento Inside Sales',
            'id_financeiro' => $finPagamentoId
        ]);
        $novoPagamento->save();

        return $novoPagamento->id;
    }


    /**
     * Recupera a mensagem da exceção, verificando se a mesma é
     * uma mensagem de erro válida do sistema financeiro
     *
     * @param $message
     * @return void
     */
    public function getExceptionMessage($response) {
        $message = $response;
        
        if(isset($response->error)) {
            $message = $response->error;
            if(isset($response->error->description)) {
                $message = $response->error->description;
            }
        }

        return $message;
    }

    ///////////////////////////////////////////////////////////////////////
    // MÉTODOS ANTIGOS DE CRIAÇÃO DE CLIENTE E ASSINATURA NO SUPERLÓGICA///
    ///////////////////////////////////////////////////////////////////////

    public function assinarSuperlogica($usuarioSuperlogica, $cliente)
    {

        $idUsuarioSuperlogica = $usuarioSuperlogica->data->id_sacado_sac;

        $pets = $cliente->pets;


        foreach ($pets as $pet) {

            $dados =  [
                'PLANOS' => [],
            ];

            $plano = $pet->plano();
            $identificador = $pet->nome_pet . "_" . $plano->nome_plano .  '_VD_InsideSales' . time();
            $precoAdesao = $pet->petsPlanosAtual()->first()->adesao;

            if ($pet->regime == "MENSAL") {
                $idPlanoSuperlogica = $plano->id_superlogica;
                $recorrencia = 1;
                $idProduto = "999999982";
            } else {
                $idPlanoSuperlogica = $plano->id_superlogica_anual;
                $recorrencia = 0;
                $idProduto = "3";
            }

            $dados['PLANOS'][] = [
                'ID_SACADO_SAC' => $usuarioSuperlogica->data->id_sacado_sac,
                'DT_CONTRATO_PLC' => (new Carbon())->format('m/d/Y'),
                "ST_IDENTIFICADOREXTRA_PLC" => $identificador,
                "ST_IDENTIFICADOR_PLC" => $identificador,
                "ID_PLANO_PLA" => $idPlanoSuperlogica,
                "FL_TRIAL_PLC" => 0,
                "FL_MULTIPLO_COMPO" => 1,
            ];

            // Cobrança da Mensalidade/Anuidade
            $dados['OPCIONAIS'][] = [
                "ID_PRODUTO_PRD" => $idProduto,
                "SELECIONAR_PRODUTO" => 1,
                "NM_QNTD_PLP" => 1,
                "valor_unitario" => $pet->valor,
                "FL_RECORRENTE_PLP" => $recorrencia
            ];

            // Cobrança da Adesão
            $dados['OPCIONAIS'][] = [
                "ID_PRODUTO_PRD" => "999999983",
                "SELECIONAR_PRODUTO" => 1,
                "NM_QNTD_PLP" => 1,
                "valor_unitario" => $precoAdesao,
                "FL_RECORRENTE_PLP" => 0
            ];

            $PlansManager = new Plans();
            $response = $PlansManager->sign($dados);

            if (is_array($response)) {
                $response = $response[0];
            }

            $cliente->id_externo = $idUsuarioSuperlogica;
            $cliente->update();
        }

        if ($response->status == "200") {

            return [
                "signed" => true,
                "response" => $response
            ];
        } else {
            return [
                "signed" => false,
                "response" => $response,
                "dados" => $dados,
            ];
        }
    }


    public function createSuperlogicaUser($input)
    {

        $postData = [
            'ST_TELEFONE_SAC' => $input['cliente']['dados']['telefone_fixo'],
            'ST_NOME_SAC' => $input['cliente']['dados']["nome_cliente"],
            'ST_NOMEREF_SAC' => $input['cliente']['dados']["nome_cliente"],
            'ST_CGC_SAC' => $input['cliente']['dados']['cpf'],
            'ST_EMAIL_SAC' => $input['cliente']['dados']["email"],
            'ST_DIAVENCIMENTO_SAC' => $input['cliente']['dados']["vencimento"],

            'ID_GRUPO_GRP' => 1,

            'ST_CEP_SAC' => $input['cliente']['endereco']['cep'],
            'ST_ENDERECO_SAC' => $input['cliente']['endereco']['rua'],
            'ST_NUMERO_SAC' => $input['cliente']['endereco']['numero_endereco'],
            'ST_BAIRRO_SAC' => $input['cliente']['endereco']['bairro'],
            'ST_COMPLEMENTO_SAC' => $input['cliente']['endereco']['complemento_endereco'],
            'ST_CIDADE_SAC' => $input['cliente']['endereco']['cidade'],
            'ST_ESTADO_SAC' => $input['cliente']['endereco']['estado'],
        ];

        if ($input['cliente']['forma_pagamento'] == 'Boleto') {
            $infoPagamento = [
                'FL_PAGAMENTOPREF_SAC' => 0
            ];
        } else {
            $infoPagamento = [
                'ST_CARTAO_SAC' => preg_replace('/\s+/', '', $input['cliente']['cartao']["numero_cartao"]),
                'ST_MESVALIDADE_SAC' => explode('/', $input['cliente']['cartao']["validade"])[0],
                'ST_ANOVALIDADE_SAC' => explode('/', $input['cliente']['cartao']["validade"])[1],
                'ST_SEGURANCACARTAO_SAC' => $input['cliente']['cartao']["cvv"],
                'FL_PAGAMENTOPREF_SAC' => 3
            ];
        }

        $postData = array_merge($postData, $infoPagamento);
        $response = (new Client())->register($postData);

        return $response;
    }

   

    
}
