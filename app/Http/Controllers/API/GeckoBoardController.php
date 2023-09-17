<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Cancelamento;
use App\Models\Clientes;
use App\Models\Pagamentos;
use App\Models\Pets;
use App\Models\PetsPlanos;
use App\Models\Notas;
use App\Models\Planos;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use \stdClass;

class GeckoBoardController extends Controller
{
    public function vidasAtivas(string $tipo, int $totalMeses)
    {
        if ($tipo == 'serial') {
            $date = Carbon::now()->subMonths(12);
            $response = [];

            while($totalMeses > 0) {
                $date->addMonth();
                $data_vidas = [];
                $data_vidas['label'] = $date->format('M/y');
                $registro = \App\Models\DadosTemporais::where('indicador', 'vidas_ativas')->where('data_referencia', $date->copy()->endOfMonth()->format('Y-m-d'))->first();
                $data_vidas['valor'] = $registro ? $registro->valor_numerico : 0;

                if($date->isToday()) {
                    $data_vidas['valor'] = \App\Models\Pets::where('ativo', 1)->count('id');
                }

                $response[] = $data_vidas;

                $totalMeses--;
            }

            $data['x_axis']['labels'] = [];
            $series = new stdClass();
            foreach ($response as $resp) {
                $data['x_axis']['labels'][] = $resp['label'];
                $series->data[] = $resp['valor'];
            }
            $series->name = "Vidas ativas (últimos 30 dias)";
            $data['series'] = [$series];
        } else {
            $vidas = \App\Models\Pets::where('ativo','1')->count('id');

            $data["item"][] = [
                "value" => $vidas,
                "text" => "Vidas Ativas"
            ];
        }

        return $data;
    }

    public function novasVidasSite()
    {
        $hoje = Carbon::now();
        $primeiroDiaDoMesAtual = Carbon::now()->startOfMonth();

        $response = [];
        for($i = 0; $i <= $hoje->diffInDays($primeiroDiaDoMesAtual); $i++) {
            $data_vidas = [];
            $date = $primeiroDiaDoMesAtual->copy()->addDays($i);
            $data_vidas['label'] = $date->format('d/m/Y');
            $qtdNovasVidas = PetsPlanos::where('data_inicio_contrato', $date->format('Y-m-d'))
                ->where('transicao', 'Nova compra do e-commerce')
                ->count('id_pet');

            $data_vidas['valor'] = $qtdNovasVidas ? $qtdNovasVidas : 0;

            $response[] = $data_vidas;
        }

        $data['x_axis']['labels'] = [];
        $series = new stdClass();

        foreach ($response as $resp) {
            $data['x_axis']['labels'][] = $resp['label'];
            $series->data[] = $resp['valor'];
        }
        $series->name = "Novas Vidas (mês atual)";
        $data['series'] = [$series];

        return $data;
    }

    public function novasVidasInsideSales()
    {
        $hoje = Carbon::now();
        $primeiroDiaDoMesAtual = Carbon::now()->startOfMonth();

        $response = [];
        for($i = 0; $i <= $hoje->diffInDays($primeiroDiaDoMesAtual); $i++) {
            $data_vidas = [];
            $date = $primeiroDiaDoMesAtual->copy()->addDays($i);
            $data_vidas['label'] = $date->format('d/m/Y');
            $qtdNovasVidas = PetsPlanos::where('data_inicio_contrato', $date->format('Y-m-d'))
                ->where('transicao', 'Nova compra feita diretamente com o CX ou Inside Sales')
                ->count('id_pet');

            $data_vidas['valor'] = $qtdNovasVidas ? $qtdNovasVidas : 0;

            $response[] = $data_vidas;
        }

        $data['x_axis']['labels'] = [];
        $series = new stdClass();

        foreach ($response as $resp) {
            $data['x_axis']['labels'][] = $resp['label'];
            $series->data[] = $resp['valor'];
        }
        $series->name = "Novas Vidas (mês atual)";
        $data['series'] = [$series];

        return $data;
    }

    public function novasVidas(string $tipo, string $plan = null)
    {
        if ($tipo == 'serial') {
            $hoje = Carbon::now();
            $primeiroDiaDoMesAtual = Carbon::now()->startOfMonth();

            $response = [];
            for($i = 0; $i <= $hoje->diffInDays($primeiroDiaDoMesAtual); $i++) {
                $data_vidas = [];
                $date = $primeiroDiaDoMesAtual->copy()->addDays($i);
                $data_vidas['label'] = $date->format('d/m/Y');
                $qtdNovasVidas = PetsPlanos::where('data_inicio_contrato', $date->format('Y-m-d'))
                    ->where('transicao', 'LIKE', '%nova compra%')
                    ->where('id_plano', $plan)
                    ->count('id_pet');

                $data_vidas['valor'] = ($qtdNovasVidas) ?: 0;

                $response[] = $data_vidas;
            }

            $data['x_axis']['labels'] = [];
            $series = new stdClass();
            
            foreach ($response as $resp) {
                $data['x_axis']['labels'][] = $resp['label'];
                $series->data[] = $resp['valor'];
            }
            $series->name = "Novas Vidas (mês atual)";
            $data['series'] = [$series];
        } else {
            $inicio = Carbon::now()->startOfMonth();
            $fim = Carbon::now();

            $query = PetsPlanos::whereBetween('data_inicio_contrato', [$inicio, $fim])->where('transicao', 'LIKE', '%nova compra%')->count('id_pet');

            $data["item"][] = [
                'value' => $query ?: 0,
                "text" => "Novas Vidas"
            ];
        }
        return $data;
    }

    public function novasVidasAmount()
    {
        $hoje = Carbon::now();
        $primeiroDiaDoMesAtual = Carbon::now()->startOfMonth();

        $response = [];
        for($i = 0; $i <= $hoje->diffInDays($primeiroDiaDoMesAtual); $i++) {
            $data_vidas = [];
            $date = $primeiroDiaDoMesAtual->copy()->addDays($i);
            $data_vidas['label'] = $date->format('d/m/Y');
            $qtdNovasVidas = PetsPlanos::where('data_inicio_contrato', $date->format('Y-m-d'))
                ->where('transicao', 'LIKE', '%nova compra%')
                ->get();
            $countVidasNovas = 0;
            $valueVidasNovas = 0;
            foreach ($qtdNovasVidas as $vidas){
                $valueVidasNovas += $vidas['valor_momento'];
                $countVidasNovas++;
            }

            $data_vidas['valor'] = $valueVidasNovas;

            $response[] = $data_vidas;
        }

        $data['x_axis']['labels'] = [];
        $series = new stdClass();

        foreach ($response as $resp) {
            $data['x_axis']['labels'][] = $resp['label'];
            $series->data[] = $resp['valor'];
        }
        $series->name = "Novas Vidas (mês atual)";
        $data['series'] = [$series];

        return $data;
    }

