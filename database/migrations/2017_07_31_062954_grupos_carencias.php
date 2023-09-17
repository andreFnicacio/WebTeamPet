<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class Grupos_carencias.
 *
 * @author  The scaffold-interface created at 2017-07-31 06:29:54pm
 * @link  https://github.com/amranidev/scaffold-interface
 */
class GruposCarencias extends Migration
{
    /**
     * Run the migrations.
     *
     * @return  void
     */
    public function up()
    {
        Schema::create('grupos_carencias',function (Blueprint $table){

            $table->increments('id');
            
            $table->string('nome_grupo');
            
            /**
             * Foreignkeys section
             */
            
            
            $table->timestamps();
            
            
            // type your addition here

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return  void
     */
    public function down()
    {
        Schema::drop('grupos_carencias');
    }
}
