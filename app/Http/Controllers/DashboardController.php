<?php

namespace App\Http\Controllers;

use App\Curl;
use App\Helpers\Utils;
use App\Models\DadosTemporais;
use App\Models\GrupoHospitalar;
use App\Models\Participacao;
use App\Models\Pets;
use App\Models\PetsPlanos;
use App\Models\Planos;
use App\Models\Procedimentos;
use App\Models\Vendas;
use App\Models\VendedoresPontuacao;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Guides\Entities\HistoricoUso;

/**
 * Class DashboardController
 * @package App\Http\Controllers
 * Este controller fornecerá todos os resultados para os endpoints do Dashboard
 */
class DashboardController extends AppBaseController
{

    public function home()
    {
        if (!\Entrust::can('ver_dashboard')) {
            return self::notAllowed();
        }

        return view('dashboard.home');
    }

    // --- CAIXAS

    /**
     * Caixa NPS
     * O valor da nota de NPS
     */
    public function nps(Request $request)
    {
        list($start, $end) = array_values(self::getDates($request));

        $nps = DadosTemporais::getNps($start);
        if(!$nps) {
            return 0;
        }

        return [
            'data' => (new Carbon()),
            'value' => $nps,
            'message' => '',
            'permission' => true
        ];
    }

    /**
     * API NPS SurveyMonkey
     * O valor da nota de NPS
     */
    public function npsSurveyMonkey(Request $request)
    {
        ini_set('max_execution_time', 300);

        $nps = 0;
        $choices = [];
        $ch = new Curl();
        $url = 'https://api.surveymonkey.com/v3/surveys/151430118/pages/28431485/questions/100157203/rollups';
        $ch->getDefaults($url);
        $npsData = json_decode($ch->execute([
            'Authorization: BEARER 53oHUGbKqIRe68TikL85BP3f0lzZ9oznJQscif0onxtH7CmCnfAFv1778std6nlSPSVrFQQQWwg7Jq6ll.RGIZBs4ObHIw5evIqF0oSNlEQfjk0qOVDj0Bn3Q4.kZ.Oz'
        ]));
        if (isset($npsData->summary)) {
            $choices = $npsData->summary[0]->rows[0]->choices;
            $nps = $choices[10]->count + $choices[9]->count - ($choices[6]->count + $choices[5]->count + $choices[4]->count + $choices[3]->count + $choices[2]->count + $choices[1]->count + $choices[0]->count);
    
            $nps = $nps / $npsData->summary[0]->answered;
            $nps = round($nps * 100);
        } else {
            $nps = false;
        }

        return [
            'data' => (new Carbon()),
            'value' => $nps,
            'message' => '',
            'permission' => true
        ];
    }

    /**
     * Caixa de vidas ativas
     * Todos os PETS que estão ativos no sistema
     */
    public function vidasAtivas(Request $request, $checkPermissions = true, $raw = false)
    {
        $exclusivamentePorcentagem = false;
        if($checkPermissions) {
            if(!\Entrust::can('dashboard_vidas_ativas')) {
                return [
                    'permission' => false
                ];
            }
            $exclusivamentePorcentagem = \Entrust::can('exclusivamente_porcentagem');
        }

        ini_set('max_execution_time', 300);
        list($start, $end) = array_values(self::getDates($request));

        $vidas = \App\Models\Pets::where('ativo','1')->count('id');
        $mensais = \App\Models\Pets::where('ativo','1')->where('regime', Pets::REGIME_MENSAL)->count('id');
        $anuais = \App\Models\Pets::where('ativo','1')->where('regime', 'LIKE', '%' . Pets::REGIME_ANUAL . '%')->count('id');

        $mensaisPercentual = number_format(($mensais/$vidas)*100, 2);
        $anuaisPercentual = number_format(($anuais/$vidas)*100, 2);


        $ratio = 0;
        $extra = [];
        if(!$exclusivamentePorcentagem) {
            $extra = [
                [
                    'percent' => false,
                    'description' => "Mensais: $mensaisPercentual% | Anuais: $anuaisPercentual%",
                    'value' => "$mensais | $anuais"
                ],
                [
                    'percent' => false,
                    'description' => "Vidas Inativas",
                    'value' => '<i class="bold fa fa-ban text-danger"></i> ' . self::vidasInativas($request, false)['value']
                ],
            ];
        } else {
            $vidasAtivasAnteriores = DadosTemporais::getVidasAtivas($end->copy()->subMonth());
            $ratio = ($vidasAtivasAnteriores ? $vidasAtivasAnteriores->valor : 0)/$vidas*100;
        }

        return [
            'intervalo' => [
                'start' => $start,
                'end' => $end
            ],
            'data' => (new Carbon()),
            'value' => $exclusivamentePorcentagem && !$raw ? Utils::ratio(100-$ratio) : $vidas,
            'message' => '',
            'extra' => $extra,
            'permission' => true
        ];
    }

    /**
     * Caixa de vidas ativas mensais
     * PETS que estão ativos no sistema e são de regime MENSAL
     */
    public function vidasAtivasMensais(Request $request, $checkPermissions = true)
    {
        if($checkPermissions) {
            if(!\Entrust::can('dashboard_vidas_ativas_mensais')) {
                return [
                    'permission' => false
                ];
            }
        }

        ini_set('max_execution_time', 300);
        list($start, $end) = array_values(self::getDates($request));


        $total = $this->vidasAtivas($request, $checkPermissions)['value'];
        $vidas = \App\Models\Pets::where('ativo','1')->where('regime', Pets::REGIME_MENSAL)->count('id');

        $extra = $vidas/$total * 100;

        return [
            'intervalo' => [
                'start' => $start,
                'end' => $end
            ],
            'data' => (new Carbon()),
            'value' => $vidas,
            'message' => '',
            'extra' => [
                [
                    'description' => '% em relação às vidas ativas',
                    'value' => number_format($extra,2, ',', '.')
                ]
            ],
            'permission' => true
        ];
    }

    /**
     * Caixa de vidas ativas anuais
     * PETS que estão ativos no sistema e são de regime MENSAL
     */
    public function vidasAtivasAnuais(Request $request, $checkPermissions = true)
    {
        if($checkPermissions) {
            if(!\Entrust::can('dashboard_vidas_ativas_anuais')) {
                return [
                    'permission' => false
                ];
            }
        }

        ini_set('max_execution_time', 300);
        list($start, $end) = array_values(self::getDates($request));

        $vidas = \App\Models\Pets::where('ativo','1')->where('regime', 'LIKE', '%' . Pets::REGIME_ANUAL . '%')->count('id');
        $total = $this->vidasAtivas($request, $checkPermissions)['value'];
        $extra = $vidas/$total * 100;

        return [
            'intervalo' => [
                'start' => $start,
                'end' => $end
            ],
            'data' => (new Carbon()),
            'value' => $vidas,
            'message' => '',
            'extra' => [
                [
                    'description' => '% em relação às vidas ativas',
                    'value' => number_format($extra,2)
                ]
            ],
            'permission' => true
        ];
    }

