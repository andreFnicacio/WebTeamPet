<?php


namespace App\Helpers\API\Superlogica\V2;


use App\Helpers\API\LifepetIntegration\Domains\Pet\Pet;
use App\Helpers\API\Superlogica\V2\Domain\Models\CreditCard;
use App\Helpers\API\Superlogica\V2\Transformers\MonthlySignature;
use App\Helpers\API\Superlogica\V2\Transformers\YearlySignature;
use App\Helpers\API\Superlogica\V2\Utils\Date;
use App\Models\Clientes;
use App\Models\Pets;
use App\Models\PetsPlanos;
use App\Models\Planos;
use Carbon\Carbon;
use Exception;

class Signature
{
    const ENDPOINT = '/v2/financeiro/assinaturas';

    private $client;

    public function __construct()
    {
        $this->client = Client::getInstance();
    }

    /**
     * @throws Exceptions\InvalidChargeInvalidationReason
     * @throws Exceptions\InvalidCallException
     * @throws CreditCardRequiredException|Exceptions\IdDidNotMatchAnyCustomer
     * @throws Exception
     */
    public function sign(Pets $pet, $firstCharge = true, CreditCard $credidCard = null, $forceCreditCardInsertion = false)
    {
        $customerService = new Customer();
        if(!$pet->cliente->id_superlogica) {
            $customerService->createFromCustomerData($pet->cliente);
        }

        //Add card
        if($pet->cliente->forma_pagamento === Clientes::FORMA_PAGAMENTO_CARTAO) {
            //TODO: Verificar se já há um cartão anterior

            if($forceCreditCardInsertion) {
                //Verificar se há um novo cartão sendo informado
                if(!$credidCard) {
                    throw new CreditCardRequiredException();
                }

                //Adicionar cartão novo
                $paymentService = new PaymentMethod();
                $paymentService->addCard($pet->cliente->id_superlogica, $credidCard);
            }
        }

        if($pet->isRegimeAnual()) {
            $signatureTransformed = new YearlySignature($pet);
        } else {
            $signatureTransformed = new MonthlySignature($pet);
        }

        //Checar se já existe assinatura existente
        $recurrenceService = new Recurrence();
        $signatureId = $recurrenceService->alreadyHasSignature($pet);
        if($signatureId) {
            $this->updateCurrentPetSignature($pet, $signatureId);
            return $signatureId;
        }

        $response = $this->client->post(self::ENDPOINT, [
            'form_params' => $signatureTransformed->toArray()
        ]);

        if(!empty($response)) {
            $this->updateLocalSignatureInfo($response[0], $pet);

            if(!$firstCharge) {
                $this->cancelFirstCharge($pet);
            }
            return $response[0]->data->id_planocliente_plc;
        }

        return null;
    }

    /**
     * Altera os dados no Superlógica de acordo com as modificações do ERP
     *
     * @param PetsPlanos $pp
     * @return false|mixed
     * @throws Exceptions\InvalidCallException
     */
    public function sync(PetsPlanos $pp)
    {
        if(!$pp->id_contrato_superlogica) {
            return $this->sign($pp->pet, true);
        }

        $pet = $pp->pet;
        $dadosCliente = [
            'CLIENTES' => [
                $pp->pet->cliente->id_superlogica
            ],
        ];
        $dadosContrato = [
            'ID_PLANOCLIENTE_PLC' => $pp->id_contrato_superlogica,
            'ST_VALOR_MENS' => number_format($pet->getValorPlano(), 2, '.', ''),
            'ID_PRODUTO_PRD' => $pet->isRegimeAnual() ? YearlySignature::ID_PRODUTO_ANUALIDADE : MonthlySignature::ID_PRODUTO_MENSALIDADE,
            'DT_INICIO_MENS' => $pp->data_inicio_contrato->format(Date::FORMAT),
            'DT_VENCIMENTO_CONTRATO' => $pp->pet->cliente->dia_vencimento
        ];

        if($pp->data_encerramento_contrato) {
            $dadosContrato = array_merge($dadosContrato, [
                'DT_FIM_MENS' => $pp->data_encerramento_contrato->format(Date::FORMAT)
            ]);
        }

        $dadosRequisicao = array_merge($dadosCliente, $dadosContrato);

        $response = $this->client->put(self::ENDPOINT, [
            'form_params' => $dadosRequisicao
        ]);

        return $response;
    }

    /**
     * Cancela assinatura do Superlógica
     * @param PetsPlanos $pp
     * @param Carbon|null $canceledAt
     * @param bool $cancelNow
     * @throws Exceptions\InvalidCallException
     */
    public function cancel(PetsPlanos $pp, Carbon $canceledAt = null, $cancelNow = true)
    {
        if(!$pp->id_contrato_superlogica) {
            return;
        }

        if(!$canceledAt) {
            $canceledAt = now();
        }

        return $this->client->put(self::ENDPOINT, [
            'form_params' => [
                'ID_PLANOCLIENTE_PLC' => $pp->id_contrato_superlogica,
                'DT_CANCELAMENTO_PLC' => $canceledAt->format(Date::FORMAT),
                'FL_CANCELAMENTOIMEDIATO' => (int) $cancelNow
            ]
        ]);
    }

    /**
     * @throws Exceptions\InvalidCallException|Exceptions\InvalidChargeInvalidationReason
     */
    public function cancelFirstCharge(Pets $pet)
    {
        $pp = $pet->petsPlanosAtual()->first();
        $chargeService = new Charge();
        //Verificar se a cobrança está aberta.
        $charge = $chargeService->getCharge($pp->id_primeira_cobranca_superlogica);
        if($charge) {
            $status = (int) $charge->fl_status_recb;
            if($status > 0) {
                return;
            }
        }

        $chargeService->invalidate($pp->id_primeira_cobranca_superlogica, Charge::INVALIDATE_REASON__SIGNATURE_ATIVATION);
    }

    /**
     * @param Pets $pet
     * @param $signatureId
     */
    public function updateCurrentPetSignature(Pets $pet, $signatureId): void
    {
        $pp = $pet->petsPlanosAtual()->first();
        $pp->id_contrato_superlogica = $signatureId;
        $pp->update();
    }

    /**
     * @param $response
     * @param Pets $pet
     */
    private function updateLocalSignatureInfo($response, Pets $pet)
    {
        $id_contrato = $response->data->id_planocliente_plc;
        $pp = $pet->petsPlanosAtual()->first();
        $pp->id_contrato_superlogica = $id_contrato;
        if(isset($response->data->id_recebimento_recb)) {
            $pp->id_primeira_cobranca_superlogica = $response->data->id_recebimento_recb;
        }

        $pp->update();
    }
}