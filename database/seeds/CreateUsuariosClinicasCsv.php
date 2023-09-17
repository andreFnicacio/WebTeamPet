<?php

use Illuminate\Database\Seeder;

class CreateUsuariosClinicasCsv extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $usuarios_clinicasCsv = storage_path('csv/usuarios_clinicas.csv');
        $usuarios_clinicas = \App\Helpers\Utils::csvToArray($usuarios_clinicasCsv, ",");


        $selection = $usuarios_clinicas;
        foreach ($selection as &$selected) {
        	$clinica = \Modules\Clinics\Entities\Clinicas::find($selected['id_clinica']);
        	if(!$clinica) {
        		echo "Não foi encontrada uma clínica para esse cadastro.\n";
        		continue;
        	}
    		$data = [
    			'name' => $clinica->nome_clinica,
    			'email' => $selected['email'],
    			'password' => bcrypt($selected['senha'])
    		];
    		$user = \App\User::where('email', $selected['email'])->first();
    		if(!$user) {
    			$user = \App\User::create($data);
	            if($user) {
	                $clinica->id_usuario = $user->id;
	                $clinica->update();
	            }	
    		}
            
            $user->roles()->attach(5);
        }
    }
}
