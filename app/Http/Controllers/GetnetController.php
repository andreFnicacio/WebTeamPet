<?php

namespace App\Http\Controllers;

use App\Helpers\API\Financeiro\DirectAccess\Models\Sale;
use App\Helpers\API\Financeiro\Financeiro;
use App\Helpers\API\Getnet\Service;
use App\Helpers\API\RDStation\Services\RDLinkPixGuiaService;
use App\Http\Util\LogEvent;
use App\Http\Util\Logger;
use App\Http\Util\LogPriority;
use App\Models\Cobrancas;
use App\Pix;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Guides\Entities\HistoricoUso;

class GetnetController extends Controller
{
    protected $service;

    public function __construct()
    {
        $this->service = new Service();
    }

    /**
     * @param Request $request
     * @param $numeroGuia
     * @param int $valor Valor de cobrança em centavos
     * @return JsonResponse
     */
    public function generateForServiceGuide(Request $request, $numeroGuia, $valor): JsonResponse
    {
        //Verificar existência da guia mencionada
        if(!HistoricoUso::where('numero_guia', $numeroGuia)->where('status', '<>', HistoricoUso::STATUS_LIBERADO)->exists()) {
            abort(422, 'The given guide can\'t be found.');
        }

        /**
         * @var HistoricoUso $atendimento
         */
        $atendimento = HistoricoUso::where('numero_guia', $numeroGuia)->first();

        //Checar valor mínimo (em reais)
        $minumumValue = 1;
        if(((int) $valor) / 100 < $minumumValue) {
            abort(422, 'The given amount doens\'t reach the minimum value (10)');
        }

        //Generate
        $idCliente = $atendimento->pet->cliente->id;
        $pix = $this->service->makePix((int) $valor, $numeroGuia, $idCliente);

        //Save
        $localDescription = 'Guia de atendimento #' . $numeroGuia;
        $pix = Pix::adapt($pix, $numeroGuia, $localDescription, $idCliente, $valor);

        $callbackUrl = route('getnet.pix.guide.confirm', ['id' => $pix->id]);
        $pix->callback_url = $callbackUrl;
        $pix->update();

        try {
            $rd = new RDLinkPixGuiaService();
            $rd->process($pix);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
        
        return response()->json(['pix' => $pix->toArray()]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function confirmPayment(Request $request): JsonResponse
    {
        $paymentType = $request->get('payment_type');
        if(!$paymentType) {
            abort(422, 'Payment Type is mandatory.');
        }

        switch ($paymentType) {
            case 'pix':
                return $this->confirmPixPayment($request);
                break;
            default:
                abort(501, 'Not implemented yet.');
        }
    }

    public function cancelPayment(Request $request)
    {

    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function confirmPixPayment(Request $request): JsonResponse
    {
        //Check order id
        $orderId = $request->get('order_id');
        $paymentId = $request->get('payment_id');
        $status = $request->get('status');

        $pix = Pix::orderId($orderId)
                  ->paymentId($paymentId)
                  ->orderBy('id','DESC')->first();

        if(!$pix) {
            abort(404, 'Transaction not found');
        }

        if($pix->status === $status) {
            return response()->json([
                'success' => true
            ]);
        }

        //Update status
        $pix->status = $status;
        $pix->update();

        //Dispatches callback
        if($pix->status === Pix::STATUS__APPROVED) {
            $response = $pix->dispatch();
        }

        return response()->json([
            'success' => true
        ]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return array
     */
    public function confirmServiceGuide(Request $request, $id): array
    {
        /**
         * @var Pix $pix
         */
        $pix = Pix::find($id);
        if(!$pix) {
            abort(404, 'Entity can\'t be found.');
        }
        $pixData = $pix->formatToLog();


        //Confirmar autorização da guia
        $atendimento = HistoricoUso::where('numero_guia', $pix->order_id)->get();
        if(!$atendimento) {
            $message = 'A guia de número #' . $pix->order_id . ' não pode ser encontrada no sistema.';
            Logger::log(LogEvent::WARNING, 'pix', LogPriority::HIGH, $message, 1, 'pix', $pix->id);
        }

        if($atendimento->first()->status === HistoricoUso::STATUS_LIBERADO) {
            return [
                'success' => true,
                'pix' => $id
            ];
        }

        //Incluir informação de pagamento no sistema financeiro
        if(env('APP_ENV') === 'production') {
            //TODO: Adicionar informação ao superlógica
            $sale = new Sale();
            $financeiro = new Financeiro();
            $customer = $financeiro->customerByRefcode($pix->cliente->id_externo);
            $sale = $sale->pix($customer->id, $pix->amountAsMoney, Carbon::now()->format('m/Y'), $pix->transaction_id, '', ['guia:' . $atendimento->first()->numero_guia]);
            $sale->save();
        } else {
            $sale = (object) [
                'id' => 0
            ];
        }

        //Incluir informação de pagamento no histórico financeiro do cliente
        $cliente = $pix->cliente;
        Cobrancas::cobrancaAutomatica($cliente, $pix->amountAsMoney, 'Pagamento de coparticipação via PIX. Guia #' . $pix->order_id, null, null, $sale->id, true, $sale->id);

        //Incluir informação em log
        $message = 'Pagamento via PIX confirmado. ' . $pixData;
        Logger::log(LogEvent::WARNING, 'pix', LogPriority::HIGH, $message, 1, 'pix', $pix->id);

        //Liberando a guia para atendimento.
        foreach($atendimento as $a) {
            $a->status = HistoricoUso::STATUS_LIBERADO;
            $a->update();
        }

        return [
            'success' => true,
            'pix' => $id
        ];
    }

    public function servicePaymentStatus(Request $request, $numeroGuia)
    {
        $pix = Pix::orderId($numeroGuia)->status(Pix::STATUS__APPROVED)->first();

        if(!$pix) {
            return [
                'approved' => false
            ];
        }

        return [
            'status' => $pix->status,
            'approved' => $pix->status === 'APPROVED'
        ];
    }
}
