<?php

namespace App\Helpers\API\Financeiro;

use App\Helpers\API\Financeiro\Services\Customer;
use App\Helpers\API\Financeiro\Services\Subscription;
use App\Http\Util\LogEvent;
use App\Http\Util\LogPriority;
use App\Models\Clientes;
use Carbon\Carbon;

class FinanceRepository
{

    private $customer;
    private $subscription;
    private $logger;
    private $cliente;

    public function __construct(Clientes $cliente=null, $logs = null)
    {
        $this->logger = $logs;
        $this->cliente = $cliente;
        $this->customer = new Customer();
        $this->subscription = new Subscription();
    }

    public function saveLog($event = LogEvent::NOTICE, $priority=LogPriority::HIGH, $message='', $id=null)
    {
        if ($this->logger !== null)
        {
            $this->logger->register(
                $event,
                $priority,
                $message,
                $id
            );
        }
    }

    private function verifyCPF($customer, $cpf)
    {

    }
    private function verifyRefCode($customer)
    {

    }
    public function verifyStatusCode($response)
    {
        if (isset($response->httpCode) && ($response->httpCode === 200 || $response->httpCode === 201))
            return true;
        return false;
    }

    public function getCustomerByDocument()
    {
        return $this->customer->getByDocument($this->cliente->cpf);
    }
    public function checkExistsCustomerCPF()
    {

        $customer_cpf = $this->customer->getByDocument($this->cliente->cpf);

        if (!$this->verifyStatusCode($customer_cpf)) {

            $this->cliente->sfIntegration->error_cpf = Carbon::now();

            $message = "O cadastro do cliente {$this->cliente->nome_cliente} com o CPF {$this->cliente->cpf} não foi encontrado no Sistema Financeiro. \n" . json_encode($customer_cpf);
            $this->saveLog(LogEvent::NOTICE, LogPriority::HIGH, $message, $this->cliente->id);

            return false;

        }
        if ($customer_cpf->body->ref_code != $this->cliente->id_externo) {

            $this->cliente->sfIntegration->error_customer = Carbon::now();

            $message =  "O cadastro do cliente {$this->cliente->nome_cliente} achou outra conta vinculada ao Sistema Financeiro. \n" . json_encode($customer_cpf);
            $this->saveLog(LogEvent::NOTICE, LogPriority::HIGH, $message, $this->cliente->id);
            return false;
        }

        return $customer_cpf;

    }

    public function checkCustomerByRefCode()
    {
        $customer = $this->customer->getByRefCode($this->cliente->id_externo);
        if (!$this->verifyStatusCode($customer))
        {
            $this->cliente->sfIntegration->error_customer = Carbon::now();

            $message = "O cadastro do cliente {$this->cliente->nome_cliente} não encontrou conta associada no Sistema Financeiro. \n".json_encode($customer);
            $this->saveLog(LogEvent::NOTICE, LogPriority::HIGH, $message, $this->cliente->id);
            return false;
        }

        if ($customer->body->cpf_cnpj != $this->cliente->cpf)
        {

            $this->cliente->sfIntegration->error_cpf = Carbon::now();

            $message = "O cadastro do cliente {$this->cliente->nome_cliente} está com CPF divergente do Sistema Financeiro. \n".json_encode($customer);
            $this->saveLog(LogEvent::NOTICE, LogPriority::HIGH, $message, $this->cliente->id);
            return false;
        }
        return $customer;
    }

