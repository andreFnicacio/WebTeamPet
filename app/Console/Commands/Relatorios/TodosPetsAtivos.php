<?php

namespace App\Console\Commands\Relatorios;

use App\Helpers\Utils;
use App\Models\Pets;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use stdClass;

class TodosPetsAtivos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'relatorios:petsAtivos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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


        $this->info('Iniciando relatório.');

        $pets = Pets::where('ativo', 1)->get();

        $bar = $this->output->createProgressBar(count($pets));

        $bar->start();


        $data = $pets->map(function($pet) use ($bar) {

            $plano = $pet->plano();
            $petsPlanos = $pet->petsPlanosAtual()->first();

            $inicio_contrato = 'Erro';
            $valor = 'Erro';
            $nome_cliente = 'Erro';
            $celular = 'Erro';
            $email = 'Erro';
            $telefone_fixo = 'Erro';

            if ($petsPlanos)
            {
                $inicio_contrato = $petsPlanos->data_inicio_contrato->format('d/m/Y');
                $valor = $petsPlanos->valor_momento;

            }

            if ($pet->cliente)
            {
                $nome_cliente = $pet->cliente->nome_cliente;
                $celular = $pet->cliente->celular;
                $email = $pet->cliente->email;
                $telefone_fixo = $pet->cliente->telefone_fixo;
            }
            $obj = new stdClass;
            $obj->ID = $pet->id;
            $obj->Nome = $pet->nome_pet;
            $obj->Regime = $pet->regime;
            $obj->Valor = $valor;
            $obj->Status = $pet->ativo ? 'Ativo' : 'Desativado';
            $obj->DataInicioContrato = $inicio_contrato;
            $obj->Tutor = $nome_cliente;
            $obj->Email = $email;
            $obj->Celular = $celular;
            $obj->Telefone = $telefone_fixo;

            $bar->advance();

            return (array) $obj;

        });

        $csv_array = Utils::ArrayToCsv($data, null, ';');

        $date = date('YmdHis');
        $file = Storage::put("relatorios/pets_ativos_{$date}.csv", $csv_array);
        $bar->finish();
        $this->info('Finalizado relatório');
        $this->info($file);

    }
}