    public function cancelamentos(string $tipo)
    {
        if ($tipo == 'serial') {
            $hoje = Carbon::now();
            $primeiroDiaDoMesAtual = Carbon::now()->startOfMonth();

            $response = [];
            for($i = 0; $i <= $hoje->diffInDays($primeiroDiaDoMesAtual); $i++) {
                $cancelamentos = [];
                $dataCancelamento = $primeiroDiaDoMesAtual->copy()->addDays($i);

                $qtdCancelamentos = DB::table('cancelamentos')
                                        ->where('data_cancelamento', $dataCancelamento->format('Y-m-d'))
                                        ->count();

                $response[] = [
                    'label' => $dataCancelamento->format('d/m/Y'),
                    'valor' => $qtdCancelamentos ? $qtdCancelamentos : 0
                ];
            }

            $data['x_axis']['labels'] = [];
            $series = new stdClass();

            foreach ($response as $resp) {
                $data['x_axis']['labels'][] = $resp['label'];
                $series->data[] = $resp['valor'];
            }

            $series->name = "Cancelamentos (mês atual)";
            $data['series'] = [$series];
        } else {
            $inicio = Carbon::now()->startOfMonth();
            $fim = Carbon::now();

            $query = Cancelamento::whereBetween('data_cancelamento', [$inicio, $fim])->count();

            $data["item"][] = [
                'value' => $query ?: 0,
                "text" => "cancelamentos"
            ];
        }
        return $data;
    }

    public function activeSubscriptionsCurrentMonth()
    {
        $hoje = Carbon::now();
        $primeiroDiaDoMesAtual = Carbon::now()->startOfMonth();

        $response = [];
        for($i = 0; $i <= $hoje->diffInDays($primeiroDiaDoMesAtual); $i++) {
            $data_vidas = [];
            $date = $primeiroDiaDoMesAtual->copy()->addDays($i);
            $beginOfDay = $date->copy()->startOfDay();
            $endOfDay = $date->copy()->endOfDay();

            $data_vidas['label'] = $date->format('d/m/Y');

            $vidas = Pets::join('pets_planos as pp', function($query) {
                $query->on('pets.id','=','pp.id_pet')
                    ->whereRaw('pp.id IN (select MAX(pp2.id) from pets_planos as pp2 join pets as p2 on p2.id = pp2.id_pet group by p2.id)');
            })
                ->join('planos', 'planos.id', '=', 'pp.id_plano')
                ->where('pets.ativo', 1)
                ->whereIn('planos.id', [74, 75, 76, 79])
                ->whereNull('pp.data_encerramento_contrato')
                ->whereBetween('pp.created_at', [$beginOfDay, $endOfDay])
                ->count('pets.id');

            $data_vidas['valor'] = $vidas;

            $response[] = $data_vidas;
        }

        $data['x_axis']['labels'] = [];
        $series = new stdClass();

        foreach ($response as $resp) {
            $data['x_axis']['labels'][] = $resp['label'];
            $series->data[] = $resp['valor'];
        }
        $series->name = "Novas Vidas (mês atual)";
        $data['series'] = [$series];

        return $data;
    }

    public function subscriptionsByPlan($plan, $isPaid, $periodicity, $active)
    {
        $plans = $plan;

        if (strstr($plan, ',')) {
            $plans = explode(',', $plan);
        }

        $date = Carbon::now();
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();
        $allPeriodicity = Pets::REGIMES_ANUAIS;
        $allPeriodicity[] = Pets::REGIME_MENSAL;

        $vidas = Pets::join('pets_planos as pp', function($query) {
            $query->on('pets.id','=','pp.id_pet')
                ->whereRaw('pp.id IN (select MAX(pp2.id) from pets_planos as pp2 join pets as p2 on p2.id = pp2.id_pet group by p2.id)');
        })
            ->join('planos', 'planos.id', '=', 'pp.id_plano');

            if (is_array($plans)) {
                $vidas->getQuery()->whereIn('planos.id', $plans);
            } else {
                $vidas->getQuery()->where('planos.id', $plans);
            }

            if ($active) {
                $vidas->getQuery()->where('pets.ativo', 1);
            } else {
                $vidas->getQuery()->where('pets.ativo', 0);
            }

            if ($isPaid != 'todos') {
                $vidas->getQuery()->where('pp.valor_momento', ($isPaid) ? '>' : '=', 0);
            }

            if ($periodicity == 'mensal') {
                $vidas->getQuery()->where('pets.regime', strtoupper($periodicity));
            } else if ($periodicity == 'anual') {
                $vidas->getQuery()->whereIn('pets.regime', Pets::REGIMES_ANUAIS);
            } else {
                $vidas->getQuery()->whereIn('pets.regime', $allPeriodicity);
            }

            $vidas->getQuery()->whereNull('pp.data_encerramento_contrato')
                ->whereBetween('pp.created_at', [$startOfMonth, $endOfMonth]);

        $data["item"][] = [
            "value" => $vidas->count('pets.id'),
            "text" => "New subscriptions for plan " . $plan
        ];

        return $data;
    }

    public function subscriptionsAmount($periodicity)
    {
        $date = Carbon::now();
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();
        $allPeriodicity = Pets::REGIMES_ANUAIS;
        $allPeriodicity[] = Pets::REGIME_MENSAL;

        $vidas = Pets::join('pets_planos as pp', function($query) {
            $query->on('pets.id','=','pp.id_pet')
                ->whereRaw('pp.id IN (select MAX(pp2.id) from pets_planos as pp2 join pets as p2 on p2.id = pp2.id_pet group by p2.id)');
        })
            ->join('planos', 'planos.id', '=', 'pp.id_plano')
            ->whereIn('planos.id', [74, 75, 76, 79])
            ->where('pets.ativo', 1);

        if ($periodicity == 'mensal') {
            $vidas->getQuery()->where('pets.regime', strtoupper($periodicity));
        } else if ($periodicity == 'anual') {
            $vidas->getQuery()->whereIn('pets.regime', Pets::REGIMES_ANUAIS);
        } else {
            $vidas->getQuery()->whereIn('pets.regime', $allPeriodicity);
        }

        $vidas->getQuery()->whereNull('pp.data_encerramento_contrato')
            ->whereBetween('pp.created_at', [$startOfMonth, $endOfMonth]);

        $data["item"][] = [
            "value" => $vidas->sum('pp.valor_momento'),
            "text" => "MRR"
        ];

        return $data;
    }