    public function updateSubscriptions($customer)
    {
        /**
         * Check Pets
         */
     //   $petsPlanos = $pets->petsPlanosAtual()->first();
     //   $petsPlanos = $pets->petsPlanosAtual()->first();

        foreach($this->cliente->pets as $pet)
        {


            /**
             * Verifica se vai subir o plano do cliente para o financeiro
             */
            $planoPets = $pet->petsPlanosAtual()->first();

            if (
                ($planoPets !== null && $planoPets->id_conveniada !== null) ||
                $planoPets === null
            )
            {
                continue;
            }

            $subscriptions = new \stdClass;
            $subscriptions->id = null;
            if (isset($customer->body->subscription))
                foreach($customer->body->subscription as $key => $subscription)
                {

                    if ($subscription->ref_code == $pet->id)
                    {

                        $customerSubscription = $subscription;
                        unset($customer->body->subscription[$key]);
                        break;
                    }
                }





            $subscriptions->id = isset($customerSubscription) ? (int) $customerSubscription->id : null;
            $subscriptions->status = $pet->ativo ? 'A' : 'I';//I ou A
            $subscriptions->customer_id = $customer->body->id;
            $subscriptions->product_id = $pet->plano()->id;//
            $subscriptions->ref_code = $pet->id;
            $subscriptions->name = 'PLANO - ' . strtoupper($pet->nome_pet);
            $subscriptions->interval = $pet->regime === 'MENSAL' ? 'M' : 'A';
            //  $subscriptions->due_date = '';//$customer->body->due_date; // yyyy-mm-dd
            $subscriptions->due_day = $customer->body->due_day; //integer
            $subscriptions->price =  number_format((($planoPets->valor_momento) * 100), '0', '', ''); // financeiro tira , e . e divide por 100
            $subscriptions->payment_type = $customer->body->payment_type; //boleto ou creditcard
            $subscriptions->days_free = 0;
            // $subscriptions->start_at = null;
            // $subscriptions->end_at = null;
            $subscriptions->membership_fee = 0;
            $subscriptions->auto_renewal = 'N';
            if ($planoPets->valor_momento == 0 ||  $pet->regime !== 'MENSAL')
            {
                $subscriptions->status = 'I';
            }
            //dd($planoPets, number_format(($planoPets->valor_momento * 100), '0', '', ''), $subscription, $subscriptions);
            /**
             *  Verifica se já tem o plano, para criar ou update.
             */
            if ($subscriptions->id !== null)
            {
                $id = $subscriptions->id;
                unset($subscriptions->id);
                $res = $this->subscription->update($id, $subscriptions);

                /**
                 * Logs
                 */
                if ($this->verifyStatusCode($res)) {

                    $message = "O cadastro do cliente {$this->cliente->nome_cliente} atualizou plano do pet ID {$pet->id} no Sistema Financeiro. \n";
                    $this->saveLog(LogEvent::NOTICE, LogPriority::MEDIUM, $message, $this->cliente->id);

                } else {
                    $this->cliente->sfIntegration->error_subscription = Carbon::now();

                    $message = "O cadastro do cliente {$this->cliente->nome_cliente} teve um erro ao dar update no plano do pet ID {$pet->id} no Sistema Financeiro. \n".json_encode($res);
                    $this->saveLog(LogEvent::NOTICE, LogPriority::HIGH, $message, $this->cliente->id);
                }

            } else {
                if ($subscriptions->status === 'A')
                {
                    $res = $this->subscription->create($subscriptions);


                    /**
                     * logs
                     */
                    if ($this->verifyStatusCode($res)) {

                        $message = "O cadastro do cliente {$this->cliente->nome_cliente} criou o plano do pet ID {$pet->id} no Sistema Financeiro. \n";
                        $this->saveLog(LogEvent::NOTICE, LogPriority::MEDIUM, $message, $this->cliente->id);

                    } else {
                        $this->cliente->sfIntegration->error_subscription = Carbon::now();

                        $message = "O cadastro do cliente {$this->cliente->nome_cliente} teve um erro ao criar plano do pet ID {$pet->id} do Sistema Financeiro. \n".json_encode($res);
                        $this->saveLog(LogEvent::NOTICE, LogPriority::HIGH, $message, $this->cliente->id);

                    }
                }
            }

            unset($subscriptions);
            unset($customerSubscription);

        }
        $this->disableSubscription($customer->body->subscription);
    }

    /**
     * @param $subscriptions
     * Excluir
     *
     */
    public function disableSubscription($subscriptions)
    {
        foreach($subscriptions as $subscription)
        {

            $form = new \stdClass();
            $form->status = 'I';

            $id = $subscription->id;

            $res = $this->subscription->update($id, $form);

            /**
             * Logs
             */
            if ($this->verifyStatusCode($res)) {

                $message = "O cadastro do cliente {$this->cliente->nome_cliente} desabilitou o plano do pet ID {$id} no Sistema Financeiro. \n";
                $this->saveLog(LogEvent::NOTICE, LogPriority::MEDIUM, $message, $this->cliente->id);

            } else {
                $this->cliente->sfIntegration->error_subscription = Carbon::now();

                $message = "O cadastro do cliente {$this->cliente->nome_cliente} teve um erro ao dar update para desabilitar no plano do pet ID {$id} no Sistema Financeiro. \n".json_encode($res);
                $this->saveLog(LogEvent::NOTICE, LogPriority::HIGH, $message, $this->cliente->id);
            }

        }

    }

}