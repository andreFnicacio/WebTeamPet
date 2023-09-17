<?php
/**
 * Created by PhpStorm.
 * User: Breno
 * Date: 29/09/2020
 * Time: 15:06
 */

namespace App\Http\Controllers\API;


use App\Helpers\API\Financeiro\Financeiro;
use App\Helpers\API\LifepetIntegration\Domains\Customer\Customer;
use App\Helpers\API\LifepetIntegration\Domains\Pet\Pet;
use App\Helpers\API\LifepetIntegration\Persistences\ERP\ERP;
use App\Helpers\API\LifepetIntegration\Persistences\Finance\Finance;
use App\Helpers\API\RDStation\Services\RDCompraParaTodosBoasVindasService;
use App\Http\Controllers\Controller;
use App\Http\Util\Logger;
use App\LifepetCompraRapida;
use App\Models\Clientes;
use App\Models\Cobrancas;
use App\Models\LPTCodigosPromocionais;
use App\Models\PetsPlanos;
use App\Models\Planos;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

const NAORESPONDA_LIFEPET_COM_BR = 'noreply@lifepet.com.br';

class AssinaturasAPIController extends Controller
{
    /**
     * @var ERP
     */
    private $erp;
    /**
     * @var Finance
     */
    private $finance;

    /**
     * @var Customer
     */
    private $customer;
    /**
     * @var Pet[]
     */
    private $pets = [];

    private $cupom;

    public static $prices = [
        55 => [
            1 => 14.90,
            2 => 19.90,
            3 => 29.90,
            4 => 29.90,
            5 => 29.90,
            6 => 29.90,
        ],
        56 => [
            1 => 49.90,
            2 => 99.80,
            3 => 149.70,
            4 => 199.60,
            5 => 249.50,
            6 => 299.40,
        ],
        57 => [
            1 => 99.90,
            2 => 199.80,
            3 => 299.70,
            4 => 399.60,
            5 => 499.50,
            6 => 599.40,
        ],
        58 => [
            1 => 14.90,
            2 => 19.90,
            3 => 29.90,
            4 => 29.90,
            5 => 29.90,
            6 => 29.90,
        ],
        59 => [ //PARA TODOS - SP
            1 => 14.90,
            2 => 19.90,
            3 => 29.90,
            4 => 29.90,
            5 => 29.90,
            6 => 29.90,
        ],
        61 => [
            1 => 14.90,
            2 => 19.90,
            3 => 29.90,
            4 => 29.90,
            5 => 29.90,
            6 => 29.90,
        ],
    ];

    public function __construct() {
        $this->erp = new ERP();
        $this->finance = new Finance();
    }

