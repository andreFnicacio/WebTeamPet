<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUploadsAddSoftdelete extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('uploads', function(Blueprint $table) {
            $table->integer('id_usuario_delete')->unsigned()->comment("ID do usuário que excluiu o upload");
            $table->string('justificativa_delete')->comment("Justificativa de exclusão do upload");
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('uploads', function(Blueprint $table) {
            $table->dropColumn('id_usuario_delete');
            $table->dropColumn('justificativa_delete');
            $table->dropSoftDeletes();
        });
    }
}
