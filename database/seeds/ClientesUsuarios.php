<?php

use Illuminate\Database\Seeder;

class ClientesUsuarios extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $usuarios_clientesCsv = storage_path('csv/usuarios_clientes.csv');
        $usuarios_clientes = \App\Helpers\Utils::csvToArray($usuarios_clientesCsv, ",");


        $selection = $usuarios_clientes;
        $notFounds = [];
        foreach ($selection as &$selected) {
        	$cliente = \App\Models\Clientes::find($selected['id']);
        	if(!$cliente) {
        		echo "Não foi encontrado um cliente para esse cadastro (" . $selected['id'] . ")\n";
        		$notFounds[$selected['id']] = $selected['nome'];
        		continue;
        	}
    		$data = [
    			'name' => $cliente->nome_cliente,
    			'email' => $selected['e-mail'],
    			'password' => bcrypt($selected['senha'])
    		];
    		$user = \App\User::where('email', $selected['e-mail'])->first();
    		if(!$user) {
    			$user = \App\User::create($data);
	            if($user) {
	                $cliente->id_usuario = $user->id;
	                $cliente->update();
	            }	
    		}

            try {
            	$user->roles()->attach(1);
            } catch (\PDOException $e) {
            	echo $e->getMessage() . "\n";
            }
        }
    }
}
