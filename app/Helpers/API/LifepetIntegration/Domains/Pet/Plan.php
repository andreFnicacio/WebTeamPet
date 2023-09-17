<?php

namespace App\Helpers\API\LifepetIntegration\Domains\Pet;

class Plan {

    private $id;
    private $petId;
    private $planId;
    //private $paymentFrequency;
    private $paymentValue;
    private $dateInitContract;
    private $dateEndContract;
    private $participative;
    private $sellerId;
    private $status;
    private $membershipFee;

    public function setId(int $data) {

        if(!is_int($data)) {
            throw new \Exception('Plan::setId: Não foi possível preencher o ID. É necessário que o valor seja um número inteiro');
        }

        $this->id = $data;
    }

    public function getId() {
        return $this->id;
    }

    public function setPetId(int $data) {

        if(!is_int($data)) {
            throw new \Exception('Plan::setPetId: Não foi possível preencher o ID do pet. É necessário que o valor seja um número inteiro');
        }

        $this->petId = $data;
    }

    public function getPetId() {
        return $this->petId;
    }

    public function setPlanId($data) {

        if(!is_int($data)) {
            throw new \Exception('Plan::setPlanId: Não foi possível preencher o ID do plano escolhido. É necessário que o valor seja um número inteiro');
        }

        $this->planId = $data;
    }

    public function getPlanId() {
        return $this->planId;
    }

    // public function setPaymentFrequency($data) {

    //     if(!is_int($data)) {
    //         throw new \Exception('Plan::setPlanId: Não foi possível preencher o ID do plano escolhido. É necessário que o valor seja um número inteiro');
    //     }

    //     $this->paymentFrequency = $data;
    // }

    // public function getPaymentFrequency() {
    //     return $this->paymentFrequency;
    // }

    public function setPaymentValue($data) {
        $this->paymentValue = $data;
    }

    public function getPaymentValue() {
        return $this->paymentValue;
    }

    public function setDateInitContract($data) {
        $d = \DateTime::createFromFormat('Y-m-d', $data);

        if(!$d || $d->format('Y-m-d') != $data) {
            throw new \Exception("Plan::setDateInitContract: Não foi possível preencher a data de nascimento. É necessário que o valor seja no formato Y-m-d ");
        }

        $this->dateInitContract = $data;
    }

    public function getDateInitContract() {
        return $this->dateInitContract;
    }

    public function setDateEndContract($data) {
        $d = \DateTime::createFromFormat('Y-m-d', $data);

        if(!$d || $d->format('Y-m-d') != $data) {
            throw new \Exception("Plan::setDateInitContract: Não foi possível preencher a data de nascimento. É necessário que o valor seja no formato Y-m-d ");
        }
        
        $this->dateEndContract = $data;
    }

    public function getDateEndContract() {
        return $this->dateEndContract;
    }

    public function setParticipative($data) {
        $this->participative = $data;
    }

    public function getParticipative() {
        return $this->participative;
    }

    public function setSellerId($data) {
        $this->sellerId = $data;
    }

    public function getSellerId() {
        return $this->sellerId;
    }

    public function setStatus($data) {
        $this->status = $data;
    }

    public function getStatus() {
        return $this->status;
    }

    public function setMembershipFee($data) {
        $this->membershipFee = $data;
    }

    public function getMembershipFee() {
        return $this->membershipFee;
    }

    public function populateErrorMessage(string $message) {
        return "Erro ao preencher o OBJ Pet\Plan: " . $message;
    }

    public function populate(array $data) {

    

        if(!isset($data['plan_id'])) {
            throw new \Exception($this->populateErrorMessage('O campo plan_id (Id do plano) não foi encontrado!'));
        }

        // if(!isset($data['payment_frequency'])) {
        //     throw new \Exception($this->populateErrorMessage('O campo payment_frequency (Frequência de pagamento) não foi encontrado!'));
        // }

        if(!isset($data['payment_value'])) {
            throw new \Exception($this->populateErrorMessage('O campo payment_value (Valor de pagamento) não foi encontrado!'));
        }

        if(!isset($data['date_init_contract'])) {
            throw new \Exception($this->populateErrorMessage('O campo date_init_contract (Data de início do contrato) não foi encontrado!'));
        }

        $this->setPlanId($data['plan_id']);
        //$this->setPaymentFrequency($data['payment_frequency']);
        $this->setPaymentValue($data['payment_value']);
        $this->setDateInitContract($data['date_init_contract']);
        
        if(isset($data['id'])) {
            $this->setId($data['id']);
        }

        if(isset($data['pet_id'])) {
            $this->setPetId($data['pet_id']);
        }

        if(isset($data['date_end_contract'])) {
            $this->setDateEndContract($data['date_end_contract']);
        }

        if(isset($data['participative'])) {
            $this->setParticipative($data['participative']);
        }

        if(isset($data['seller_id'])) {
            $this->setSellerId($data['seller_id']);
        }

        if(isset($data['status'])) {
            $this->setStatus($data['status']);
        }

        if(isset($data['membership_fee'])) {
            $this->setMembershipFee($data['membership_fee']);
        }

    }

}