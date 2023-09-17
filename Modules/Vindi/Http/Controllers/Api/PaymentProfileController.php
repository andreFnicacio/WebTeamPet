<?php

namespace Modules\Vindi\Http\Controllers\Api;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use InfyOm\Generator\Utils\ResponseUtil;
use Modules\Vindi\Http\Requests\CreatePaymentProfileRequest;
use Modules\Vindi\Http\Requests\GetPaymentProfileRequest;
use Modules\Vindi\Http\Requests\TokenPaymentProfileRequest;
use Modules\Vindi\Services\VindiService;

class PaymentProfileController extends Controller
{
    /**
     * @var VindiService
     */
    protected $financialService;

    public function __construct()
    {
        $this->financialService = app(VindiService::class);
    }

    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(GetPaymentProfileRequest $request)
    {
        $financialCustomer = $this->financialService->customer()->getByCode($request->get('customer_id'));

        if (empty($financialCustomer)) {
            return Response::json(
                ResponseUtil::makeError('Customer not found in Financial Service', []),
                404
            );
        }

        $paymentProfile = $this->financialService->paymentProfile()->getByCustomerId($financialCustomer->id);

        if (empty($paymentProfile)) {
            return Response::json(ResponseUtil::makeError('', []), 404);
        }

        $response = [
            'profile_id' => $paymentProfile->id,
            'card_number_first_six' => $paymentProfile->card_number_first_six,
            'card_number_last_four' => $paymentProfile->card_number_last_four,
            'financial_id' => $paymentProfile->customer->id
        ];

        if ($paymentProfile->payment_method->code === 'credit_card') {
            $response['credit_cart_type'] = $paymentProfile->payment_company->code;
        }

        return Response::json(
            ResponseUtil::makeResponse('', $response)
        );
    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(CreatePaymentProfileRequest $request)
    {
        $paymentProfileService = $this->financialService->paymentProfile();

        try {
            $paymentProfile = $paymentProfileService->create($request->toArray());
        } catch (\Exception $e) {
            Log::error("Unable to create payment profile. Error: " . $e->getMessage());
            return Response::json(ResponseUtil::makeError('', []), 500);
        }

        return Response::json(ResponseUtil::makeResponse( '', $paymentProfile->toArray()), 201);

    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\Http\JsonResponse
     */
    public function appPaymentProfile(CreatePaymentProfileRequest $request)
    {
        $financialCustomer = $this->financialService->customer()->getByCode($request->get('customer_id'));

        if (empty($financialCustomer)) {
            return Response::json(
                ResponseUtil::makeError('Customer not found in Financial Service', []),
                404
            );
        }

        $paymentProfileService = $this->financialService->paymentProfile();

        $request = $request->toArray();
        $request['customer_id'] = $financialCustomer->id;

        try {
            $paymentProfile = $paymentProfileService->create($request);
        } catch (\Exception $e) {
            Log::error("Unable to create payment profile. Error: " . $e->getMessage());
            return Response::json(ResponseUtil::makeError('', []), 500);
        }

        return Response::json(ResponseUtil::makeResponse( '', $paymentProfile->toArray()), 201);

    }

    public function token(TokenPaymentProfileRequest $request)
    {
        $this->financialService->paymentProfile();

        Log::debug("Creating token for credit card: " . json_encode($request->toArray()));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        //
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
