<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterClientesAddSenhaPlano extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('clientes', function(Blueprint $table) {
			$table->string('senha_plano', 4)->default('0000');
		});

		$clientes = (new \App\Models\Clientes())->all();
		foreach ($clientes as $cliente) {
			$cpf = $cliente->cpf;
			$cpf = str_replace('.', '', $cpf);
			$cpf = str_replace('-', '', $cpf);
			$cpfIniciais = substr($cpf, 0, 4);
			$cliente->senha_plano = $cpfIniciais;
			$cliente->save();
		}
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('clientes', function(Blueprint $table) {
			$table->dropColumn('senha_plano');
		});
	}
}