    /**
     * Caixa de vidas inativas
     * Todos os PETS que estão inativos no sistema
     * @return array
     */
    public function vidasInativas(Request $request, $checkPermissions = true)
    {
        if($checkPermissions) {
            if(!\Entrust::can('dashboard_vidas_inativas')) {
                return [
                    'permission' => false
                ];
            }
        }

        ini_set('max_execution_time', 300);
        list($start, $end) = array_values(self::getDates($request));


        $vidas = \App\Models\Pets::where('ativo','0')->count('id');
        return [
            'intervalo' => [
                'start' => $start,
                'end' => $end
            ],
            'data' => (new Carbon()),
            'value' => $vidas,
            'message' => '',
            'permission' => true
        ];
    }

    /**
     * Caixa de vendas
     * Contratos com a DATA DE INICIO DO CONTRATO no mês e status igual a "PRIMEIRO PLANO"
     * @return array
     */
    public function vendas(Request $request)
    {
        ini_set('max_execution_time', 300);

        $exclusivamentePorcentagem = \Entrust::can('exclusivamente_porcentagem');
        if(!\Entrust::can('dashboard_vendas')) {
            return [
                'permission' => false
            ];
        }

        list($start, $end) = array_values(self::getDates($request));

        $start = $start->setTime(0,0,0);
        $end->setTime(23,59,59);

        $novasVidas = \App\Models\PetsPlanos::whereBetween('data_inicio_contrato', [$start, $end])->where('status', 'P')->count();
        $ratio = Utils::decimal($this->crescimentoMensal($request));
        return [
            'intervalo' => [
                'start' => $start,
                'end' => $end
            ],
            'data' => (new Carbon()),
            'value' => $exclusivamentePorcentagem ? $ratio . "%" : $novasVidas,
            'extra' => [
                [
                    'percent' => true,
                    'description' => "% em relação ao número de vidas ativas do dia anterior ao período",
                    'value' => "$ratio"
                ]
            ],
            'permission' => true
        ];
    }

    /**
     * Caixa de cancelamentos
     * Contratos com a DATA DE ENCERRAMENTO DO CONTRATO no mês
     * @param Request $request
     * @return array
     */
    public function cancelamentos(Request $request, $checkPermissions = true)
    {
        $exclusivamentePorcentagem = false;
        if($checkPermissions) {
            if(!\Entrust::can('dashboard_cancelamentos')) {
                return [
                    'permission' => false
                ];
            }
            $exclusivamentePorcentagem = \Entrust::can('exclusivamente_porcentagem');
        }

        ini_set('max_execution_time', 300);
        list($start, $end) = array_values(self::getDates($request));

        $diff_in_months = $start->diffInMonths($end);
        $diff_in_months++;

        $start = $start->setTime(0,0,0);
        $end->setTime(23,59,59);

        $vidas = \App\Models\PetsPlanos::whereBetween('data_encerramento_contrato',[$start, $end])
            ->join('pets', 'pets.id', '=', 'pets_planos.id_pet')
            ->where('pets.ativo', 0)
            ->count('pets_planos.id');

        $vidasAtivasOntem = DadosTemporais::getVidasAtivas($start->subDay());
        if(!$vidasAtivasOntem) {
            $extra = 0;
        } else {
            $extra = number_format($vidas/$vidasAtivasOntem->valor*100, 2);
        }

        $returnedExtra = [
            [
                'description' => "% em relação ao número de vidas ativas do dia anterior ao período",
                'value' => $extra
            ]
        ];

        if ($diff_in_months > 1) {
            $returnedExtra[] = [
                'description' => 'média do período (considerando mês completo)',
                'value' => number_format($extra/$diff_in_months, 2)
            ];
        }

        return [
            'intervalo' => [
                'start' => $start,
                'end' => $end
            ],
            'data' => (new Carbon()),
            'extra' => $returnedExtra,
            'value' => $exclusivamentePorcentagem ? $extra . "%" : $vidas,
            'message' => '',
            'permission' => true
        ];
    }

    /**
     * Caixa de sinistralidade diária
     * Valor em REAIS de sinistralidade no dia
     * @param Request $request
     * @return array
     */
    public function sinistralidadeDiaria(Request $request)
    {
        if(!\Entrust::can('dashboard_sinistralidade_diaria')) {
            return [
                'permission' => false
            ];
        }

        ini_set('max_execution_time', 300);
        $dates = self::getDates($request);
        $start = $end = $dates['end'];
        $start = $start->copy()->setTime(0,0,1);
        $end->setTime(23,59,59);

        $sinistralidade = \Modules\Guides\Entities\HistoricoUso::whereBetween('created_at', [$start, $end])->where('status', HistoricoUso::STATUS_LIBERADO)->sum('valor_momento');
        return [
            'intervalo' => [
                'start' => $start,
                'end' => $end
            ],
            'data' => (new Carbon()),
            'value' => Utils::money($sinistralidade),
            'message' => '',
            'permission' => true
        ];
    }

    /**
     * Caixa de sinistralidade mensal
     * Valor em REAIS de sinistralidade agrupado por dia (dia +5)
     * @param Request $request
     * @return array
     */
    public function sinistralidadeMensal(Request $request)
    {
        $exclusivamentePorcentagem = false;
        if(!\Entrust::can('dashboard_sinistralidade_mensal')) {
            return [
                'permission' => false
            ];
            $exclusivamentePorcentagem = \Entrust::can('exclusivamente_porcentagem');
        }

        ini_set('max_execution_time', 300);
        list($start, $end) = array_values(self::getDates($request));

        $diff_in_months = $start->diffInMonths($end);
        $diff_in_months++;

        $sinistralidade = \Modules\Guides\Entities\HistoricoUso::whereBetween('created_at', [$start, $end])->where('status', HistoricoUso::STATUS_LIBERADO)->sum('valor_momento');
        $faturamentoMensal = $this->faturamentoMensalPrevisto($request, false)['value'];



        $extra = 0;
        $ratio = (($sinistralidade*100)/$faturamentoMensal);
        if($faturamentoMensal > 0) {
            $extra = number_format($ratio, 2);
        }

        $returnedExtra = [
            [
                'description' => '% em relação ao faturamento mensal',
                'value' => $extra
            ]
        ];

        if ($diff_in_months > 1) {
            $returnedExtra[] = [
                'description' => 'média do período (considerando mês completo)',
                'value' => number_format($extra/$diff_in_months, 2)
            ];
        }

        return [
            'intervalo' => [
                'start' => $start,
                'end' => $end
            ],
            'extra' => $returnedExtra,
            'data' => (new Carbon()),
            'value' => $exclusivamentePorcentagem ? Utils::ratio($ratio) : Utils::money($sinistralidade),
            'rawValue' => $sinistralidade,
            'message' => '',
            'permission' => true
        ];
    }

