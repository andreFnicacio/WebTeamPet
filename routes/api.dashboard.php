<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

\Carbon\Carbon::useMonthsOverflow(false);

Route::get('/', function() {
    return [
        'api' => 'dashboard',
        'version' => '1.0'
    ];
});

Route::get('/churnRate/{year?}/{month?}/{plano?}', function(Request $request, $year = 2020, $month = 9, $plano = null) {
    $planos = null;
    if($request->filled('planos')) {
        $planos = $request->get('planos');
    }
    if($plano) {
        $planos = [$plano];
    }
    $month = sprintf("%02d", $month);

    $start = \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", "$year-$month-01 00:00:01");//->startOfMonth();
    $end = \Carbon\Carbon::today()->endOfMonth()->endOfDay();

    $vendas = \App\Models\PetsPlanos::whereBetween('data_inicio_contrato', [
        $start,
        $end
    ])->join('pets', 'pets.id', '=', 'pets_planos.id_pet')
        ->join('clientes', 'clientes.id', '=', 'pets.id_cliente')
        ->groupBy('clientes.id')
        ->groupBy('pets_planos.id_plano')
        ->whereIn('pets_planos.id_plano', $planos ?: \App\Models\Planos::PLANOS_PARA_TODOS)
        ->select(DB::raw('COUNT(clientes.id) as vendas, DATE(data_inicio_contrato) as dia, clientes.id as id'))
        ->orderBy('data_inicio_contrato')
        ->get(['vendas', 'dia', 'id']);


    $cancelamentos = \App\Models\PetsPlanos::whereBetween('data_encerramento_contrato', [
        $start,
        $end
    ])->join('pets', 'pets.id', '=', 'pets_planos.id_pet')
      ->join('clientes', 'clientes.id', '=', 'pets.id_cliente')
      ->whereIn('pets_planos.id_plano', $planos ?: \App\Models\Planos::PLANOS_PARA_TODOS)
      ->where('clientes.ativo', 0)
      ->groupBy(DB::raw('DATE(data_encerramento_contrato)'))
      ->groupBy('clientes.id')
      ->select(DB::raw('COUNT(*) as cancelamentos, DATE(data_encerramento_contrato) as dia, clientes.id as id_cliente, pets_planos.id_plano'))
      ->get();

    $response = [];

    while($start->lte($end)) {
        $dia = $start->format('Y-m-d');

        $c = $cancelamentos->where('dia', $dia) ? $cancelamentos->where('dia', $dia)->count() : 0;
        $v = $vendas->where('dia', $dia) ? $vendas->where('dia', $dia)->count() : 0;

        $response[] = [
            'data' => $dia,
            'vendas' => $v,
            'clientes_vendas' => $vendas->where('dia', $dia)->pluck('id'),
            'cancelamentos' => $c,
        ];

        $start->addDay();
    }


    return $response;
});

Route::get('relativeChurn/{year?}/{month?}/{plano?}', function(Request $request, $year = 2020, $month = 9, $plano = null) {
    $planos = null;
    if($request->filled('planos')) {
        $planos = $request->get('planos');
    }

    if($plano) {
        $planos = [$plano];
    }

    $month = sprintf("%02d", $month);

    $start = \Carbon\Carbon::createFromFormat("Y-m-d", "$year-$month-01");//->startOfMonth();
    $end = \Carbon\Carbon::today()->endOfMonth();

    $cancelamentos = \App\Models\PetsPlanos::whereBetween('data_encerramento_contrato', [
        $start,
        $end
    ])->join('pets', 'pets.id', '=', 'pets_planos.id_pet')
        ->join('clientes', 'clientes.id', '=', 'pets.id_cliente')
        ->whereIn('pets_planos.id_plano', $planos ?: \App\Models\Planos::PLANOS_PARA_TODOS)
        ->where('clientes.ativo', 0)
        ->groupBy([DB::raw('DATE_FORMAT(`data_encerramento_contrato`,\'%Y-%m\')')])
        ->groupBy('clientes.id')
        ->select(DB::raw('COUNT(*) as cancelamentos, DATE_FORMAT(data_encerramento_contrato, \'%Y-%m\') as competencia, clientes.id as id_cliente'))
        ->get();

    $response = [];

    while($start->format('Ym') <= $end->format('Ym')) {
        $competencia = $start->format('Y-m');
        $competenciaAnterior = $start->copy()->subMonth(1)->format('Y-m');
        $c = $cancelamentos->where('competencia', $competencia) ? $cancelamentos->where('competencia', $competencia)->count() : 0;
        $vidasativasLPT = \App\Models\DadosTemporais::where('indicador', 'vidasAtivasLPT')
                                                    ->groupBy(DB::raw('DATE_FORMAT(data_referencia, \'%Y-%m\')'))
                                                    ->whereRaw('DATE_FORMAT(data_referencia, \'%Y-%m\')' . ' = ' .  "'" . $competenciaAnterior . "'")
                                                    ->orderBy('id', 'DESC')
                                                    ->first();

        $vidasAtivas = ($vidasativasLPT ? $vidasativasLPT->valor_numerico : 0);

        $response[] = [
            'competencia' => $competencia,
            'competencia_extenso' => $start->format('M-Y'),
            'cancelamentos' => $c,
            'vidasAtivas' => $vidasativasLPT ? $vidasativasLPT->valor_numerico : 0,
            'percentual' => $vidasAtivas ? (($c / $vidasAtivas) * 100) : 0
        ];

        $start->addMonth();
    }

    return $response;
});