    public function sign(Request $request, $hash)
    {
        $compraRapida = LifepetCompraRapida::where('hash', $hash)->
                                             where('concluido', 0)->
                                             where('pagamento_confirmado', 1)->
                                            orderBy('created_at', 'DESC')->first();

        if(!$compraRapida) {
            abort(404, 'Seu cadastro não foi encontrado em nosso sistema');
        }

        $cupom = $compraRapida->cupom;
        $this->cupom = $cupom;

        $input = array_merge($request->all(), [
            'cpf' => $compraRapida->cpf,
            'name' => $compraRapida->nome,
            'email' => $compraRapida->email,
            'pagamento_confirmado' => $compraRapida->pagamento_confirmado,
            'data_inicio_contrato' => $compraRapida->updated_at ?: $compraRapida->created_at,
            'payment_frequency' => strtoupper($compraRapida->regime),
            'cupom' => $cupom ?: null
        ]);

        try {
            $this->adaptCustomer($input);
            $this->adaptPets($input);
        } catch (\Exception $e) {
            $this->notifyAdaptError($e->getMessage(), null);
            throw new \Exception($e->getMessage());
        }

        try {
            $this->insertIntoERP();
        } catch (\Exception $e) {
            //$this->notifyERPError($e->getMessage());
            throw new \Exception($e->getMessage());
        }

        if(!$compraRapida->pagamento_confirmado) {
            $this->erp->customer->addNote($this->customer->getId(), 'Cliente cadastrado de forma INATIVA pois o pagamento não pôde ser confirmado.');
        }

        $this->erp->customer->setPaymentType($this->customer->getId(), 'cartao');
        $this->erp->customer->linkWithFinance($this->customer->getId());

        $compraRapida->concluido = 1;
        $compraRapida->update();

        $rd = new RDCompraParaTodosBoasVindasService($compraRapida->plano);
        $rd->process($compraRapida);

        //Check previous invoices
        $cliente = Clientes::find($this->customer->getId());

        try {
            if($cliente) {
                $finance = new Financeiro();
                $data = $finance->get("/customer/{$cliente->id_externo}/payments");
                if($data) {
                    foreach($data->data as $payment) {
                        if($payment->status != 'AVAILABLE') {
                            continue;
                        }

                        $idFinanceiro = null;
                        $valor = $payment->amount;
                        $complemento = "Sincronia automática.";
                        if($payment->id) {
                            $complemento .= " Fatura #{$payment->invoice_id} (SF)";
                            $idFinanceiro = $payment->id;
                        }
                        $dataVencimento = Carbon::createFromFormat('Y-m-d', $payment->due_date);
                        $competencia = Carbon::createFromFormat('m/Y', $payment->reference)->format('Y-m');

                        Cobrancas::cobrancaAutomatica($cliente, $valor, $complemento, $dataVencimento, $competencia, $idFinanceiro, true, $payment->id);
                    }
                }
            }
        } catch (\Exception $e) {
            (new Logger('ecommerce'))->register(\App\Http\Util\LogEvent::ERROR, \App\Http\Util\LogPriority::MEDIUM,
                "Não foi possível obter os pagamentos do cliente #{$cliente->id} no SF. Exceção: {$e->getMessage()}\n{$e->getFile()}:{$e->getLine()}\n"
            );
        }

        $this->notifySuccess($compraRapida->plano->nome_plano);

        return redirect()->away('https://lifepet.com.br/obrigado-final');

        //return view('assinaturas.finalizado');
    }

    protected function adaptCustomer(array $data) {
        $eBillingBirthdate = \DateTime::createFromFormat('Y-m-d', $data['birthdate']);
        $eBillingBirthdate = $eBillingBirthdate->format('Y-m-d');

        $this->customer = new Customer();

        $this->customer->populate([
            'name' => strtoupper($data['name']),
            'email' => $data['email'],
            'phone' => preg_replace( '/[^0-9]/', '', $data['phone']),
            'obs' => 'Cliente cadastrado automaticamente via integração e-commerce',
            'cpf' => preg_replace( '/[^0-9]/', '', $data['cpf']),
            'birthdate' => $eBillingBirthdate,
            'sex' => ($data['sex'] == 'Masculino' ? 'M' : ($data['sex'] == 'Feminino' ? 'F' : 'O')),
            'ip' => $data['customer_ip_address'] ?? null,
            'payment_due_day' => Carbon::now()->day,
            'approved' => false,
            'active' => $data['pagamento_confirmado']
        ]);

        $this->customer->address->populate([
            'postal_code' => $data['cep'],
            'street' => $data['street'],
            'number' => $data['address_number'],
            'neighborhood' => $data['neighbourhood'],
            'city' => $data['city'],
            'state' => $data['state']
        ]);
    }