    /**
     * Valor em REAIS de atraso no período
     * @param Request $request
     * @return array
     */
    public function atrasoMensal(Request $request)
    {
        $exclusivamentePorcentagem = false;
        if(!\Entrust::can('dashboard_atraso_mensal')) {
            return [
                'permission' => false
            ];
            $exclusivamentePorcentagem = \Entrust::can('exclusivamente_porcentagem');
        }

        ini_set('max_execution_time', 300);
        list($start, $end) = array_values(self::getDates($request));

        $diff_in_months = $start->diffInMonths($end);
        $diff_in_months++;

        $start = $start->firstOfMonth()->startOfDay();
        $end = $end->lastOfMonth()->endOfDay();

        $atrasoMensal = \App\Models\Cobrancas::whereBetween('data_vencimento', [$start, $end])->
        leftJoin('pagamentos as p', 'p.id_cobranca', '=', 'cobrancas.id')->
        where('cobrancas.data_vencimento', '<', (new Carbon()))->
        whereNull('cobrancas.cancelada_em')->
        whereNull('p.id')->sum('valor_original');



        $faturamentoMensal = $this->faturamentoMensal($request, false)['value'];
        $extra = 0;
        $ratio = 0;

        if($faturamentoMensal > 0) {
            $ratio = (($atrasoMensal*100)/$faturamentoMensal);
            $extra = Utils::decimal($ratio);
        }

        $returnedExtra = [];
        if ($diff_in_months == 1) {
            $returnedExtra[] = [
                'description' => '% em relação ao faturamento',
                'value' => $extra
            ];
        }
//        else {
//            $returnedExtra[] = [
//                'description' => 'média do período (considerando mês completo)',
//                'value' => number_format($extra*$diff_in_months, 2)
//            ];
//        }

        return [
            'intervalo' => [
                'start' => $start,
                'end' => $end
            ],
            'extra' => $returnedExtra,
            'data' => (new Carbon()),
            'value' => $exclusivamentePorcentagem ? Utils::ratio($ratio) : Utils::money($atrasoMensal),
            'rawValue' => $atrasoMensal,
            'message' => '',
            'permission' => true
        ];
    }

    /**
     * Valor em REAIS de faturamento GERADO
     * @param Request $request
     ** @return array
     */
    public function faturamentoMensal(Request $request, $formatMoney = true)
    {
        if(!\Entrust::can('dashboard_faturamento_mensal')) {
            return [
                'permission' => false
            ];
        }

        ini_set('max_execution_time', 300);
        list($start, $end) = array_values(self::getDates($request));
//        $start = $start->firstOfMonth()->startOfDay();
//        $end = $end->lastOfMonth()->endOfDay();

        $faturamento = \App\Models\Cobrancas::whereBetween('competencia', [$start->format('Y-m'), $end->format('Y-m')])
            ->whereNull('cancelada_em')
            ->sum('valor_original');
//        $faturamento = \App\Models\Cobrancas::where('competencia', $end->format('Y-m'))->whereNull('cancelada_em')->sum('valor_original');

        $result = $faturamento;
        if($formatMoney) {
            $result = Utils::money($faturamento);
        }
        return [
            'intervalo' => [
                'start' => $start,
                'end' => $end
            ],
            'data' => (new Carbon()),
            'value' => $result,
            'rawValue' => $faturamento,
            'message' => '',
            'permission' => true
        ];
    }

    /**
     * Caixa de média recorrente mensal
     * @param Request $request
     * @param bool $formatMoney
     * @return array
     */
    public function mediaRecorrenteMensal(Request $request)
    {
        if(!\Entrust::can('dashboard_media_recorrente_mensal')) {
            return [
                'permission' => false
            ];
        }

        ini_set('max_execution_time', 300);
        list($start, $end) = array_values(self::getDates($request));
        /**
         * @var $start Carbon
         * @var $end Carbon
         */
        $start = $start->firstOfMonth()->startOfDay();
        $end = $end->lastOfMonth()->endOfDay();

        $faturamento = $this->faturamentoMensalPrevisto($request, false)['value'];
        $vidasMensais = \App\Models\Pets::where('ativo', 1)->where('regime', 'MENSAL')->get()->count();

        $vidasMensais = $vidasMensais ?: 1;

        return [
            'intervalo' => [
                'start' => $start,
                'end' => $end
            ],
            'data' => (new Carbon()),
            'value' => Utils::money($faturamento/$vidasMensais),
            'rawValue' => ($faturamento/$vidasMensais),
            'message' => '',
            'permission' => true
        ];
    }

    /**
     * Caixa de faturamento mensal previsto
     * @param Request $request
     * @param bool $formatMoney
     * @return array
     */
    public function faturamentoMensalPrevisto(Request $request, $formatMoney = true)
    {
        if(!\Entrust::can('dashboard_faturamento_mensal_previsto')) {
            return [
                'permission' => false
            ];
        }

        ini_set('max_execution_time', 300);
        list($start, $end) = array_values(self::getDates($request));
        /**
         * @var $start Carbon
         * @var $end Carbon
         */
        $start = $start->firstOfMonth()->startOfDay();
        $end = $end->lastOfMonth()->endOfDay();

        $faturamento = \App\Models\Pets::where('ativo', 1)->where('regime', Pets::REGIME_MENSAL)->sum('valor');

        $result = $faturamento;
        if($formatMoney) {
            $result = Utils::money($faturamento);
        }
        return [
            'intervalo' => [
                'start' => $start,
                'end' => $end
            ],
            'data' => (new Carbon()),
            'value' => $result,
            'rawValue' => $faturamento,
            'message' => '',
            'permission' => true
        ];
    }


    public function participativos(Request $request, $formatMoney = true)
    {
        ini_set('max_execution_time', 300);
        list($start, $end) = array_values(self::getDates($request));
        /**
         * Do mês
         * @var $start Carbon
         * @var $end Carbon
         */
        $start = $start->firstOfMonth()->startOfDay();
        $end = $end->lastOfMonth()->endOfDay();

        $participacao = Participacao::where('competencia', $end->format('Y-m'))->sum('valor_participacao');

        if($formatMoney) {
            $participacao = Utils::money($participacao);
        }

        return [
            'intervalo' => [
//                'start' => $start,
                'end' => $end
            ],
            'data' => (new Carbon()),
            'value' => $participacao,
            'message' => '',
            'permission' => true
        ];
    }

    public function statusPetsPlanos(Request $request, $status)
    {

        ini_set('max_execution_time', 300);
        list($start, $end) = array_values(self::getDates($request));
        $start = $start->setTime(0,0,0);
        $end->setTime(23,59,59);

        $petsPlanos = PetsPlanos::whereBetween('data_inicio_contrato', [$start, $end])->where('status', $status)->count();

        return [
            'intervalo' => [
                'start' => $start,
                'end' => $end
            ],
            'data' => (new Carbon()),
            'value' => $petsPlanos,
            'message' => '',
            'permission' => true
        ];;
    }

    // --- FIM DAS CAIXAS
    //
    // --- GRÁFICOS

