<?php

namespace App\Console\Commands\Vindi;

use App\Models\Clientes;
use App\Models\PetsPlanos;
use http\Client;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\DB;
use Modules\Vindi\DTO\Customer\CustomerAddressDTO;
use Modules\Vindi\DTO\Customer\CustomerDTO;
use Modules\Vindi\DTO\Customer\CustomerPhoneDTO;
use Modules\Vindi\DTO\Customer\CustomerPhoneDTOCollection;
use Modules\Vindi\Services\Resources\CustomerResource;
use Modules\Vindi\Services\VindiService;
use Vindi\Customer;

class MigrationClientVindi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vindi:migrationClientVindi';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrar clientes do ERP';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->info('Iniciando migração.');

        $clients = $this->getClients();
        $this->getClientsVindi($clients);

    }

    private function getClientsVindi($clients){
        $customerService = app(VindiService::class)->customer();
        $filename = 'error_export_vindi.txt';
        $f = fopen($filename, 'wb');
        $filenameReport = 'report_export_vindi.csv';
        $r = fopen($filenameReport, 'wb');
        if (!$f) {
            die('Error creating the file ' . $filename);
        }
        if (!$r) {
            die('Error creating the file ' . $filenameReport);
        }
        fputcsv($r, array('id', 'nome', 'email', 'cpf', 'vindi_id', 'criacao', 'update'));
        foreach ($clients as $client){
            $customer = $customerService->find('code='.$client->id);
            if (empty($customer)){
                $this->info('Criando cliente');
                $request = $this->createRequest($client);
                try {
                    $customer = $customerService->createCustomer($request);
                } catch(\Exception $e) {
                    $request['phones'] = [];
                    try {
                        $customer = $customerService->createCustomer($request);
                    } catch (\Exception $err) {
                        fputs($f, $client->id_cliente.',');
                        continue;
                    }
                }
                fputcsv($r, array($client->id_cliente, $client->nome_cliente, $client->email, $client->cpf,
                    $customer->id, 1, 0));
                $this->updateClient($client, $customer->id);
            } elseif ($customer->status == 'archived') {
                $customerService->unarchive($customer->id);
                $this->info('Resucitando cliente');
            } else {
                    $this->info('Atualizando Cliente');
                    fputcsv($r, array($client->id_cliente, $client->nome_cliente, $client->email, $client->cpf,
                        $customer->id, 0, 1));
                    $this->updateClient($client, $customer->id);
            }
            sleep(1);
        }
        fclose($f);
        fclose($r);
    }

    private function createRequest($client){
        $request = [];
        $address = [];
        $phones = [];

        $request['name'] = $client->nome_cliente;
        $request['email'] = $client->email;
        $request['code'] = strval($client->id_cliente);
        $request['registry_code	'] = strval($client->cpf);
        $request['status']= 'inactive';
        $request['created_at'] = (new \DateTime())->format('Y-m-d H:i:s');
        $request['updated_at'] = (new \DateTime())->format('Y-m-d H:i:s');
        $address['street'] = $client->rua;
        $address['number'] = $client->numero_endereco;
        $address['zipcode'] = $client->cep;
        $address['neighborhood'] = $client->bairro;
        $address['city'] = $client->cidade;
        $address['state'] = $client->estado;
        $address['country'] = 'BR';
        $phones['phone_type'] = 'mobile';
        $phones['number'] = '55'.$client->ddd.$client->celular;

        $request['address'] = new CustomerAddressDTO($address);
        $request['phones'][0] = $phones;

        return $request;
    }
    private function replaceClients($clients){
        foreach ($clients as $key => $client){
            $client->nome_cliente = trim($client->nome_cliente);
            $client->email = trim($client->email);
            $client->ddd = (str_split($client->celular, 2))[0];
            $client->celular = substr($client->celular, 2);
            $client->rua = str_replace(',', ' ', $client->rua);
            $client->numero_endereco = str_replace('S/N', '', $client->numero_endereco);
            $client->complemento_endereco = str_replace(',', ' ', $client->complemento_endereco);
            $client->bairro = trim($client->bairro);
            $client->cidade = trim($client->cidade);
        }

        return $clients;
    }

    private function getClients(){
        $query = \DB::select ('SELECT * FROM clientes
            inner join pets on pets.id_cliente = clientes.id
            INNER JOIN `pets_planos` AS `pp` ON `pets`.`id` = `pp`.`id_pet`
            AND pp.id IN
              (SELECT MAX(pp2.id)
               FROM pets_planos AS pp2
               JOIN pets AS p2 ON p2.id = pp2.id_pet
               WHERE pp2.data_encerramento_contrato IS NULL
               GROUP BY p2.id)
               
            INNER JOIN `planos` AS `p` ON `p`.`id` = `pp`.`id_plano`
            
            WHERE `p`.`id` IN (74, 75, 76, 79)
            AND clientes.ativo = 1
            AND clientes.deleted_at IS NULL
            AND clientes.financial_id IS NULL 
            group by clientes.cpf
            order by clientes.id asc');

        $clients = $this->replaceClients($query);

        return $clients;
    }

    private function updateClient($client, $vindi_id){
        return Clientes::where('id',$client->id_cliente)->update([
            'financial_id' => $vindi_id
        ]);
    }
}