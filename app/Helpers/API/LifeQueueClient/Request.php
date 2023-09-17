<?php
namespace App\Helpers\API\LifeQueueClient;

class Request {

    private $authToken = 'D45qWV5H-D*DvfkT*Sy2A2=q2z_t%N6+uE#PNPLPyFH5p';
    private $authUuid = '8521922881112484';
    private $payload;
    private $channel = 'petmanager';
    private $environment;
    private $response;
    private $info;
    
    public function __construct(string $environment = Environment::STAGING){

        $this->payload =
        [
                "auth" => $this->authToken,
                "uuid" => $this->authUuid,
                "channel" => $this->channel
        ];

        $this->environment = $environment;
    }

    public function request(array $payload) {
        $this->payload = array_merge($this->payload, $payload);

        $ch = curl_init( $this->environment );
        $payload = json_encode( $this->payload );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        $this->response = curl_exec($ch);
        $this->info = curl_getinfo($ch);
        curl_close($ch);
        
        if($this->info['http_code'] != 200) {
            throw new LifeQueueClientException($this->response()->message);
        }
        return true;
    }

    public function response(){
        return json_decode($this->response);
    }
}