    public function cancelamentosSerial(Request $request) {
        if(!\Entrust::can('dashboard_grafico_cancelamentos')) {
            return [
                'permission' => false
            ];
        }

        ini_set('max_execution_time', 300);
        list($start, $end) = array_values(self::getSerialDates($request));

        $response = [];
        $query = \App\Models\PetsPlanos::query();

        for($i = 0; $i <= $start->diffInDays($end); $i++) {
            $data = [];
            $date = $end->copy()->subDays($i);
            $data['nome'] = $date->format('d/m/Y');
            $data['cancelamentos'] = \App\Models\PetsPlanos::where('data_encerramento_contrato', $date->format('Y-m-d'))
//                ->groupBy('data_encerramento_contrato')
                ->join('pets', 'pets.id', '=', 'pets_planos.id_pet')
                ->where('pets.ativo', 0)
                ->count('pets_planos.id');
            $data['novasVidas'] = \App\Models\PetsPlanos::where('data_inicio_contrato', $date->format('Y-m-d'))
                ->where('status', 'P')
                ->groupBy('data_inicio_contrato')
                ->count('id');

            $response[] = $data;
        }

        return [
            'items' => array_reverse($response),
            'permission' => true,
            'options' => [
                'theme' => 'light',
                "marginRight" => 50,
                'categoryAxis' => [
                    'labelsEnabled' => true,
                    "axisAlpha" => 0,
                    'labelRotation' => 0,
                    'labelFrequency' => 3,
                    'labelOffset' => 0,
                    'offset' => 0,
                    'showFirstLabel' => false,
                ],
                'valueAxes' => [
                    [
                        'labelsEnabled' => false,
                        "axisAlpha" => 0,
                    ]
                ],
                'export' => [
                    'enabled' => false
                ],
                'graphs' => [
                    [
                        "lineColor" => "#E35B5A",
                        "fillAlphas" => 0.3,
                        "fillColorsField" => "lineColor",
                        "balloonText" => "Cancelamentos em [[category]]: <b>[[value]]</b>",
                        "valueField" => "cancelamentos",
                    ],
                    [
                        "lineColor" => "#2AB4C0",
                        "fillAlphas" => 0.3,
                        "fillColorsField" => "lineColor",
                        "balloonText" => "Novas Vidas em [[category]]: <b>[[value]]</b>",
                        "valueField" => "novasVidas",
                        //"bullet" => "round",
                        "bulletSize" => 8,
                        "lineThickness" => 2,
                        'type' => 'column'
                    ]
                ]
            ]
        ];
    }

    /**
     * Agora informa a quantidade de VIDAS ATIVAS por dia registradas diáriamente
     * @param Request $request
     * @return array
     */
    public function novasVidasSerial(Request $request) {
        if(!\Entrust::can('dashboard_grafico_novas_vidas')) {
            return [
                'permission' => false
            ];
        }

        ini_set('max_execution_time', 300);
        list($start, $end) = array_values(self::getSerialDates($request));

        $response = [];

        for($i = 0; $i <= $start->diffInDays($end); $i++) {
            $data = [];
            $date = $end->copy()->subDays($i);
            $data['nome'] = $date->format('d/m/Y');
            $registro = \App\Models\DadosTemporais::where('indicador', 'vidas_ativas')->where('data_referencia', $date->format('Y-m-d'))->first();
            $data['valor'] = $registro ? $registro->valor_numerico : 0;
            $data['original'] = $registro;

            if($date->isToday()) {
                $data['valor'] = \App\Models\Pets::where('ativo', 1)->count('id');
            }

            $response[] = $data;
        }

        return [
            'items' => array_reverse($response),
            'permission' => true,
            'options' => [
                'theme' => 'light',
                "marginRight" => 50,

                'categoryAxis' => [
                    'labelsEnabled' => true,
                    "axisAlpha" => 0,
                    'labelRotation' => 0,
                    'labelFrequency' => 3,
                    'labelOffset' => 0,
                    'offset' => 0,
                    'showFirstLabel' => false,
                ],
                'valueAxes' => [
                    [
                        "axisAlpha" => 0,
                        "gridAlpha" => 0,
                        "labelsEnabled" => false,
//                        'labelRotation' => 45
                    ]
                ],
                'export' => [
                    'enabled' => false
                ],
                'graphs' => [
                    [
                        "lineColor" => "#26C281",
                        "fillAlphas" => 0.3,
                        "fillColorsField" => "lineColor"
                    ]
                ]
            ]
        ];
    }

    public function sinistralidadePorCredenciada(Request $request)
    {
        if(!\Entrust::can('dashboard_grafico_sinistralidade_por_credenciada')) {
            return [
                'permission' => false
            ];
        }

        list($start, $end) = array_values(self::getDates($request));

        $query = \Modules\Guides\Entities\HistoricoUso::whereBetween('historico_uso.created_at', [$start, $end])->where('status', HistoricoUso::STATUS_LIBERADO);
        $total = $query->sum('valor_momento');
        if($total == 0) {
            return [];
        }

        $top = $query->groupBy('historico_uso.id_clinica')
                     ->selectRaw('SUM(historico_uso.valor_momento) as valor, c.nome_clinica as nome')
                     ->join('clinicas as c', 'historico_uso.id_clinica', '=', 'c.id')
                     ->limit(5)->orderByRaw('SUM(historico_uso.valor_momento) DESC')->get();
        $topTotal = $top->sum('valor');
        $outros = new \stdClass();
        $outros->nome = "Outros";
        $outros->valor = $total -  $topTotal;
        $top->push($outros);



        return [
            'items' => $top->map(function($t) {
                return $t;
            }),
            'permission' => true
        ];
    }

    public function caes(Request $request)
    {
        if(!\Entrust::can('dashboard_grafico_caes')) {
            return [
                'permission' => false
            ];
        }

        list($start, $end) = array_values(self::getDates($request));
        $pets = \App\Models\Pets::where('tipo', 'CACHORRO')
            ->groupBy('SEXO')
            ->selectRaw('COUNT(pets.id) as valor, pets.sexo as nome')->get();
        $pets = $pets->map(function($p) {
            return self::transformPetPieData($p);
        });

        return [
            'items' => $pets,
            'permission' => true,
            'options' => [
                "colorField" => "color"
            ]
        ];
    }

    public function gatos(Request $request)
    {
        if(!\Entrust::can('dashboard_grafico_gatos')) {
            return [
                'permission' => false
            ];
        }

        list($start, $end) = array_values(self::getDates($request));
        $pets = \App\Models\Pets::where('tipo', 'GATO')
            ->groupBy('SEXO')
            ->selectRaw('COUNT(pets.id) as valor, pets.sexo as nome')->get();

        $pets = $pets->map(function($p) {
            return self::transformPetPieData($p);
        });

        return [
            'permission' => true,
            'items' => $pets,
            'options' => [
                "colorField" => "color"
            ]
        ];
    }

