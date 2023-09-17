<?php

namespace Modules\Subscriptions\Http\Controllers\Api;

use App\Models\Clientes;
use App\Models\Cobrancas;
use App\Models\Pets;
use App\Models\PetsPlanos;
use App\Models\Planos;
use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use InfyOm\Generator\Utils\ResponseUtil;
use Modules\Subscriptions\Exceptions\FinancialCustomerException;
use Modules\Subscriptions\Http\Requests\CreateSubscriptionRequest;
use Modules\Subscriptions\Services\SubscriptionService;
use Modules\Vindi\Services\VindiService;
use Vindi\Exceptions\RateLimitException;
use Vindi\Exceptions\RequestException;

class SubscriptionController extends Controller
{
    protected $financialService;
    protected SubscriptionService $subscriptionService;

    public function __construct()
    {
        $this->financialService = app(VindiService::class);
        $this->subscriptionService = app(SubscriptionService::class);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function store(CreateSubscriptionRequest $request)
    {
        $financialSubscriptionService = app(SubscriptionService::class);

        $paymentData = $request->get('payment');
        $customer = $financialSubscriptionService->createCustomer($request->get('customer'), $paymentData);
        $pets = $financialSubscriptionService->createPets($request->get('pets'), $customer);
        $subscriptions = $financialSubscriptionService->subscribeTo($pets, $customer->id);

        $customerService = $this->financialService->customer();
        $paymentProfileService = $this->financialService->paymentProfile();
        $customerData = $customerService->map($customer->toArray());

        Log::debug("Financial Service | Customer Data creation: " . json_encode($customerData));

        $customerPaymentProfile = null;
        try {
            $financialCustomer = $customerService->findOrCreate($customerData);

            if (str_contains($paymentData['payment_method_code'], "credit_card")) {
                $customerPaymentProfile = $paymentProfileService->findOrCreate($financialCustomer, $request->get('payment'));
            }

        } catch (RequestException $e) {
            Log::error("Unable to create financial customer: " . json_encode($e->getMessage()));
            throw new FinancialCustomerException();
        } catch (GuzzleException $e) {
            Log::error("Unable to create financial customer due to guzzle exception: " . $e->getMessage());
        } catch (RateLimitException $e) {
            Log::error("Unable to create financial customer due to rate limit: " . $e->getMessage());
        }

        $planService = $this->financialService->plan();

        $financialSubscriptionService = $this->financialService->subscription();

        $success = [];
        $failed = [];

        /** @var PetsPlanos $subscriptionModel */
        foreach ($subscriptions as $subscriptionModel) {

            $planModel = Planos::where('id', $subscriptionModel->id_plano)->first();

            if ($subscriptionModel->pet()->first()->regime == Pets::REGIME_MENSAL) {
                $planData = $planService->get($planModel->financial_plan_monthly_id);
            } else {
                $planData = $planService->get($planModel->financial_plan_annual_id);
            }

            $subscriptionData = $financialSubscriptionService->map(
                $subscriptionModel,
                $financialCustomer,
                $planData,
                $paymentData,
                $customerPaymentProfile
            );

            try {

                Log::debug("Subscription Request Data: " . json_encode($subscriptionData, true));

                $financialSubscription = $financialSubscriptionService->createSubscription($subscriptionData);
                $financialSubscription['bill']['payment_method_code'] = $paymentData['payment_method_code'];

                $subscriptionModel->financial_id = $financialSubscription['id'];
                $subscriptionModel->save();

                if ($financialSubscription['bill']['status'] === "pending" &&
                    $subscriptionData['payment_method_code'] === 'credit_card'
                ) {
                    try {
                        $this->subscriptionService->handleSubscriptionCancelling($financialSubscription);
                    } catch (\Exception $e) {
                        throw $e;
                    }
                }

                $this->subscriptionService->generateFinancialHistoryRecord($customer->id, $subscriptionModel);

                $success = $financialSubscription;
            } catch (\Exception $e) {
                Log::error("Unable to create subscription on financial service. Error: " . $e->getMessage());
                $failed = $subscriptionData;
            }
        }

        if (count($failed)) {
            return Response::json(ResponseUtil::makeError("", $failed), 500);
        }

        return Response::json(ResponseUtil::makeResponse("", $success), 200);
    }
}
