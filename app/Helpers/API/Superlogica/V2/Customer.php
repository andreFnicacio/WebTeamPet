<?php


namespace App\Helpers\API\Superlogica\V2;


use App\Helpers\API\Superlogica\V2\Utils\Date;
use App\LifepetCompraRapida;
use App\Models\Clientes;
use Illuminate\Support\Facades\Log;

class Customer
{
    const ENDPOINT = '/v2/financeiro/clientes';

    private $client;

    public function __construct()
    {
        $this->client = Client::getInstance();
    }

    //get
    public function getById($id)
    {
        return $this->client->get(self::ENDPOINT, [
            'form_params' => [
                'id' => $id
            ],
        ]);
    }

    public function getByCpf($cpf, $simplificado = false)
    {
        return $this->client->get(self::ENDPOINT, [
            'query' => [
                'pesquisa' => $cpf,
                'apenasColunasPrincipais' => (int) $simplificado
            ],
        ]);
    }

    public function cpfAlreadyInUse($cpf): bool
    {
        return !empty($this->getByCpf($cpf));
    }

    public function getByEmail($email, $simplificado = true)
    {
        return $this->client->get(self::ENDPOINT, [
            'query' => [
                'pesquisa' => $email,
                'apenasColunasPrincipais' => (int) $simplificado
            ],
        ]);
    }

    public function emailAlreadyInUse($email): bool
    {
        return !empty($this->getByEmail($email));
    }

    public function create(Transformers\Customer $customer)
    {
        try {
            $response = $this->client->post(self::ENDPOINT, [
                'form_params' => $customer->toArray()
            ]);
        } catch (\Exception $e) {
            Log::info(sprintf("SUPERLOGICA | Client with email %s already exist", $customer->ST_EMAIL_SAC));
        }

        if(!empty($response)) {
            $found = $response[0];
            return $found->data->id_sacado_sac;
        }

        return null;
    }

    //create

    /**
     * @throws \Exception
     */
    public function createFromCustomerData(Clientes &$cliente): Clientes
    {
        //Checks
        $found = $this->getByCpf($cliente->numericCpf);
        if(!$found) {
            $found = $this->getByEmail($cliente->email);
        }

        if(empty($found)) {
            //Cadastrar
            $customer = Transformers\Customer::fromClientData($cliente);
            $id = $this->create($customer);

            if($id) {
                $cliente->id_superlogica = $id;
            }
        } else {
            //Vincular
            $found = $found[0];
            $cliente->id_superlogica = $found->id_sacado_sac;
        }

        $cliente->last_sync = now();
        $cliente->update();
        return $cliente;
    }

    //sync
    public function update(Clientes $cliente)
    {
        $customer = Transformers\Customer::fromClientData($cliente);

        $response = $this->client->put(self::ENDPOINT, [
            'form_params' => array_merge([
                'ID_SACADO_SAC' => $cliente->id_superlogica
            ], $customer->toArray())
        ]);

        return $response;
    }

    public function inactivate(Clientes $cliente)
    {
        if ($cliente->id_superlogica == null) {
            Log::info("SUPERLOGICA | Trying to inactivate a customer without SuperLogica ID: " . $cliente->email);
            return null;
        }

        return $this->client->put(self::ENDPOINT, [
            'form_params' => array_merge([
                'ID_SACADO_SAC' => $cliente->id_superlogica,
                'DT_DESATIVACAO_SAC' => now()->format(Date::FORMAT),
                'FL_INVALIDARCOBSFUTURAS_SAC' => 1
            ])
        ]);
    }
}