    public function castradosVersusNaoCastrados(Request $request)
    {
        if(!\Entrust::can('dashboard_grafico_castracao')) {
            return [
                'permission' => false
            ];
        }

        $procedimentosCastracao = Procedimentos::where('id_grupo','=', 99909)->pluck('id');
        $todosOsPets = Pets::where('ativo', 1)->count();
        $petsCastrados = Pets::whereIn('id', HistoricoUso::where('status', HistoricoUso::STATUS_LIBERADO)
                            ->whereIn('id_procedimento', $procedimentosCastracao)
                            ->groupBy('id_pet')->pluck('id_pet'))->where('ativo', 1)->count();

        return [
            'permission' => true,
            'items' => [
                [
                    'nome' => 'Castrados',
                    'valor' => $petsCastrados
                ],
                [
                    'nome' => 'Não castrados',
                    'valor' => $todosOsPets - $petsCastrados
                ],
            ],
            'options' => [
                "colorField" => "color"
            ]
        ];
    }

    /**
     * Gráfico de participativos versus integrais
     * @param Request $request
     * @return array
     */
    public function petsParticipativosVersusIntegrais(Request $request)
    {
        if(!\Entrust::can('dashboard_grafico_participativos_versus_integrais')) {
            return [
                'permission' => false
            ];
        }

        $todos = \App\Models\Pets::where('ativo', 1)->count('id');
        $participativos = \App\Models\Pets::where('participativo', 1)->count('id');

        return [
            'items' => [
                [
                    'nome' => 'Participativos',
                    'valor' => $participativos
                ],
                [
                    'nome' => 'Integrais',
                    'valor' => $todos - $participativos
                ]
            ],
            'options' => [
                'theme' => 'light'
            ],
            'permission' => true
        ];
    }

    /**
     * Gráfico de pets por plano
     * @param Request $request
     * @return array
     */
    public function petsPorPlano(Request $request) {
        if(!\Entrust::can('dashboard_grafico_pets_por_plano')) {
            return [
                'permission' => false
            ];
        }

        $pets = \App\Models\PetsPlanos::groupBy('pl.id')
            ->selectRaw('pl.nome_plano as nome, COUNT(p.id) as valor')
            ->join('pets as p', 'p.id', '=', 'pets_planos.id_pet')
            ->join('planos as pl', 'pl.id', '=', 'pets_planos.id_plano', 'right outer')
            ->where('p.ativo', 1)
            ->orderByRaw('COUNT(p.id) DESC')->get();

        $pets = self::fillColorsGradient($pets, "#F3C200");

        return [
            'items' => $pets,
            'permission' => true,
            'options' => [
                'theme' => 'light',
                'categoryAxis' => [
                    'labelsEnabled' => true,
                    "axisAlpha" => 0,
                    'gridAlpha' => 0,
                    'labelRotation' => 45,
                    'labelOffset' => 0,
                    'offset' => 0,
                ],
                'valueAxes' => [
                    [
                        'gridAlpha' => 0
                    ]
                ],
                'graphs' => [
                    [
                        'fillColorsField' => 'color'
                    ]
                ]
            ]
        ];
    }

    /**
     * Gráfico de pets por idade
     * @param Request $request
     * @return array
     */
    public function petsPorIdade(Request $request) {
        if(!\Entrust::can('dashboard_grafico_pets_por_idade')) {
            return [
                'permission' => false
            ];
        }

        $idadeMaxima = 18;
        $resultado = [];
        $total = 0;
        for($i = 0; $i <= $idadeMaxima; $i++) {
            $intervaloInicial = (new Carbon())->subYear($i);
            $intervaloFinal = (new Carbon())->subYear($i-1);
            $pets = \App\Models\Pets::whereBetween('data_nascimento', [$intervaloInicial, $intervaloFinal])->where('ativo', 1)->count('id');
            $total += $pets;
            $resultado[] = [
                'nome' => "Até " . ($i+1) . " ano(s)",
                'valor' => $pets
            ];
        }

        return [
            'items' => $resultado,
            'permission' => true,
            'options' => [
                'theme' => 'light',
                'categoryAxis' => [
                    'labelsEnabled' => true,
                    "axisAlpha" => 0,
                    'gridAlpha' => 0,
                    'labelRotation' => 45,
                    'labelOffset' => 0,
                    'offset' => 0,
                ],
                'valueAxes' => [
                    [
                        'gridAlpha' => 0
                    ]
                ]
            ]
        ];
    }

    /**
     * Gráfico do ranking de vendedores
     * @param Request $request
     * @return array
     */
    public function rankingVendedores(Request $request)
    {
        if(!\Entrust::can('dashboard_grafico_ranking_vendedores')) {
            return [
                'permission' => false
            ];
        }

        list($start, $end) = array_values(self::getDates($request));
        /**
         * @var $start Carbon
         * @var $end Carbon
         */
        //$start = $start->firstOfMonth()->startOfDay();
        //$end = $end->lastOfMonth()->endOfDay();

        /**
        $vendas = \App\Models\PetsPlanos::selectRaw('COUNT(pets_planos.id) as valor, vendedores.nome, vendedores.avatar, vendedores.id')
            ->whereBetween('data_inicio_contrato', [$start, $end])
            ->join('vendedores', 'vendedores.id', '=', 'pets_planos.id_vendedor')
            ->whereNotNull('id_vendedor')
            ->where('vendedores.direto', 1)
            ->groupBy('id_vendedor')
            ->orderByRaw('COUNT(pets_planos.id) DESC')
            ->limit(10)
            ->get();
        **/
        
        $vendas = \App\Models\Vendas::selectRaw('COUNT(vendas.id) as valor, vendedores.nome, vendedores.avatar, vendedores.id')
            ->whereBetween('data_inicio_contrato', [$start, $end])
            ->leftJoin('vendedores', 'vendedores.id', '=', 'vendas.id_vendedor')
            ->whereNotNull('id_vendedor')
            ->where('vendedores.direto', 1)
            ->groupBy('id_vendedor')
            ->orderByRaw('COUNT(vendas.id) DESC')
            ->limit(10)
            ->get();

        $count = 0;
        $size = $vendas->count();
        $max = 100;
        foreach($vendas as &$vendedor) {
            $vendedor->avatar = route('vendedores.avatar', $vendedor->id);
            $vendedor->color = "#" . self::colorBlendOpacity("#29B4B6", 100 - ceil(($max/$size)*$count) );
            $count++;
        }

        return [
            'items' => $vendas,
            'permission' => true,
            'options' => [
                'theme' => 'light',
                'categoryAxis' => [
                    'labelsEnabled' => true,
                    "axisAlpha" => 0,
                    'gridAlpha' => 0,
                    'labelRotation' => 45,
                    'labelOffset' => 0,
                    'offset' => 0,
                ],
                'smoothCustomBullets' => [
                    'borderRadius' => 'auto'
                ],
                'graphs' => [
                    [
                        'customBulletField' => 'avatar',
                        "bulletOffset" => 10,
                        "bulletSize" => 50,
                        "type" => "column",
                        'fillColorsField' => 'color'
                    ]
                ],
                'valueAxes' => [
                    [
                        "gridAlpha" => 0,
                    ]
                ],
                "chartCursor" => false
            ]
        ];
    }

