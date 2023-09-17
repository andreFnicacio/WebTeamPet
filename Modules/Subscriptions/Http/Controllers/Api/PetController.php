<?php

namespace Modules\Subscriptions\Http\Controllers\Api;

use App\Models\Pets;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Response;
use InfyOm\Generator\Utils\ResponseUtil;
use Modules\Subscriptions\Http\Requests\CreatePetRequest;

class PetController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {

    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreatePetRequest $request)
    {
        $pet = Pets::where('nome_pet', $request->nome_pet)
            ->where('id_cliente', $request->id_cliente)->first();

        if ($pet) {
            return Response::json(ResponseUtil::makeError(__("Pet already exists!"), $pet), 302);
        }

        $data = $request->all();
        $data['ativo'] = false;
        $pet = Pets::create($data);
        $pet->numero_microchip = "PT" . $pet->id;
        $pet->save();
        return Response::json(ResponseUtil::makeResponse(__("Pet saved successfully!"), $pet), 201);
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
