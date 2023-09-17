<?php


namespace App\Helpers\API\Superlogica\V2;


use App\Helpers\API\Superlogica\V2\Domain\Models\Billing;
use App\Helpers\API\Superlogica\V2\Exceptions\InvalidChargeInvalidationReason;
use App\Helpers\API\Superlogica\V2\Transformers\Billing as BillingData;
use App\Helpers\API\Superlogica\V2\Utils\Date;
use App\Helpers\Utils;
use Carbon\Carbon;

class Charge
{
    const ENDPOINT = '/v2/financeiro/cobranca';

    const INVALIDATE_REASON__IMPROBER_BILLING = 0;
    const INVALIDATE_REASON__BILL_REPLACED = 1;
    const INVALIDATE_REASON__BILL_CANCELED = 2;
    const INVALIDATE_REASON__BONUS = 3;
    const INVALIDATE_REASON__SUSPENSION = 4;
    const INVALIDATE_REASON__OTHER = 5;
    const INVALIDATE_REASON__PROPOSAL_CANCELED = 6;
    const INVALIDATE_REASON__PDD = 7;
    const INVALIDATE_REASON__NEGOTIATION = 8;
    const INVALIDATE_REASON__INSUFICIENT_PAYMENT = 9;
    const INVALIDATE_REASON__SIGNATURE_ATIVATION = 10;

    private $client;

    public function __construct()
    {
        $this->client = Client::getInstance();
    }

    /**
     * @throws Exceptions\InvalidCallException|InvalidChargeInvalidationReason
     */
    public function invalidate($id, int $reason, $dataPrimeiroPagamento = null)
    {
        if($reason < self::INVALIDATE_REASON__IMPROBER_BILLING || $reason > self::INVALIDATE_REASON__SIGNATURE_ATIVATION) {
            throw new InvalidChargeInvalidationReason($reason);
        }

        $params = [
            'form_params' => [
                'ID_RECEBIMENTO_RECB' => $id,
                'FL_STATUS_RECB' => 2,
                'FL_MOTIVOCANCELAR_RECB' => $reason
            ]
        ];

        if($reason === self::INVALIDATE_REASON__SIGNATURE_ATIVATION) {
            $params['form_params'] = array_merge($params['form_params'], [
                'ALTERAR_MOTIVO' => 1,
                'FL_PRIMEIROPAGNOTIFICADO_PLC' => 0,
                'DT_PRIMEIROPAG_PLC' => $dataPrimeiroPagamento ?? Carbon::now()->format(Date::FORMAT),
            ]);
        }

        try {
            $response = $this->client->put(self::ENDPOINT, $params);
        } catch (\Exception $e) {
            dd($e->getMessage() . "\n" . json_encode($params));
        }
    }

    public function getCharge($id)
    {
        $params = [
            'query' => [
                'id' => $id,
                'exibirComposicaoDosBoletos' => 1
            ]
        ];

        try {
            $response = $this->client->get(self::ENDPOINT, $params);
            if($response && is_array($response) && isset($response[0])) {
                return $response[0];
            }

            return null;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function find($id, $show_compositions = 1)
    {
        $params = [
            'query' => [
                'id' => $id,
                'exibirComposicaoDosBoletos' => $show_compositions
            ]
        ];

        try {
            $response = $this->client->get(self::ENDPOINT, $params);
            if($response && is_array($response) && isset($response[0])) {
                return $response[0];
            }

            return null;
        } catch (\Exception $e) {
            dd($e->getMessage() . "\n" . json_encode($params));
        }
    }

    /**
     * @param Transformers\Billing $billing
     */
    public function create(BillingData $billing)
    {

        $params = [
            'query' => $billing->toArray()
        ];

        try {
            $response = $this->client->post(self::ENDPOINT, $params);
            if($response && is_array($response) && isset($response[0])) {
                return $response[0];
            }

            return null;
        } catch (\Exception $e) {
            dd($e->getMessage() . "\n" . json_encode($params));
        }

    }

    public function Schedule(Billing $billing)
    {

        $billing->fl_cieloforcarpagamento_recb = 1;
        $params = [
            'form_params' => [
                'ID_RECEBIMENTO_RECB' => $billing->id_recebimento_recb,
                'FL_CIELOFORCARPAGAMENTO_RECB' => $billing->fl_cieloforcarpagamento_recb
            ]
        ];


        try {
            $response = $this->client->put(self::ENDPOINT, $params);
            if($response && is_array($response) && isset($response[0])) {
                return $response[0];
            }

            return null;
        } catch (\Exception $e) {
            dd($e->getMessage() . "\n" . json_encode($params));
        }

    }

    public function reverse(Billing $billing)
    {
        $billing->fl_status_recb = 0;
        $params = [
            'form_params' => [
                'ID_RECEBIMENTO_RECB' => $billing->id_recebimento_recb,
                'FL_STATUS_RECB' => $billing->fl_status_recb
            ]
        ];


        try {
            $response = $this->client->put(self::ENDPOINT . "/estornar", $params);
            if($response && is_array($response) && isset($response[0])) {
                return $response[0];
            }

            return null;
        } catch (\Exception $e) {
            dd($e->getMessage() . "\n" . json_encode($params));
        }

    }

    public function settler(Billing $billing)
    {
        $params = [
            'form_params' => [
                'ID_RECEBIMENTO_RECB' => $billing->id_recebimento_recb,
                'DT_LIQUIDACAO_RECB' => $billing->dt_liquidacao_recb,
                'DT_RECEBIMENTO_RECB' => $billing->dt_recebimento_recb,
                'VL_TOTAL_RECB' => $billing->vl_total_recb,
            ]
        ];


        try {
            $response = $this->client->put(self::ENDPOINT . "/liquidar", $params);
            if($response && is_array($response) && isset($response[0])) {
                return $response[0];
            }

            return null;
        } catch (\Exception $e) {
            dd($e->getMessage() . "\n" . json_encode($params));
        }

    }
}