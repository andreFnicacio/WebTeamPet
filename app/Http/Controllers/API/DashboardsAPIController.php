<?php

namespace App\Http\Controllers\API;

use App\Helpers\Utils;
use App\Http\Controllers\AppBaseController;
use App\Models\{DadosTemporais, Nps, PetsPlanos};
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Modules\Clinics\Entities\Clinicas;
use Modules\Guides\Entities\HistoricoUso;

class DashboardsAPIController extends AppBaseController
{

    // 'bar'
    // 'bullet'
    // 'line'
    // 'area'
    // 'column'
    // 'pie'
    // 'gauge'
    // 'stat'
    // 'list'
    // 'table'

    /**************************
     *          STAT          *
     **************************/

    public function statCredenciados(Request $request)
    {
        $dates = self::getDates($request);
        $query = Clinicas::where('ativo', 1)->count();

        $data = [
            'value' => $query
        ];
        return $data;
    }

    public function statVidasAtivas(Request $request)
    {
        $dates = self::getDates($request);
        $date = new Carbon($dates['start']);

        if($date->diffInDays(Carbon::now()) == 0) {
            return ['value' => $vidas = \App\Models\Pets::where('ativo','1')->count('id')];
        }
        
        $query = DadosTemporais::where('indicador', 'vidas_ativas')
                                ->where('data_referencia', $dates['start'])
                                ->first();

        $data = [
            'value' => isset($query, $query->valor_numerico) ? $query->valor_numerico : 0
        ];
        return $data;
    }

    public function statNovasVidas(Request $request)
    {
        $dates = self::getDates($request);
        $query = PetsPlanos::where('status', PetsPlanos::STATUS_PRIMEIRO_PLANO)
                        ->whereBetween('data_inicio_contrato', $dates)
                        ->groupBy('id_pet')
                        ->get()
                        ->count();

        $data = [
            'value' => $query ?: 0
        ];
        return $data;
    }

    public function statCancelamentos(Request $request)
    {
        $dates = self::getDates($request);
        $query = PetsPlanos::whereBetween('data_encerramento_contrato', $dates)
                            ->groupBy('id_pet')
                            ->count();

        $data = [
            'value' => $query ?: 0
        ];
        return $data;
    }

    public function statQtdGlosas(Request $request)
    {
        $dates = self::getDates($request);
        $query = HistoricoUso::whereIn('glosado', ['1','3'])
                            ->where('status', HistoricoUso::STATUS_LIBERADO)
                            ->where(function($query) use ($dates) {
                                $query->where(function($query) use ($dates) {
                                    $query->where('tipo_atendimento', "!=", HistoricoUso::TIPO_ENCAMINHAMENTO)
                                        ->whereBetween('historico_uso.created_at', $dates);
                                });
                                $query->orWhere(function($query) use ($dates) {
                                    $query->where('tipo_atendimento', HistoricoUso::TIPO_ENCAMINHAMENTO)
                                        ->whereBetween('historico_uso.realizado_em', $dates);
                                });
                            })
                            ->count();

        $data = [
            'value' => $query ?: 0
        ];
        return $data;
    }

    public function statValorGlosas(Request $request)
    {
        $dates = self::getDates($request);
        $query = HistoricoUso::whereIn('glosado', ['1','3'])
                                ->where('status', HistoricoUso::STATUS_LIBERADO)
                                ->where(function($query) use ($dates) {
                                    $query->where(function($query) use ($dates) {
                                        $query->where('tipo_atendimento', "!=", HistoricoUso::TIPO_ENCAMINHAMENTO)
                                            ->whereBetween('historico_uso.created_at', $dates);
                                    });
                                    $query->orWhere(function($query) use ($dates) {
                                        $query->where('tipo_atendimento', HistoricoUso::TIPO_ENCAMINHAMENTO)
                                            ->whereBetween('historico_uso.realizado_em', $dates);
                                    });
                                })
                            ->sum('valor_momento');
        $data = [
            'prefix' => '',
            'value' => $query ? Utils::money($query) : 0
        ];
        return $data;
    }

