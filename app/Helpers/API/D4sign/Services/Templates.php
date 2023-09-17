<?php

namespace App\Helpers\API\D4sign\Services;

use App\Helpers\API\D4sign\Client;
use App\Helpers\API\D4sign\Service;

class Templates extends Service
{
	public function find($templateKey = '')
    {
        $data = array("id_template"=> json_encode($templateKey));
        return $this->client->request("/templates", "POST", $data, 200);
    }

}
