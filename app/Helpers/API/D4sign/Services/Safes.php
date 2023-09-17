<?php

namespace App\Helpers\API\D4sign\Services;

use App\Helpers\API\D4sign\Client;
use App\Helpers\API\D4sign\Service;

class Safes extends Service
{
	
	public function find($safeKey = '')
    {
        $data = array();
        return $this->client->request("/safes/$safeKey", "GET", $data, 200);
    }

}