    public function receitaTotal(string $tipo, int $totalMeses)
    {
        if ($tipo == 'serial') {
            $date = Carbon::now()->subMonths($totalMeses);
            $response = [];

            while($totalMeses > 0) {
                $date->addMonth();

                $startOfMonth = $date->copy()->startOfMonth();
                $endOfMonth = $date->copy()->endOfMonth();

                $pagamentos = Pagamentos::join('cobrancas', 'cobrancas.id', '=', 'pagamentos.id_cobranca')
                    ->select('pagamentos.valor_pago')
                    ->where('cobrancas.competencia', $date->format('Y-m'))
                    ->where('pagamentos.complemento', 'NOT LIKE', '%VALOR RECEBIDO%')
                    ->groupBy('pagamentos.complemento')
                    ->get();

                $pagamentosPicpay = Notas::select('corpo')
                    ->where('notas.corpo', 'LIKE', '%Pagamento de coparticipação da guia%PICPAY%R$%')
                    ->whereNull('deleted_at')
                    ->whereBetween('notas.created_at', [$startOfMonth, $endOfMonth])
                    ->get();
                    
                $valoresPicpay = $pagamentosPicpay->map(function ($pagamentoPicpay) {
                    $corpo = explode('R$', $pagamentoPicpay->corpo);

                    return floatval(trim($corpo[1]));
                });
                
                $totalPagamentos = $pagamentos->sum('valor_pago');
                $totalPicpay = $valoresPicpay->sum();

                $receitaTotal = $totalPagamentos + $totalPicpay;

                $response[] = [
                    'label' => $date->format('M/y'),
                    'valor' => $receitaTotal
                ];

                $totalMeses--;
            }

            $data['x_axis']['labels'] = [];
            $data['y_axis']['format'] = 'currency';
            $data['y_axis']['unit'] = 'BRL';
            $series = new stdClass();
            foreach ($response as $resp) {
                $data['x_axis']['labels'][] = $resp['label'];
                $series->data[] = $resp['valor'];
            }
            $data['series'] = [$series];

            return $data;
        }

        $today = Carbon::now();
        $startOfMonth = $today->copy()->startOfMonth();
        $endOfMonth = $today->copy()->endOfMonth();

        $pagamentos = Pagamentos::join('cobrancas', 'cobrancas.id', '=', 'pagamentos.id_cobranca')
            ->select('pagamentos.valor_pago')
            ->where('cobrancas.competencia', $today->format('Y-m'))
            ->where('pagamentos.complemento', 'NOT LIKE', '%VALOR RECEBIDO%')
            ->groupBy('pagamentos.complemento')
            ->get();

            $pagamentosPicpay = Notas::select('corpo')
            ->where('notas.corpo', 'LIKE', '%Pagamento de coparticipação da guia%PICPAY%R$%')
            ->whereNull('deleted_at')
            ->whereBetween('notas.created_at', [$startOfMonth, $endOfMonth])
            ->get();

            $valoresPicpay = $pagamentosPicpay->map(function ($pagamentoPicpay) {
                $corpo = explode('R$', $pagamentoPicpay->corpo);

                return floatval(trim($corpo[1]));
            });
            
            $totalPagamentos = $pagamentos->sum('valor_pago');
            $totalPicpay = $valoresPicpay->sum();

            $receitaTotal = $totalPagamentos + $totalPicpay;

        $data["item"][] = [
            'value' => $receitaTotal,
            "text" => "Receita | mês"
        ];
        
        return $data;
    }

    public function receitaCoparticipacao(string $tipo, int $totalMeses)
    {
        if ($tipo == 'serial') {
            $date = Carbon::now()->subMonths($totalMeses);
            $response = [];

            while($totalMeses > 0) {
                $date->addMonth();

                $startOfMonth = $date->copy()->startOfMonth();
                $endOfMonth = $date->copy()->endOfMonth();

                $pagamentos = Pagamentos::join('cobrancas', 'cobrancas.id', '=', 'pagamentos.id_cobranca')
                ->select('pagamentos.valor_pago')
                ->where('cobrancas.competencia', $date->format('Y-m'))
                ->where('pagamentos.complemento', 'LIKE', '%guia%')
                ->groupBy('pagamentos.id_cobranca')
                ->get();

                $pagamentosPicpay = Notas::select('corpo')
                    ->where('notas.corpo', 'LIKE', '%Pagamento de coparticipação da guia%PICPAY%R$%')
                    ->whereNull('deleted_at')
                    ->whereBetween('notas.created_at', [$startOfMonth, $endOfMonth])
                    ->get();
                    
                $valoresPicpay = $pagamentosPicpay->map(function ($pagamentoPicpay) {
                    $corpo = explode('R$', $pagamentoPicpay->corpo);

                    return floatval(trim($corpo[1]));
                });

                $totalPagamentos = $pagamentos->sum('valor_pago');
                $totalPicpay = $valoresPicpay->sum();

                $coparticipacaoTotal = $totalPagamentos + $totalPicpay;

                $response[] = [
                    'label' => $date->format('M/y'),
                    'valor' => $coparticipacaoTotal
                ];

                $totalMeses--;
            }

            $data['x_axis']['labels'] = [];
            $data['y_axis']['format'] = 'currency';
            $data['y_axis']['unit'] = 'BRL';
            $series = new stdClass();
            foreach ($response as $resp) {
                $data['x_axis']['labels'][] = $resp['label'];
                $series->data[] = $resp['valor'];
            }
            $data['series'] = [$series];

            return $data;
        }
        $today = Carbon::now();
        $startOfMonth = $today->copy()->startOfMonth();
        $endOfMonth = $today->copy()->endOfMonth();

        $pagamentos = Pagamentos::join('cobrancas', 'cobrancas.id', '=', 'pagamentos.id_cobranca')
            ->select('pagamentos.valor_pago')
            ->where('cobrancas.competencia', $today->format('Y-m'))
            ->where('pagamentos.complemento', 'LIKE', '%guia%')
            ->groupBy('pagamentos.id_cobranca')
            ->get();

        $pagamentosPicpay = Notas::select('corpo')
            ->where('notas.corpo', 'LIKE', '%Pagamento de coparticipação da guia%PICPAY%R$%')
            ->whereNull('deleted_at')
            ->whereBetween('notas.created_at', [$startOfMonth, $endOfMonth])
            ->get();
        
            $valoresPicpay = $pagamentosPicpay->map(function ($pagamentoPicpay) {
                $corpo = explode('R$', $pagamentoPicpay->corpo);

                return floatval(trim($corpo[1]));
            });

            $totalPagamentos = $pagamentos->sum('valor_pago');
            $totalPicpay = $valoresPicpay->sum();

            $coparticipacaoTotal = $totalPagamentos + $totalPicpay;

        $data["item"][] = [
            'value' => $coparticipacaoTotal,
            "text" => "Receita | mês"
        ];

        return $data;
    }

    public function receitaRecorrencia(string $tipo, int $totalMeses)
    {
        if ($tipo == 'serial') {
            $date = Carbon::now()->subMonths($totalMeses);
            $response = [];

            while($totalMeses > 0) {
                $date->addMonth();

                $receita = Pagamentos::join('cobrancas', 'cobrancas.id', '=', 'pagamentos.id_cobranca')
                    ->select('pagamentos.valor_pago')
                    ->where('cobrancas.competencia', $date->format('Y-m'))
                    ->where('pagamentos.complemento', 'LIKE', '%PLANO%')
                    ->groupBy('pagamentos.complemento')
                    ->get();

                    $receitaTotal = $receita->sum('valor_pago');

                $response[] = [
                    'label' => $date->format('M/y'),
                    'valor' => $receitaTotal
                ];

                $totalMeses--;
            }

            $data['x_axis']['labels'] = [];
            $data['y_axis']['format'] = 'currency';
            $data['y_axis']['unit'] = 'BRL';
            $series = new stdClass();
            foreach ($response as $resp) {
                $data['x_axis']['labels'][] = $resp['label'];
                $series->data[] = $resp['valor'];
            }
            $data['series'] = [$series];

            return $data;
        }

        $receita = Pagamentos::join('cobrancas', 'cobrancas.id', '=', 'pagamentos.id_cobranca')
            ->select('pagamentos.valor_pago')
            ->where('cobrancas.competencia', Carbon::now()->format('Y-m'))
            ->where('pagamentos.complemento', 'LIKE', '%PLANO%')
            ->groupBy('pagamentos.complemento')
            ->get();

            $receitaTotal = $receita->sum('valor_pago');

        $data["item"][] = [
            'value' => $receitaTotal,
            "text" => "Receita | mês"
        ];

        return $data;
    }

