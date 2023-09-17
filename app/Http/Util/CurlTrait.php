<?php
/**
 * Created by PhpStorm.
 * User: criacao
 * Date: 12/03/18
 * Time: 15:33
 */

namespace App\Http\Util;


trait CurlTrait
{
    private $ch;

    private function init()
    {
        $this->ch = curl_init();
    }

    private static function params(array $params, $query = false) {
        $query = $query ? "?" : "";

        return $query . http_build_query($params);
    }

    private function option($option, $value) {
        curl_setopt($this->ch, $option, $value);
        return $this;
    }

    private function close() {
        return curl_close($this->ch);
    }

    private function defaults($url) {
        $this->option(CURLOPT_URL, $url)
            ->option(CURLOPT_RETURNTRANSFER, TRUE)
            ->option(CURLOPT_FOLLOWLOCATION, TRUE)
            ->option(CURLOPT_SSL_VERIFYPEER, FALSE)
            ->option(CURLOPT_SSL_VERIFYHOST, FALSE);
        return $this;
    }

    public function execute($headers = null) {
        if(!$headers) {
            $headers = array(
                "Content-Type: application/x-www-form-urlencoded",
            );
        }
        $this->option(CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($this->ch);

        if(self::JSON) {
            $result = json_decode($result);
        }

        return $result;
    }

    private function getDefaults($url) {
        $this->defaults($url)->option(CURLOPT_CUSTOMREQUEST, "GET");
    }

    private function postDefaults($url) {
        $this->defaults($url)->option(CURLOPT_CUSTOMREQUEST, "POST");
    }

    private function putDefaults($url) {
        $this->defaults($url)->option(CURLOPT_CUSTOMREQUEST, "PUT");
    }

    private function postFields(array $fields) {
        $this->option(CURLOPT_POSTFIELDS, self::params($fields));
    }
}