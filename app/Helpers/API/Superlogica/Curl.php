<?php

namespace App\Helpers\API\Superlogica;

class Curl {
	const JSON = true;
	const QUERY = true;
	protected $ch = null;
	private $appToken = "EAvu2fdfHEp0";
    private $accessToken = "WgFn6Erz9svM";
    private $secret = "bDUEHs7j7ZCN";

    public function __construct() {
		$this->ch = curl_init(); 
	}
        
    public function getCurlObject() {
        return $this->ch;
    }

	public static function params(array $params, $query = false) {
		$query = $query ? "?" : "";

		return $query . http_build_query($params); 
	}

	public function option($option, $value) {
		curl_setopt($this->ch, $option, $value);
		return $this;
	}

	public function execute($headers = null, $filters = 'all') {
		if(!$headers) {
			$headers = array(
				"Content-Type: application/x-www-form-urlencoded",
	            "app_token: {$this->appToken}",
	            "access_token: {$this->accessToken}",
	        );
		}
		$this->option(CURLOPT_HTTPHEADER, $headers);

		$result = curl_exec($this->ch);
		
		if(self::JSON) {
			$result = json_decode($result);
		}

		//Resposta encontrada
		if(isset($result->data)) {
			return $result->data;
		}

		//Erro na requisição
        if($filters === 'all') {
		    return $result;
        }

        if(is_array($result)) {
        	if(isset($result[0])) {
        		return $result[0];
        	}
            return $result;
        }
		return $result;
	}

	public function close() {
		return curl_close($this->ch);
	}

	public function defaults($url) {
		$this->option(CURLOPT_URL, $url)
			 ->option(CURLOPT_RETURNTRANSFER, TRUE)
			 ->option(CURLOPT_FOLLOWLOCATION, TRUE)
			 ->option(CURLOPT_SSL_VERIFYPEER, FALSE)
			 ->option(CURLOPT_SSL_VERIFYHOST, FALSE);
	 	return $this;
	}

	public function getDefaults($url) {
		$this->defaults($url)->option(CURLOPT_CUSTOMREQUEST, "GET");
	}
	public function postDefaults($url) {
		$this->defaults($url)->option(CURLOPT_CUSTOMREQUEST, "POST");
	}
    public function putDefaults($url) {
        $this->defaults($url)->option(CURLOPT_CUSTOMREQUEST, "PUT");
    }

    public function postFields(array $fields) {
		$this->option(CURLOPT_POSTFIELDS, self::params($fields));
	}
}