    public function receitaRecorrenciaParticipativos(string $tipo, int $totalMeses)
    {
        if ($tipo == 'serial') {
            $date = Carbon::now()->subMonths($totalMeses);
            $response = [];

            while($totalMeses > 0) {
                $date->addMonth();

                $receita = Pagamentos::join('cobrancas', 'cobrancas.id', '=', 'pagamentos.id_cobranca')
                    ->join('clientes', 'clientes.id', '=', 'cobrancas.id_cliente')
                    ->join('pets', 'pets.id_cliente', '=', 'clientes.id')
                    ->join('pets_planos', 'pets_planos.id_pet', '=', 'pets.id')
                    ->join('planos', 'planos.id', '=', 'pets_planos.id_plano')
                    ->select('pagamentos.valor_pago')
                    ->where('cobrancas.competencia', $date->format('Y-m'))
                    ->where('pagamentos.complemento', 'LIKE', '%PLANO%')
                    ->where('planos.participativo', 1)
                    ->groupBy('pagamentos.complemento')
                    ->get();

                    $receitaTotal = $receita->sum('valor_pago');

                $response[] = [
                    'label' => $date->format('M/y'),
                    'valor' => $receitaTotal
                ];

                $totalMeses--;
            }

            $data['x_axis']['labels'] = [];
            $data['y_axis']['format'] = 'currency';
            $data['y_axis']['unit'] = 'BRL';
            $series = new stdClass();
            foreach ($response as $resp) {
                $data['x_axis']['labels'][] = $resp['label'];
                $series->data[] = $resp['valor'];
            }
            $data['series'] = [$series];

            return $data;
        }

        $receita = Pagamentos::join('cobrancas', 'cobrancas.id', '=', 'pagamentos.id_cobranca')
            ->join('clientes', 'clientes.id', '=', 'cobrancas.id_cliente')
            ->join('pets', 'pets.id_cliente', '=', 'clientes.id')
            ->join('pets_planos', 'pets_planos.id_pet', '=', 'pets.id')
            ->join('planos', 'planos.id', '=', 'pets_planos.id_plano')
            ->select('pagamentos.valor_pago')
            ->where('cobrancas.competencia', Carbon::now()->format('Y-m'))
            ->where('pagamentos.complemento', 'LIKE', '%PLANO%')
            ->where('planos.participativo', 1)
            ->groupBy('pagamentos.complemento')
            ->get();

            $receitaTotal = $receita->sum('valor_pago');

        $data["item"][] = [
            'value' => $receitaTotal,
            "text" => "Receita | mês"
        ];

        return $data;
    }

    public function receitaTotalParticipativos(string $tipo, int $totalMeses)
    {
        if ($tipo == 'serial') {
            $date = Carbon::now()->subMonths($totalMeses);
            $response = [];

            while($totalMeses > 0) {
                $date->addMonth();

                $startOfMonth = $date->copy()->startOfMonth();
                $endOfMonth = $date->copy()->endOfMonth();

                $pagamentos = Pagamentos::join('cobrancas', 'cobrancas.id', '=', 'pagamentos.id_cobranca')
                    ->join('clientes', 'clientes.id', '=', 'cobrancas.id_cliente')
                    ->join('pets', 'pets.id_cliente', '=', 'clientes.id')
                    ->join('pets_planos as pp', function($query) {
                        $query->on('pets.id','=','pp.id_pet')
                        ->whereRaw('pp.id IN (select MAX(pp2.id) from pets_planos as pp2 join pets as p2 on p2.id = pp2.id_pet group by p2.id)');
                    })
                    ->join('planos', 'planos.id', '=', 'pp.id_plano')
                    ->select('pagamentos.valor_pago')
                    ->where('cobrancas.competencia', $date->format('Y-m'))
                    ->where('pagamentos.complemento', 'NOT LIKE', '%VALOR RECEBIDO%')
                    ->where('planos.participativo', 1)
                    ->groupBy('pagamentos.complemento')
                    ->get();

                $pagamentosPicpay = Notas::select('corpo')
                    ->where('notas.corpo', 'LIKE', '%Pagamento de coparticipação da guia%PICPAY%R$%')
                    ->whereNull('deleted_at')
                    ->whereBetween('notas.created_at', [$startOfMonth, $endOfMonth])
                    ->get();
                    
                $valoresPicpay = $pagamentosPicpay->map(function ($pagamentoPicpay) {
                    $corpo = explode('R$', $pagamentoPicpay->corpo);

                    return floatval(trim($corpo[1]));
                });
                
                $totalPagamentos = $pagamentos->sum('valor_pago');
                $totalPicpay = $valoresPicpay->sum();

                $receitaTotal = $totalPagamentos + $totalPicpay;

                $response[] = [
                    'label' => $date->format('M/y'),
                    'valor' => $receitaTotal
                ];

                $totalMeses--;
            }

            $data['x_axis']['labels'] = [];
            $data['y_axis']['format'] = 'currency';
            $data['y_axis']['unit'] = 'BRL';
            $series = new stdClass();
            foreach ($response as $resp) {
                $data['x_axis']['labels'][] = $resp['label'];
                $series->data[] = $resp['valor'];
            }
            $data['series'] = [$series];

            return $data;
        }

        $today = Carbon::now();
        $startOfMonth = $today->copy()->startOfMonth();
        $endOfMonth = $today->copy()->endOfMonth();

        $pagamentos = Pagamentos::join('cobrancas', 'cobrancas.id', '=', 'pagamentos.id_cobranca')
            ->join('clientes', 'clientes.id', '=', 'cobrancas.id_cliente')
            ->join('pets', 'pets.id_cliente', '=', 'clientes.id')
            ->join('pets_planos', 'pets_planos.id_pet', '=', 'pets.id')
            ->join('planos', 'planos.id', '=', 'pets_planos.id_plano')
            ->select('pagamentos.valor_pago')
            ->where('cobrancas.competencia', $today->format('Y-m'))
            ->where('pagamentos.complemento', 'NOT LIKE', '%VALOR RECEBIDO%')
            ->where('planos.participativo', 1)
            ->groupBy('pagamentos.complemento')
            ->get();

            $pagamentosPicpay = Notas::select('corpo')
            ->where('notas.corpo', 'LIKE', '%Pagamento de coparticipação da guia%PICPAY%R$%')
            ->whereNull('deleted_at')
            ->whereBetween('notas.created_at', [$startOfMonth, $endOfMonth])
            ->get();

            $valoresPicpay = $pagamentosPicpay->map(function ($pagamentoPicpay) {
                $corpo = explode('R$', $pagamentoPicpay->corpo);

                return floatval(trim($corpo[1]));
            });
            
            $totalPagamentos = $pagamentos->sum('valor_pago');
            $totalPicpay = $valoresPicpay->sum();

            $receitaTotal = $totalPagamentos + $totalPicpay;

        $data["item"][] = [
            'value' => $receitaTotal,
            "text" => "Receita | mês"
        ];
        
        return $data;
    }