    public function adaptPets($data) {
        if(!isset($data['pets']) || !is_array($data['pets'])) {
            throw new \Exception($this->adaptNotFoundMessage("pets", "Array de planos comprados"));
        };

        $pets = [];
        foreach($data['pets'] as $k => $item) {

            $eItemPetBirthdate = \DateTime::createFromFormat('Y-m-d', $item['birthdate']);

            if(!$eItemPetBirthdate) {
                throw new \Exception("O campo Data de nascimento enviado não está no formato válido (d/m/Y)");
            }

            $eItemPetBirthdate = $eItemPetBirthdate->format('Y-m-d');

            $item['plan'] = (int) $data['id_plano'];
            //$plan = Planos::where('nome_plano', 'Lifepet Para Todos - 2020/2')->first();
            $plan = Planos::find($item['plan']);
            if(!$plan) {
                throw new \Exception("Não foi possível encontrar o plano com o id ({$item['plan']})");
            }

            $priceToPay = \App\Http\Controllers\LifepetParaTodosController::obterPrecoPlano($plan, count($data['pets']), $data['payment_frequency']);
            if($data['cupom']) {
                /**
                 * @var LPTCodigosPromocionais $cupom
                 */
                $cupom = $data['cupom'];
                if($cupom->permanente) {
                    $priceToPay = $cupom->aplicar($priceToPay);
                }
            }

            if(is_null($priceToPay)) {
                $price = self::$prices[$item['plan']][count($data['pets'])]/count($data['pets']);
            } else {
                $price = $priceToPay / count($data['pets']);
            }

            $pets[] = [
                'name' => strtoupper($item['name']),
                'active' => $data['pagamento_confirmado'],
                'species' => $item['species'] == 'Cão' ? 'CACHORRO' : 'GATO',
                'breed_id' => (int) $item['breed'],
                'sex' => $item['sex'] == 'Macho' ? 'M' : 'F',
                'birthdate' => $eItemPetBirthdate,
                'contains_pre_existing_disease' => false,
                'familiar' => false,
                'participative' => false,
                'exam_last_12_months' => false,
                'payment_frequency' => $data['payment_frequency'],
                'payment_value' => $price,
                'payment_due_day' => isset($data['data_inicio_contrato']) ? $data['data_inicio_contrato']->day : Carbon::now()->day,
                'payment_readjustment_month' => isset($data['data_inicio_contrato']) ? $data['data_inicio_contrato']->month : Carbon::now()->month,
                'obs' => 'Pet cadastrado automaticamente via integração e-commerce',

                'plan' => [
                    'plan_id' => (int)$plan->id,
                    //'payment_frequency' => $eItemPaymentFrequency,
                    'date_init_contract' => isset($data['data_inicio_contrato']) ? $data['data_inicio_contrato']->format('Y-m-d') : Carbon::today()->format('Y-m-d'),
                    'payment_value' => $price,
                    'status' => 'P',
                    'membership_fee' => 0.00,
                    'participative' => 0,
                ]
            ];

        }

        if(!empty($pets)) {
            foreach($pets as $pet) {
                $petObj = new Pet();
                $petObj->populate($pet);
                $petObj->plan->populate($pet['plan']);

                $this->pets[] = $petObj;
            }
        }
    }

    private function insertIntoERP() {
        // Verifica se os planos existem, se estão ativos e se tem ID externo (do sistema financeiro)
        if(!empty($this->pets)) {

            // Percorre os pets para verificar
            foreach($this->pets as $k => $pet) {
                $this->getPlanExternalId($pet);
            }
        }

        // Verifica se o cliente com o mesmo cpf existe e o traz se existir
        $customer = $this->erp->customer->getBy('cpf', $this->customer->getCPF());

        //
        //  TODO: VERIFICAR SE O CLIENTE COM CPF EXISTE E SE ESTÁ INATIVO. SE ESTIVER INATIVO, ATUALIZAR OS DADOS (VERIFICAR POSSIBILIDADE)
        //

        // Se não existir o cliente
        if(empty($customer)) {

            // Insere o cliente enviado
            $customerId = $this->erp->customer->save($this->customer);
            $this->erp->customer->addNote($customerId, 'Cliente, pet(s) e plano(s) cadastrados automaticamente via integração e-commerce.');
            // Insere o ID do cliente recém inserido no obj Customer
            $this->customer->setId($customerId);

            //Define o numero de contrato baseado no ID. Só pode ser feito pós-cadastro
            $cliente = Clientes::find($customerId);
            if($cliente) {
                $cliente->update([
                    'numero_contrato' => $cliente->id
                ]);
            }

            //Verificar se existem pagamentos presentes no SF

        } else {
            $this->customer = $customer;
            $this->erp->customer->addNote($this->customer->getId(), 'Pets e planos cadastrados automaticamente via integração e-commerce. Cliente já estava cadastrado anteriormente.');
        }



        //$this->sale->setClientId($this->customer->getId());

        // Verifica se há pets para inserir
        if(!empty($this->pets)) {
            // Percorre os pets para inserir
            foreach($this->pets as $k => $pet) {

                // Preenche o id do cliente no obj Pet
                $pet->setCustomerId($this->customer->getId());

                // Insere o pet enviado
                $petId = $this->erp->pet->save($pet);

                $petModel = \App\Models\Pets::find($petId);

                $this->pets[$k]->setId($petId);

                // Preenche o id do pet no obj Plan
                $this->pets[$k]->plan->setPetId($petId);

                // Insere o registro do plano adquirido do pet
                $petPlanId = $this->erp->petPlan->save($this->pets[$k]->plan, PetsPlanos::TRANSICAO__NOVA_COMPRA);

                $this->pets[$k]->setPetPlanId($petPlanId);

                //TODO: Verificar se é de microchip virtual ou não
                $plan = \App\Models\Planos::find($pet->plan->getPlanId());

                if($plan && $plan->microchip_virtual) {
                    if($petModel && !$petModel->numero_microchip) {
                        $petModel->numero_microchip = 'PT' . $petModel->id;
                    }
                }

                $petModel->update();

                $this->erp->pet->save($this->pets[$k]);
                
                if($this->cupom){
                    $this->erp->customer->addNote($this->customer->getId(), "Pet " . $petModel->nome_pet . " cadastrado via integração e-commerce no plano " . $plan->nome_plano . " usando o cupom " . $this->cupom->codigo);
                }
            }
        }
    }

