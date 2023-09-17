<?php

namespace Modules\Subscriptions\Http\Controllers\Api;

use App\Models\Planos;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Response;
use InfyOm\Generator\Utils\ResponseUtil;

class PlanController extends Controller
{
    private $plans = [
        "Essencial",
        "Plus",
        "Prime"
    ];

    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $plans = Planos::whereIn('nome_plano', $this->plans)
            ->where('participativo', 1)
            ->get()
            ->map(function(Planos $plan) {
                return [
                    'id' => $plan->id,
                    'name' => $plan->nome_plano,
                    'price' => $plan->preco_plano_individual
                ];
            });

        return Response::json(ResponseUtil::makeResponse("", $plans));
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {

    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('subscriptions::show');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
}
