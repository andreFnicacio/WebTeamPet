<?php

namespace App\Helpers\API\LifepetIntegration\Domains\Customer;
use App\Helpers\API\LifepetIntegration\Domains\Customer\Address;

class Customer {

    private $id;
    private $name;
    private $email;
    private $cpf;
    private $birthdate;
    private $telephone;
    private $active;
    private $approved;
    private $rg;
    private $contractNumber;
    private $landline;
    private $externalId;
    private $maritalStatus;
    private $obs;
    private $paymentValue;
    private $paymentDueDay;
    private $participative;
    private $agreedId;
    private $userId;
    private $firstAccess;
    private $secondResponsibleName;
    private $secondResponsibleEmail;
    private $secondResponsiblePhone;
    private $group;
    private $signature;
    private $tokenFirebase;
    private $proposalData;
    private $planPassword;
    private $token;
    private $ip;

    public $address;

    public function __construct() {
        $this->address = new Address();
    }
    

    public function setId(int $data) {

        if(!is_int($data)) {
            throw new \Exception('Customer::setId: Não foi possível preencher o ID. É necessário que o valor seja um número inteiro');
        }

        $this->id = $data;
    }

    public function getId() {
        return $this->id;
    }


    public function setName(string $data) {

        if(!is_string($data)) {
            throw new \Exception('Customer::setName: Não foi possível preencher o nome. É necessário que o valor seja uma string');
        }

        $this->name = $data;
    }

    public function getName() {
        return $this->name;
    }

    public function setCPF(string $data) {
        
        if(strlen($data) != 11 && !is_numeric($data)) {
            throw new \Exception('Customer::setCPF: Não foi possível preencher o CPF. É necessário que o valor seja apenas números');
        }

        $this->cpf = $data;
    }

    public function getCPF() {
        return $this->cpf;
    }

