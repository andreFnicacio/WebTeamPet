<?php

namespace App\Helpers\API\LifepetIntegration\Persistences\Finance;
use App\Helpers\API\LifepetIntegration\Domains\Customer\Customer;
use App\Helpers\API\LifepetIntegration\Domains\Pet\Pet;
use App\Http\Util\Superlogica\Client;
use App\Http\Util\Superlogica\Plans;
use Carbon\Carbon;

class Superlogica implements FinanceInterface {

    public static function createCustomer(Customer $customer) {


        $postData = [
            'ST_NOME_SAC' => $customer->getName(),
			'ST_NOMEREF_SAC' =>  $customer->getName(),
			'ST_TELEFONE_SAC' => $customer->getPhone(),
            'ST_CGC_SAC' => $customer->getCPF(),
            'ST_EMAIL_SAC' => $customer->getEmail(),
            'ST_DIAVENCIMENTO_SAC' => Carbon::now()->day,

            'ID_GRUPO_GRP' => 1,

            'ST_CEP_SAC' => $customer->address->getPostalCode(),
            'ST_ENDERECO_SAC' => $customer->address->getStreet(),
            'ST_NUMERO_SAC' => $customer->address->getNumber(),
            'ST_BAIRRO_SAC' => $customer->address->getNeighborhood(),
            'ST_COMPLEMENTO_SAC' => $customer->address->getComplement(),
            'ST_CIDADE_SAC' => $customer->address->getCity(),
            'ST_ESTADO_SAC' => $customer->address->getState(),
            'FL_PAGAMENTOPREF_SAC' => 0
        ];

        $response = (new Client())->register($postData);

		if (!isset($response->status) || $response->status != "200") {
			throw new FinanceException($response->msg ?? 'Falha ao cadastrar o cliente no sistema financeiro');
		}

		if(!isset($response->data) || !isset($response->data->id_sacado_sac)) {
			throw new FinanceException('Falha ao cadastrar o cliente no sistema financeiro. Não foi encontrado o ID do cliente cadastrado.');
		}
		
        return $response->data->id_sacado_sac;
    }

    public static function createSubscription(Customer $costumer, Pet $pet, int $superlogicaPlanId, $liquidateFirstCharge = false) {
        if(!$costumer->getExternalId()) {
			throw new FinanceException('Não foi possível cadastrar a assinatura. Cliente sem vinculo com o sistema financeiro');
		}
		
        $dados =  [
			'PLANOS' => [],
			'OPCIONAIS' => []
        ];
        
		$dtInitContract = \DateTime::createFromFormat('Y-m-d', $pet->plan->getDateInitContract());

		if(!isset($dtInitContract)) {
			throw new FinanceException('Não foi possível cadastrar a assinatura. A data de início do contrato está inválida.');
		}

		$dados['PLANOS'][] = [
			'ID_SACADO_SAC' => $costumer->getExternalId(),
			'DT_CONTRATO_PLC' => $dtInitContract->format('m/d/Y'),
			"ST_IDENTIFICADOREXTRA_PLC" => '',
			"ST_IDENTIFICADOR_PLC" => $pet->getName() . '_ecommerce_lifepet_' . time(),
			"ID_PLANO_PLA" => $superlogicaPlanId,
			"FL_TRIAL_PLC" => 0,
			"FL_MULTIPLO_COMPO" => 1,
			"FL_NOTIFICARCLIENTE" => 0
        ];
        
        // Cobrança da Mensalidade/Anuidade
		$dados['OPCIONAIS'][] = [
			"ID_PRODUTO_PRD" => $pet->getPaymentFrequency() == 'ANUAL' ? '3' : '999999982',
			"SELECIONAR_PRODUTO" => 1,
			"NM_QNTD_PLP" => 1,
			"valor_unitario" => $pet->plan->getPaymentValue(),
			"FL_RECORRENTE_PLP" => $pet->getPaymentFrequency() == 'ANUAL' ? 0 : 1
        ];
        
        // Taxa de adesão
        if($pet->plan->getMembershipFee() > 0) {
			$dados['OPCIONAIS'][] = [
				"ID_PRODUTO_PRD" => "999999983",
				"SELECIONAR_PRODUTO" => 1,
				"NM_QNTD_PLP" => 1,
				"valor_unitario" => $pet->plan->getMembershipFee(),
				"FL_RECORRENTE_PLP" => 0
			];
		}

		
		$plans = new Plans();
		$response = $plans->sign($dados);

		if (!isset($response->status) || $response->status != "200") {
			throw new FinanceException($response->msg ?? 'Falha ao cadastrar a assinatura no sistema financeiro');
		}

		if(!isset($response->data) || !isset($response->data->id_sacado_sac)) {
			throw new FinanceException('Falha ao cadastrar a assinatura no sistema financeiro. Não foi encontrado o ID da assinatura.');
		}

		if($liquidateFirstCharge) {
			if(!isset($response->data->id_recebimento_recb)) {
				throw new FinanceException('Não foi encontrado o ID da primeira cobrança da assinatura cadastrada.');
			}

			self::liquidateCharge($response->data->id_recebimento_recb, Carbon::today()->format('Y-m-d'), $pet->plan->getPaymentValue());
		}

		return $response->data->id_planocliente_plc;
	}

	public static function liquidateCharge($chargeId, $date, $value) {

		$plans = new Plans();

		$date = \DateTime::createFromFormat('Y-m-d', $date);

		if(!isset($date)) {
			throw new FinanceException('Não foi possível liquidar a cobrança. A data de liquidação está inválida.');
		}

		$response = $plans->liquidateCharge([
			'ID_RECEBIMENTO_RECB' => $chargeId,
			'DT_LIQUIDACAO_RECB' => $date->format('m/d/Y'),
			'DT_RECEBIMENTO_RECB' => $date->format('m/d/Y'),
			'VL_TOTAL_RECB' => $value,
		]);

		if (!isset($response[0]->status) || ($response[0]->status != "201" && $response[0]->status != "200")) {
			throw new FinanceException('Falha ao liquidar a cobrança ('.$chargeId.') no sistema financeiro. ' . ($response->msg ?? null));
		}

		return true;
	}
	
}