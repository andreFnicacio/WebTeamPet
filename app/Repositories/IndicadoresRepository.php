<?php

namespace App\Repositories;

use App\Models\PetsPlanos;
use Carbon\Carbon;

class IndicadoresRepository {

    public static function churnRateMensal() {
        
        $data = new Carbon('2016-01');
        $hoje = Carbon::now()->addMonthNoOverflow();

        $churns = [];
        while($data->lastOfMonth() != $hoje->lastOfMonth()) {
            
            $inicioMes = $data->copy()->firstOfMonth()->format('Y-m-d H:i:s');
            $fimMes =  $data->copy()->lastOfMonth()->format('Y-m-d H:i:s');
       
            // INTEGRAIS
            $canceladosNoPeriodo = PetsPlanos::totalCanceladosQuery()
                ->whereBetween('pets_planos.data_encerramento_contrato', [$inicioMes, $fimMes])
                ->pluck('total')
                ->first();
                
            // INTEGRAIS
            $ativosAtePeriodo = PetsPlanos::totalAtivosQuery()
                ->where('pets_planos.data_inicio_contrato', '<', $inicioMes )
                ->where(function ($query) {
                    $query->whereNull('pets_planos.data_encerramento_contrato')
                        ->orWhere('pets_planos.data_encerramento_contrato', '=', '0000-00-00');
                })
                ->pluck('total')
                ->first();

            // INTEGRAIS
            $iniciadosNoPeriodo = PetsPlanos::totalPrimeiroPlanoQuery()
                ->whereBetween('pets_planos.data_inicio_contrato', [$inicioMes, $fimMes])
                ->pluck('total')
                ->first();
                
            $churns[] = [
                'data' => $fimMes,
                'churn_rate' => $canceladosNoPeriodo / ($ativosAtePeriodo + $iniciadosNoPeriodo),
                'cancelados_no_periodo' => $canceladosNoPeriodo,
                'iniciados_no_periodo' => $iniciadosNoPeriodo,
                'ativos_ate_periodo' => $ativosAtePeriodo
            ];

            $data->addMonthNoOverflow();
      
        }

        return $churns;
    }

    public static function upgradeMensal() {
        $data = new Carbon('2016-01');
        $hoje = Carbon::now()->addMonthNoOverflow();

        $upgrades = [];
        while($data->lastOfMonth() != $hoje->lastOfMonth()) {
            
            $inicioMes = $data->copy()->firstOfMonth()->format('Y-m-d H:i:s');
            $fimMes =  $data->copy()->lastOfMonth()->format('Y-m-d H:i:s');

            $upgradesNoPeriodo = PetsPlanos::totalUpgradesQuery()
                ->whereBetween('data_inicio_contrato', [$inicioMes, $fimMes])
                ->pluck('total')
                ->first();

            $data->addMonthNoOverflow();

            $upgrades[] = [
                'data' => $fimMes,
                'total' => $upgradesNoPeriodo
            ];
        }

        return $upgrades;
    }

  
}