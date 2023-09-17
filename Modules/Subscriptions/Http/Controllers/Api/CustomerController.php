<?php

namespace Modules\Subscriptions\Http\Controllers\Api;

use Illuminate\Routing\Controller;
use App\Models\Clientes;
use App\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use InfyOm\Generator\Utils\ResponseUtil;
use Modules\Subscriptions\Http\Requests\CreateCustomerRequest;

class CustomerController extends Controller
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
    public function store(CreateCustomerRequest $request)
    {
        $customer = Clientes::where('cpf', $request->cpf)
            ->orWhere('email', $request->email)->first();

        if ($customer) {
            return Response::json(
                ResponseUtil::makeError(__("Customer already exists!"), $customer->toArray()),
                302
            );
        }

        $customer = Clientes::create($request->all());

        User::associateCustomer($customer);

        return Response::json(ResponseUtil::makeResponse(__("Customer saved successfully!"), $customer), 201);
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