    public function cancelamentosParticipativos(string $tipo)
    {
        if ($tipo == 'serial') {
            $hoje = Carbon::now();
            $primeiroDiaDoMesAtual = Carbon::now()->startOfMonth();

            $response = [];
            for($i = 0; $i <= $hoje->diffInDays($primeiroDiaDoMesAtual); $i++) {
                $cancelamentos = [];
                $dataCancelamento = $primeiroDiaDoMesAtual->copy()->addDays($i);

                $qtdCancelamentos = DB::table('cancelamentos')
                                        ->join('pets', 'pets.id', '=', 'cancelamentos.id_pet')
                                        ->join('pets_planos as pp', function($query) {
                                            $query->on('pets.id','=','pp.id_pet')
                                            ->whereRaw('pp.id IN (select MAX(pp2.id) from pets_planos as pp2 join pets as p2 on p2.id = pp2.id_pet group by p2.id)');
                                            })
                                        ->join('planos', 'planos.id', '=', 'pp.id_plano')
                                        ->where('data_cancelamento', $dataCancelamento->format('Y-m-d'))
                                        ->where('planos.participativo', 1)
                                        ->count('cancelamentos.id');

                $response[] = [
                    'label' => $dataCancelamento->format('d/m/Y'),
                    'valor' => $qtdCancelamentos ? $qtdCancelamentos : 0
                ];
            }

            $data['x_axis']['labels'] = [];
            $series = new stdClass();

            foreach ($response as $resp) {
                $data['x_axis']['labels'][] = $resp['label'];
                $series->data[] = $resp['valor'];
            }

            $series->name = "Cancelamentos (mês atual)";
            $data['series'] = [$series];
        } else {
            $inicio = Carbon::now()->startOfMonth();
            $fim = Carbon::now();

            $query = DB::table('cancelamentos')
                ->join('pets', 'pets.id', '=', 'cancelamentos.id_pet')
                ->join('pets_planos as pp', function($query) {
                    $query->on('pets.id','=','pp.id_pet')
                        ->whereRaw('pp.id IN (select MAX(pp2.id) from pets_planos as pp2 join pets as p2 on p2.id = pp2.id_pet group by p2.id)');
                    })
                ->join('planos', 'planos.id', '=', 'pp.id_plano')
                ->whereBetween('data_cancelamento', [$inicio, $fim])
                ->where('planos.participativo', 1)
                ->count();

            $data["item"][] = [
                'value' => $query ?: 0,
                "text" => "cancelamentos"
            ];
        }
        return $data;
    }

    public function vidasAtivasParticipativos(string $tipo, int $totalMeses)
    {
        if ($tipo == 'serial') {
            $date = Carbon::now()->subMonths($totalMeses);
            $response = [];

            while($totalMeses > 0) {
                $date->addMonth();

                $endOfMonth = $date->copy()->endOfMonth();

                $dataVidas = [];
                $dataVidas['label'] = $date->format('M/y');

//                $vidasAtivas = PetsPlanos::join('pets', 'pets.id', '=', 'pets_planos.id_pet')
//                    ->join('planos', 'planos.id', '=', 'pets_planos.id_plano')
//                    ->where('pets_planos.created_at', '<=', $endOfMonth->format('Y-m-d'))
//                    ->where(function($query) use($endOfMonth){
//                        $query->where('pets_planos.data_encerramento_contrato', '>', $endOfMonth->format('Y-m-d'))
//                            ->orWhereNull('pets_planos.data_encerramento_contrato');
//                    })
//                    ->where('planos.participativo', 1)
//                    ->where('pets_planos.created_at', '>', '2020-09-01 00:00:00')
//                    ->whereNull('pets.deleted_at')
//                    ->whereNull('pets_planos.deleted_at')
//                    ->groupBy('pets_planos.id_pet')
//                ->get()->count();

                $vidasAtivas = DB::table('pets_planos')
                    ->join('pets', 'pets.id', '=', 'pets_planos.id_pet')
                    ->join('planos', 'planos.id', '=', 'pets_planos.id_plano')
                    ->where('pets_planos.created_at', '<=', $endOfMonth->format('Y-m-d'))
                    ->where(function($query) use($endOfMonth){
                        $query->where('pets_planos.data_encerramento_contrato', '>', $endOfMonth->format('Y-m-d'))
                            ->orWhereNull('pets_planos.data_encerramento_contrato');
                    })
                    ->where('planos.participativo', 1)
                    ->where('pets_planos.created_at', '>', '2020-09-01 00:00:00')
                    ->whereNull('pets.deleted_at')
                    ->whereNull('pets_planos.deleted_at')
                    ->groupBy(['pets.id'])
                ->get()->count();
        
                $dataVidas['valor'] = $vidasAtivas ? $vidasAtivas : 0;

                $response[] = $dataVidas;

                $totalMeses--;
            }

            $data['x_axis']['labels'] = [];
            $series = new stdClass();
            foreach ($response as $resp) {
                $data['x_axis']['labels'][] = $resp['label'];
                $series->data[] = $resp['valor'];
            }
            $series->name = "Vidas ativas Mensal";
            $data['series'] = [$series];
        } else {
            $vidas = Pets::join('pets_planos as pp', function($query) {
                $query->on('pets.id','=','pp.id_pet')
                ->whereRaw('pp.id IN (select MAX(pp2.id) from pets_planos as pp2 join pets as p2 on p2.id = pp2.id_pet group by p2.id)');
                })
                ->join('planos', 'planos.id', '=', 'pp.id_plano')
                ->where('planos.participativo', 1)
                ->whereNull('pp.data_encerramento_contrato')
                ->count('pets.id');

            $data["item"][] = [
                "value" => $vidas,
                "text" => "Vidas Ativas"
            ];
        }

        return $data;
    }

    public function vidasAtivasIntegral(int $totalMeses)
    {
        $date = Carbon::now()->subMonths($totalMeses);
        $response = [];

        while($totalMeses > 0) {
            $date->addMonth();

            $endOfMonth = $date->copy()->endOfMonth();

            $dataVidas = [];
            $dataVidas['label'] = $date->format('M/y');

            $vidasAtivas = PetsPlanos::join('pets', 'pets.id', '=', 'pets_planos.id_pet')
                ->join('planos', 'planos.id', '=', 'pets_planos.id_plano')
                ->where('pets_planos.created_at', '<=', $endOfMonth->format('Y-m-d'))
                ->where(function($query) use($endOfMonth){
                    $query->where('pets_planos.data_encerramento_contrato', '>', $endOfMonth->format('Y-m-d'))
                        ->orWhereNull('pets_planos.data_encerramento_contrato');
                })
                ->where('planos.participativo', 0)
                ->whereNull('pets.deleted_at')
                ->whereNull('pets_planos.deleted_at')
                ->groupBy('pets_planos.id_pet')
                ->get()->count();

            $dataVidas['valor'] = $vidasAtivas ? $vidasAtivas : 0;

            $response[] = $dataVidas;

            $totalMeses--;
        }

        $data['x_axis']['labels'] = [];
        $series = new stdClass();
        foreach ($response as $resp) {
            $data['x_axis']['labels'][] = $resp['label'];
            $series->data[] = $resp['valor'];
        }
        $series->name = "Vidas ativas Mensal";
        $data['series'] = [$series];

        return $data;
    }

