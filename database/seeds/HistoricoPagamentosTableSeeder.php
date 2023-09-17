<?php

use Illuminate\Database\Seeder;

class HistoricoPagamentosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $historicoCsv = storage_path('csv/historico_pagamento.csv');
        $historico    = \App\Helpers\Utils::csvToArray($historicoCsv, ";");

        $selection = $historico;
        $errorBag = [];
        foreach ($selection as &$selected) {

            try {
                $cobranca = [
                    'valor_original' => floatval(str_replace(',', '.', $selected['valor_original'])),
                    'data_vencimento' => \Carbon\Carbon::createFromFormat('d/m/Y', $selected['vencimento']),
                    'competencia' => $selected['competencia'],
                    'status'      => 1,
                    'id_cliente' => $selected['id_cliente']
                ];

                $cobranca = \App\Models\Cobrancas::create($cobranca);

                $pagamento = [
                    'id_cobranca' => $cobranca->id,
                    'data_pagamento' => \Carbon\Carbon::createFromFormat('d/m/Y', $selected['data_pagamento']),
                    'valor_pago' => floatval(str_replace(',', '.', $selected['valor_pago'])),
                    'forma_pagamento' => 0
                ];
                $pagamento = \App\Models\Pagamentos::create($pagamento);
            } catch (Exception $e) {
                $errorBag[] = $e->getMessage();
            }
        }

        file_put_contents(storage_path('logs/historico_pagamento.txt'), json_encode($errorBag, JSON_PRETTY_PRINT));
    }
}
