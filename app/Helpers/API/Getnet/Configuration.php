<?php
/**
 * Created by PhpStorm.
 * User: Breno
 * Date: 27/04/2021
 * Time: 14:21
 */

namespace App\Helpers\API\Getnet;


class Configuration
{
    const TOKEN__SCOPE = 'oob';
    const TOKEN__GRANT_TYPE = 'client_credentials';

    //SANDBOX
    //const CLIENT_ID = '28477c3d-d800-4d09-b879-1891fb482f50';
    //const CLIENT_SECRET = 'e60402a4-da00-4bb1-b9db-bbf558397a6e';

    //PRODUCAO
    const CLIENT_ID = '23b4c893-1278-453d-85a9-f340f53a341b';
    const CLIENT_SECRET = 'd415edf8-e8b9-4e5a-91cc-e02136afa717';
    const SELLER_ID = '2e73d48a-5c2f-40a8-9a0f-36941b3951da';

    const API_URLS = [
        //'SANDBOX' => 'https://api-sandbox.getnet.com.br/',
        'HOMOLOGACAO' => 'https://api.getnet.com.br/',
        'PRODUCAO' => 'https://api.getnet.com.br/',
    ];

    public function getBasicAuthHeader()
    {
        return base64_encode(self::CLIENT_ID . ':' . self::CLIENT_SECRET);
    }

    public function url($endpoint, $base = null)
    {
        if(!$base) {
            $base = self::API_URLS['PRODUCAO'];
        }

        if($endpoint[0] == '/') {
            $endpoint = substr($endpoint, 1);
        }

        return $base . $endpoint;
    }

    public function getLoginFormParams()
    {
        return [
            'scope' => self::TOKEN__SCOPE,
            'grant_type' => self::TOKEN__GRANT_TYPE
        ];
    }
}