    public function statUpgrades(Request $request)
    {
        $dates = self::getDates($request);

        $query = PetsPlanos::whereBetween('data_inicio_contrato', $dates)
        ->where('status', PetsPlanos::STATUS_UPGRADE)
        ->count();

        $data = [
            'value' => $query ?: 0
        ];
        return $data;
    }

    /**************************
     *          LINE          *
     **************************/

    public function lineVidasAtivas(Request $request)
    {
        $dates = self::getDates($request);
        $query = DadosTemporais::where('indicador', 'vidas_ativas')
                                ->whereBetween('data_referencia', $dates);

        $data = $query->get()->map(function ($q) {
            return [
                'label' => $q->data_referencia->format('d/m/Y'),
                'value' => $q->valor_numerico
            ];
        });
        return $data;
    }

    public function lineNovasVidas(Request $request)
    {
        $dates = self::getDates($request);
        $period = CarbonPeriod::create($dates['start'], $dates['end']);

        foreach ($period as $date) {
            $q = PetsPlanos::where('status', PetsPlanos::STATUS_PRIMEIRO_PLANO)
                            ->where('data_inicio_contrato', $date->format('Y-m-d'))
                            ->groupBy('id_pet')
                            ->get();
            $data[] = [
                'label' => $date->format('d/m/Y'),
                'value' => $q->count()
            ];
        }
        return $data;
    }

    public function lineCancelamentos(Request $request)
    {
        $dates = self::getDates($request);
        $period = CarbonPeriod::create($dates['start'], $dates['end']);

        foreach ($period as $date) {
            $q = PetsPlanos::where('pets_planos.data_encerramento_contrato', $date->format('Y-m-d'))
                            ->where('pets.ativo', 0)
                            ->join('pets', 'pets.id', '=', 'pets_planos.id_pet')
                            ->get();

            $data[] = [
                'label' => $date->format('d/m/Y'),
                'value' => $q->count()
            ];
        }

        return $data;
    }
 
    public function lineSinistralidadePlanos(Request $request)
    {
        $dates = self::getDates($request);
        $query = HistoricoUso::selectRaw('planos.id, planos.nome_plano, SUM(historico_uso.valor_momento) as valor')
            ->join('planos', 'historico_uso.id_plano', '=', 'planos.id')
            ->where(function ($query) use ($dates) {
                $query->where(function ($query) use ($dates) {
                    $query->where('historico_uso.tipo_atendimento', "!=", HistoricoUso::TIPO_ENCAMINHAMENTO)
                        ->whereBetween('historico_uso.created_at', $dates);
                });
                $query->orWhere(function ($query) use ($dates) {
                    $query->where('historico_uso.tipo_atendimento', HistoricoUso::TIPO_ENCAMINHAMENTO)
                        ->whereBetween('historico_uso.realizado_em', $dates);
                });
            })
            ->where('status', HistoricoUso::STATUS_LIBERADO)
            ->groupBy('id_plano')
            ->orderByRaw('SUM(id_plano) DESC')
            ->limit(10);
        $data = $query->get()->map(function ($q) {
            return [
                'label' => $q->id.'-'.$q->nome_plano,
                'prefix' => 'R$',
                'value' => round($q->valor,2)
            ];
        });
        return $data;
    }

    public function lineSinistralidadeCredenciados(Request $request)
    {
        $dates = self::getDates($request);
        $query = HistoricoUso::where(function ($query) use ($dates) {
            $query->where(function ($query) use ($dates) {
                $query->where('historico_uso.tipo_atendimento', "!=", HistoricoUso::TIPO_ENCAMINHAMENTO)
                    ->whereBetween('historico_uso.created_at', $dates);
            });
            $query->orWhere(function ($query) use ($dates) {
                $query->where('historico_uso.tipo_atendimento', HistoricoUso::TIPO_ENCAMINHAMENTO)
                    ->whereBetween('historico_uso.realizado_em', $dates);
            });
        })
        ->where('status', HistoricoUso::STATUS_LIBERADO);
        $total = $query->sum('valor_momento');
        if($total == 0) {
            return [];
        }

        $query = $query->groupBy('historico_uso.id_clinica')
            ->selectRaw('SUM(historico_uso.valor_momento) as valor, c.nome_clinica as nome')
            ->join('clinicas as c', 'historico_uso.id_clinica', '=', 'c.id')
            ->limit(10)
            ->orderByRaw('SUM(historico_uso.valor_momento) DESC');
        $data = $query->get()->map(function ($q) use ($total) {
            return [
                'label' => $q->nome,
                'suffix' => '%',
                'value' => round(($q['valor'] * 100) / $total,2)
            ];
        });
        return $data;
    }

