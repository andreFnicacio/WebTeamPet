<?php
namespace App\Helpers\API\RDStation\v1_2;

class Request {

    const TOKEN = "0eb70ce4d806faa1a1a23773e3d174d4";
    private $payload;
    private $url;
    private $response;
    private $info;
    
    public function __construct($url){
        $this->setUrl($url);

        $this->payload =
        [
            "token_rdstation" => self::TOKEN,
        ];

    }

    public function setUrl(string $url) {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new RDStationException('URL para requisição inválida');
        }

        $this->url = $url;
    }

    public function request(array $payload) {
        $this->payload = array_merge($this->payload, $payload);

        $ch = curl_init( $this->url );
        $payload = json_encode( $this->payload );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        $this->response = curl_exec($ch);
        $this->info = curl_getinfo($ch);
        curl_close($ch);

        
        if($this->info['http_code'] != 200) {
            throw new RDStationException($this->response()->message);
        }
        return true;
    }

    public function response(){
        return json_decode($this->response);
    }
}