    private function petCount() {
        return count($this->pets);
    }

    public function getPlanExternalId(Pet $pet) {
        // Busca o plano de acordo com o adicionado na instancia Pet\Plan e retorna uma instância de Plan\Plan
        $plan = $this->erp->plan->getById($pet->plan->getPlanId());

        // Verifica se o plano está ativo
        if($plan->getActive() == false) {
            throw new \Exception('O plano ' . $plan->getName() . '('.$plan->getId().') não está ativo.');
        }

        // Verifica se o regime de pagamento do plano do pet é anual e se existe um id do plano externo para regime anual
        if($pet->getPaymentFrequency() == 'ANUAL') {

            // retorna erro se não houver id_externo_anual cadastrado pro plano
            if($plan->getExternalAnualId() == null) {
                throw new \Exception('O plano ' . $plan->getName() . '('.$plan->getId().') não tem ID externo anual (do sistema financeiro) cadastrado.');
            }

            // retorna o id_externo_anual cadastrado pro plano
            return $plan->getExternalAnualId();

        }

        // retorna erro se não houver id_externo cadastrado pro plano
        if($plan->getExternalId() == null) {
            throw new \Exception('O plano ' . $plan->getName() . '('.$plan->getId().') não tem ID externo (do sistema financeiro) cadastrado.');
        }

        // retorna o id_externo cadastrado pro plano
        return $plan->getExternalId();
    }

    private function insertIntoFinanceSystem() {
        // Cadastra o cliente no sistema financeiro


        if($this->customer->getExternalId() == null) {
            // TODO: VERIFICAR NO ERP SE O CLIENTE JA EXISTE COM O EMAIL
            $finCustomerId = $this->finance->createCustomer($this->customer);

            $this->customer->setExternalId($finCustomerId);
            $this->erp->customer->save($this->customer);
        }

        if(isset($this->pets)) {
            foreach($this->pets as $k => $pet) {

                $finPetId = $this->finance->createSubscription($this->customer, $pet, $this->getPlanExternalId($pet), true);

                $this->pets[$k]->setExternalId($finPetId);
                $this->erp->pet->save($this->pets[$k]);

                $this->sale->setStatus('sucesso');
            }
        }
    }

