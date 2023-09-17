<?php

namespace App\Helpers\API\LifepetIntegration\Repositories;
use App\Helpers\API\LifepetIntegration\Domains\Pet\Pet;
use App\Helpers\API\LifepetIntegration\Domains\Pet\Plan;
use App\Models\PetsPlanos;

class PetPlanRepository {

    public function save(Plan $plan, $transition = null)  : int {


        $petPlan = new PetsPlanos();

		if($plan->getId() == null) {
            $petPlan = new PetsPlanos();
        } else {
            $petPlan = PetsPlanos::find($plan->getId());
		}
		
        $dateInitContract = \DateTime::createFromFormat('Y-m-d', $plan->getDateInitContract());
        $dateEndContract = \DateTime::createFromFormat('Y-m-d', $plan->getDateEndContract());

		if($plan->getPetId() != null) {
			$petPlan->id_pet = $plan->getPetId();
		}

		if($plan->getPlanId() != null) {
			$petPlan->id_plano = $plan->getPlanId();
		}

		if($plan->getPaymentValue() != null) {
			$petPlan->valor_momento = $plan->getPaymentValue();
		}

		if($dateInitContract != null) {
			$petPlan->data_inicio_contrato = $dateInitContract->format('d/m/Y');
		}

		if($dateEndContract != null) {
			$petPlan->data_encerramento_contrato = $dateEndContract->format('d/m/Y');
		}

		if($plan->getParticipative() !== null) {
			$petPlan->participativo = $plan->getParticipative() === true ? 1 : 0;
		}

		if($plan->getSellerId() != null) {
			$petPlan->id_vendedor = $plan->getSellerId();
		}

		if($plan->getStatus() != null) {
			$petPlan->status = $plan->getStatus();
		}

		if($plan->getMembershipFee() != null) {
			$petPlan->adesao = $plan->getMembershipFee();
		}

		if($transition) {
		    $petPlan->transicao = $transition;
        }

		$petPlan->save();

        return $petPlan->id;
        
	}
	
	public function getBy($field, $value) {
		$petPlan = PetsPlanos::where($field, $value);
		if($petPlan->count() == 0) {
			return [];
		}
		return $this->adapt($petPlan->first());
	}

    public function getById(int $id) {
		$petPlan = PetsPlanos::find($id);
		return $this->adapt($petPlan);
	}

	public function adapt(PetsPlanos $plan) {
		$petPlanObj = new Plan();
		$petPlanObj->populate([
			'id' => $plan->id,
			'pet_id' => $plan->id_pet,
            'plan_id' => $plan->id_plano,
            'payment_value' => $plan->valor_momento,
            'date_init_contract' => $plan->data_inicio_contrato->format('Y-m-d'),
            'date_end_contract' => (isset($plan->data_encerramento_contrato) ? $plan->data_encerramento_contrato->format('Y-m-d') : null),
			'participative' => $plan->participativo,
			'status' => $plan->status,
			'membership_fee' => $plan->adesao,
			'seller_id' => $plan->id_vendedor
		]);

		return $petPlanObj;
	}
	

    


}