<?php


namespace App\Helpers\API\Superlogica\V2;


use App\Helpers\API\Superlogica\V2\Exceptions\InvalidCallException;
use GuzzleHttp\Exception\GuzzleException;

class Client
{
    const BASE_URI = "https://api.superlogica.net/v2/";

    private static $instance;
    /**
     * @var \GuzzleHttp\Client
     */
    private $client;

    private function __construct()
    {
        $base_uri = env('SUPERLOGICA_BASE_URI', static::BASE_URI);

        $this->client = new \GuzzleHttp\Client([
            'verify'          => false,
            'base_uri'        => $base_uri,
            'allow_redirects' => false,
            'debug'           => false,
            'headers'         => [
                'Content-Type'  => 'application/form-url-encoded',
                'app_token'     => env('SUPERLOGICA_APP_TOKEN'),
                'access_token'  => env('SUPERLOGICA_ACCESS_TOKEN')
            ]
        ]);
    }

    /**
     * @return Client*
     */
    public static function getInstance(): Client
    {
        if(!static::$instance) {
            static::$instance = new Client();
        }

        return static::$instance;
    }

    /**
     * @param $uri
     * @param array $options
     * @return mixed|null
     */
    public function get($uri, array $options = [])
    {
        $response = $this->client->get($uri, $options);

        if($response->getStatusCode() === 200) {
            return json_decode($response->getBody()->getContents());
        }

        return null;
    }

    /**
     * @throws InvalidCallException
     */
    public function post($uri, $options = [])
    {
        $response = $this->client->post($uri, $options);

        if($response->getStatusCode() === 200) {
            return json_decode($response->getBody()->getContents());
        } else {
            $error = json_decode($response->getBody()->getContents());
            $error = "Message: {$error[0]->msg}\n";
            $error .= "Method: POST \n";
            $error .= "Endpoint: $uri \n";
            $error .= "Payload: " . json_encode($options) . "\n";

            throw new InvalidCallException($error);
        }
    }

    /**
     * @throws InvalidCallException
     */
    public function put($uri, $options = [])
    {
        $response = $this->client->put($uri, $options);

        if($response->getStatusCode() === 200) {
            return json_decode($response->getBody()->getContents());
        } else {
            $error = json_decode($response->getBody()->getContents());
            $error = "Message: {$error[0]->msg}\n";
            $error .= "Method: PUT";
            $error .= "Endpoint: $uri\n";
            $error .= "Payload: " . json_encode($options) . "\n";

            throw new InvalidCallException($error);
        }
    }
}