    public function lineSinistralidadeProcedimentos(Request $request)
    {
        $dates = self::getDates($request);
        $query = HistoricoUso::selectRaw('procedimentos.nome_procedimento, SUM(historico_uso.valor_momento) as valor')
            ->join('procedimentos', 'historico_uso.id_procedimento', '=', 'procedimentos.id')
            ->where(function ($query) use ($dates) {
                $query->where(function ($query) use ($dates) {
                    $query->where('historico_uso.tipo_atendimento', "!=", HistoricoUso::TIPO_ENCAMINHAMENTO)
                        ->whereBetween('historico_uso.created_at', $dates);
                });
                $query->orWhere(function ($query) use ($dates) {
                    $query->where('historico_uso.tipo_atendimento', HistoricoUso::TIPO_ENCAMINHAMENTO)
                        ->whereBetween('historico_uso.realizado_em', $dates);
                });
            })
            ->where('status', HistoricoUso::STATUS_LIBERADO)
            ->groupBy('id_procedimento')
            ->orderByRaw('SUM(valor_momento) DESC')
            ->limit(10);

        $data = $query->get()->map(function ($q) {
            return [
                'label' => $q->nome_procedimento,
                'prefix' => 'R$',
                'value' => round($q->valor,2)
            ];
        });
        return $data;
    }

    /*************************
     *         GAUGE         *
     *************************/

    public function gaugeNps(Request $request)
    {
        $dates = self::getDates($request);
        $query = Nps::getNpsGlobal();

        $data = [
            'value' => $query['nps']
        ];
        return $data;
    }


    /*************************
     *      SUPERLOGICA      *
     *************************/

    public function superlogicaMetricas(Request $request)
    {
        $dates = self::getDates($request);
        $qtdMeses = $dates['start']->diffInMonths($dates['end']);
        $qtdMeses = $qtdMeses == "0" ? 1 : $qtdMeses;

        $curl = new \App\Helpers\API\Superlogica\Curl();
        $url = "https://api.superlogica.net/v2/financeiro/metricas?quantidadeMeses=".$qtdMeses."&dtFim=".$dates['end']->format('m/d/Y')."&PLANOS&PRODUTOS&GRADE_PLANO&GRUPO";

        $curl->getDefaults($url);
        $response = $curl->execute();
        $curl->close();

        $data = [
            'value' => $response ?: 0
        ];
        return $data;
    }

    public function superlogicaLtv(Request $request)
    {
        $dates = self::getDates($request);
        $qtdMeses = $dates['start']->diffInMonths($dates['end']);
        $qtdMeses = $qtdMeses == "0" ? 1 : $qtdMeses;

        $curl = new \App\Helpers\API\Superlogica\Curl();
        $url = "https://api.superlogica.net/v2/financeiro/metricas?quantidadeMeses=".$qtdMeses."&dtFim=".$dates['end']->format('m/d/Y')."&PLANOS&PRODUTOS&GRADE_PLANO&GRUPO";

        $curl->getDefaults($url);
        $response = $curl->execute();
        $curl->close();

        $data = [
            'value' => isset($response, $response[0]->ltv) ? Utils::money($response[0]->ltv) : '0,00'
        ];
        return $data;
    }

    public function superlogicaChurn(Request $request)
    {
        $dates = self::getDates($request);
        $qtdMeses = $dates['start']->diffInMonths($dates['end']);
        $qtdMeses = $qtdMeses == "0" ? 1 : $qtdMeses;

        $curl = new \App\Helpers\API\Superlogica\Curl();
        $url = "https://api.superlogica.net/v2/financeiro/metricas?quantidadeMeses=".$qtdMeses."&dtFim=".$dates['end']->format('m/d/Y')."&PLANOS&PRODUTOS&GRADE_PLANO&GRUPO";

        $curl->getDefaults($url);
        $response = $curl->execute();
        $curl->close();

        $data = [
            'suffix' => '%',
            'value' => floatval($response[0]->{'churn mrr'})
        ];
        return $data;
    }

