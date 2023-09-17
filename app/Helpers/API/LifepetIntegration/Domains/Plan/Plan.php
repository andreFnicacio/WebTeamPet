<?php

namespace App\Helpers\API\LifepetIntegration\Domains\Plan;

class Plan {

    private $id;
    private $name;
    private $externalId;
    private $externalAnualId;

    public function setId(int $data) {
        $this->id = $data;
    }

    public function getId() {
        return $this->id;
    }

    public function setName($data) {
        $this->name = $data;
    }

    public function getName() {
        return $this->name;
    }

    public function setExternalId(int $data) {
        $this->externalId = $data;
    }

    public function getExternalId() {
        return $this->externalId;
    }

    public function setExternalAnualId(int $data) {
        $this->externalAnualId = $data;
    }

    public function getExternalAnualId() {
        return $this->externalAnualId;
    }

    public function setActive(bool $data) {
        $this->active = $data;
    }

    public function getActive() {
        return $this->active;
    }

    public function populateErrorMessage(string $message) {
        return "Erro ao preencher o OBJ Plan: " . $message;
    }

    public function populate(array $data) {
        if(!isset($data['name'])) {
            throw new \Exception($this->populateErrorMessage('O campo name não foi encontrado!'));
        }

        if(!isset($data['active'])) {
            throw new \Exception($this->populateErrorMessage('O campo active (Id do plano anual externo) não foi encontrado!'));
        }


        if(isset($data['id'])) {
            $this->setId($data['id']);
        }

        $this->setName($data['name']);
        //$this->setPaymentFrequency($data['payment_frequency']);

        if(isset($data['external_id'])) {
            $this->setExternalId($data['external_id']);
        }

        if(isset($data['external_anual_id'])) {
            $this->setExternalAnualId($data['external_anual_id']);
        }
       
        $this->setActive($data['active']);
    }
}