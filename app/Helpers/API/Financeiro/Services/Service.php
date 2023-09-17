<?php

namespace App\Helpers\API\Financeiro\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class Service
{

    private $client;
    private $appId;
    private $secretId;
    private $base_url;

    private $headers = [];

    public function __construct($appid=null, $secretid=null, $base_url=null){

        $this->client = new Client;

        $this->setSecretId($secretid);
        $this->setAppId($appid);
        $this->setBaseUrl($base_url);

    }
    private function getHeaders($payload=null)
    {

        $headers = ['headers'=>$this->headers];

        if ($payload !== null)
            $headers['form_params'] = $payload;

        return $headers;

    }
    private function setHeaders($headers=[])
    {
        $this->headers = [
            'User-Agent' => 'PetManager/1.0',
            'Accept'     => 'application/json',
            'appid' => $this->appId,
            'secretid' => $this->secretId
        ];
        $this->headers = array_merge($this->headers, $headers);
    }

    private function setAppId($appid)
    {

        $this->appId = $appid !== null ? $appid : config('financeiro.api.app_id');

    }

    private function setSecretId($secretid)
    {

        $this->secretId = $secretid !== null ? $secretid : config('financeiro.api.secret_id');

    }

    private function setBaseUrl($base_url)
    {

        $this->base_url =  $base_url !== null ? $base_url : config('financeiro.api.url');

    }

    private function mountEndpoint($endpoint)
    {

        return $this->base_url . $endpoint;

    }

    private function mountResponse($httpCode=0, $body='')
    {
        return (object) [
          'httpCode'=>$httpCode,
          'body'=>$body
        ];
    }
    private function exec($method, $endpoint, $headers)
    {

        try {

            $response = $this->client->request($method, $endpoint, $headers);

            $body = json_decode($response->getBody());
            $body = !is_null($body) ? $body : '';

            if (isset($body->data))
                $body = $body->data;

            return $this->mountResponse($response->getStatusCode(), $body);

        } catch( RequestException $e) {

            $response = $e->getResponse();
            $body = json_decode($response->getBody());

            $body = !is_null($body) ? $body : '';

            if (isset($body->error))
                $body = $body->error;

            if (isset($body->data))
                $body = $body->data;

            return $this->mountResponse($response->getStatusCode(), $body);

        }

    }
    public function get($endpoint, $headers=[])
    {

        $this->setHeaders($headers);

        return $this->exec('GET', $this->mountEndpoint($endpoint), $this->getHeaders());

    }

    public function post($endpoint, $payload=null, $headers=[])
    {
        $this->setHeaders($headers);

        return $this->exec('POST', $this->mountEndpoint($endpoint), $this->getHeaders($payload));

    }

    public function put($endpoint, $payload=null, $headers=[])
    {
        $this->setHeaders($headers);

        return $this->exec('PUT', $this->mountEndpoint($endpoint), $this->getHeaders($payload));
    }

    public function delete($endpoint, $payload=null, $headers=[])
    {
        $this->setHeaders($headers);

        return $this->exec('DELETE', $this->mountEndpoint($endpoint), $this->getHeaders($payload));
    }

}