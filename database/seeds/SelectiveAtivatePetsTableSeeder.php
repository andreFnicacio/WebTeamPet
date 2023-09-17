<?php

use Illuminate\Database\Seeder;

class SelectiveAtivatePetsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $petsCsv = storage_path('csv/id_pets_ativos.csv');
        $pets = \App\Helpers\Utils::csvToArray($petsCsv, ";");

        $selection = $pets;
        foreach ($selection as &$selected) {
            $found = \App\Models\Pets::find($selected['id']);
            if($found) {
                $found->ativo = 1;
                $found->update();
                $found = null;
            }
        }
    }
}