    public function superlogicaChurnValor(Request $request)
    {
        $dates = self::getDates($request);
        $qtdMeses = $dates['start']->diffInMonths($dates['end']);
        $qtdMeses = $qtdMeses == "0" ? 1 : $qtdMeses;

        $curl = new \App\Helpers\API\Superlogica\Curl();
        $url = "https://api.superlogica.net/v2/financeiro/metricas?quantidadeMeses=".$qtdMeses."&dtFim=".$dates['end']->format('m/d/Y')."&PLANOS&PRODUTOS&GRADE_PLANO&GRUPO";

        $curl->getDefaults($url);
        $response = $curl->execute();
        $curl->close();

        $data = [
            // 'prefix' => 'R$',
            'value' => isset($response[0], $response[0]->desativadas) ? Utils::money($response[0]->desativadas) : 'R$ 0,00'
        ];
        return $data;
    }

    public function superlogicaTicket(Request $request)
    {
        $dates = self::getDates($request);
        $qtdMeses = $dates['start']->diffInMonths($dates['end']);
        $qtdMeses = $qtdMeses == "0" ? 1 : $qtdMeses;

        $curl = new \App\Helpers\API\Superlogica\Curl();
        $url = "https://api.superlogica.net/v2/financeiro/metricas?quantidadeMeses=".$qtdMeses."&dtFim=".$dates['end']->format('m/d/Y')."&PLANOS&PRODUTOS&GRADE_PLANO&GRUPO";

        $curl->getDefaults($url);
        $response = $curl->execute();
        $curl->close();

        $data = [
            // 'prefix' => 'R$',
            'value' => isset($response[0]->ticket) ? Utils::money($response[0]->ticket) : 'R$ 0,00'
        ];
        return $data;
    }

    public function superlogicaInadimplencia(Request $request)
    {
        $dates = self::getDates($request);
        $curl = new \App\Helpers\API\Superlogica\Curl();
        $url = 'https://api.superlogica.net/v2/financeiro/clientes/inadimplencia?posicaoEm='.$dates['start']->format('m/d/Y');

        $curl->getDefaults($url);
        $response = $curl->execute();
        $curl->close();

        $totalInadimplencia = 0;
        foreach ($response as $clientes) {
            foreach ($clientes->recebimento as $cobranca) {
                foreach ($cobranca->encargos as $encargo) {
                    $totalInadimplencia += $encargo->valorcorrigido;
                }
            }
        }

        $data = [
            // 'prefix' => 'R$',
            'value' => isset($totalInadimplencia) ? Utils::money($totalInadimplencia) : $totalInadimplencia
        ];
        return $data;
    }

    /*************************
     *       MULTILINE       *
     *************************/

    public function multilineVidasCancelamentos(Request $request)
    {
        $dates = self::getDates($request);
        $period = CarbonPeriod::create($dates['start'], $dates['end']);

        foreach ($period as $date) {
            $queryVidas = PetsPlanos::where('status', PetsPlanos::STATUS_PRIMEIRO_PLANO)
                                    ->where('data_inicio_contrato', $date->format('Y-m-d'))
                                    ->groupBy('id_pet')
                                    ->get();

            $queryCancelamentos = PetsPlanos::where('pets_planos.data_encerramento_contrato', $date->format('Y-m-d'))
                                            ->where('pets.ativo', 0)
                                            ->join('pets', 'pets.id', '=', 'pets_planos.id_pet')
                                            ->get();

            $datavalues[] = [
                'data' => $date->format('d/m/Y'),
                'vidas' => $queryVidas->count(),
                'cancelamentos' => $queryCancelamentos->count(),
            ];
        }

        $data = [
            'category' => 'data',
            'lines' => ['vidas', 'cancelamentos'],
            'titles' => ['Novas Vidas', 'Cancelamentos'],
            'values' => $datavalues
        ];
        return $data;
    }
}
