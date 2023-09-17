<?php

namespace Modules\Vindi\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use InfyOm\Generator\Utils\ResponseUtil;
use Modules\Subscriptions\Services\SubscriptionService;
use Modules\Vindi\DTO\Subscription\SubscriptionDTO;
use Modules\Vindi\Services\VindiService;
use Modules\Vindi\Services\WebhookService;
use Vindi\Exceptions\WebhookHandleException;

class WebhookController extends Controller
{
    private WebhookService $webhookService;

    public function __construct(WebhookService $webhookService)
    {
        $this->webhookService = $webhookService;
    }

    /**
     * @throws WebhookHandleException
     */
    public function index(Request $request)
    {
        $secret = $request->get('secret');
        if (empty($secret) || $secret !== strval(config('services.vindi.webhook_secret'))) {
            Log::warning(__("Authentication failed"));
            return Response::json(ResponseUtil::makeError(__("Authentication failed")), 401);
        }

        $webhookHandler = app(VindiService::class)->getWebhookHandle();

        try {
            $event = $webhookHandler->handle();
        } catch (WebhookHandleException $exception) {
            Log::error("Unable to process webhook: " . $exception->getMessage());
            throw $exception;
        }

//        $subscriptionDTO = new SubscriptionDTO($event->data);

        switch ($event->type) {
            case VindiService::EVENT_SUBSCRIPTION_CANCELED:
//                $subscriptionService->unsubscribe(
//                    $subscriptionDTO->code,
//                    __("Webhook | Plano cancelado no Sistema Financeiro"),
//                    $subscriptionDTO->cancel_at
//                );
                break;
            case VindiService::EVENT_SUBSCRIPTION_CREATED:
                // Lidar com o evento de Assinatura efetuada
                break;
            case VindiService::EVENT_CHARGE_REJECTED:
                // Lidar com o evento de Cobrança rejeitada
                break;
            case VindiService::EVENT_BILL_CREATED:
                $this->webhookService->handleBillCreated($event->data);
                break;
            case VindiService::EVENT_BILL_PAID:
                $this->webhookService->handleBillPaid($event->data);
//                $subscriptionService->activate($subscriptionDTO);
                break;
            case VindiService::EVENT_PERIOD_CREATED:
                // Lidar com o evento de Período criado
                break;
            case VindiService::EVENT_TEST:
                Log::info("Webhook Request without body (Possible testing the connectivity of the webhook :)");
                break;
            default:
                // Lidar com falhas e eventos novos ou desconhecidos
                break;
        }
    }
}
