<?php

namespace App\Helpers\API\LifepetIntegration\Domains\Pet;

class Pet {
    
    private $id;
    private $name;
    private $species;
    private $breedId;
    private $sex;
    private $birthdate;
    private $microchipNumber;
    private $externalId;
    private $customerId;
    private $containsPreExistingDisease;
    private $preExistingDisease;
    private $familiar;
    private $obs;
    private $active;
    private $paymentFrequency;
    private $paymentValue;
    private $paymentDueDay;
    private $participative;
    private $agreedId;
    private $petPlanId;
    private $paymentReadjustmentMonth;
    private $photo;
    private $examLast12Months;

    public $plan;

    public function __construct() {
        $this->plan = new Plan();
    }

    public function setId($data) {

        if(!is_int($data)) {
            throw new \Exception('Pet::setId: Não foi possível preencher o ID. É necessário que o valor seja um número inteiro');
        }

        $this->id = $data;
    }

    public function getId() {
        return $this->id;
    }

    public function setName(string $data) {
        $this->name = $data;
    }

    public function getName() {
        return $this->name;
    }

    public function setSpecies($data) {

        if(!in_array($data, ['CACHORRO', 'GATO'])) {
            throw new \Exception("Pet::setSpecies: Não foi possível preencher a espécie/tipo. É necessário que o valor seja 'CACHORRO' ou 'GATO' ");
        }

        $this->species = $data;
    }
    
    public function getSpecies() {
        return $this->species;
    }

    public function setBreedId($data) {

        if(!is_int($data)) {
            throw new \Exception('Pet::setBreedId: Não foi possível preencher o ID da raça. É necessário que o valor seja um número inteiro');
        }

        $this->breedId = $data;
    }

    public function getBreedId() {
        return $this->breedId;
    }

    public function setSex($data) {

        if(!in_array($data, ['M', 'F', 'ND'])) {
            throw new \Exception("Pet::setSex: Não foi possível preencher o Sexo. É necessário que o valor seja 'M', 'F' ou 'ND' ");
        }

        $this->sex = $data;
    }
    
    public function getSex() {
        return $this->sex;
    }

    public function setBirthdate($data) {

        $d = \DateTime::createFromFormat('Y-m-d', $data);

        if(!$d || $d->format('Y-m-d') != $data) {
            throw new \Exception("Pet::setBirthdate: Não foi possível preencher a data de nascimento. É necessário que o valor seja no formato Y-m-d ");
        }

        $this->birthdate = $data;
    }

    public function getBirthdate() {
        return $this->birthdate;
    }
    
    public function setMicrochipNumber($data) {

        // if(!is_numeric($data)) {
        //     throw new \Exception('Pet::setMicrochipNumber: Não foi possível preencher o Núero do Microchip. É necessário que o valor seja apenas números');
        // }

        $this->microchipNumber = $data;
    }

    public function getMicrochipNumber() {
        return $this->microchipNumber;
    }

    public function setExternalId(int $data) {

        if(!is_int($data)) {
            throw new \Exception('Pet::setExternalId: Não foi possível preencher o ID externo. É necessário que o valor seja um número inteiro');
        }

        $this->externalId = $data;
    }

    public function getExternalId() {
        
        return $this->externalId;
    }

    public function setCustomerId($data) {

        if(!is_int($data)) {
            throw new \Exception('Pet::setCustomerId: Não foi possível preencher o ID do cliente. É necessário que o valor seja um número inteiro');
        }

        $this->customerId = $data;
    }

    public function getCustomerId() {
        return $this->customerId;
    }

    public function setContainsPreExistingDisease(bool $data) {

        if(!is_bool($data)) {
            throw new \Exception("Pet::setContainsPreExistingDisease: Não foi possível preencher o campo 'Contém doença pré-existente'. É necessário que o valor seja verdadeiro ou falso");
        }

        $this->containsPreExistingDisease = $data;
    }

    public function getContainsPreExistingDisease() {
        return $this->containsPreExistingDisease;
    }

