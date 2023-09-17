<?php


namespace App\Helpers\API\Superlogica\V2;


use App\Helpers\API\Superlogica\V2\Transformers\MontlyPlan;
use App\Helpers\API\Superlogica\V2\Transformers\YearlyPlan;
use App\Models\Planos;

class Plan
{
    const ENDPOINT = '/v2/financeiro/planos';

    private $client;

    public function __construct()
    {
        $this->client = Client::getInstance();
    }

    public function register(Planos $plano): array
    {
        $yearly = $this->registerYearlyPlan($plano);
        $monthly = $this->registerMonthlyPlan($plano);

        return [
            'yearly' => $yearly,
            'monthly' => $monthly
        ];
    }

    private function registerMonthlyPlan(Planos $plan)
    {
        if($plan->id_superlogica) {
            return $plan->id_superlogica;
        }

        $transformed = new MontlyPlan($plan);

        $response = $this->client->post(self::ENDPOINT, [
            'form_params' => $transformed->toArray()
        ]);

        if(!empty($response)) {

            $found = $response[0];
            $plan->id_superlogica = $found->data->id_plano_pla;
            $plan->update();
            return $plan->id_superlogica;
        }

        return null;
    }

    private function registerYearlyPlan(Planos $plan)
    {
        if($plan->id_superlogica_anual) {
            return $plan->id_superlogica_anual;
        }

        $transformed = new YearlyPlan($plan);

        $response = $this->client->post(self::ENDPOINT, [
            'form_params' => $transformed->toArray()
        ]);

        if(!empty($response)) {
            $found = $response[0];
            $plan->id_superlogica_anual = $found->data->id_plano_pla;
            $plan->update();
            return $plan->id_superlogica_anual;
        }

        return null;
    }

    /**
     * Ativa boletos em planos existentes
     * @param Planos $planos
     * @param bool $status
     * @throws Exceptions\InvalidCallException
     */
    public function setBoleto(Planos $planos, bool $status = true)
    {
        if($planos->id_superlogica) {
            $this->client->put(self::ENDPOINT, [
                'form_params' => [
                    'ID_PLANO_PLA' => $planos->id_superlogica,
                    'FL_BOLETO_PLA' => (int) $status
                ]
            ]);
        }

        if($planos->id_superlogica_anual) {
            $this->client->put(self::ENDPOINT, [
                'form_params' => [
                    'ID_PLANO_PLA' => $planos->id_superlogica_anual,
                    'FL_BOLETO_PLA' => (int) $status
                ]
            ]);
        }
    }

    /**
     * Ativa boletos em planos existentes
     * @param Planos $planos
     * @param bool $status
     * @throws Exceptions\InvalidCallException
     */
    public function setMultipleSignatures(Planos $planos, bool $status = true)
    {
        if($planos->id_superlogica) {
            $this->client->put(self::ENDPOINT, [
                'form_params' => [
                    'ID_PLANO_PLA' => $planos->id_superlogica,
                    'FL_MULTIPLOSIDENTIFICADORES_PLA' => (int) $status
                ]
            ]);
        }

        if($planos->id_superlogica_anual) {
            $this->client->put(self::ENDPOINT, [
                'form_params' => [
                    'ID_PLANO_PLA' => $planos->id_superlogica_anual,
                    'FL_MULTIPLOSIDENTIFICADORES_PLA' => (int) $status
                ]
            ]);
        }
    }
}