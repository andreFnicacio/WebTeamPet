<?php
namespace App\Helpers\API\Financeiro;
use App\Helpers\Utils;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\BadResponseException;
use Carbon\Carbon;

class Financeiro {
    // Informações abaixo estão no config/financeiro.php
    // const API = 'https://financeiro-api.lifepet.com.br';
    // const SECRETID = '13277c2c15388107af72c6560a248';
    // const APPID = '060d7f4be93a21ee31df';

    public function __construct(){

    }

    public function get($endpoint){
        $client = new Client();
        try {
        $res = $client->request('GET', config('financeiro.api.url') . $endpoint, [
            'headers' => [
                'User-Agent' => 'PetManager/1.0',
                'Accept'     => 'application/json',
                'appid' => config('financeiro.api.app_id'),
                'secretid' => config('financeiro.api.secret_id')
                ]
            ]);
          
            return in_array($res->getStatusCode(), [200, 201]) ? json_decode($res->getBody()) : '';
        }
        catch(BadResponseException $e) {

            $message = $e->getMessage();
            if($e->getResponse()) {
                $badResponse = $e->getResponse()->getBody()->getContents();
                if(isset($badResponse)) {
                    $badResponse = json_decode($badResponse);
    
                    if(isset($badResponse->error, $badResponse->error->description)) {
                        $message = $badResponse->error->description;
                    }
                }
            }
            

            throw new \Exception($message);
        }catch (RequestException $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function post($endpoint, $form, $json = true) {
        $client = new Client();
        try {
        $res = $client->request('POST', config('financeiro.api.url') . $endpoint, [
            'headers' => [
                'User-Agent' => 'PetManager/1.0',
                'Accept'     => 'application/json',
                'appid' => config('financeiro.api.app_id'),
                'secretid' => config('financeiro.api.secret_id')
            ],
            'form_params' => $form
            ]);

            if(in_array($res->getStatusCode(), [200, 201]) ) {
                if($json) {
                    return json_decode($res->getBody());
                }

                return $res->getBody();
            }

            return '';
        }
        catch(BadResponseException $e) {

            $message = $e->getMessage();
            if($e->getResponse()) {
                $badResponse = $e->getResponse()->getBody()->getContents();
                if(isset($badResponse)) {
                    $badResponse = json_decode($badResponse);
    
                    if(isset($badResponse->error, $badResponse->error->description)) {
                        $message = $badResponse->error->description;
                    }
                }
            }
            

            throw new \Exception($message);
        } catch (RequestException $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function delete($endpoint, $form = []) {
        $client = new Client();
        try {
            $res = $client->request('DELETE', config('financeiro.api.url') . $endpoint, [
                'headers' => [
                    'User-Agent' => 'PetManager/1.0',
                    'Accept'     => 'application/json',
                    'appid' => config('financeiro.api.app_id'),
                    'secretid' => config('financeiro.api.secret_id')
                ],
                'form_params' => $form
            ]);
            return in_array($res->getStatusCode(), [200, 201]) ? json_decode($res->getBody()) : '';
        }
        catch(BadResponseException $e) {

            $message = $e->getMessage();
            if($e->getResponse()) {
                $badResponse = $e->getResponse()->getBody()->getContents();
                if(isset($badResponse)) {
                    $badResponse = json_decode($badResponse);

                    if(isset($badResponse->error, $badResponse->error->description)) {
                        $message = $badResponse->error->description;
                    }
                }
            }


            throw new \Exception($message);
        } catch (RequestException $e) {
            throw new \Exception($e->getMessage());
        }
    }



    public function createSubscription($data) {
        if(empty($data['customer_id'] ?? null)){
            throw new \Exception('Subscription error: Informe o customer_id');
        }

        foreach ($data['pets'] as $pet) {
            
            $subscriptions = [];
            
            
            $plano = $pet->plano();
            $identificador = 'PLANO - '.$pet->nome_pet;
            $precoAdesao = number_format($pet->petsPlanosAtual()->first()->adesao,2,'.','');
            $valor = number_format($pet->petsPlanosAtual()->first()->valor_momento,2,'.','');
            
            $financeiro = new Financeiro();
            
            $interval = 'M';
            
            if ($pet->regime != "MENSAL") {
                $interval = 'A';   
            }
            
            $dados = [
                'customer_id' => $data['customer_id'],
                'status' => 'A',
                'due_day' => $data['dia_vencimento'],
                'price' => $valor,
                'membership_fee' => $precoAdesao,
                'payment_type' => $data['forma_pagamento'],
                'ref_code' => $pet->id,
                'product_id' => $plano->id,
                'name' => 'PLANO - '.$pet->nome_pet,
                'ref_code' => $pet->id,
                'interval' => $interval,
                'start_at' => (new Carbon())->format('Y-m-d')
            ];
            
            $this->post('/payment-subscription', $dados);
        } 
    }

    public function createCustomer($data) {
        $paymentType = 'boleto';
        if (isset($data['forma_pagamento']) && $data['forma_pagamento'] == 'cartao') {
            $paymentType = 'creditcard';
        }
        
        $financeiro = new Financeiro();
        
        $form = [
            'name' => $data["nome_cliente"],
            'email' => $data["email"],
            'cpf_cnpj' => $data['cpf'],
            'status' => 'A',
            'due_day' => $data["dia_vencimento"],
            'payment_type' => $paymentType,
            'gender' => $data['sexo'],
            'address[0][zipcode]' => $data['cep'],
            'address[0][address1]' =>  $data['rua'],
            'address[0][number]' => $data['numero_endereco'],
            'address[0][address2]' => $data['bairro'],
            'address[0][city]' =>  $data['cidade'],
            'address[0][country]' => 'Brasil',
            'address[0][state]' =>  $data['estado'],
            'address[0][ibge]' =>  isset($data['ibge']) ? $data['ibge'] : null,
            //'ref_code' => $data['id_externo'],
            'financial_status' => 1
        ];

        if(!empty($data['data_nascimento'] ?? null)){
            $form['birthdate'] = $data['data_nascimento']->format('Y-m-d');
        }

        return $this->post('/customer', $form);
    }

    public function createBasicCustomer($data)
    {
        foreach($data as $key => $d) {
            $data[$key] = Utils::remove_accents($d);
        }

        $customer = null;
        //Check if customer already exists
        $cpf = $data['cpf'];
        try {
            $customer = $this->get("/customer/cpfcnpj/{$cpf}");
            if(isset($customer->data)) {
                $customer = $customer->data;
            }
        } catch (\Exception $e) {
            //If it's not, create it and returns
            if($e->getMessage() !== 'Nenhum registro encontrado!') {
                throw $e;
            } else {
                $paymentType = 'creditcard';

                $form = [
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'address[0][zipcode]' => $data['cep'],
                    'address[0][address1]' =>  $data['street'],
                    'address[0][number]' => $data['address_number'],
                    'address[0][address2]' => $data['neighbourhood'],
                    'address[0][city]' =>  $data['city'],
                    'address[0][country]' => 'Brasil',
                    'address[0][state]' =>  $data['state'],
                    'address[0][ibge]' =>  isset($data['ibge']) ? $data['ibge'] : null,
                    'payment_type' => $paymentType,
                    'cpf_cnpj' => $data['cpf'],
                    'status' => 'A',
                    'financial_status' => 1,
                ];

                $customer = $this->post('/customer', $form);
            }
        }

        return $customer;
    }

    public function addCreditCard($customer, $data)
    {
        if(isset($customer->data)) {
            $customer = $customer->data;
        }

        $id = $customer->id;

        $form = [
            'hash' => $customer->hash,
            'number' => $data['card_number'],
            'brand' => $data['brand'],
            'valid' => $data['expires_in'],
            'ccv' => $data['ccv'],
            'holder' => $data['holder'],
        ];

        return $this->post("/customer/card/{$id}", $form);
    }

    /**
     * @param $data
     * @return mixed|string
     * @throws \Exception
     */
    public function pay($data)
    {
        return $this->post('/payment/transaction', $data);
    }

    public function customer($cpfcnpj) {
        try {
            $customer = $this->get("/customer/cpfcnpj/{$cpfcnpj}");

            if(isset($customer->data)) {
                $customer = $customer->data;
            }

            return $customer;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function customerByRefcode($refcode)
    {
        try {
            $customer = $this->get("/customer/refcode/{$refcode}");

            if(isset($customer->data)) {
                $customer = $customer->data;
            }

            return $customer;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function fingerprint()
    {
        $guzzle = new Client();

        $session = session()->get('fingerprint_session', function() {
            return sha1(rand());
        });
        session()->put('fingerprint_session', $session);

        try {
            $response = $guzzle->get("https://h.online-metrix.net/fp/tags.js?org_id=k8vif92e&session_id={$session}");
            if($response->getStatusCode() == 200) {
                return $session;
            }
        } catch (\Exception $e) {
            return null;
        }

        return null;
    }
}