    public function setPreExistingDisease($data) {
        $this->containsPreExistingDisease = $data;
    }

    public function getPreExistingDisease() {
        return $this->containsPreExistingDisease;
    }

    public function setFamiliar(bool $data) {

        if(!is_bool($data)) {
            throw new \Exception("Pet::setFamiliar: Não foi possível preencher o campo 'Familiar'. É necessário que o valor seja verdadeiro ou falso");
        }

        $this->familiar = $data;
    }

    public function getFamiliar() {
        return $this->familiar;
    }

    public function setObs($data) {
        $this->obs = $data;
    }

    public function getObs() {
        return $this->obs;
    }

    public function setActive(bool $data) {
        if(!is_bool($data)) {
            throw new \Exception("Pet::setActive: Não foi possível preencher o campo 'Ativo'. É necessário que o valor seja verdadeiro ou falso");
        }

        $this->active = $data;
    }

    public function getActive() {
        return $this->active;
    }

    public function setPaymentFrequency($data) {

        if(!in_array($data, ['ANUAL', 'MENSAL', 'ANUAL EM 2X', 'ANUAL EM 3X', 'ANUAL EM 4X', 'ANUAL EM 5X',
                             'ANUAL EM 6X', 'ANUAL EM 7X', 'ANUAL EM 8X', 'ANUAL EM 9X', 'ANUAL EM 10X', 'ANUAL EM 11X', 'ANUAL EM 12X'])) {
            throw new \Exception("Pet::setPaymentFrequency: Não foi possível preencher o campo 'Regime'. É necessário que o valor seja 'ANUAL', 'MENSAL', 'ANUAL EM 2X ATÉ ANUAL 12X'");
        }
        $this->paymentFrequency = $data;
    }

    public function getPaymentFrequency() {
        return $this->paymentFrequency;
    }

    public function setPaymentValue($data) {

        if(!is_numeric($data)) {
            throw new \Exception('Pet::setPaymentValue: Não foi possível preencher o Valor (pagamento). É necessário que o valor seja apenas números');
        }

        $this->paymentValue = $data;
    }

    public function getPaymentValue() {
        return $this->paymentValue;
    }

    public function setPaymentDueDay(int $data) {

        if(!is_int($data)) {
            throw new \Exception('Pet::setPaymentDueDay: Não foi possível preencher o Dia de vencimento. É necessário que o valor seja um número inteiro');
        }

        $this->paymentDueDay = $data;
    }

    public function getPaymentDueDay() {
        return $this->paymentDueDay;
    }

    public function setParticipative(bool $data) {

        if(!is_bool($data)) {
            throw new \Exception('Pet::setParticipative: Não foi possível preencher o campo Participativo. É necessário que o valor seja verdadeiro ou falso');
        }

        $this->participative = $data;
    }

    public function getParticipative() {
        return $this->participative;
    }

    public function setAgreedId(int $data) {

        if(!is_int($data)) {
            throw new \Exception('Pet::setAgreedId: Não foi possível preencher o ID do conveniado. É necessário que o valor seja um número inteiro');
        }

        $this->agreedId = $data;
    }

    public function getAgreedId() {
        return $this->agreedId;
    }

    public function setPetPlanId(int $data) {

        if(!is_int($data)) {
            throw new \Exception('Pet::setPetPlanId: Não foi possível preencher o ID do plano do pet. É necessário que o valor seja um número inteiro');
        }

        $this->petPlanId = $data;
    }

    public function getPetPlanId() {
        return $this->petPlanId;
    }

    public function setPaymentReadjustmentMonth(int $data) {

        if(!is_int($data) || $data < 1 || $data > 12) {
            throw new \Exception("Pet::setPaymentReadjustmentMonth: Não foi possível preencher o campo 'Mês reajuste'. É necessário que o valor seja um número inteiro de 1 a 12");
        }

        $this->paymentReadjustmentMonth = $data;
    }

    public function getPaymentReadjustmentMonth() {
        return $this->paymentReadjustmentMonth;
    }

