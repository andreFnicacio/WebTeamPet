<?php


namespace App\Contracts;


trait Dispatchable
{
    protected $callback_url;

    public function dispatch($method = 'POST', $callback_url = null): array
    {
        $callback_url = $this->attributes['callback_url'] ?: $callback_url;

        if($callback_url) {
            try {
                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, $callback_url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
                curl_setopt($ch,CURLOPT_CUSTOMREQUEST, $method);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                    '_token' => csrf_token()
                ]));
                

                $headers = ["Content-Type: application/x-www-form-urlencoded"];

                curl_setopt($ch,CURLOPT_HTTPHEADER, $headers);

                $result = curl_exec($ch);
                $result = json_decode($result);

                return ['status' => true, 'data' => $result];
            } catch (\Exception $e) {

                return ['status' => false, 'data' => $e->getMessage()];
            }
        }

        return ['status' => false, 'data' => null];
    }
}