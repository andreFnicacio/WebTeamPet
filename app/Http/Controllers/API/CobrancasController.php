<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;

/**
 * Class CobrancasController
 * @package App\Http\Controllers\API
 */

class CobrancasController extends AppBaseController
{
    public function sync($idCobrancas, $status){
        $cobrancas = \App\Models\Cobrancas::where('id_financeiro', '=', $idCobrancas)->get();
        foreach ($cobrancas as $cobranca) {
            if ($cobranca->driver == 'VINDI' and $status == 1) {
                $cobranca->baixaManual();
                $cliente = \App\Models\Clientes::where('id', '=', $cobranca->id_cliente)->first();
                $cliente->ativo = 1;
                $cliente->update();
                return $this->sendResponse('', 'Baixa manual', 201);
            }
            if ($cobranca->driver == 'VINDI' and $status == 0) {
                $cobranca->status = 0;
                $cobranca->update();
                return $this->sendResponse('', 'Cancelameno', 201);
            }
            return $this->sendResponse('', 'entro', 201);
        }
        return $this->sendResponse('', 'Client saved successfully', 201);
    }

    public function syncClientSubscription($idSubcription){
        $pet_planos = \App\Models\PetsPlanos::where('financial_id', '=', $idSubcription)->first();
        $resp = (object)[];
        if($pet_planos){
            $pet = \App\Models\Pets::where('id', '=', $pet_planos->id_pet)->first();
            $cliente = \App\Models\Clientes::where('id', '=', $pet->id_cliente)->first();
            $resp->pet = $pet;
            $resp->cliente = $cliente;
        }
        return (json_encode($resp));
    }

    public function searchClientDocument($id){
        $cliente = \App\Models\Clientes::query('id', '=',  $id)->first();
        $resp = (object)[];
        if($cliente){
            $pets = \App\Models\Pets::where('id_cliente', '=', $cliente->id)->get();
            $resp->pet = $pets;
            $resp->cliente = $cliente;
        }
        return (json_encode($resp));
    }
}