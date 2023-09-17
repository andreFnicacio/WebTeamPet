<?php

namespace App\Helpers\API\Financeiro\Services;

use App\Helpers\API\Financeiro\Financeiro;

class Customer
{

    private $finance;
    public $data;
    public $form;

    public function __construct()
    {
        $this->finance = new Service();
    }

    public function create(\stdClass $payload)
    {

        return $this->finance->post('/customer', $payload);

    }
    public function update($customer_id, \stdClass $payload)
    {
        return $this->finance->post('/customer/'.$customer_id, $payload);

    }
    public function getByDocument($document=null)
    {

        $customer = $this->finance->get("/customer/cpfcnpj/{$document}");

        return $customer;


    }

    public function getByRefCode($refcode)
    {

        $customer = $this->finance->get("/customer/refcode/{$refcode}");

        return $customer;

    }



}