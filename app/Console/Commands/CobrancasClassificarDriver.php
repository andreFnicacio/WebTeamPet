<?php

namespace App\Console\Commands;

use App\Models\Cobrancas;
use Illuminate\Console\Command;

class CobrancasClassificarDriver extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cobrancas:driver.classificar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Classifica o driver da cobrança de acordo com seu ID externo';

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
     * @return void
     */
    public function handle()
    {
        $query = Cobrancas::whereNull('driver')->where(function($query) {
            return $query->where('id_superlogica', '<>', '')
                ->orWhere('old_superlogica_id', '<>', '')
                ->orWhere('id_financeiro', '<>', '');
        });
        $count = $query->count();
        $perPage = 1000;
        $pages = ceil($count / $perPage);

        for($i = 1; $i < $pages - 1; $i++) {
            $cobrancas = $query->select([
                'id',
                'id_superlogica',
                'old_superlogica_id',
                'id_financeiro',
                'driver'
            ])->forPage($i, $perPage)->get();

            foreach($cobrancas as $c) {
                $this->info("Atualizando status da cobrança #{$c->id}");
                //Se for Superlógica antigo:
                if($c->old_superlogica_id) {
                    $c->driver = Cobrancas::DRIVER__SUPERLOGICA_V1;
                    $c->update();
                    $this->info("Cobrança #{$c->id} -> " . Cobrancas::DRIVER__SUPERLOGICA_V1);
                    continue;
                }
                //Se for Superlógica novo:
                if($c->id_superlogica) {
                    $c->driver = Cobrancas::DRIVER__SUPERLOGICA_V2;
                    $c->update();
                    $this->info("Cobrança #{$c->id} -> " . Cobrancas::DRIVER__SUPERLOGICA_V2);
                    continue;
                }
                //Se for Superlógica antigo:
                if($c->id_financeiro) {
                    $c->driver = Cobrancas::DRIVER__SF;
                    $c->update();
                    $this->info("Cobrança #{$c->id} -> " . Cobrancas::DRIVER__SF);
                }
            }

            unset($cobrancas);
        }
    }
}
