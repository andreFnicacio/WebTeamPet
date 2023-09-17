<?php

namespace App\Helpers\API\LifepetIntegration\Persistences\ERP;
use App\Helpers\API\LifepetIntegration\Repositories\{CustomerRepository,PetRepository,PetPlanRepository,PlanRepository,BreedRepository};

class ERP {
    
    public $customer;
    public $pet;
    public $petPlan;
    public $plan;
    public $breed;

    public function __construct() {
        $this->customer = new CustomerRepository();
        $this->pet = new PetRepository();
        $this->petPlan = new PetPlanRepository();
        $this->plan = new PlanRepository();
        $this->breed = new BreedRepository();
    }
}