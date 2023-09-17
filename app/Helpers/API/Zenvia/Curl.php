<?php

namespace App\Helpers\API\Zenvia;

class Curl {
	const JSON = true;
	const QUERY = true;
	private $ch = null;
	private $accessKey = null;
    private $user = "lifepet.rest";
    private $password = "6W7ByplAUf";

    public function __construct() {
        $this->accessKey = base64_encode("{$this->user}:{$this->password}");
        $this->ch = curl_init();
	}
        
    public function getCurlObject() {
        return $this->ch;
    }

	public function option($option, $value) {
		curl_setopt($this->ch, $option, $value);
		return $this;
	}

	public function execute($headers = []) {
		$defaults = array(
			"Content-Type: application/json;  charset=UTF-8",
			"Accept: application/json",
			"Authorization: Basic " .  $this->accessKey,
		);

		if(!empty($headers)) {
			$headers = array_merge($headers, $defaults);
		} else {
			$headers = $defaults;
		}
		$this->option(CURLOPT_HTTPHEADER, $headers);

		$result = curl_exec($this->ch);

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

    public function postData($fields) {
		$this->option(CURLOPT_POSTFIELDS, json_encode($fields));
	}
}