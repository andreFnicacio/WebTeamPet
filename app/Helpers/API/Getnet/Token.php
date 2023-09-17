<?php
/**
 * Created by PhpStorm.
 * User: Breno
 * Date: 27/04/2021
 * Time: 14:46
 */

namespace App\Helpers\API\Getnet;


use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

class Token
{
    protected $access_token = null;
    protected $token_type = null;
    protected $expires_in = null;
    protected $scope = null;
    protected $created_at = null;

    private function __construct(Response $response)
    {
        $status = $response->getStatusCode();
        if($status == '200') {
            $data = $response->getBody();
            if(!empty($data)) {
                $data = json_decode($data);
                $this->access_token = $data->access_token;
                $this->token_type = $data->token_type;
                $this->expires_in = $data->expires_in;
                $this->scope = $data->scope;
                $this->created_at = Carbon::now();
            }
        }
    }

    public function isValid()
    {
        return Carbon::now()->lte($this->created_at->addSeconds($this->expires_in));
    }

    public static function build(Configuration $config)
    {
        $http = new Client([
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Authorization' => 'Basic ' . $config->getBasicAuthHeader()
            ]
        ]);
        try {
            $response = $http->request('POST', $config->url('auth/oauth/v2/token'), [
                'form_params' => $config->getLoginFormParams()
            ]);

            return new self($response);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function __toString()
    {
        if(!$this->isValid()) {
            return '';
        }

        return $this->access_token;
    }
}