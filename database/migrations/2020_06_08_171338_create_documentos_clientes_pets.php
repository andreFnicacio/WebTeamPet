<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\{Clientes,DocumentosClientes,DocumentosPets,Pets};

class CreateDocumentosClientesPets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documentos_clientes', function (Blueprint $table) {
            $table->increments('id');

            $table->string('tipo');
            $table->string('nome');
            $table->enum('status', ['PENDENTE', 'ENVIADO', 'APROVADO', 'REPROVADO']);
            $table->boolean('avaliacao_obrigatoria')->default(0);
            $table->datetime('data_envio')->nullable();
            $table->datetime('data_reprovacao')->nullable();
            $table->datetime('data_aprovacao')->nullable();
            $table->text('motivo_reprovacao')->nullable();

            $table->bigInteger('id_cliente')->unsigned();
            $table->foreign('id_cliente')->references('id')->on('clientes');

            $table->integer('id_usuario_aprovacao')->unsigned()->nullable();
            $table->foreign('id_usuario_aprovacao')->references('id')->on('users');

            $table->integer('id_usuario_reprovacao')->unsigned()->nullable();
            $table->foreign('id_usuario_reprovacao')->references('id')->on('users');

            $table->timestamps();
            $table->softDeletes();
        });

        // $clientes = Clientes::all();
        // foreach ($clientes as $cliente) {
        //     foreach ($cliente::DOCUMENTOS_OBRIGATORIOS as $tipo => $doc) {
        //         DocumentosClientes::create([
        //             'tipo' => $tipo,
        //             'nome' => $doc,
        //             'status' => 'APROVADO',
        //             'avaliacao_obrigatoria' => 1,
        //             'id_cliente' => $cliente->id
        //         ]);
        //     }
        // }

        Schema::create('documentos_pets', function (Blueprint $table) {
            $table->increments('id');

            $table->string('tipo');
            $table->string('nome');
            $table->enum('status', ['PENDENTE', 'ENVIADO', 'APROVADO', 'REPROVADO']);
            $table->boolean('avaliacao_obrigatoria')->default(0);
            $table->datetime('data_envio')->nullable();
            $table->datetime('data_reprovacao')->nullable();
            $table->datetime('data_aprovacao')->nullable();
            $table->text('motivo_reprovacao')->nullable();

            $table->integer('id_pet')->unsigned();
            $table->foreign('id_pet')->references('id')->on('pets');

            $table->integer('id_usuario_aprovacao')->unsigned()->nullable();
            $table->foreign('id_usuario_aprovacao')->references('id')->on('users');

            $table->integer('id_usuario_reprovacao')->unsigned()->nullable();
            $table->foreign('id_usuario_reprovacao')->references('id')->on('users');

            $table->timestamps();
            $table->softDeletes();
        });

        // $pets = Pets::all();
        // foreach ($pets as $pet) {
        //     DocumentosPets::create([
        //         'tipo' => 'carteirinha_vacinacao',
        //         'nome' => 'Carteirinha de Vacinação',
        //         'status' => 'APROVADO',
        //         'avaliacao_obrigatoria' => 0,
        //         'id_pet' => $pet->id
        //     ]);
        //     if ($pet->lifepetPlusAssinatura()) {
        //         DocumentosPets::create([
        //             'tipo' => 'exame_inicial_lifepet_plus',
        //             'nome' => 'Exame Inicial Lifepet+',
        //             'status' => 'APROVADO',
        //             'avaliacao_obrigatoria' => 1,
        //             'id_pet' => $pet->id
        //         ]);
        //     }
        // }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('documentos_clientes');
        Schema::drop('documentos_pets');
    }
}