    public function vidasAtivasTotal(int $totalMeses)
    {
        $date = Carbon::now()->subMonths($totalMeses);
        $response = [];

        while($totalMeses > 0) {
            $date->addMonth();

            $endOfMonth = $date->copy()->endOfMonth();

            $dataVidas = [];
            $dataVidas['label'] = $date->format('M/y');

            $vidasAtivas = PetsPlanos::join('pets', 'pets.id', '=', 'pets_planos.id_pet')
                ->join('planos', 'planos.id', '=', 'pets_planos.id_plano')
                ->where('pets_planos.created_at', '<=', $endOfMonth->format('Y-m-d'))
                ->where(function($query) use($endOfMonth){
                    $query->where('pets_planos.data_encerramento_contrato', '>', $endOfMonth->format('Y-m-d'))
                        ->orWhereNull('pets_planos.data_encerramento_contrato');
                })
                ->whereNull('pets.deleted_at')
                ->whereNull('pets_planos.deleted_at')
                ->groupBy('pets_planos.id_pet')
                ->get()->count();

            $dataVidas['valor'] = $vidasAtivas ? $vidasAtivas : 0;

            $response[] = $dataVidas;

            $totalMeses--;
        }

        $data['x_axis']['labels'] = [];
        $series = new stdClass();
        foreach ($response as $resp) {
            $data['x_axis']['labels'][] = $resp['label'];
            $series->data[] = $resp['valor'];
        }
        $series->name = "Vidas ativas Mensal";
        $data['series'] = [$series];

        return $data;
    }

