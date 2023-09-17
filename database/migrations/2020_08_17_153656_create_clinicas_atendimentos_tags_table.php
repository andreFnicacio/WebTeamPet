<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClinicasAtendimentosTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clinicas_atendimentos_tags', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nome', 250);
            $table->unsignedInteger('created_by');

            $table->index(["created_by"], 'fk_clinicas_atendimentos_tags_users1_idx');
            $table->timestamps();


            $table->foreign('created_by', 'fk_clinicas_atendimentos_tags_users1_idx')
                ->references('id')->on('users')
                ->onDelete('no action')
                ->onUpdate('no action');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clinicas_atendimentos_tags');
    }
}