    /**
     * Gráfico do ranking de vendedores
     * @param Request $request
     * @return array
     */
    public function comissaoVendas(Request $request)
    {
        if(!\Entrust::can('dashboard_grafico_ranking_vendedores')) {
            return [
                'permission' => false
            ];
        }

        /**
         * @var $start Carbon
         * @var $end Carbon
         */
        list($start, $end) = array_values(self::getDates($request));


        $vendedores = \App\Models\Vendedores::where('direto', 1)
                                            ->orderBy('nome')->get();

        $base = [
            'permission' => true,
            'headers' => [
                'Nome',
                'Estrelas',
                'Vendas',
                'Bônus',
                'Comissão',
                'Total',
            ],
            'rows' => []
        ];

        foreach($vendedores as $v) {
            $r = [];
            $vendas = Vendas::where('id_vendedor', $v->id)->whereBetween('data_inicio_contrato', [$start, $end])->pluck('id');
            $pontos = VendedoresPontuacao::whereIn('id_venda', $vendas)->sum('pontuacao');
            $bonus = VendedoresPontuacao::bonus($pontos);
            $comissao = Vendas::where('id_vendedor', $v->id)->whereBetween('data_inicio_contrato', [$start, $end])->sum('comissao');


            $r = [
                $v->nome,
                $pontos,
                count($vendas),
                Utils::money($bonus),
                Utils::money($comissao),
                Utils::money($comissao + $bonus)
            ];
            $base['rows'][] = $r;
        }

        $result = collect($base['rows']);
        $result = $result->sortByDesc(function($item) {
            return Utils::moneyReverse($item[4]);
        });

        $base['rows'] = $result->values()->all();

        return $base;
    }

    // --- FIM DOS GRÁFICOS ---
    //
    // --- TABELAS ---

    public function rankingProcedimentos(Request $request)
    {
        if(!\Entrust::can('dashboard_tabela_ranking_procedimentos')) {
            return [
                'permission' => false
            ];
        }

        /**
         * @var $start Carbon
         * @var $end Carbon
         */
        list($start, $end) = array_values(self::getDates($request));


        $credenciados = \Modules\Clinics\Entities\Clinicas::orderBy('nome_clinica')->get();
        $topFive = HistoricoUso::where('status', HistoricoUso::STATUS_LIBERADO)->
                                groupBy('id_procedimento')->
                                orderByRaw('COUNT(id_procedimento) DESC')->
                                limit(5)->
                                get();

        $base = [
            'permission' => true,
            'headers' => [
                'Credenciado',
                $topFive[0]->procedimento->nome_procedimento,
                $topFive[1]->procedimento->nome_procedimento,
                $topFive[2]->procedimento->nome_procedimento,
                $topFive[3]->procedimento->nome_procedimento,
                $topFive[4]->procedimento->nome_procedimento,
            ],
            'rows' => []
        ];

        foreach($credenciados as $c) {
            $r = [];
            $r[0] = $c->nome_clinica;
            $r[1] = HistoricoUso::usosPorCredenciada($c, $topFive[0]->procedimento, $start, $end);
            $r[2] = HistoricoUso::usosPorCredenciada($c, $topFive[1]->procedimento, $start, $end);
            $r[3] = HistoricoUso::usosPorCredenciada($c, $topFive[2]->procedimento, $start, $end);
            $r[4] = HistoricoUso::usosPorCredenciada($c, $topFive[3]->procedimento, $start, $end);
            $r[5] = HistoricoUso::usosPorCredenciada($c, $topFive[4]->procedimento, $start, $end);
            $base['rows'][] = $r;
        }

        $result = collect($base['rows']);
        $result = $result->sortByDesc(function($item) {
            return $item[1] + $item[2];
        });

        $base['rows'] = $result->values()->all();

        return $base;
    }

    public function vendasPorVendedor(Request $request)
    {
        if(!\Entrust::can('dashboard_grafico_ranking_vendedores')) {
            return [
                'permission' => false
            ];
        }

        list($start, $end) = array_values(self::getDates($request));
        /**
         * @var $start Carbon
         * @var $end Carbon
         */
        $start = $start->firstOfMonth()->startOfDay();
        $end = $end->lastOfMonth()->endOfDay();

        $vendedores = \App\Models\Vendedores::all();
        $base = [
            'headers' => [
                'Nome',
                'Vendas'
            ],
            'rows' => []
        ];

        foreach($vendedores as $v) {
            $vendas = \App\Models\PetsPlanos::where('id_vendedor', $v->id)->whereBetween('data_inicio_contrato', [$start, $end])->count(['id']);
            $resultado[] = [
                'nome' => $v->nome,
                'valor' => $vendas
            ];
        }

        $resultado = collect($resultado);
        $resultado = $resultado->sortByDesc('valor');

        $base['rows'] = $resultado->sortByDesc('valor')->values()->all();
        return $base;
    }

    /**
     * Tabela de controle de vacinas
     * @param Request $request
     * @return array
     */
    public function controleVacinas(Request $request)
    {
        if(!\Entrust::can('dashboard_tabela_controle_vacinas')) {
            return [
                'permission' => false
            ];
        }

        list($start, $end) = array_values(self::getDates($request));
        /**
         * @var $start Carbon
         * @var $end Carbon
         */
        $start = $start->firstOfMonth()->subYear()->startOfDay();
        $end = $end->lastOfMonth()->endOfDay()->subYear();

        $procedimentos = \App\Models\Procedimentos::where('id_grupo', 22100)->get(['id']);
        $usos = \Modules\Guides\Entities\HistoricoUso::selectRaw('CONCAT(pets.nome_pet, " - ", p.nome_procedimento) as nome, DATE_FORMAT(historico_uso.created_at, "%d/%m/%Y %H:%i") as valor')
            ->join('pets', 'pets.id', '=', 'historico_uso.id_pet')
            ->join('procedimentos as p', 'p.id', '=', 'historico_uso.id_procedimento')
            ->where('historico_uso.status', '=', HistoricoUso::STATUS_LIBERADO)
            ->whereIn('id_procedimento', $procedimentos)
            ->whereBetween('historico_uso.created_at', [$start, $end])
            ->orderBy('historico_uso.created_at')
            ->get();

        $base = [
            'permission' => true,
            'headers' => [
                'Pet - Procedimento',
                'Data'
            ],
            'rows' => []
        ];

        foreach ($usos as $uso) {
            $u = [];
            $u[0] = $uso->nome;
            $u[1] = $uso->valor;
            $base['rows'][] = $u;
        }

        return $base;
    }