Route::get('/income/{plano?}', function(Request $request, $plano = null) {
    $planos = null;
    if($request->filled('planos')) {
        $planos = $request->get('planos');
    }

    if($plano) {
        $planos = [$plano];
    }

    $totalMensal = \App\Models\PetsPlanos::query()
        ->join('pets', 'pets.id', '=', 'pets_planos.id_pet')
        ->join('clientes', 'clientes.id', '=', 'pets.id_cliente')
        ->whereIn('pets_planos.id_plano', $planos ?: \App\Models\Planos::PLANOS_PARA_TODOS)
        ->where('clientes.ativo', 1)
        ->where('pets.ativo', 1)
        ->where('pets.regime', '=', 'MENSAL')
        ->whereNull('pets_planos.data_encerramento_contrato')
        ->sum('valor_momento');

    $totalAnual = \App\Models\PetsPlanos::query()
        ->join('pets', 'pets.id', '=', 'pets_planos.id_pet')
        ->join('clientes', 'clientes.id', '=', 'pets.id_cliente')
        ->whereIn('pets_planos.id_plano', $planos ?: \App\Models\Planos::PLANOS_PARA_TODOS)
        ->where('clientes.ativo', 1)
        ->where('pets.ativo', 1)
        ->where('pets.regime', '=', 'ANUAL')
        ->whereNull('pets_planos.data_encerramento_contrato')
        ->sum('valor_momento');

    $total = $totalMensal + ($totalAnual/12);

    $ativos = \App\Models\PetsPlanos::query()
        ->join('pets', 'pets.id', '=', 'pets_planos.id_pet')
        ->join('clientes', 'clientes.id', '=', 'pets.id_cliente')
        ->whereIn('pets_planos.id_plano', $planos ?: \App\Models\Planos::PLANOS_PARA_TODOS)
        ->where('clientes.ativo', 1)
        ->where('pets.ativo', 1)
        ->whereNull('pets_planos.data_encerramento_contrato')
        ->groupBy('clientes.id')
        ->select(['clientes.id as id_cliente', 'clientes.email as email'])
        ->get()->unique('id_cliente')->count();

    $average = $total / $ativos;
    return [
        'ativos' => (float) $ativos,
        'income' => (float) number_format($total, 2, '.', ''),
        'averageTicket' => (float) number_format($average, 2, '.', ''),
    ];
});

