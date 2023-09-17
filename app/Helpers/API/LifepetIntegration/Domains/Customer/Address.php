<?php

namespace App\Helpers\API\LifepetIntegration\Domains\Customer;

class Address {

    private $postalCode;
    private $street;
    private $number;
    private $complement;
    private $neighborhood;
    private $city;
    private $state;

    public function setPostalCode($data) {
        $this->postalCode = $data;
    }

    public function getPostalCode() {
        return $this->postalCode;
    }

    public function setStreet($data) {
        $this->street = $data;
    }

    public function getStreet() {
        return $this->street;
    }

    public function setNumber($data) {
        $this->number = substr($data, 0, 10);
    }

    public function getNumber() {
        return $this->number;
    }

    public function setComplement($data) {
        $this->complement = $data;
    }

    public function getComplement() {
        return $this->complement;
    }

    public function setNeighborhood($data) {
        $this->neighborhood = $data;
    }

    public function getNeighborhood() {
        return $this->neighborhood;
    }

    public function setCity($data) {
        $this->city = $data;
    }

    public function getCity() {
        return $this->city;
    }

    public function setState($data) {
        $this->state = $data;
    }

    public function getState() {
        return $this->state;
    }

    public function populateErrorMessage(string $message) {
        return "Erro ao preencher o OBJ Customer\Address: " . $message;
    }

    public function populate(array $data) {

        if(!isset($data['postal_code'])) {
            throw new \Exception($this->populateErrorMessage('O campo postal_code (CEP) não foi encontrado!'));
        }

        if(!isset($data['street'])) {
            throw new \Exception($this->populateErrorMessage('O campo street (Rua) não foi encontrado!'));
        }

        if(!isset($data['number'])) {
            throw new \Exception($this->populateErrorMessage('O campo number (Número) não foi encontrado!'));
        }

        if(!isset($data['neighborhood'])) {
            throw new \Exception($this->populateErrorMessage('O campo neighborhood (Bairro) não foi encontrado!'));
        }

        if(!isset($data['city'])) {
            throw new \Exception($this->populateErrorMessage('O campo city (Cidade) não foi encontrado!'));
        }

        if(!isset($data['state'])) {
            throw new \Exception($this->populateErrorMessage('O campo state (Estado) não foi encontrado!'));
        }

        $this->setPostalCode($data['postal_code']);
        $this->setStreet($data['street']);
        $this->setNumber($data['number']);
        $this->setNeighborhood($data['neighborhood']);
        $this->setCity($data['city']);
        $this->setState($data['state']);
        
        if(isset($data['complement'])) {
            $this->setComplement($data['complement']);
        }

    }

}