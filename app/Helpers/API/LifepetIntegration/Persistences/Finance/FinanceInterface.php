<?php

namespace App\Helpers\API\LifepetIntegration\Persistences\Finance;
use App\Helpers\API\LifepetIntegration\Domains\Customer\Customer;
use App\Helpers\API\LifepetIntegration\Domains\Pet\Pet;
interface FinanceInterface {
    public static function createCustomer(Customer $customer);
    public static function createSubscription(Customer $costumer, Pet $pet, int $superlogicaPlanId);
}