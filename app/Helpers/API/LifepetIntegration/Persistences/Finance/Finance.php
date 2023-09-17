<?php

namespace App\Helpers\API\LifepetIntegration\Persistences\Finance;
use App\Helpers\API\LifepetIntegration\Domains\Customer\Customer;
use App\Helpers\API\LifepetIntegration\Domains\Pet\Pet;

class Finance {

    private $model;

    public function createCustomer(Customer $data) {
        try {
            return $this->model()::createCustomer($data);
        } catch(FinanceException $e) {
            throw new \Exception($e->getMessage());
        }
       
    }

    public function createSubscription(Customer $customer, Pet $pet, $finPlanId, $liquidateFirstCharge = null) {
        try {
            return $this->model()::createSubscription($customer, $pet, $finPlanId, $liquidateFirstCharge);
        } catch(FinanceException $e) {
            throw new \Exception($e->getMessage());
        }
        
    }

    public function model() {
        return Superlogica::class;
    }
}