    public function setEmail(string $data) {

        if(!filter_var($data, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception('Customer::setEmail: Não foi possível preencher o E-mail. É necessário que o valor seja um e-mail válido');
        }

        $this->email = $data;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setSex(string $data) {

        if(!in_array($data, ['M', 'F', 'O'])) {
            throw new \Exception("Customer::setSex: Não foi possível preencher o Sexo. É necessário que o valor seja 'M', 'F' ou 'O' ");
        }

        $this->sex = $data;
    }

    public function getSex() {
        return $this->sex;
    }

    public function setBirthdate($data) {
        
        $d = \DateTime::createFromFormat('Y-m-d', $data);
        
        if(!$d || $d->format('Y-m-d') != $data) {
            throw new \Exception("Customer::setBirthdate: Não foi possível preencher a data de nascimento. É necessário que o valor seja no formato Y-m-d ");
        }

        $this->birthdate = $data;
    }

    public function getBirthdate() {
        return $this->birthdate;
    }

    public function setPhone(string $data) {

        if(!is_numeric($data)) {
            throw new \Exception('Customer::setPhone: Não foi possível preencher o Telefone/Celular. É necessário que o valor seja apenas números');
        }

        $this->telephone = $data;
    }

    public function getPhone() {
        return $this->telephone;
    }

    public function setLandline(string $data) {
        if(!is_numeric($data)) {
            throw new \Exception('Customer::setPhone: Não foi possível preencher o Telefone fixo. É necessário que o valor seja apenas números');
        }

        $this->landline = $data;
    }

    public function getLandline() {
        return $this->landline;
    }

    public function setActive(bool $data) {

        if(!is_bool($data)) {
            throw new \Exception('Customer::setActive: Não foi possível preencher o campo Ativo. É necessário que o valor seja verdadeiro ou falso');
        }

        $this->active = $data;
    }

    public function getActive() {
        return $this->active;
    }

    public function setApproved(bool $data) {

        if(!is_bool($data)) {
            throw new \Exception('Customer::setApproved: Não foi possível preencher o campo Aprovado. É necessário que o valor seja verdadeiro ou falso');
        }

        $this->approved = $data;
    }

    public function getApproved() {
        return $this->approved;
    }

    public function setRG(string $data) {
        $this->rg = $data;
    }

    public function getRG() {
        return $this->rg;
    }

    public function setContractNumber(string $data) {

        if(!is_numeric($data)) {
            throw new \Exception('Customer::setContractNumber: Não foi possível preencher o Número do contrato. É necessário que o valor seja apenas números');
        }

        $this->contractNumber = $data;
    }

    public function getContractNumber() {
        return $this->contractNumber;
    }

    public function setExternalId(int $data) {

        if(!is_int($data)) {
            throw new \Exception('Customer::setExternalId: Não foi possível preencher o ID externo. É necessário que o valor seja um número inteiro');
        }

        $this->externalId = $data;
    }

    public function getExternalId() {
        return $this->externalId;
    }

    public function setMaritalStatus(string $data) {

        if(!in_array($data, ['SOLTEIRO', 'CASADO', 'DIVORCIADO', 'RELACIONAMENTO ESTAVEL', 'VIUVO'])) {
            throw new \Exception("Customer::setSex: Não foi possível preencher o Estado civil. É necessário que o valor seja 'SOLTEIRO', 'CASADO', 'DIVORCIADO', 'RELACIONAMENTO ESTAVEL' ou 'VIUVO' ");
        }

        $this->maritalStatus = $data;
    }

    public function getMaritalStatus() {
        return $this->maritalStatus;
    }

    public function setObs(string $data) {
        $this->obs = $data;
    }

    public function getObs() {
        return $this->obs;
    }

    public function setPaymentValue($data) {
        if(!is_numeric($data)) {
            throw new \Exception('Customer::setPaymentValue: Não foi possível preencher o Valor (pagamento). É necessário que o valor seja apenas números');
        }

        $this->paymentValue = $data;
    }

    public function getPaymentValue() {
        return $this->paymentValue;
    }

    public function setPaymentDueDay(int $data) {
        if(!is_int($data)) {
            throw new \Exception('Customer::setPaymentDueDay: Não foi possível preencher o Dia de vencimento. É necessário que o valor seja um número inteiro');
        }

        $this->paymentDueDay = $data;
    }

    public function getPaymentDueDay() {
        return $this->paymentDueDay;
    }

    public function setParticipative(bool $data) {
        if(!is_bool($data)) {
            throw new \Exception('Customer::setParticipative: Não foi possível preencher o campo Participativo. É necessário que o valor seja verdadeiro ou falso');
        }

        $this->participative = $data;
    }

    public function getParticipative() {
        return $this->participative;
    }

    public function setAgreedId(int $data) {

        if(!is_int($data)) {
            throw new \Exception('Customer::setAgreedId: Não foi possível preencher o ID do conveniado. É necessário que o valor seja um número inteiro');
        }

        $this->agreedId = $data;
    }

    public function getAgreedId() {
        return $this->agreedId;
    }

    public function setUserId(int $data) {

        if(!is_int($data)) {
            throw new \Exception('Customer::setUserId: Não foi possível preencher o ID do usuário. É necessário que o valor seja um número inteiro');
        }

        $this->userId = $data;
    }

    public function getUserId() {
        return $this->userId;
    }

    public function setFirstAccess(bool $data) {

        if(!is_bool($data)) {
            throw new \Exception('Customer::setFirstAccess: Não foi possível preencher o campo Primeiro Acesso. É necessário que o valor seja um número inteiro');
        }

        $this->firstAccess = $data;
    }

    public function getFirstAccess() {
        return $this->firstAccess;
    }

    public function setSecondResponsibleName(string $data) {
        $this->secondResponsibleName = $data;
    }

    public function getSecondResponsibleName() {
        return $this->secondResponsibleName;
    }

    public function setSecondResponsibleEmail(string $data) {

        if(!filter_var($data, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception('Customer::setSecondResponsibleEmail: Não foi possível preencher o E-mail do segundo responsável. É necessário que o valor seja um e-mail válido');
        }

        $this->secondResponsibleEmail = $data;
    }

    public function getSecondResponsibleEmail() {
        return $this->secondResponsibleEmail;
    }

    public function setSecondResponsiblePhone(string $data) {

        if(!is_numeric($data)) {
            throw new \Exception('Customer::setSecondResponsiblePhone: Não foi possível preencher o Telefone/Celular do segundo responsável. É necessário que o valor seja apenas números');
        }

        $this->secondResponsiblePhone = $data;
    }

    public function getSecondResponsiblePhone() {
        return $this->secondResponsiblePhone;
    }

    public function setGroup(string $data) {
        $this->group = $data;
    }

    public function getGroup() {
        return $this->group;
    }

    public function setSignature(string $data) {
        $this->signature = $data;
    }

    public function getSignature() {
        return $this->signature;
    }


    public function setTokenFirebase(string $data) {
        $this->tokenFirebase = $data;
    }

    public function getTokenFirebase() {
        return $this->tokenFirebase;
    }

    public function setProposalData(string $data) {
        $this->proposalData = $data;
    }

    public function getProposalData() {
        return $this->proposalData;
    }

    public function setPlanPassword(string $data) {
        $this->planPassword = $data;
    }

    public function getPlanPassword() {
        return $this->planPassword;
    }

    public function setToken(string $data) {
        $this->token = $data;
    }

    public function getToken() {
        return $this->token;
    }

    public function setIp(string $data) {
        $this->ip = $data;
    }

    public function getIp() {
        return $this->ip;
    }

    public function setAddress(Address $address) {
        $this->address = $address;
    }

    public function getAddress() {
        return $this->address;
    }
  
    
    public function populateErrorMessage(string $message) {
        return "Erro ao preencher o OBJ Customer: " . $message;
    }

    public function populate(array $data) {

        if(!isset($data['name'])) {
            throw new \Exception($this->populateErrorMessage('O campo name (nome) não foi encontrado!'));
        }

        if(!isset($data['email'])) {
            throw new \Exception($this->populateErrorMessage('O campo email não foi encontrado!'));
        }

        if(!isset($data['phone'])) {
            throw new \Exception($this->populateErrorMessage('O campo phone (telefone) não foi encontrado!'));
        }

        if(!isset($data['cpf'])) {
            throw new \Exception($this->populateErrorMessage('O campo cpf não foi encontrado!'));
        }

        if(!isset($data['birthdate'])) {
            throw new \Exception($this->populateErrorMessage('O campo birthdate (data nascimento) não foi encontrado!'));
        }

        if(!isset($data['sex'])) {
            throw new \Exception($this->populateErrorMessage('O campo sex (Sexo) não foi encontrado!'));
        }

        $this->setName($data['name']);
        $this->setEmail($data['email']);
        $this->setPhone($data['phone']);
        $this->setCPF($data['cpf']);
        $this->setBirthdate($data['birthdate']);
        $this->setSex($data['sex']);
        $this->setActive(isset($data['active']) && $data['active'] == true ? true : false);
        $this->setApproved(isset($data['approved']) && $data['approved'] == false ? false : true);
        
        if(isset($data['id'])) {
            $this->setId($data['id']);
        }

        if(isset($data['rg'])) {
            $this->setRG($data['rg']);
        }

        if(isset($data['contract_number'])) {
            $this->setContractNumber($data['contract_number']);
        }

        if(isset($data['landline'])) {
            $this->setLandline($data['landline']);
        }

        if(isset($data['external_id'])) {
            $this->setExternalId($data['external_id']);
        }

        if(isset($data['marital_status'])) {
            $this->setMaritalStatus($data['marital_status']);
        }

        if(isset($data['obs'])) {
            $this->setObs($data['obs']);
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

        if(isset($data['user_id'])) {
            $this->setAgreedId($data['user_id']);
        }

        if(isset($data['first_access'])) {
            $this->setFirstAccess($data['first_access']);
        }

        if(isset($data['second_responsible_name'])) {
            $this->setSecondResponsibleName($data['second_responsible_name']);
        }

        if(isset($data['second_responsible_email'])) {
            $this->setSecondResponsibleEmail($data['second_responsible_email']);
        }

        if(isset($data['second_responsible_phone'])) {
            $this->setSecondResponsiblePhone($data['second_responsible_phone']);
        }

        if(isset($data['group'])) {
            $this->setGroup($data['group']);
        }

        if(isset($data['signature'])) {
            $this->setSignature($data['signature']);
        }

        if(isset($data['token_firebase'])) {
            $this->setTokenFirebase($data['token_firebase']);
        }

        if(isset($data['proposal_data'])) {
            $this->setProposalData($data['proposal_data']);
        }

        if(isset($data['plan_password'])) {
            $this->setPlanPassword($data['plan_password']);
        }

        if(isset($data['token'])) {
            $this->setToken($data['token']);
        }

        if(isset($data['ip'])) {
            $this->setIp($data['ip']);
        }
        
    }


 
}