Route::get('averageTicket/{year?}/{month?}/{plano?}', function(Request $request, $year = 2020, $month = 9, $plano = null) {
    $planos = null;
    if($request->filled('planos')) {
        $planos = $request->get('planos');
    }

    if($plano) {
        $planos = [$plano];
    }

    $month = sprintf("%02d", $month);

    $start = \Carbon\Carbon::createFromFormat("Y-m-d", "$year-$month-01")->startOfMonth();
    $end = \Carbon\Carbon::today();

    $vendas = \App\Models\PetsPlanos::whereBetween('data_inicio_contrato', [
        $start,
        $end
    ])->join('pets', 'pets.id', '=', 'pets_planos.id_pet')
        ->join('clientes', 'clientes.id', '=', 'pets.id_cliente')
        ->groupBy('clientes.id')
        ->groupBy('pets_planos.id_plano')
        ->whereIn('pets_planos.id_plano', $planos ?: \App\Models\Planos::PLANOS_PARA_TODOS)
        ->select(DB::raw('COUNT(clientes.id) as vendas, DATE(data_inicio_contrato) as dia, DATE_FORMAT(`data_inicio_contrato`,\'%Y-%m\') as competencia, clientes.id as id'))
        ->orderBy('data_inicio_contrato')
        ->get(['vendas', 'dia', 'id']);

    $receitasMensais = \App\Models\PetsPlanos::query()
        ->join('pets', 'pets.id', '=', 'pets_planos.id_pet')
        ->join('clientes', 'clientes.id', '=', 'pets.id_cliente')
        ->whereIn('pets_planos.id_plano', $planos ?: \App\Models\Planos::PLANOS_PARA_TODOS)
        ->where('pets.regime', '=', 'MENSAL')
        ->groupBy([DB::raw('DATE_FORMAT(`data_inicio_contrato`,\'%Y-%m\')')])
        ->select(DB::raw('SUM(pets_planos.valor_momento) as receita, DATE_FORMAT(`data_inicio_contrato`,\'%Y-%m\') as competencia'))
        ->get(['receita', 'competencia']);

    $receitasAnuais = \App\Models\PetsPlanos::query()
        ->join('pets', 'pets.id', '=', 'pets_planos.id_pet')
        ->join('clientes', 'clientes.id', '=', 'pets.id_cliente')
        ->whereIn('pets_planos.id_plano', $planos ?: \App\Models\Planos::PLANOS_PARA_TODOS)
        ->where('pets.regime', '=', 'ANUAL')
        ->groupBy([DB::raw('DATE_FORMAT(`data_inicio_contrato`,\'%Y-%m\')')])
        ->select(DB::raw('SUM(pets_planos.valor_momento/12) as receita, DATE_FORMAT(`data_inicio_contrato`,\'%Y-%m\') as competencia'))
        ->get(['receita', 'competencia']);

    $response = [];

    while($start->format('Ym') <= $end->format('Ym')) {
        $competencia = $start->format('Y-m');

        $v = $vendas->where('competencia', $competencia) ? $vendas->where('competencia', $competencia)->count() : 0;
        $rm = $receitasMensais->where('competencia', $competencia) ? $receitasMensais->where('competencia', $competencia)->sum('receita') : 0;
        $ra = $receitasAnuais->where('competencia', $competencia) ? $receitasAnuais->where('competencia', $competencia)->sum('receita') : 0;
        $rt = $rm + $ra;
        $response[] = [
            'competencia' => $competencia,
            'competencia_extenso' => $start->format('M-Y'),
            'vendas' => $v,
            'receita' => $rt,
            'taxa' => (float) number_format($rt/$v, 2, '.', ''),
        ];

        $start->addMonth();
    }

    return $response;
});

Route::get('activeClients/{year?}/{month?}/{plano?}', function(Request $request, $year = 2020, $month = 1, $plano = null) {
    $planos = null;
    if($request->filled('planos')) {
        $planos = $request->get('planos');
    }

    if($plano) {
        $planos = [$plano];
    }
    $month = sprintf("%02d", $month);

    $start = \Carbon\Carbon::createFromFormat("Y-m-d", "$year-$month-01")->startOfMonth();
    //$start = \Carbon\Carbon::createFromFormat("Y-m-d", "2020-09-01")->startOfMonth();
    $end = \Carbon\Carbon::today();

    $ativos = \App\Models\PetsPlanos::query()
        ->join('pets', 'pets.id', '=', 'pets_planos.id_pet')
        ->join('clientes', 'clientes.id', '=', 'pets.id_cliente')
        ->whereIn('pets_planos.id_plano', $planos ?: \App\Models\Planos::PLANOS_PARA_TODOS)
        ->where('clientes.ativo', 1)
        ->where('pets.ativo', 1)
        ->whereNull('pets_planos.data_encerramento_contrato')
        ->groupBy(['clientes.id', DB::raw('DATE_FORMAT(`data_inicio_contrato`,\'%Y-%m\')')])
        ->select(DB::raw('DATE_FORMAT(`data_inicio_contrato`,\'%Y-%m\') as competencia, clientes.id as id_cliente'))
        ->get()->unique('id_cliente');

    $response = [];

    $vTotal = 0;
    while($start->format('Ym') <= $end->format('Ym')) {

        $competencia = $start->format('Y-m');
        $vTotal += $ativos->where('competencia', $competencia) ? $ativos->where('competencia', $competencia)->count() : 0;

        $response[] = [
            'competencia' => $competencia,
            'competencia_extenso' => $start->format('M-Y'),
            'clientes' => $vTotal,
        ];
        $start->addMonth();
    }

    return $response;
});

