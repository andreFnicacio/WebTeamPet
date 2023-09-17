<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeletes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tables:set_deletes';
    private static $tables = [
        'planos',
        'pets_planos',
        'historico_uso',
        'grupos_carencias',
        'procedimentos',
        'planos_grupos',
        'especialidades',
        'tabelas_referencia',
        'clientes',
        'conveniados',
        'pets',
        'planos_procedimentos',
        'procedimentos_tabelas',
        'clinicas',
        'prestadores',
        'prestadores_clinicas',
        'tabelas_procedimentos',
        'cobrancas',
        'pagamentos',
        'notas',
        'uploads',
        'alteracoes_cadastrais',
        'sugestoes'
    ];

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates "deleted_at" fields';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        foreach (self::$tables as $t) {
            if(Schema::hasTable($t)) {
               if(!Schema::hasColumn($t, 'deleted_at')) {
                Schema::table($t, function(Blueprint $table) {
                    $table->dateTime('deleted_at')->nullable()->comment("Habilita o 'soft delete'");
                });
               } 
            }
        }
    }
}
