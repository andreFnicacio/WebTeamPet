<?php

namespace App\Helpers\API\D4sign\Services;

use App\Helpers\API\D4sign\Client;
use App\Helpers\API\D4sign\Service;

class Batches extends Service
{
	
    public function create($keys)
    {
    	if (!$keys){
    		return "Invalid keys";
    	}
    	$data = array("keys" => json_encode($keys));
    	return $this->client->request("/batches/", "POST", $data, 200);
    }

}
