<?php

namespace App\Http\Controllers\API;


use Illuminate\Http\Request;
use \App\Models\RelatorioPetsPlanos;
use \App\Models\Nps;
use App\Http\Controllers\AppBaseController;
use App\Helpers\Utils;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Repositories\IndicadoresRepository;
class IndicadoresAPIController extends AppBaseController
{
    public function vidasAtivas(Request $request) {
        $vidasAtivas = RelatorioPetsPlanos::get(['data', 'qtde_total']);

        return response()->json($vidasAtivas);

    }

    public function npsAcumuladoDia(Request $request) {
        $npsDia = Nps::listarAcumuladoPorDia();
        return response()->json($npsDia);
    }

    public function npsAcumuladoMes(Request $request) {
        $npsAcumulado = Nps::listarAcumuladoPorMes();
        return response()->json($npsAcumulado);
    }

    public function npsMes(Request $request) {
        $npsMes = Nps::listarPorMes();
        return response()->json($npsMes);
    }

    public function churnRateMensal(Request $request) {
        $churnRateMensal = IndicadoresRepository::churnRateMensal();
        
        return response()->json($churnRateMensal, 200);
    }

    public function upgradeMensal(Request $request) {
        $upgradeMensal = IndicadoresRepository::upgradeMensal();

        return response()->json($upgradeMensal, 200);
    }
   

}