<?php
/**
 * Created by PhpStorm.
 * User: desenvolvimento
 * Date: 03/12/18
 * Time: 16:08
 */

namespace App;


class Curl extends \App\Helpers\API\Superlogica\Curl
{
    public function execute($headers = [], $filters = null) {
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

    public function postFields(array $fields) {
        $this->option(CURLOPT_POSTFIELDS, self::params($fields));
    }
}