Route::get('leads/{plano?}', function(Request $request, $plano = null) {
    $planos = null;
    if($request->filled('planos')) {
        $planos = $request->get('planos');
    }

    if($plano) {
        $planos = [$plano];
    }

    $vendas = \App\Models\PetsPlanos::query()
        ->join('pets', 'pets.id', '=', 'pets_planos.id_pet')
        ->join('clientes', 'clientes.id', '=', 'pets.id_cliente')
        ->groupBy(['clientes.id', DB::raw('DATE_FORMAT(`data_inicio_contrato`,\'%Y-%m\')')])
        ->groupBy('pets_planos.id_plano')
        ->whereIn('pets_planos.id_plano', $planos ?: \App\Models\Planos::PLANOS_PARA_TODOS)
        ->select(DB::raw('COUNT(clientes.id) as vendas, DATE_FORMAT(`data_inicio_contrato`,\'%Y-%m\') as competencia, clientes.id as id'))
        ->get(['vendas', 'competencia', 'id']);

    $leads = \App\Models\DadosTemporais::leads()->get();
    $midia = \App\Models\DadosTemporais::midia()->get();

    $cac = [];
    foreach($midia as $m) {
        $competencia = $m->competencia;
        $v = $vendas->where('competencia', $competencia) ? $vendas->where('competencia', $competencia)->count() : 0;

        $cac[] = [
            'competencia' => $competencia,
            'vendas' => $v,
            'midia' => $m->valor,
            'valor' => (float) number_format($m->valor / $v, 2, '.', ''),
        ];
    }

    $taxaConversao = [];
    foreach($leads as $l) {
        $competencia = $l->competencia;
        $v = $vendas->where('competencia', $competencia) ? $vendas->where('competencia', $competencia)->count() : 0;

        $taxaConversao[] = [
            'competencia' => $competencia,
            'vendas' => $v,
            'leads' => $l->valor,
            'valor' => (float) number_format( ($v / $l->valor)*100, 2, '.', ''),
        ];
    }

    return [
        'midia' => $midia,
        'leads' => $leads,
        'cac'   => $cac,
        'taxaConversao' => $taxaConversao,
    ];
});

Route::get('nps/{days?}', function(Request $request, $days = 30) {
    $apiToken = '4d5ff81d924e27b0e15a901329bac64e';

    $codes = ['10', '16', '20', '24','25'];
    $codes = join(',', $codes);
    //$start = \Carbon\Carbon::today()->subDays($days)->format('Y-m-d');
    $start = Carbon\Carbon::now()->startOfMonth()->format('Y-m-d');
    $end = \Carbon\Carbon::today()->format('Y-m-d');

    $uri = "https://api.tracksale.co/v2/";
    $http = new \GuzzleHttp\Client(['base_uri' => $uri]);

    $response = $http->request('GET', 'report/nps', [
        'query' => [
            'codes' => $codes,
            'start' => $start,
            'end' => $end
        ],
        'headers' => [
            'Authorization' => "Bearer $apiToken"
        ]
    ])->getBody()->getContents();

    $nps = null;
    if($response) {
        $nps = json_decode($response);
    }

    return [
        'nps' => $nps ? $nps->nps : null
    ];
});

Route::get('meta', function() {
    $meta  = \App\Models\DadosTemporais::meta()->first() ? \App\Models\DadosTemporais::meta()->first()->valor_numerico : 0;

    return [
        'meta' => $meta
    ];
});

Route::get('meta/monthly/{plano?}', function(Request $request, $plano = null) {
    $planos = null;
    if($request->filled('planos')) {
        $planos = $request->get('planos');
    }

    if($plano) {
        $planos = [$plano];
    }

    $first = App\Models\DadosTemporais::where('indicador', 'metaLPT')->orderBy('data_referencia', 'ASC')->first();
    $start = $first ? $first->data_referencia : \Carbon\Carbon::today();
    $end = \Carbon\Carbon::now()->endOfMonth();
    $start = $start->endOfMonth();

    $query  = \App\Models\DadosTemporais::query();

    $metas = $query->where('indicador', 'metaLPT')
        ->select(DB::raw('DATE_FORMAT(`data_referencia`,\'%Y-%m\') as competencia, valor_numerico, data_referencia'))
        ->groupBy(DB::raw('DATE_FORMAT(`data_referencia`,\'%Y-%m\')'))
        ->orderBy('data_referencia', 'DESC')
        ->get();

    $vendas = \App\Models\PetsPlanos::query()
        ->join('pets', 'pets.id', '=', 'pets_planos.id_pet')
        ->join('clientes', 'clientes.id', '=', 'pets.id_cliente')
        ->groupBy(['clientes.id', DB::raw('DATE_FORMAT(`data_inicio_contrato`,\'%Y-%m\')')])
        ->groupBy('pets_planos.id_plano')
        ->whereIn('pets_planos.id_plano', $planos ?: \App\Models\Planos::PLANOS_PARA_TODOS)
        ->select(DB::raw('COUNT(clientes.id) as vendas, DATE_FORMAT(`data_inicio_contrato`,\'%Y-%m\') as competencia, clientes.id as id'))
        ->get(['vendas', 'competencia', 'id']);

    $response = [];
    while($start->format('Ym') <= $end->format('Ym')) {
        $c = $start->format('Y-m');
        $m = $metas->where('competencia', $c)->first();
        $mv = $m ? $m->valor_numerico : 0;
        $v = $v = $vendas->where('competencia', $c) ? $vendas->where('competencia', $c)->count() : 0;

        $response[] = [
            'competencia' => $c,
            'meta' => $mv,
            'vendas' => $v,
            'meta_percentual' => ($mv && $v) ? (float) number_format(($v / $mv) * 100, 2) : 0
        ];

        $start->addMonth();
    }


    return $response;
});