    /**
     * Tabela de controle de vacinas
     * @param Request $request
     * @return array
     */
    public function rentabilidadeDePlano(Request $request, $checkPermissions = true)
    {
        $startTime = time();
        if($checkPermissions) {
            if(!\Entrust::can('dashboard_rentabilidade_de_plano')) {
                return [
                    'permission' => false
                ];
            }
        }

        ini_set('max_execution_time', 300);

        list($start, $end) = array_values(self::getDates($request));

        $base = [
            'permission' => true,
            'time' => 0,
            'headers' => [
                'Plano',
                'Faturas',
                'Participação',
                'Recebido',
                'Sinistralidade',
                '%'
            ],
            'rows' => []
        ];

        $planos = Planos::all();

        foreach($planos as $plano) {
            $recebimentos = $plano->recebimentos();
            $participado = Participacao::participadoPlano($plano->id, $start, $end);

            $sinistralidade = $plano->sinistralidade($start, $end);
            $ratio = 0;
            if($sinistralidade && $recebimentos) {
                $ratio = $sinistralidade / $recebimentos;
            }

            $u = [];
            $u[0] = $plano->nome_plano;
            $u[1] = Utils::money($recebimentos);
            $u[2] = Utils::money($participado);
            $u[3] = Utils::money($recebimentos+$participado);
            $u[4] = Utils::money($sinistralidade);

            $u[5] = Utils::decimal($ratio * 100);
            $base['rows'][] = $u;
        }

        $base['time'] = (time() - $startTime);

        return $base;
    }

    /**
     * Tabela de vencimento de vacinas
     * @param Request $request
     * @return array
     */
    public static function vencimentoVacinas(Request $request)
    {
        if(!\Entrust::can('dashboard_tabela_vencimento_vacinas')) {
            return [
                'permission' => false
            ];
        }

        list($start, $end) = array_values(self::getDates($request));
        /**
         * @var $start Carbon
         * @var $end Carbon
         */
        $start = $start->firstOfMonth()->subYear()->startOfDay();
        $end = $end->lastOfMonth()->subYear()->endOfDay();

        $historico = \Modules\Guides\Entities\HistoricoUso::selectRaw('COUNT(p.id) as count, p.nome_procedimento')
            ->join('procedimentos as p', 'p.id', '=', 'historico_uso.id_procedimento')
            ->join('grupos_carencias as gc', 'gc.id', '=', 'p.id_grupo')
            ->join('pets as p2', 'p2.id', '=', 'historico_uso.id_pet')
            ->join('clientes as c', 'c.id', '=', 'p2.id_cliente')
            ->where('historico_uso.status', '=', HistoricoUso::STATUS_LIBERADO)
            ->where('c.ativo', '=', 1)
            ->where('gc.id', '=', 22100)
            ->whereBetween('historico_uso.created_at', [$start, $end])
            ->groupBy('p.id')
            ->get();

        foreach($historico as $h) {
            $resultado[] = [
                'nome' => $h->nome_procedimento,
                'valor' => $h->count
            ];
        }

        return [
            'items' => $resultado,
            'permission' => true,
        ];
    }

    public function sinistralidadePorPrestador(Request $request)
    {
        if(!\Entrust::can('dashboard_tabela_sinistralidade_por_prestador')) {
            return [
                'permission' => false
            ];
        }

        list($start, $end) = array_values(self::getDates($request));
        $usos = \Modules\Guides\Entities\HistoricoUso::groupBy('id_prestador')
            ->selectRaw('CONCAT(p.id, " - ", p.nome) as nome, SUM(historico_uso.valor_momento) as valor')
            ->join('prestadores as p', 'p.id', '=', 'historico_uso.id_prestador')
            ->where('status', HistoricoUso::STATUS_LIBERADO)
            ->where(function($query) use ($start, $end) {
                $query->where(function($query) use ($start, $end) {
                    $query->where('tipo_atendimento', "!=", HistoricoUso::TIPO_ENCAMINHAMENTO)
                        ->whereBetween('historico_uso.created_at', [$start, $end]);
                });
                $query->orWhere(function($query) use ($start, $end) {
                    $query->where('tipo_atendimento', HistoricoUso::TIPO_ENCAMINHAMENTO)
                        ->whereBetween('historico_uso.realizado_em', [$start, $end]);
                });
            })
            ->orderByRaw('SUM(historico_uso.valor_momento) DESC');

        $usos = $usos->get();

        $base = [
            'permission' => true,
            'headers' => [
                'Prestador',
                'Sinistralidade'
            ],
            'rows' => []
        ];

        foreach ($usos as $uso) {
            $u = [];
            $u[0] = $uso->nome;
            $u[1] = Utils::money($uso->valor);
            $base['rows'][] = $u;
        }

        return $base;
    }

    public function petsAniversariantes(Request $request)
    {
        if(!\Entrust::can('dashboard_tabela_pets_aniversariantes')) {
            return [
                'permission' => false
            ];
        }

        list($start, $end) = array_values(self::getDates($request));
        $pets = \App\Models\Pets::whereMonth('data_nascimento', '=', $end->format('m'))->orderBy(DB::raw("DAY(data_nascimento)"))->get(['nome_pet', 'data_nascimento', 'id_cliente']);

        $base = [
            'permission' => true,
            'headers' => [
                'Nome',
                'Data',
                'Email',
                'Telefone'
            ],
            'rows' => []
        ];

        foreach ($pets as $pet) {
            $p = [];
            $c = $pet->cliente()->first();

            if (!is_object($c)) {
                continue;
            }
            
            $p[0] = $pet->nome_pet . " - " . $c->nome_cliente;
            $p[1] = $pet->data_nascimento->format('d/m/Y');
            $p[2] = $c->email;
            $p[3] = $c->celular;
            $base['rows'][] = $p;
        }

        return $base;
    }

    public function clientesAniversariantes(Request $request)
    {
        if(!\Entrust::can('dashboard_tabela_clientes_aniversariantes')) {
            return [
                'permission' => false
            ];
        }

        list($start, $end) = array_values(self::getDates($request));
        $clientes = \App\Models\Clientes::whereMonth('data_nascimento', '=', $end->format('m'))->orderBy(DB::raw("DAY(data_nascimento)"))->get(['nome_cliente', 'data_nascimento', 'email', 'celular']);

        $base = [
            'permission' => true,
            'headers' => [
                'Nome',
                'Data',
                'Email',
                'Telefone'
            ],
            'rows' => []
        ];

        foreach ($clientes as $cliente) {
            $c = [];
            $c[0] = $cliente->nome_cliente;
            $c[1] = $cliente->data_nascimento->format('d/m/Y');
            $c[2] = $cliente->email;
            $c[3] = $cliente->celular;
            $base['rows'][] = $c;
        }

        return $base;
    }

    public function petsPorBairro(Request $request)
    {
        if(!\Entrust::can('dashboard_tabela_pets_ativos_por_bairro')) {
            return [
                'permission' => false
            ];
        }
        //list($start, $end) = array_values(self::getDates($request));
        $pets = \App\Models\Clientes::groupBy(DB::raw('CONCAT(clientes.bairro, " - ", clientes.cidade)'))
            ->selectRaw('COUNT(p.id) as valor, CONCAT(clientes.bairro, " - ", clientes.cidade) as nome')
            ->join('pets as p', 'p.id_cliente', '=', 'clientes.id')
            ->orderByRaw('COUNT(p.id) DESC')
            ->where('p.ativo', 1)->get();

        $base = [
            'permission' => true,
            'headers' => [
                'Nome',
                'Quantidade'
            ],
            'rows' => []
        ];

        foreach ($pets as $pet) {
            $p = [];
            $p[0] = $pet->nome;
            $p[1] = $pet->valor;
            $base['rows'][] = $p;
        }

        return $base;
    }