    public function cancelamentosParticipativosMensal(string $tipo, int $totalMeses)
    {
        if ($tipo == 'serial') {
            $date = Carbon::now()->subMonths($totalMeses);
            $response = [];

            while($totalMeses > 0) {
                $date->addMonth();
                $startOfMonth = $date->copy()->startOfMonth();
                $endOfMonth = $date->copy()->endOfMonth();

                if($totalMeses == 1) {
                    $endOfMonth = Carbon::today();
                }

                $qtdCancelamentos = PetsPlanos::join('pets', 'pets_planos.id_pet', '=', 'pets.id')
                    ->join('planos', 'planos.id', '=', 'pets_planos.id_plano')
                    ->whereBetween('pets_planos.data_encerramento_contrato', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
                    ->where('planos.participativo', 1)
                    ->whereNull('pets.deleted_at')
                    ->whereNull('pets_planos.deleted_at')
                    ->where('pets_planos.created_at', '>', '2020-09-01 00:00:00')
                    ->groupBy('pets.id')
                    ->get()
                    ->count();

                $response[] = [
                    'valor' => $qtdCancelamentos ? $qtdCancelamentos : 0,
                    'label' => $date->format('m/Y')
                ];

                $totalMeses--;
            }

            $data['x_axis']['labels'] = [];
            $series = new stdClass();

            foreach ($response as $resp) {
                $data['x_axis']['labels'][] = $resp['label'];
                $series->data[] = $resp['valor'];
            }

            $series->name = "Cancelamentos Participativo";
            $data['series'] = [$series];
        } else {
            $inicio = Carbon::now()->startOfMonth();
            $fim = Carbon::now();

            $query = DB::table('cancelamentos')
                ->join('pets', 'pets.id', '=', 'cancelamentos.id_pet')
                ->join('pets_planos', 'pets_planos.id_pet', '=', 'pets.id')
                ->join('planos', 'planos.id', '=', 'pets_planos.id_plano')
                ->whereBetween('data_cancelamento', [$inicio, $fim])
                ->where('planos.participativo', 1)
                ->count();

            $data["item"][] = [
                'value' => $query ?: 0,
                "text" => "cancelamentos participativo"
            ];
        }
        return $data;
    }

    public function cancelamentosIntegralMensal(string $tipo, int $totalMeses)
    {
        if ($tipo == 'serial') {
            $date = Carbon::now()->subMonths($totalMeses);
            $response = [];

            while($totalMeses > 0) {
                $date->addMonth();
                $startOfMonth = $date->copy()->startOfMonth();
                $endOfMonth = $date->copy()->endOfMonth();

                if($totalMeses == 1) {
                    $endOfMonth = Carbon::today();
                }

                $qtdCancelamentos = DB::table('cancelamentos')
                    ->join('pets', 'pets.id', '=', 'cancelamentos.id_pet')
                    ->join('pets_planos', 'pets_planos.id_pet', '=', 'pets.id')
                    ->join('planos', 'planos.id', '=', 'pets_planos.id_plano')
                    ->whereBetween('data_cancelamento', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
                    ->where('planos.participativo', 0)
                    ->groupBy('cancelamentos.id_pet')
                    ->get()
                    ->count();

                $response[] = [
                    'valor' => $qtdCancelamentos ? $qtdCancelamentos : 0,
                    'label' => $date->format('m/Y')
                ];

                $totalMeses--;
            }

            $data['x_axis']['labels'] = [];
            $series = new stdClass();

            foreach ($response as $resp) {
                $data['x_axis']['labels'][] = $resp['label'];
                $series->data[] = $resp['valor'];
            }

            $series->name = "Cancelamentos (Integral)";
            $data['series'] = [$series];
        } else {
            $inicio = Carbon::now()->startOfMonth();
            $fim = Carbon::now();

            $query = DB::table('cancelamentos')
                ->join('pets', 'pets.id', '=', 'cancelamentos.id_pet')
                ->join('pets_planos', 'pets_planos.id_pet', '=', 'pets.id')
                ->join('planos', 'planos.id', '=', 'pets_planos.id_plano')
                ->whereBetween('data_cancelamento', [$inicio, $fim])
                ->where('planos.participativo', 1)
                ->count();

            $data["item"][] = [
                'value' => $query ?: 0,
                "text" => "cancelamentos integral"
            ];
        }
        return $data;
    }

    public function petsPagantesParticipativos(string $tipo, int $totalMeses)
    {
        if ($tipo == 'serial') {
            $date = Carbon::now()->subMonths($totalMeses);
            $response = [];

            while($totalMeses > 0) {
                $date->addMonth();
                $endOfMonth = $date->copy()->endOfMonth();

                $dataVidas = [];
                $dataVidas['label'] = $date->format('M/y');

                $vidasAtivas = Pets::join('pets_planos as pp', 'pets.id','=','pp.id_pet')
                    ->join('planos', 'planos.id', '=', 'pp.id_plano')
                    ->where('pp.data_inicio_contrato', '<=', $endOfMonth)
                    ->where(function($query) use($endOfMonth){
                        $query->where('pp.data_encerramento_contrato', '>', $endOfMonth)
                            ->orWhereNull('pp.data_encerramento_contrato');
                    })
                    ->where('planos.participativo', 1)
                    ->where('pp.valor_momento', '>', 0)
                    ->count('pets.id');

                $dataVidas['valor'] = $vidasAtivas ? $vidasAtivas : 0;

                $response[] = $dataVidas;

                $totalMeses--;
            }

            $data['x_axis']['labels'] = [];
            $series = new stdClass();
            foreach ($response as $resp) {
                $data['x_axis']['labels'][] = $resp['label'];
                $series->data[] = $resp['valor'];
            }
            $series->name = "Pets pagantes (participativos)";
            $data['series'] = [$series];
        } else {
            $pets = Pets::join('pets_planos as pp', 'pets.id','=','pp.id_pet')
                ->join('planos', 'planos.id', '=', 'pp.id_plano')
                ->where('planos.participativo', 1)
                ->where('pp.valor_momento', '>', 0)
                ->count('pets.id');

            $data["item"][] = [
                "value" => $pets,
                "text" => "Pets Pagantes (participativos)"
            ];
        }

        return $data;
    }

    public function petsNaoPagantesParticipativos(string $tipo, int $totalMeses)
    {
        if ($tipo == 'serial') {
            $date = Carbon::now()->subMonths($totalMeses);
            $response = [];

            while($totalMeses > 0) {
                $date->addMonth();
                $endOfMonth = $date->copy()->endOfMonth();

                $dataVidas = [];
                $dataVidas['label'] = $date->format('M/y');

                $vidasAtivas = Pets::join('pets_planos as pp', 'pets.id','=','pp.id_pet')
                    ->join('planos', 'planos.id', '=', 'pp.id_plano')
                    ->where('pp.data_inicio_contrato', '<=', $endOfMonth)
                    ->where(function($query) use($endOfMonth){
                        $query->where('pp.data_encerramento_contrato', '>', $endOfMonth)
                            ->orWhereNull('pp.data_encerramento_contrato');
                    })
                    ->where('planos.participativo', 1)
                    ->where('pp.valor_momento', '=', 0)
                    ->count('pets.id');

                $dataVidas['valor'] = $vidasAtivas ? $vidasAtivas : 0;

                $response[] = $dataVidas;

                $totalMeses--;
            }

            $data['x_axis']['labels'] = [];
            $series = new stdClass();
            foreach ($response as $resp) {
                $data['x_axis']['labels'][] = $resp['label'];
                $series->data[] = $resp['valor'];
            }
            $series->name = "Pets não pagantes (participativos)";
            $data['series'] = [$series];
        } else {
            $pets = Pets::join('pets_planos as pp', 'pets.id','=','pp.id_pet')
                ->join('planos', 'planos.id', '=', 'pp.id_plano')
                ->where('planos.participativo', 1)
                ->where('pp.valor_momento', '=', 0)
                ->count('pets.id');

            $data["item"][] = [
                "value" => $pets,
                "text" => "Pets não pagantes (participativos)"
            ];
        }

        return $data;
    }

    public function churnParticipativosPeriodo(int $meses)
    {
        if (empty($meses) || $meses === 0) {
            $meses = 1;
        }

        $period = $meses;

        $date = Carbon::now()->subMonths($meses);
        $response = [];

        while($meses > 0) {
            $date->addMonth();
            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();

            if($meses == 1) {
                $endOfMonth = Carbon::today();
            }

            $qtdCancelamentos = DB::table('cancelamentos')
                ->join('pets', 'pets.id', '=', 'cancelamentos.id_pet')
                ->join('pets_planos', 'pets_planos.id_pet', '=', 'pets.id')
                ->join('planos', 'planos.id', '=', 'pets_planos.id_plano')
                ->whereBetween('data_cancelamento', [$startOfMonth, $endOfMonth])
                ->where('planos.participativo', 1)
                ->count();

            $response[] = [
                'valor' => $qtdCancelamentos ? $qtdCancelamentos : 0,
                'label' => $date->format('m/Y')
            ];

            $meses--;
        }

        $totalCancelled = 0;

        $data['x_axis']['labels'] = [];
        $series = new stdClass();

        foreach ($response as $resp) {
            $data['x_axis']['labels'][] = $resp['label'];
            $series->data[] = $resp['valor'];
            $totalCancelled += $resp['valor'];
        }

        $series->name = "Cancelamentos";
        $data['series'] = [$series];
        $data['churn_medium'] = round($totalCancelled / $period);

        return $data;
    }

    public function novasVidasTotalMensal(int $totalMeses)
    {
        $date = Carbon::now()->subMonths($totalMeses);
        $response = [];

        while($totalMeses > 0) {
            $date->addMonth();
            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();

            $pets = PetsPlanos::join('pets', 'pets.id', '=', 'pets_planos.id_pet')
                ->join('planos', 'pets_planos.id_plano', '=', 'planos.id')
                ->whereBetween('pets_planos.created_at', [$startOfMonth, $endOfMonth])
                ->where('planos.participativo', 1)
                ->where('pets_planos.created_at', '>', '2020-09-01 00:00:00')
                ->whereNull('pets.deleted_at')
                ->whereNull('pets_planos.deleted_at')
                ->groupBy('pets.id')
                ->get()->count();

            $response[] = [
                'label' => $date->format('M/y'),
                'valor' => $pets ? $pets : 0
            ];

            $totalMeses--;
        }

        $data['x_axis']['labels'] = [];
        $series = new stdClass();
        foreach ($response as $resp) {
            $data['x_axis']['labels'][] = $resp['label'];
            $series->data[] = $resp['valor'];
        }
        $series->name = "Novas Vidas (total por mês)";
        $data['series'] = [$series];

        return $data;
    }

    public function novasVidasIntegralMensal(int $totalMeses)
    {
        $date = Carbon::now()->subMonths($totalMeses);
        $response = [];

        while($totalMeses > 0) {
            $date->addMonth();
            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();

            $pets = PetsPlanos::join('pets', 'pets.id', '=', 'pets_planos.id_pet')
                ->join('planos', 'pets_planos.id_plano', '=', 'planos.id')
                ->whereBetween('pets_planos.created_at', [$startOfMonth, $endOfMonth])
                ->where('planos.participativo', 0)
                ->whereNull('pets.deleted_at')
                ->whereNull('pets_planos.deleted_at')
                ->groupBy('pets.id')
                ->get()->count();

            $response[] = [
                'label' => $date->format('M/y'),
                'valor' => $pets ? $pets : 0
            ];

            $totalMeses--;
        }

        $data['x_axis']['labels'] = [];
        $series = new stdClass();
        foreach ($response as $resp) {
            $data['x_axis']['labels'][] = $resp['label'];
            $series->data[] = $resp['valor'];
        }
        $series->name = "Novas Vidas (total por mês)";
        $data['series'] = [$series];

        return $data;
    }

    public function novasVidasNaoPagantesMensal(string $tipo, int $totalMeses)
    {
        if ($tipo == 'serial') {
            $date = Carbon::now()->subMonths($totalMeses);
            $response = [];

            while($totalMeses > 0) {
                $date->addMonth();
                $startOfMonth = $date->copy()->startOfMonth();
                $endOfMonth = $date->copy()->endOfMonth();

                $pets = Pets::join('pets_planos', 'pets_planos.id_pet', '=', 'pets.id')
                    ->join('planos', 'pets_planos.id_plano', '=', 'planos.id')
                    ->whereBetween('pets_planos.data_inicio_contrato', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
                    ->whereBetween('pets.created_at', [$startOfMonth, $endOfMonth])
                    ->where('planos.participativo', 1)
                    ->where('pets_planos.valor_momento', '=', 0)
                    ->groupBy('pets.nome_pet')
                    ->get()
                    ->count();

                $response[] = [
                    'label' => $date->format('M/y'),
                    'valor' => $pets ? $pets : 0
                ];

                $totalMeses--;
            }

            $data['x_axis']['labels'] = [];
            $series = new stdClass();
            foreach ($response as $resp) {
                $data['x_axis']['labels'][] = $resp['label'];
                $series->data[] = $resp['valor'];
            }
            $series->name = "Novas Vidas (total por mês)";
            $data['series'] = [$series];
        } else {
            $inicio = Carbon::now()->startOfMonth();
            $fim = Carbon::now();

            $query = Pets::join('pets_planos', 'pets_planos.id_pet', '=', 'pets.id')
                ->join('planos', 'pets_planos.id_plano', '=', 'planos.id')
                ->whereBetween('pets_planos.data_inicio_contrato', [$inicio->format('Y-m-d'), $fim->format('Y-m-d')])
                ->whereBetween('pets.created_at', [$inicio, $fim])
                ->where('planos.participativo', 1)
                ->where('pets_planos.valor_momento', '=', 0)
                ->groupBy('pets.nome_pet')
                ->get()
                ->count();

            $data["item"][] = [
                'value' => $query ?: 0,
                "text" => "Novas Vidas (total por mês)"
            ];
        }

        return $data;
    }

    public function novasVidasPagantesMensal(string $tipo, int $totalMeses)
    {
        if ($tipo == 'serial') {
            $date = Carbon::now()->subMonths($totalMeses);
            $response = [];

            while($totalMeses > 0) {
                $date->addMonth();
                $startOfMonth = $date->copy()->startOfMonth();
                $endOfMonth = $date->copy()->endOfMonth();

                $pets = Pets::join('pets_planos', 'pets_planos.id_pet', '=', 'pets.id')
                    ->join('planos', 'pets_planos.id_plano', '=', 'planos.id')
                    ->whereBetween('pets_planos.data_inicio_contrato', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
                    ->whereBetween('pets.created_at', [$startOfMonth, $endOfMonth])
                    ->where('planos.participativo', 1)
                    ->where('pets_planos.valor_momento', '>', 0)
                    ->groupBy('pets.nome_pet')
                    ->get()
                    ->count();

                $response[] = [
                    'label' => $date->format('M/y'),
                    'valor' => $pets ? $pets : 0
                ];

                $totalMeses--;
            }

            $data['x_axis']['labels'] = [];
            $series = new stdClass();
            foreach ($response as $resp) {
                $data['x_axis']['labels'][] = $resp['label'];
                $series->data[] = $resp['valor'];
            }
            $series->name = "Novas Vidas (total por mês)";
            $data['series'] = [$series];
        } else {
            $inicio = Carbon::now()->startOfMonth();
            $fim = Carbon::now();

            $query = Pets::join('pets_planos', 'pets_planos.id_pet', '=', 'pets.id')
                ->join('planos', 'pets_planos.id_plano', '=', 'planos.id')
                ->whereBetween('pets_planos.data_inicio_contrato', [$inicio->format('Y-m-d'), $fim->format('Y-m-d')])
                ->whereBetween('pets.created_at', [$inicio, $fim])
                ->where('planos.participativo', 1)
                ->where('pets_planos.valor_momento', '>', 0)
                ->groupBy('pets.nome_pet')
                ->get()
                ->count();

            $data["item"][] = [
                'value' => $query ?: 0,
                "text" => "Novas Vidas (total por mês)"
            ];
        }

        return $data;
    }

    public function migracoesMensal(int $totalMeses)
    {
        $date = Carbon::now()->subMonths($totalMeses);
        $response = [];

        while($totalMeses > 0) {
            $date->addMonth();
            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();

            $pets = Pets::whereBetween('pets.updated_at', [$startOfMonth, $endOfMonth])
                ->with(['petsPlanos.plano'])
                ->get();
            
            $contador = 0;
            foreach ($pets as $pet) {
                $ultimoPlano = $pet->petsPlanos->last();
                if (!$ultimoPlano) {
                    continue;
                }
                
                $penultimoPlano = $pet->petsPlanos
                    ->where('id', '!=', $ultimoPlano->id)
                    ->last();
                if (!$penultimoPlano) {
                    continue;
                }
    
                if (
                    $ultimoPlano->plano->participativo == 1 &&
                    $penultimoPlano->plano->participativo == 0 &&
                    $ultimoPlano->created_at->format('Y-m-d') >= $startOfMonth->format('Y-m-d') &&
                    $ultimoPlano->created_at->format('Y-m-d') <= $endOfMonth->format('Y-m-d')
                ) {
                    $contador++;
                }
            }

            $response[] = [
                'label' => $date->format('M/y'),
                'valor' => $contador
            ];

            $totalMeses--;
        }

        $data['x_axis']['labels'] = [];
        $series = new stdClass();
        foreach ($response as $resp) {
            $data['x_axis']['labels'][] = $resp['label'];
            $series->data[] = $resp['valor'];
        }
        $series->name = "Migrações Integral -> Participativo (quantidade mensal)";
        $data['series'] = [$series];
    
        return $data;
    }

    public function migracoesPagantesMensal(int $totalMeses)
    {
        $date = Carbon::now()->subMonths($totalMeses);
        $response = [];

        while($totalMeses > 0) {
            $date->addMonth();
            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();

            $pets = Pets::where('ativo', 1)
                ->whereHas('cliente', function ($cliente) {
                    $cliente->where('ativo', 1);
                })
                ->with([
                    'cliente',
                    'petsPlanos.plano'
                ])
                ->get();
            
            $contador = 0;
            foreach ($pets as $pet) {
                $ultimoPlano = $pet->petsPlanos->last();
                if (!$ultimoPlano) {
                    continue;
                }
                
                $penultimoPlano = $pet->petsPlanos
                    ->where('id', '!=', $ultimoPlano->id)
                    ->last();
                if (!$penultimoPlano) {
                    continue;
                }
    
                if (
                    $ultimoPlano->plano->participativo == 1 &&
                    $penultimoPlano->plano->participativo == 0 &&
                    $ultimoPlano->data_inicio_contrato->format('Y-m') == $startOfMonth->format('Y-m') &&
                    $ultimoPlano->valor_momento > 0
                ) {
                    $contador++;
                }
            }

            $response[] = [
                'label' => $date->format('M/y'),
                'valor' => $contador
            ];

            $totalMeses--;
        }

        $data['x_axis']['labels'] = [];
        $series = new stdClass();
        foreach ($response as $resp) {
            $data['x_axis']['labels'][] = $resp['label'];
            $series->data[] = $resp['valor'];
        }
        $series->name = "Migrações Integral -> Participativo (quantidade mensal)";
        $data['series'] = [$series];
    
        return $data;
    }
    

    public function migracoesNaoPagantesMensal(int $totalMeses)
    {
        $date = Carbon::now()->subMonths($totalMeses);
        $response = [];

        while($totalMeses > 0) {
            $date->addMonth();
            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();

            $pets = Pets::where('ativo', 1)
                ->whereHas('cliente', function ($cliente) {
                    $cliente->where('ativo', 1);
                })
                ->with([
                    'cliente',
                    'petsPlanos.plano'
                ])
                ->get();
            
            $contador = 0;
            foreach ($pets as $pet) {
                $ultimoPlano = $pet->petsPlanos->last();
                if (!$ultimoPlano) {
                    continue;
                }
                
                $penultimoPlano = $pet->petsPlanos
                    ->where('id', '!=', $ultimoPlano->id)
                    ->last();
                if (!$penultimoPlano) {
                    continue;
                }
    
                if (
                    $ultimoPlano->plano->participativo == 1 &&
                    $penultimoPlano->plano->participativo == 0 &&
                    $ultimoPlano->data_inicio_contrato->format('Y-m') == $startOfMonth->format('Y-m') &&
                    $ultimoPlano->valor_momento == 0
                ) {
                    $contador++;
                }
            }

            $response[] = [
                'label' => $date->format('M/y'),
                'valor' => $contador
            ];

            $totalMeses--;
        }

        $data['x_axis']['labels'] = [];
        $series = new stdClass();
        foreach ($response as $resp) {
            $data['x_axis']['labels'][] = $resp['label'];
            $series->data[] = $resp['valor'];
        }
        $series->name = "Migrações Integral -> Participativo (quantidade mensal)";
        $data['series'] = [$series];
    
        return $data;
    }
}

