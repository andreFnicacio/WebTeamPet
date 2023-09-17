<?php

namespace App\Helpers\API\D4sign\Services;

use App\Helpers\API\D4sign\Client;
use App\Helpers\API\D4sign\Service;

class Account extends Service
{
	
	public function balance()
    {
        $data = array();
        return $this->client->request("/account/balance", "GET", $data, 200);
    }

}