    public function setPhoto($data) {
        $this->photo = $data;
    }

    public function getPhoto() {
        return $this->photo;
    }

    public function setExamLast12Months(bool $data) {

        if(!is_bool($data)) {
            throw new \Exception("Pet::setExamLast12Months: Não foi possível preencher o campo 'Exame nos últimos 12 meses'. É necessário que o valor seja verdadeiro ou falso");
        }

        $this->examLast12Months = $data;
    }

    public function getExamLast12Months() {
        return $this->examLast12Months;
    }

    public function setPlan(Plan $plan) {
        $this->plan = $plan;
    }

    public function populateErrorMessage(string $message) {
        return "Erro ao preencher o OBJ Pet: " . $message;
    }

    public function populate(array $data) {
        if(!isset($data['name'])) {
            throw new \Exception($this->populateErrorMessage('O campo name (Nome do pet) não foi encontrado!'));
        }

        if(!isset($data['species'])) {
            throw new \Exception($this->populateErrorMessage('O campo species (Espécie do pet) não foi encontrado!'));
        }

        if(!isset($data['breed_id'])) {
            throw new \Exception($this->populateErrorMessage('O campo breed_id (Id da raça) não foi encontrado!'));
        }

        if(!isset($data['birthdate'])) {
            throw new \Exception($this->populateErrorMessage('O campo sex (Data de nascimento do pet) não foi encontrado!'));
        }

        if(!isset($data['active'])) {
            throw new \Exception($this->populateErrorMessage('O campo active (Ativo) não foi encontrado!'));
        }

        if(!isset($data['contains_pre_existing_disease'])) {
            throw new \Exception($this->populateErrorMessage('O campo contains_pre_existing_disease (Contém doença pré-existente) não foi encontrado!'));
        }

        if(!isset($data['exam_last_12_months'])) {
            throw new \Exception($this->populateErrorMessage('O campo exam_last_12_months (Exame nos últimos 12 meses) não foi encontrado!'));
        }

        if(!isset($data['familiar'])) {
            throw new \Exception($this->populateErrorMessage('O campo familiar (Familiar) não foi encontrado!'));
        }

        if(isset($data['customer_id'])) {
            $this->setCustomerId($data['customer_id']);
        }
        

        $this->setName($data['name']);
        $this->setSpecies($data['species']);
        $this->setBreedId($data['breed_id']);
        $this->setBirthdate($data['birthdate']);
        $this->setMicrochipNumber($data['microchip_number'] ?? 0);
        $this->setActive($data['active']);
        $this->setContainsPreExistingDisease($data['contains_pre_existing_disease']);
        $this->setFamiliar($data['familiar']);
        $this->setExamLast12Months($data['exam_last_12_months']);

        if(isset($data['external_id'])) {
            $this->setExternalId($data['external_id']);
        }
        
        if(isset($data['sex'])) {
            $this->setSex($data['sex']);
        }

        if(isset($data['pre_existing_disease'])) {
            $this->setPreExistingDisease($data['pre_existing_disease']);
        }

        if(isset($data['obs'])) {
            $this->setObs($data['obs']);
        }

        if(isset($data['payment_frequency'])) {
            $this->setPaymentFrequency($data['payment_frequency']);
        }

        if(isset($data['payment_value'])) {
            $this->setPaymentValue($data['payment_value']);
        }

        if(isset($data['payment_due_day'])) {
            $this->setPaymentDueDay($data['payment_due_day']);
        }

        if(isset($data['participative'])) {
            $this->setParticipative($data['participative']);
        }

        if(isset($data['agreed_id'])) {
            $this->setAgreedId($data['agreed_id']);
        }

        if(isset($data['pet_plan_id'])) {
            $this->setPetPlanId($data['pet_plan_id']);
        }

        if(isset($data['payment_readjustment_month'])) {
            $this->setPaymentReadjustmentMonth($data['payment_readjustment_month']);
        }

        if(isset($data['photo'])) {
            $this->setPhoto($data['photo']);
        }

    }
}
