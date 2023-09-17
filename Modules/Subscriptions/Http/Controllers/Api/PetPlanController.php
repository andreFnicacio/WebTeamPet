<?php

namespace Modules\Subscriptions\Http\Controllers\Api;

use App\Models\Pets;
use App\Models\PetsPlanos;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Response;
use InfyOm\Generator\Utils\ResponseUtil;
use Modules\Subscriptions\Http\Requests\CreatePetPlanRequest;
use Modules\Subscriptions\Services\SubscriptionService;
use Modules\Vindi\Services\VindiService;

class PetPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreatePetPlanRequest $request)
    {
        $defaultStatus = PetsPlanos::STATUS_PRIMEIRO_PLANO;
        $currentSubscription = null;

        if (PetsPlanos::petHasPlan($request->id_pet)) {
            $defaultStatus = PetsPlanos::STATUS_ALTERACAO;
            $currentSubscription = PetsPlanos::getCurrentSubscription($request->id_pet);
        }

        $subscriptionService = app(SubscriptionService::class);

        /** Unsubscribe pet, internally, from its current subscription */
        if ($currentSubscription) {
            $subscriptionService->unsubscribe(
                $currentSubscription->id,
                __("SITE/APP | Plano encerrado, pet contratou um novo plano.")
            );

            /** Unsubscribe on financial service */
            $financialSubscriptionService = app(VindiService::class)->subscription();
            $currentFinancialSubscription = $financialSubscriptionService->findByCode($currentSubscription->id);
            if ($currentFinancialSubscription) {
                $financialSubscriptionService->cancelSubscription($currentFinancialSubscription->id);
            }
        }

        $data = $request->all();
        $data['status'] = $defaultStatus;
        $data['participativo'] = true;
        $data['transicao'] = PetsPlanos::TRANSICAO__NOVA_COMPRA;
        $petPlan = PetsPlanos::create($data);

        $pet = Pets::find($request->id_pet);
        $pet->regime = $request->regime;
        $pet->save();

        return Response::json(
            ResponseUtil::makeResponse(__("Pet associated to new plan successfully!"), $petPlan)
        );
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $petPlano = PetsPlanos::find($id);

        if (is_null($petPlano)) {
            return Response::json(ResponseUtil::makeError(""), 404);
        }

        return Response::json(ResponseUtil::makeResponse("", $petPlano));
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
