<?php

namespace App\Console\Commands\Relatorios;

use App\Helpers\Utils;
use App\Models\Clientes;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use stdClass;

class TodosClientesAtivos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'relatorios:clientesAtivos';

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


        $clientes = Clientes::where('ativo', '1')->get();

        $bar = $this->output->createProgressBar(count($clientes));

        $bar->start();

        $data = $clientes->map(function($cliente) use ($bar) {


            $obj = new stdClass;
            $obj->ID = $cliente->id;
            $obj->Nome = $cliente->nome_cliente;
            $obj->Email = $cliente->email;
            $obj->Celular = $cliente->celular;
            $obj->Telefone = $cliente->telefone_fixo;
            $obj->Vencimento = $cliente->dia_vencimento;
            $obj->Status = $cliente->ativo ? 'Ativo' : 'Desativado';
            $obj->StatusFinanceiro = $cliente->statusPagamento();
            $obj->FormaPagamento = $cliente->forma_pagamento;
            $obj->QtdPets = $cliente->pets()->where('ativo', '1')->count();

            $bar->advance();

            return (array) $obj;

        });

        $csv_array = Utils::ArrayToCsv($data, null, ';');

        $date = date('YmdHis');
        $file = Storage::put("relatorios/clientes_ativos_{$date}.csv", $csv_array);
        $bar->finish();
        $this->info('Finalizado relatório');
        $this->info($file);

    }
}
