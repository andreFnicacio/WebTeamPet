<?php

namespace App\Console\Commands\Guias;

use App\Helpers\API\Financeiro\DirectAccess\Models\Payment;
use App\Helpers\Utils;
use App\Models\Pagamentos;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\Guides\Entities\HistoricoUso;

class CorrigirSemNumero extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'guias:corrigir';

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

        $csv = storage_path('csv/pagamentosemnumeroguia.csv');

        //$array_csv = Utils::csvToArray($csv, ';');
       // dd($array_csv);

    //    exit;
        /**
         * Lista para buscar
         * variavel: [guia]
         */
        $search_list = [
            [
                'search' => 'Referente à guia #:',
                'replace_to' => 'Referente à guia #[guia]:'
            ]
        ];
        $customer_hidden = [
        //    1020752763
        ];
        $ignore_guia = [];
        //$participacao = [];
        $possibilities = [];

        foreach($search_list as $search_row) {

            $search = $search_row['search'];
            /**
             * Procura pagamentos com a busca
             */
            $pagamentos = Pagamentos::where("complemento", "like", "%$search%");


            /**
             * Roda todos os pagamentos
             */
            foreach($pagamentos->get() as $pagamento) {

                if (!isset($possibilities[$pagamento->id]))
                {
                    $possibilities[$pagamento->id] = [
                        'pagamento' => [
                            'id' => $pagamento->id,
                            'valor_pago' => $pagamento->valor_pago,
                            'data_pagamento' => $pagamento->data_pagamento->format('d/m/Y'),
                            'real_data_pagamento' => $pagamento->data_pagamento,
                            'id_financeiro' => $pagamento->id_financeiro,
                            'created_at' => $pagamento->created_at
                        ],
                        'cobranca' => [],
                        'cliente' => [],
                        'matches' => [],
                        'nothing' => [],
                    ];
                }

            //    echo "\n Pagamento sem guia #{$pagamento->id} \n";

            //    echo " \n";

                /**
                 * Gera Cobranca, cliente e pets, para se não encontrar
                 */
                $cobranca = $pagamento->cobranca;
                if (!$cobranca)
                {
             //       echo "Não encontrou cobranca do Pagamento id $pagamento->id \n";
                    continue;
                }

                $possibilities[$pagamento->id]['cobranca'] = [
                    'id' => $cobranca->id,
                    'competencia' => $cobranca->competencia,
                    'data_vencimento' => $cobranca->data_vencimento->format('d/m/Y'),
                    'real_data_vencimento' => $cobranca->data_vencimento,
                ];

                $cliente = $cobranca->cliente;

                if (!$cliente)
                {
            //        echo "Não encontrou cliente do Pagamento id $pagamento->id \n";

                    continue;
                }

                if (isset($array_csv))
                {
                    $has_customer_csv = false;
                    foreach($array_csv as $line_csv) {
                        if ($line_csv['CPF/CNPJ'] == str_replace(['.', ',', '-'], '', $cliente->cpf))
                        {
                            $has_customer_csv = true;
                            continue;
                        }
                    }
                    if (!$has_customer_csv) {
                        unset($possibilities[$pagamento->id]);
                        continue;
                    }
                }


                $possibilities[$pagamento->id]['cliente'] = [
                    'id' => $cliente->id,
                    'nome' => $cliente->nome_cliente,
                    'cpf' => $cliente->cpf,
                ];

                if (array_search($cliente->id, $customer_hidden) !== false) {
                //    echo " Ignorando Cliente #$cliente->id - $cliente->nome_cliente \n";
                    continue;
                }
                $pets = $cliente->pets;
                if (count($pets)===0)
                {
               //     echo "Não encontrou Pets do Cliente #$cliente->id\n";
                    continue;
                }

                foreach($pets as $pet) {


                    $date_max = $pagamento->created_at;//'2021-12-31 23:59:59';////->addDays(20);
                    $date_min = $cobranca->created_at->addDays(-5);//'2021-12-01 00:00:00';

                    /**
                     * Pega a lista de histórico de uso
                     * entre a data de inserção do pagamento
                     * e 5 dias anteriores a data
                     * de inserção da cobrança
                     *
                     */
                    $histUsoGuias = $pet->historicoUsos()
                        ->where('status', 'LIBERADO')
                        ->where('created_at' ,'>', $date_min)
                        ->where('created_at', '<', $date_max)
                        ->groupBy('numero_guia')
                        ->get();

                    foreach ($histUsoGuias as $histUsoGuia) {

                        $numero_guia = $histUsoGuia->numero_guia;

                        /**
                         * Verifica se a guia já foi utilizada
                         * caso, ja tenha sido, pula para a próxima
                         */
                        if (array_search($numero_guia, $ignore_guia) !== false)
                        {
                            $ignore_guia[] = $numero_guia;
                            continue;
                        }

                        $histUsos = HistoricoUso::where('numero_guia', $numero_guia)
                            ->get();
                        $calcValUso = 0;


                        foreach ($histUsos as $histUso) {
                          //  dd($histUso->getValorProcedimento());
                            $calcValUso = $calcValUso + $histUso->getValorProcedimento();
                        }

                        if ($calcValUso === $cobranca->valor_original)
                        {

                            if (Pagamentos::where("complemento", "like", "%$numero_guia%")->first())
                             {
                  //               echo   "Guia já está utilizada em um pagamento";

                 //                echo "\n";
                                 continue;
                             }

                            $possibilities[$pagamento->id]['matches'][] = [
                                'hist_uso_id' => $histUsoGuia->id,
                                'numero_guia' => $numero_guia,
                                'valor_calculado' => $calcValUso,
                            ];
                        }

                    //    echo " \n";

                     //   echo "Cliente {$cliente->nome_cliente} $cliente->id \n";

                      //  echo " \n";

                      //  echo "NumeroGuia #$numero_guia \n";



                     //   echo   "Cobranca: Valor Original: R$".$cobranca->valor_original . ' ' .$cobranca->created_at . ' ' .$cobranca->competencia;

                     //   echo "\n";
                     //   echo    "Pagamento: Valor Pago: R$".$pagamento->valor_pago . ' ' .$pagamento->created_at;

                      //  echo "\n";
                   //     echo  "Hist_uso: Valor calculado: R$".$calcValUso . ' ' .$histUsoGuia->created_at . ' ' .$histUsoGuia->id_solicitador;

                       // ]);
                    //    echo "\n";


                    }
                }


                /**
                 *
                 * Ajustar o pagamento, caso tenha encontrado alguma guia
                 *
                 */

                /**
                 * Ajusta caso possua somente uma possibilidade do valor da guia
                 */

                if (count($possibilities[$pagamento->id]['matches']) === 1)
                {

                    DB::beginTransaction();
                    $nova_guia = str_replace(
                        '[guia]',
                        $possibilities[$pagamento->id]['matches'][0]['numero_guia'],
                        $search_row['replace_to']
                    );

                    $pagamento->complemento = str_replace(
                        $search_row['search'],
                        $nova_guia,
                        $pagamento->complemento
                    );

                    $pagamento->save();
                    /**
                     * altera financeiro tb
                     */
                    $id_financeiro = str_replace('PAYMENT-', '', $pagamento->id_financeiro);
                    $finance_payment = Payment::where([
                        ['id', '=', $id_financeiro],
                        ['tags', 'like', '%;guia:']
                    ])->first();

                    if ($finance_payment)
                    {
                        $finance_payment->tags = $finance_payment->tags . $possibilities[$pagamento->id]['matches'][0]['numero_guia'];

                        $finance_payment->save();

                        //continue;
                    } else {
                        echo "Não encontrou id do financeiro $id_financeiro,  pagamento->id_financeiro $pagamento->id_financeiro \n";
                    }



                    //($finance_payment->toArray());
                  //  DB::rollBack();
                    DB::commit();

                }


            }


            $count_possibilities = count($possibilities);
            $count_exec = 0;
            foreach($possibilities as $possibility) {
                if (count($possibility['matches']) === 1)
                {
                    $count_exec++;
                }

            }

            $percent = ($count_exec * 100) / $count_possibilities;
            echo "Executou {$count_exec} correcoes \n";
            echo "Equivalente a {$percent}% \n";

            exit;




            /**
             * Exportar CSV com os dados
             */


            /**
             * relatório geral
             */
            $response = [];
            foreach($possibilities as $possibility) {
                $new_pos = [];

                $new_pos['ID_PAGAMENTO'] = $possibility['pagamento']['id'];
                $new_pos['DATA_PAGAMENTO'] = $possibility['pagamento']['data_pagamento'];
                $new_pos['REAL_DATA_PAGAMENTO'] = $possibility['pagamento']['real_data_pagamento'];
                $new_pos['ID_CLIENTE'] = $possibility['cliente']['id'];
                $new_pos['CLIENTE'] = $possibility['cliente']['nome'];
                $new_pos['HIST_USO_ID'] = '';
                $new_pos['NUMERO_GUIA'] = '';
                $new_pos['VALOR'] = '';

                $new_pos['ID_COBRANCA'] = $possibility['cobranca']['id'];
                $new_pos['REF'] = $possibility['cobranca']['competencia'];
                $new_pos['DATA_VENCIMENTO'] = $possibility['cobranca']['data_vencimento'];
                $new_pos['REAL_DATA_VENCIMENTO'] = $possibility['cobranca']['real_data_vencimento'];
                foreach($possibility['matches'] as $match) {
                    $new_pos['HIST_USO_ID'] = $match['hist_uso_id'];
                    $new_pos['NUMERO_GUIA'] = $match['numero_guia'];
                    $new_pos['VALOR'] = $match['valor_calculado'];

                    $response[] = $new_pos;
                }

            }


            $csv = Utils::ArrayToCsv($response);
            $date = date('YmdHis');
            Storage::put("relatorios/sem_guias_{$date}.csv", $csv);




            /**
             * relatório importando
             * exportado o mesmo relatório adicionando
             * o campo com possibilidades das guias
             */
            if (isset($array_csv)) {

                foreach($array_csv as $key_csv => $line_csv)
                {
                    $guias = [];
                    $line_csv['DT CRÉDITO'] = Carbon::createFromFormat('d/m/Y',  $line_csv['DT CRÉDITO'])
                        ->format('d/m/Y');

                    $line_csv['DT_VENCIMENTO'] = Carbon::createFromFormat('d/m/Y',  $line_csv['DT_VENCIMENTO'])
                        ->format('d/m/Y');

                    foreach($possibilities as $possibility) {

                        if ($line_csv['CPF/CNPJ'] == $possibility['cliente']['cpf'])
                        {
                            if (
                                $line_csv['VALOR PAGO'] == $possibility['pagamento']['valor_pago']
                                &&
                                $line_csv['DT CRÉDITO'] == $possibility['pagamento']['data_pagamento']
                                &&
                                $line_csv['DT_VENCIMENTO'] == $possibility['cobranca']['data_vencimento']
                            )
                            {
                                foreach($possibility['matches'] as $match) {
                                    $guias[] = $match['numero_guia'];
                                }

                            } else {
                                if (
                                    $line_csv['VALOR PAGO'] == $possibility['pagamento']['valor_pago']
                                ) {
                                    echo "Cliente {$line_csv['CLIENTE']} \n";
                                    echo "Valor Pago |{$line_csv['VALOR PAGO']}| vs |{$possibility['pagamento']['valor_pago']}| \n";
                                    echo "Data Credito |{$line_csv['DT CRÉDITO']}| vs |{$possibility['pagamento']['data_pagamento']}| \n";
                                    echo "Data Vencimento |{$line_csv['DT_VENCIMENTO']}| vs |{$possibility['cobranca']['data_vencimento']}| \n";
                                    echo "Pagamento Created_at |{$possibility['pagamento']['created_at']}| \n";
                                }

                            }


                        }

                    }
                    if (count($guias) === 0) $guias[] = '-';

                    $array_csv[$key_csv]['guias'] = implode(',', $guias);
                }


                $csv = Utils::ArrayToCsv($array_csv, null, ';');
                $date = date('YmdHis');
                Storage::put("relatorios/sem_guias_bianca_{$date}.csv", $csv);
            }



           // dd($response);

            //var_dump($pagamentos->toSql(), $pagamentos->getBindings(), $pagamentos->count());

        }


    }
}
