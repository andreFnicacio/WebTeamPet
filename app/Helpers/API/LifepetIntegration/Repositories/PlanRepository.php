<?php

namespace App\Helpers\API\LifepetIntegration\Repositories;
use App\Helpers\API\LifepetIntegration\Domains\Plan\Plan;
use App\Models\Planos;

class PlanRepository {

    public function getById($id) {
        $planos = Planos::find($id);
        //dd($planos);

        if(!isset($planos)) {
            throw new \Exception("Não foi possível encontrar o plano enviado ({$id}) em nosso banco de dados");
        }

        return $this->adapt($planos);
    }

    public function adapt(Planos $plano) {
        $planObj = new Plan();
        $planObj->populate([
            'id' => $plano->id,
            'name' => $plano->nome_plano,
            'external_id' => $plano->id_superlogica,
            'external_anual_id' => $plano->id_superlogica_anual,
            'active' => $plano->ativo
        ]);

        return $planObj;
    }
    
}