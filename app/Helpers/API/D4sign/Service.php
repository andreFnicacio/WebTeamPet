<?php

namespace App\Helpers\API\D4sign;

class Service
{
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }
}