Route::get('incomplete/{plano?}', function(Request $request, $plano = null) {
    $planos = null;
    if($request->filled('planos')) {
        $planos = $request->get('planos');
    }

    if($plano) {
        $planos = [$plano];
    }

    $start = \Carbon\Carbon::createFromFormat("Y-m-d", "2020-09-01")->startOfMonth();
    $end = \Carbon\Carbon::today();

    $incompletos = \App\LifepetCompraRapida::query()
                           ->where('concluido', 0)
                           ->where('pagamento_confirmado', 1)
                           ->whereBetween('created_at', [$start, $end])
                           ->select(DB::raw('DATE_FORMAT(`created_at`,\'%Y-%m\') as competencia'))
                           ->groupBy(DB::raw('DATE_FORMAT(`created_at`,\'%Y-%m\')'))->get();

    $response = [];

    while($start->format('Ym') <= $end->format('Ym')) {
        $competencia = $start->format('Y-m');

        $i = $incompletos->where('competencia', $competencia) ? $incompletos->where('competencia', $competencia)->count() : 0;

        $response[] = [
            'competencia' => $competencia,
            'incompletos' => $i
        ];

        $start->addMonth();
    }

    return $response;
});

Route::post('leads', function(Request $request) {
    return \App\Models\DadosTemporais::registrarLeadsLPT($request->get('leads', 0));
});

Route::group(['prefix' => 'reports'], function() {
    Route::get('churnRate/sales/{year?}/{month?}/{plano?}', function(Request $request, $year = 2020, $month = 9, $plano = null) {
        $planos = null;
        if($request->filled('planos')) {
            $planos = $request->get('planos');
        }

        if($plano) {
            $planos = [$plano];
        }

        $month = sprintf("%02d", $month);

        $start = \Carbon\Carbon::createFromFormat("Y-m-d", "$year-$month-01");//->startOfMonth();
        $end = \Carbon\Carbon::today()->endOfMonth();

        $vendas = \App\Models\PetsPlanos::whereBetween('data_inicio_contrato', [
            $start,
            $end
        ])->join('pets', 'pets.id', '=', 'pets_planos.id_pet')
            ->join('clientes', 'clientes.id', '=', 'pets.id_cliente')
            ->groupBy('clientes.id')
            ->groupBy('pets_planos.id_plano')
            ->whereIn('pets_planos.id_plano', $planos ?: \App\Models\Planos::PLANOS_PARA_TODOS)
            ->select(DB::raw('COUNT(clientes.id) as vendas, DATE(data_inicio_contrato) as dia, clientes.id as id, clientes.nome_cliente as nome_cliente'))
            ->orderBy('data_inicio_contrato')
            ->get(['vendas', 'dia', 'id']);

        $response = [];

        while($start->lte($end)) {
            $dia = $start->format('Y-m-d');

            $v = $vendas->where('dia', $dia);

            $response[] = [
                'data' => $dia,
                'vendas' => $v
            ];

            $start->addDay();
        }

        return $response;
    });
});

Route::group(['prefix' => 'views'], function() {
    Route::get('{viewName}/{page?}/{itemsPerPage?}', function(Request $request, $viewName, int $page = null, $itemsPerPage = 100) {
        //Check if table exists:

        try {
            if($page) {
                $results = \Illuminate\Support\Facades\DB::table($viewName)->forPage($page, $itemsPerPage)->get();
            } else {
                $results = \Illuminate\Support\Facades\DB::table($viewName)->get();
            }
        } catch (\Doctrine\DBAL\Query\QueryException $exception) {
            return abort(404, 'View not found on database.');
        }

        return [
            'data' => $results
        ];
    });
});