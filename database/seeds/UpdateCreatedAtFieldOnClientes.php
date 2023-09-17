<?php

use Illuminate\Database\Seeder;

class UpdateCreatedAtFieldOnClientes extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //$this->clients();
        $this->pets();
    }

    private function clients() {
        $clientesCsv = storage_path('csv/clientes_created_at.csv');
        $clientes = \App\Helpers\Utils::csvToArray($clientesCsv, ";");

        $selection = $clientes;
        foreach ($selection as &$selected) {
            $found = \App\Models\Clientes::find($selected['id']);

            if($found) {
                $found->created_at = $selected['created_at'];
                $found->update();
                $found = null;
            }
        }
    }

    private function pets() {
        $petsCsv = storage_path('csv/pets_created_at.csv');
        $pets = \App\Helpers\Utils::csvToArray($petsCsv, ";");

        $selection = $pets;
        foreach ($selection as &$selected) {
            $found = \App\Models\Pets::find($selected['id']);

            if($found) {
                $found->created_at = $selected['created_at'];
                $found->update();
                $found = null;
            }
        }
    }
}
