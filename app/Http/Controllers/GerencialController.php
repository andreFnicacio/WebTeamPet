<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateGerencialRequest;
use App\Http\Requests\UpdateGerencialRequest;
use App\Repositories\GerencialRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Entrust;

class GerencialController extends AppBaseController
{
    /**
     * Retorna o total de vidas ativas do sistema
     *
     * @param Request $request
     * @return int
     */
    public function vidasAtivas(Request $request)
    {
        $query = \App\Models\Pets::query();
        $query->where('ativo', true);

        return $query->count();
    }

    /**
     * Retorna a quantidade de novos cadastros de pets dentro do período
     *
     * @param Request $request
     * @return int
     */
    public function novasVidas(Request $request)
    {
        $query = \App\Models\Pets::query();
        $dates = self::getDates($request);
        $query->where('ativo', true)
              ->whereBetween('created_at', $dates);

        return $query->count();
    }

    /**
     * Total de cancelamentos no período.
     *
     * @param Request $request
     * @return int
     */
    public function cancelamentos(Request $request)
    {
        $query = \App\Models\PetsPlanos::query();
        $dates = self::getDates($request);
        $query->whereBetween('data_encerramento_contrato', $dates);
        $query->groupBy('id_pet');

        return $query->count();
    }

    public function inadimplentes()
    {
        $query = DB::table('cobrancas')
                    ->select('id_cliente')
                    ->where('status', 1)
                    ->whereNotIn('id', function($query) {
                        $query->select('id_cobranca')
                              ->from('pagamentos');
                    })->groupBy('id_cliente');
        return $query->get()->count();
    }
}