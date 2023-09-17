<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;


class ClientesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $clientsCsv = storage_path('seed/clientes-2.csv');
        $clients = \App\Helpers\Utils::csvToArray($clientsCsv, ",");

        $selection = $clients;
        foreach ($selection as &$selected) {

            if($selected['id_externo'] == "") {
                $selected['id_externo'] = null;
            }

            \DB::table('clientes')->insert($selected);
        }
    }
}
