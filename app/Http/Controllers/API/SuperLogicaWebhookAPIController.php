<?php

namespace App\Http\Controllers\API;

use App\Helpers\API\LifepetIntegration\Domains\Customer\Customer;
use App\Helpers\API\LifepetIntegration\Repositories\CustomerRepository;
use App\Http\Controllers\AppBaseController;
use App\Mail\Welcome;
use App\Models\Clientes;
use App\Models\Pets;
use App\Models\PetsPlanos;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SuperLogicaWebhookAPIController extends AppBaseController
{
    const SUPERLOGICA_WEBHOOK_TOKEN = 'b6753faaa340629c3bc1ad308168128ab1322f7f';
    const NAORESPONDA_LIFEPET_COM_BR = 'noreply@lifepet.com.br';

    /** @var CustomerRepository */
    private $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function active(Request $request)
    {
        $data = $request->all();

        Log::info("SUPERLOGICA | Webhook Payload: " . json_encode($data));

        if (!$data || !array_key_exists('data', $data)) {
            return $this->sendResponse(['status' => 400], 'Invalid Request', 400);
        }

        if ($data['validationtoken'] !== self::SUPERLOGICA_WEBHOOK_TOKEN) {
            return $this->sendResponse(['status' => 400], 'Invalid Token', 400);
        }

        $data = $data['data'];

        /** @var Clientes $customer */
        $customer = Clientes::where('new_superlogica_id', $data['id_sacado_sac'])->first();

        if (!$customer) {
            Log::error("SUPERLOGICA | Unable to find customer (using new_superlogica_id) to activate while SL webhook. Customer email: " . $data['st_email_sac']);
            Log::info("SUPERLOGICA | Trying to find customer by id_superlogica...");

            $customer = Clientes::where('id_superlogica', $data['id_sacado_sac'])->first();

            if (!$customer) {
                Log::error("SUPERLOGICA | Unable to find customer (using id_superlogica). Customer email: " . $data['st_email_sac'] . " | id_superlogica: " . $data['id_sacado_sac']);
                return $this->sendResponse(['status' => 404], 'Customer not found', 404);
            }
        }

        if ($customer->ativo) {
            return $this->sendResponse(['status' => 302], 'Customer already activated', 302);
        }

        Clientes::unsetEventDispatcher();
        $customer->ativo = true;
        $customer->save();

        $this->activatePets($customer, $data['compo_recebimento']);

        $customerDomain = $this->customerRepository->getById($customer->id);
        $planName = $this->getPlanName($data['compo_recebimento']);

        try {
            $this->notifySuccess($customerDomain, $planName);
        } catch (\Exception $e) {
            Log::error("Unable to notify team about new signature: " . $e->getMessage());
        }

        $this->welcomeEmail($customerDomain, $planName);

        return JsonResponse::create(['status' => 200]);
    }

    private function activatePets(Clientes $customer, $data)
    {
        $pets = $customer->pets()->get();

        foreach ($data as $planData) {

            $nomePet = trim(str_replace("Nome do Pet: ", "", $planData['st_identificador_plc']));

            /** @var Pets $pet */
            foreach ($pets as $pet) {
                if (Str::contains($nomePet, $pet->nome_pet)) {
                    $pet->ativo = true;
                    $pet->save();

                    PetsPlanos::unsetEventDispatcher();
                    $signature = $pet->petsPlanos()->where('id', '=', $pet->id_pets_planos)->first();
                    $signature->data_inicio_contrato = Carbon::now()->format('d/m/Y');
                    $signature->save();
                }
            }
        }

    }

    private function getPlanName($plans)
    {
        $planName = "";

        if (count($plans) === 1) {
            return $plans[0]['st_nome_pla'];
        }

        if (count($plans) >= 2) {
            $planName = [];
            foreach ($plans as $planData) {
                $planName[] = $planData['st_nome_pla'];
                return implode(", ", $planName);
            }
        }

        return $planName;
    }

    private function notifySuccess(Customer $customer, $planName) {

        if($customer->getId() == null && $customer->getName() == null) {
            Log::error('Falha ao enviar o e-mail de sucesso. É necessário que haja um cliente válido.');
            return;
        }

        Mail::send([], [], function ($message) use ($planName, $customer) {
            $dateTime = Carbon::now()->format('d/m/Y H:i:s');
            $sendTo = [
                'atendimento@lifepet.com.br',
                'leandro@lifepet.com.br',
                'thiago@vixgrupo.com.br'
            ];
            $message->to($sendTo)
                ->subject('E-commerce Plano - ' . $planName . ': Novo cadastro automático')
                ->from(self::NAORESPONDA_LIFEPET_COM_BR, 'Lifepet')
                ->setBody("
                <h3>Foi cadastrado com sucesso um cliente que adquiriu um plano ($planName) pelo e-commerce</h3>
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
                    Localização: {$customer->getAddress()->getCity()}/{$customer->getAddress()->getState()}<br>
                    Quantidade de pets: 1
                </p>
                
                <div>
                    <span>Nome: </span>
                    <strong>".$customer->getName()."</strong><br>
                
                    <span>Data/hora da tentativa:</span>
                    <strong>{$dateTime}</strong><br /><br>
                    <a href='http://app.lifepet.com.br/clientes/".$customer->getId()."/edit'>Clique aqui para ver mais informações no ERP</a><br />
                    <a href='https://lifepet.superlogica.net/clients/financeiro/sacados/id/".$customer->getExternalId()."?status=2'>Clique aqui para ver mais informações no Sistema financeiro</a>
                </div>
              ", 'text/html');
        });
    }


    private function welcomeEmail(Customer $customer, $planName) {

        if($customer->getId() == null && $customer->getName() == null) {
            Log::error('Falha ao enviar o e-mail de boas-vindas. É necessário que haja um cliente válido.');
            return;
        }

        /** @TODO Change to customer email after confirming is functional */
        Mail::to($customer->getEmail())->send(new Welcome($customer->getName(), $planName));
    }
}
