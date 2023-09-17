<?php

namespace Modules\Vindi\Console;

use App\Helpers\Utils;
use App\Models\Clientes;
use App\Models\Cobrancas;
use App\Models\PetsPlanos;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Log;
use Modules\Vindi\Services\VindiService;
use App\Models\Pagamentos;

class GenerateSingleBillToGetCreditCards extends Command
{
    protected $signature = 'vindi:syncBillPayment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a single bill on financial service to capture customers credit card info';

    /**
     * @var VindiService
     */
    private $financialService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(VindiService $financialService)
    {
        parent::__construct();
        $this->financialService = $financialService;
    }

    public function handle()
    {
        $cobrancas = \DB::select('SELECT c.id, c.data_vencimento, c.valor_original, c.id_financeiro FROM cobrancas as c 
                LEFT JOIN pagamentos as p ON c.id = p.id_cobranca 
                WHERE  c.driver = "vindi" and 
                c.status = 1 and p.id IS NULL
        ');

        foreach ($cobrancas as $cobranca) {
            Log::debug(sprintf("Pet %s activated through Webhook", $cobranca->id));
            $this->info('Sync bills: '.$cobranca->id);
            $pagamento = Pagamentos::create([
                'id_cobranca' => $cobranca->id,
                'data_pagamento' => $cobranca->data_vencimento,
                'complemento' => "Pagamento da assinatura processado pela Vindi",
                'valor_pago' => $cobranca->valor_original,
                'id_financeiro' => $cobranca->id_financeiro,
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
                'forma_pagamento' => 0
            ]);
        }


//        if(!$pagamento) {
//            $pagamento = Pagamentos::create([
//                'id_cobranca' => '1020757853',
//                'data_pagamento' => date('Y-m-d'),
//                'complemento' => "Pagamento da assinatura processado pela Vindi",
//                'valor_pago' => 124.9,
//                'id_financeiro' => '26916516',
//            ]);
//        } else {
//            $pagamento->complemento .= "Pagamento da assinatura processado pela Vindi";
//            $pagamento->update();
//        }
//        dd($pagamento);
    }
}