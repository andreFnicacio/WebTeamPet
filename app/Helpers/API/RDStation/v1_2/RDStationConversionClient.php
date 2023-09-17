<?php

namespace App\Helpers\API\RDStation\v1_2;

class RDStationConversionClient {

    const URL = 'https://www.rdstation.com.br/api/1.2/conversions';
    private $request;

    public function __construct() {
        $this->request = new Request(self::URL);
    }
    
    public function request(array $payload) {
        
        try {
            return $this->request->request($payload);
        } catch (RDStationException $e) {
            throw new \Exception($e->getMessage());
        }
        
    }

}