    public function petsInativosPorBairro(Request $request)
    {
        if(!\Entrust::can('dashboard_tabela_pets_inativos_por_bairro')) {
            return [
                'permission' => false
            ];
        }

        //list($start, $end) = array_values(self::getDates($request));
        $pets = \App\Models\Clientes::groupBy(DB::raw('CONCAT(clientes.bairro, " - ", clientes.cidade)'))
            ->selectRaw('COUNT(p.id) as valor, CONCAT(clientes.bairro, " - ", clientes.cidade) as nome')
            ->join('pets as p', 'p.id_cliente', '=', 'clientes.id')
            ->orderByRaw('COUNT(p.id) DESC')
            ->where('p.ativo', 0)->get();

        $base = [
            'permission' => true,
            'headers' => [
                'Nome',
                'Quantidade'
            ],
            'rows' => []
        ];

        foreach ($pets as $pet) {
            $p = [];
            $p[0] = $pet->nome;
            $p[1] = $pet->valor;
            $base['rows'][] = $p;
        }

        return $base;
    }

    public function petsPorCidade(Request $request)
    {
        if(!\Entrust::can('dashboard_tabela_pets_ativos_por_cidade')) {
            return [
                'permission' => false
            ];
        }

        $pets = \App\Models\Clientes::groupBy('clientes.cidade')
            ->selectRaw('COUNT(p.id) as valor, clientes.cidade as nome')
            ->join('pets as p', 'p.id_cliente', '=', 'clientes.id')
            ->orderByRaw('COUNT(p.id) DESC')
            ->where('p.ativo', 1)->get();

        $base = [
            'permission' => true,
            'headers' => [
                'Nome',
                'Quantidade'
            ],
            'rows' => []
        ];

        foreach ($pets as $pet) {
            $p = [];
            $p[0] = $pet->nome;
            $p[1] = $pet->valor;
            $base['rows'][] = $p;
        }

        return $base;
    }

    public function petsInativosPorCidade(Request $request)
    {
        if(!\Entrust::can('dashboard_tabela_pets_inativos_por_cidade')) {
            return [
                'permission' => false
            ];
        }

        $pets = \App\Models\Clientes::groupBy('clientes.cidade')
            ->selectRaw('COUNT(p.id) as valor, clientes.cidade as nome')
            ->join('pets as p', 'p.id_cliente', '=', 'clientes.id')
            ->orderByRaw('COUNT(p.id) DESC')
            ->where('p.ativo', 0)->get();

        $base = [
            'permission' => true,
            'headers' => [
                'Nome',
                'Quantidade'
            ],
            'rows' => []
        ];

        foreach ($pets as $pet) {
            $p = [];
            $p[0] = $pet->nome;
            $p[1] = $pet->valor;
            $base['rows'][] = $p;
        }

        return $base;
    }

    public function reajuste(Request $request)
    {
        ini_set('max_execution_time', 300);
        list($start, $end) = array_values(self::getDates($request));
        /**
         * @var $start Carbon
         * @var $end Carbon
         */
        $start = $start->firstOfMonth()->startOfDay();
        $end = $end->lastOfMonth()->endOfDay();

        //$petsPlanos = \App\Models\PetsPlanos::where()
    }

    public function sinistralidadeGrupoVetMedicalCenter(Request $request)
    {
        list($start, $end) = array_values(self::getDates($request));

        $grupo = GrupoHospitalar::where('nome_grupo', 'Grupo Vet Medical Center');

        $query = \Modules\Guides\Entities\HistoricoUso::whereBetween('historico_uso.created_at', [$start, $end])->where('status', HistoricoUso::STATUS_LIBERADO);


        $total = $query->sum('valor_momento');
        if($total == 0) {
            return [];
        }

        $top = $query->groupBy('historico_uso.id_clinica')
            ->selectRaw('SUM(historico_uso.valor_momento) as valor, c.nome_clinica as nome')
            ->join('clinicas as c', 'historico_uso.id_clinica', '=', 'c.id')
            ->limit(5)->orderByRaw('SUM(historico_uso.valor_momento) DESC')->get();



        $topTotal = $top->sum('valor');
        $outros = new \stdClass();
        $outros->nome = "Outros";
        $outros->valor = $total -  $topTotal;
        $top->push($outros);



        return $top->map(function($t) {
            //$t->valor = Utils::money($t->valor);
            return $t;
        });
    }


    // --- FIM DAS TABELAS ---
    //
    // --- HELPERS ---

    public static function fillColorsGradient($collection, $color) {
        $count = 0;
        $size = $collection->count();
        $max = 100;

        foreach($collection as &$item) {
            $item->color = "#" . self::colorBlendOpacity($color, 100 - ceil(($max/$size)*$count) );
            $count++;
        }

        return $collection;
    }

    private function crescimentoMensal(Request $request)
    {
        list($start, $end) = array_values(self::getDates($request));

        $vidasAtivas = DadosTemporais::getVidasAtivas($start->subDay());
        if(!$vidasAtivas) {
            return 0;
        }
        $vidasAtivas = $vidasAtivas->valor;

        $vendas = \App\Models\PetsPlanos::whereBetween('data_inicio_contrato', [$start, $end])->where('status', 'P')->count();

        $ratio = $vendas/$vidasAtivas*100;

        return $ratio;
    }
    /**
     * @param Request $request
     * @return array
     * Pega as datas referentes ao mês dado na data final
     */
    protected static function getDates(Request $request) {
        if($request->filled('end')) {
            $end = Carbon::createFromFormat('d/m/Y', $request->get('end'));
            if(!$request->get('start')) {
                $start = $end->copy()->firstOfMonth();
            } else {
                $start = Carbon::createFromFormat('d/m/Y', $request->get('start'));
            }
        } else {
            $end = new Carbon();
            $start = $end->copy()->firstOfMonth();
        }
        $start->setTime(0,0,0);
        $end->setTime(23,59,59);

        return [
            'start' => $start,
            'end'   => $end
        ];
    }
    public static function getSerialDates(Request $request)
    {
        if($request->filled('end') && $request->filled('start')) {
            $start = Carbon::createFromFormat('d/m/Y', $request->get('start'));
            $end = Carbon::createFromFormat('d/m/Y', $request->get('end'));
        } else {
            $start = (new Carbon())->subMonth();
            $end = new Carbon();
        }

        $start->setTime(0,0,0);
        $end->setTime(23,59,59);

        return [
            'start' => $start,
            'end'   => $end
        ];
    }

    private static function transformPetPieData($p) {
        if($p->nome === "M") {
            $p->nome = "Macho";
            $p->color = "#3598DC";
        } else if($p->nome === "F") {
            $p->nome = "Fêmea";
            $p->color = "#E08283";
        } else {
            $p->nome = "Outros";
            $p->color = "#BFBFBF";
        }

        return $p;
    }
}
