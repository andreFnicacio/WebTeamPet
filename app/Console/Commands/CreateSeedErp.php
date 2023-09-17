<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeedErp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'erp:create_seeds';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates seeds new erp';

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
     * @return mixed
     */
    public function handle()
    {
        $this->info('Iniciando criação de seed');

        $query = \DB::select ('SELECT *, pets.id, pp.id_plano FROM clientes
            inner join pets on pets.id_cliente = clientes.id
            INNER JOIN `pets_planos` AS `pp` ON `pets`.`id` = `pp`.`id_pet`
            AND pp.id IN
              (SELECT MAX(pp2.id)
               FROM pets_planos AS pp2
               JOIN pets AS p2 ON p2.id = pp2.id_pet
               WHERE pp2.data_encerramento_contrato IS NULL
               GROUP BY p2.id)
               
            INNER JOIN `planos` AS `p` ON `p`.`id` = `pp`.`id_plano`
            INNER JOIN cobrancas as c ON c.id_cliente = clientes.id
            INNER JOIN pagamentos as pg ON pg.id_cobranca = c.id
            
            WHERE `p`.`id` IN (74, 75, 76, 79, 80, 81, 82, 83)
            AND clientes.ativo = 1
            AND clientes.deleted_at IS NULL 
            group by clientes.cpf
            order by clientes.id asc');

        $clients = $this->replaceClients($query);

        $myfile = fopen("seeds.txt", "w") or die("Unable to open file!");
        fwrite($myfile, json_encode($clients));
        fclose($myfile);
        return $clients;
    }

    private function getPets($idCliente){
        $query = \DB::select('SELECT *, pets_planos.id_plano FROM pets INNER JOIN pets_planos ON 
            pets.id_pets_planos = pets_planos.id WHERE ativo = 1 AND id_cliente = '.$idCliente.' GROUP BY pets.id ORDER BY pets.id DESC');
        return $query;
    }

    private function getPlano($idPlano){
        $query = \DB::select('SELECT * FROM planos WHERE id = '.$idPlano);
        return $query;
    }

    private function replaceClients($clients){
        $body = [];
        foreach ($clients as $key => $client){
            $tutor = (object)[];
            $tutor->name = trim($client->nome_cliente);
            $tutor->cpf_cnpj = $client->cpf;
            $tutor->financial_id = $client->financial_id;
            $tutor->driver = 'VINDI';
            $tutor->email = trim($client->email);
            $tutor->cell_phone = $client->celular;
            $tutor->address = str_replace(',', ' ', $client->rua);
            $tutor->address_number = str_replace('S/N', '', $client->numero_endereco);
            $tutor->address_complement = str_replace(',', ' ', $client->complemento_endereco);
            $tutor->neighborhood = trim($client->bairro);
            $tutor->city = trim($client->cidade);
            $tutor->state = trim($client->estado);
            $tutor->gender = $client->sexo;
            $tutor->due_day = $client->dia_vencimento;
            $tutor->created_at = $client->created_at;
            $tutor->updated_at = $client->updated_at;
            $tutor->deleted_at = $client->deleted_at;
            $tutor->pets = [];
            $queryPets = $this->getPets($client->id);
            foreach ($queryPets as $key => $pet){
                $pt = (object)[];
                $pt->name = $pet->nome_pet;
                $pt->type = $pet->tipo;
                $pt->microchip_number = $pet->numero_microchip;
                $pt->pre_existing_diseases = $pet->doencas_pre_existentes;
                $pt->breed = $pet->id_raca;
                $pt->death = $pet->obito;
                $pt->created_at = $pet->created_at;
                $pt->updated_at = $pet->updated_at;
                $pt->deleted_at = $pet->deleted_at;
                $pt->plan = [];
                $queryPlanos = $this->getPlano($pet->id_plano);
                foreach ($queryPlanos as $key => $plano) {
                    $pl = (object)[];
                    $pl->name = $plano->nome_plano;
                    $pl->name_display = $plano->display_name;
                    $pl->created_at = $plano->created_at;
                    $pl->updated_at = $plano->updated_at;
                    $pl->deleted_at = $plano->deleted_at;
                    array_push($pt->plan, $pl);
                }
                array_push($tutor->pets, $pt);
            }
            array_push($body, $tutor);
        }



        return $body;
    }
}