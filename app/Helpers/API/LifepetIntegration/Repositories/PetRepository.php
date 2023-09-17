<?php

namespace App\Helpers\API\LifepetIntegration\Repositories;
use App\Helpers\API\LifepetIntegration\Domains\Pet\Pet;
use App\Models\Pets;

class PetRepository {

    public function save(Pet $pet)  : int {
        
        if($pet->getId() == null) {
            $pets = new Pets();
        } else {
            $pets = Pets::find($pet->getId());
        }
    
        $birthdate = \DateTime::createFromFormat('Y-m-d', $pet->getBirthdate());

        if($pet->getName() != null) {
			$pets->nome_pet = $pet->getName();
        }
        
        if($pet->getSpecies() != null) {
			$pets->tipo = $pet->getSpecies();
        }
        
        if($pet->getExternalId() != null) {
			$pets->id_externo = $pet->getExternalId();
		}

        if($pet->getMicrochipNumber() != null) {
			$pets->numero_microchip = $pet->getMicrochipNumber();
        }

        if($pet->getSex() != null) {
			$pets->sexo = $pet->getSex();
        }

        if($birthdate != null) {
			$pets->data_nascimento = $birthdate->format('d/m/Y');
        }

        if($pet->getCustomerId() != null) {
			$pets->id_cliente = $pet->getCustomerId();
        }

        if($pet->getContainsPreExistingDisease() != null) {
			$pets->contem_doenca_pre_existente = $pet->getContainsPreExistingDisease();
        }

        if($pet->getPreExistingDisease() != null) {
			$pets->doencas_pre_existentes = $pet->getPreExistingDisease();
        }

        if($pet->getFamiliar() !== null) {
			$pets->familiar = $pet->getFamiliar() === true ? 1 : 0;
        }

        if($pet->getObs() != null) {
			$pets->observacoes = $pet->getObs();
        }

        if($pet->getActive() !== null) {
			$pets->ativo = $pet->getActive() === true ? 1 : 0;
        }
        
        if($pet->getPaymentFrequency() != null) {
			$pets->regime = $pet->getPaymentFrequency();
        }

        if($pet->getPaymentValue() != null) {
			$pets->valor = $pet->getPaymentValue();
        }

        if($pet->getPaymentDueDay() != null) {
			$pets->vencimento = $pet->getPaymentDueDay();
        }

        if($pet->getParticipative() !== null) {
			$pets->participativo = $pet->getParticipative() === true ? 1 : 0;
        }

        if($pet->getAgreedId() != null) {
			$pets->id_conveniado = $pet->getAgreedId();
        }

        if($pet->getBreedId() != null) {
			$pets->id_raca = $pet->getBreedId();
        }

        if($pet->getPetPlanId() != null) {
			$pets->id_pets_planos = $pet->getPetPlanId();
        }

        if($pet->getPaymentReadjustmentMonth() != null) {
			$pets->mes_reajuste = $pet->getPaymentReadjustmentMonth();
        }

        if($pet->getPhoto() != null) {
			$pets->foto = $pet->getPhoto();
        }

        if($pet->getExamLast12Months() != null) {
			$pets->exame_ultimos_12_meses = $pet->getExamLast12Months();
        }
        

		$pets->save();

        return $pets->id;
	}
	
	public function getBy($field, $value) {
		$pet = Pets::where($field, $value);
		if($pet->count() == 0) {
			return [];
        }
        
        $pets = $pet->get();

        $petsObj = [];
        foreach($pets as $pet) {
            $petsObj[] = $this->adapt($pet);
        }
        
		return $petsObj;
	}

    public function getById(int $id) {
		$pet = Pets::find($id);
		return $this->adapt($pet);
	}

	public function adapt(Pets $pet) {

		$petObj = new Pet();
		$petObj->populate([
			'id' => $pet->id,
			'name' => $pet->nome_pet,
            'species' => $pet->tipo,
            'breed_id' => $pet->id_raca,
            'sex' => $pet->sexo,
            'birthdate' => $pet->data_nascimento->format('Y-m-d'),
            'microchip_number' => (!empty($pet->numero_microchip) ? $pet->numero_microchip : 0),
            'external_id' => $pet->id_externo,
            'customer_id' => $pet->id_cliente,
            'contains_pre_existing_disease' => $pet->contem_doenca_pre_existente,
            'pre_existing_disease' => $pet->doencas_pre_existentes,
            'familiar' => $pet->familiar,
            'obs' => $pet->observacoes,
            'active' => $pet->ativo,
            'payment_frequency' => $pet->regime,
            'payment_value' => $pet->valor,
            'payment_due_date' => $pet->data_vencimento,
            'participative' => $pet->participativo,
            'agreed_id' => $pet->id_conveniado,
            'pet_plan_id' => $pet->id_pets_planos,
            'payment_readjustment_month' => $pet->mes_reajuste,
            'photo' => $pet->foto,
            'exam_last_12_months' => $pet->exame_ultimos_12_meses
		]);

	
		return $petObj;
	}
	

    


}