    public function insertIntoFinanceSystemFix(int $customerId) {
        $customer = $this->erp->customer->getById($customerId);
        $pets = $this->erp->pet->getBy('id_cliente', $customerId);

        if(!$customer->getExternalId()) {
            $finCustomerId = $this->finance->createCustomer($customer);
            $customer->setExternalId($finCustomerId);
            $this->erp->customer->save($customer);
        }

        if(isset($pets)) {
            foreach($pets as $k => $pet) {

                if($pet->getExternalId()) {
                    continue;
                }
                $petPlan = $this->erp->petPlan->getById($pet->getPetPlanId());
                $pets[$k]->setPlan($petPlan);

                $finPetId = $this->finance->createSubscription($customer, $pet, $this->getPlanExternalId($pet), true);

                $pets[$k]->setExternalId($finPetId);
                $this->erp->pet->save($pets[$k]);
            }
        }
    }

    public function adaptNotFoundMessage(string $field, $description = null) : string {
        return "O padrão recebido está inválido: Não foi encontrado o campo '{$field}'" .( isset($description) ? " ({$description})" : '');
    }

    private function notifyAdaptError($errorMessage, $saleId = null) {

        if(!isset($saleId) || $saleId == 0) {
            $saleId = 'Não encontrado';
        }

        try {
            Mail::send([], [], function ($message) use ($errorMessage, $saleId) {
                $dateTime = Carbon::now()->format('d/m/Y H:i:s');
                $message->to('breno.grillo@vixsolution.com')
                    //->to('alexandre@lifepet.com.br')
                    ->subject('E-commerce - Lifepet Para Todos: Falha ao fazer o cadastro automático')
                    ->from(NAORESPONDA_LIFEPET_COM_BR, 'Lifepet')
                    ->setBody("
                <h3>Ocorreu um erro ao cadastrar automaticamente um cliente que adquiriu um plano pelo e-commerce</h3>
                <p>
                    Este erro ocorreu antes de começar o processo de cadastro, especificamente quando estávamos validando as informações enviadas.
                </p>
                <p>
                    Sendo assim, será necessário averiguar se é um cliente válido e fazer o cadastro manual deste cliente.
                </p>
                <div>
                    <span>ID da venda (e-commerce): </span>
                    <strong>{$saleId}</strong><br />

                    <span>Data/hora da tentativa:</span>
                    <strong>{$dateTime}</strong><br />

                    <span>Descrição do erro:</span>
                    <strong>{$errorMessage}</strong>
                    
                </div>
                <p>
                
                </p>
              ", 'text/html');
            });
        } catch (\Exception $e) {
            (new Logger('ecommerce'))->register(\App\Http\Util\LogEvent::ERROR, \App\Http\Util\LogPriority::MEDIUM,
                "Não foi possível notificar o erro por email. Exceção: {$e->getMessage()}\n{$e->getFile()}:{$e->getLine()}\nAdapt Error: {$errorMessage}"
            );
        }
    }

    private function notifyERPError($errorMessage, $saleId = null) {

        if(!isset($saleId) || $saleId == 0) {
            $saleId = 'Não encontrado';
        }

        Mail::send([], [], function ($message) use ($errorMessage, $saleId) {
            $dateTime = Carbon::now()->format('d/m/Y H:i:s');
            $message->to('breno.grillo@vixsolution.com')
                //->to('alexandre@lifepet.com.br')
                ->subject('E-commerce - Lifepet Para Todos: Falha ao fazer o cadastro automático')
                ->from(NAORESPONDA_LIFEPET_COM_BR, 'Lifepet')
                ->setBody("
                <h3>Ocorreu um erro ao cadastrar automaticamente um cliente que adquiriu um plano pelo e-commerce</h3>
                <p>
                    Este erro ocorreu ao tentar cadastrar as informações enviadas no ERP.
                </p>
                <p>
                    Sendo assim, será necessário averiguar em que momento falhou pra continuar o cadastro manual a partir dali.
                </p>
                <p>
                    Favor entrar em contato com o TI para mais informações.
                </p>
                <div>
                    <span>ID da venda (e-commerce): </span>
                    <strong>{$saleId}</strong><br />

                    <span>Data/hora da tentativa:</span>
                    <strong>{$dateTime}</strong><br />

                    <span>Descrição do erro:</span>
                    <strong>{$errorMessage}</strong>
                    
                </div>
                <p>
                
                </p>
              ", 'text/html');
        });
    }

    private function notifyFinanceSystemError($errorMessage, $saleId = null) {

        if(!isset($saleId) || $saleId == 0) {
            $saleId = 'Não encontrado';
        }

        Mail::send([], [], function ($message) use ($errorMessage, $saleId) {
            $dateTime = Carbon::now()->format('d/m/Y H:i:s');
            $message->to('breno.grillo@vixsolution.com')
                //->to('alexandre@lifepet.com.br')
                ->subject('E-commerce - Lifepet Para Todos: Falha ao fazer o cadastro automático')
                ->from(NAORESPONDA_LIFEPET_COM_BR, 'Lifepet')
                ->setBody("
                <h3>Ocorreu um erro ao cadastrar automaticamente um cliente que adquiriu um plano pelo e-commerce</h3>
                <p>
                    Este erro ocorreu ao tentar cadastrar as informações enviadas no Sistema Financeiro.
                </p>
                <p>
                    Sendo assim, será necessário averiguar em que momento falhou pra continuar o cadastro manual a partir dali.
                </p>
                <p>
                    Favor entrar em contato com o TI para mais informações.
                </p>
                <div>
                    <span>ID da venda (e-commerce): </span>
                    <strong>{$saleId}</strong><br />

                    <span>Data/hora da tentativa:</span>
                    <strong>{$dateTime}</strong><br />

                    <span>Descrição do erro:</span>
                    <strong>{$errorMessage}</strong>
                    
                </div>
                <p>
                
                </p>
              ", 'text/html');
        });
    }

    private function notifySuccess($plano =  'Lifepet Para Todos') {

        if($this->customer == null || $this->customer->getId() == null && $this->customer->getName() == null) {
            throw new \Exception('Falha ao enviar o e-mail de sucesso. É necessário que haja um cliente válido.');
        }

        Mail::send([], [], function ($message) use ($plano) {
            $dateTime = Carbon::now()->format('d/m/Y H:i:s');
            $sendTo = [
                'atendimento@lifepet.com.br',
                'breno.grillo@vixsolution.com',
                'alexandre.moreira@lifepet.com.br',
                'thiago@vixgrupo.com.br'
            ];
            $message->to($sendTo)
                ->subject('E-commerce LPT - ' . $plano . ': Novo cadastro automático')
                ->from(NAORESPONDA_LIFEPET_COM_BR, 'Lifepet')
                ->setBody("
                <h3>Foi cadastrado com sucesso um cliente que adquiriu um plano ($plano) pelo e-commerce</h3>
                <p>
                    Informamos que um cliente adquiriu um plano pelo e-commerce e conseguimos cadastrar automaticamente no ERP e no sistema financeiro.
                </p>

                <p>
                    Por favor, pedimos que analise essa compra pra certificar que tudo está correto com o seu cadastro. 
                    
                </p>
                <p>
                    <strong>
                    É importante informar ao cliente que é necessário baixar o aplicativo para cadastrar seus documentos.
                    </strong>
                </p>

                <p>
                    <strong>
                        Lembramos também que é necessário solicitar os dados de cartão de crédito ao cliente, visto que essa informação é confidencial no momento da integração.
                    </strong>
                </p>
                
                <p>
                    Localização: {$this->customer->getAddress()->getCity()}/{$this->customer->getAddress()->getState()}<br>
                    Quantidade de pets: {$this->petCount()}
                </p>
                
                <div>
                    <span>Nome: </span>
                    <strong>".$this->customer->getName()."</strong><br>
                
                    <span>Data/hora da tentativa:</span>
                    <strong>{$dateTime}</strong><br /><br>
                    <a href='http://app.lifepet.com.br/clientes/".$this->customer->getId()."/edit'>Clique aqui para ver mais informações no ERP</a><br />
                    <a href='https://lifepet.superlogica.net/clients/financeiro/sacados/id/".$this->customer->getExternalId()."?status=2'>Clique aqui para ver mais informações no Sistema financeiro</a>
                </div>
              ", 'text/html');
        });
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

    public static function getPrice($id) {
        return self::$prices[$id];
    }
}