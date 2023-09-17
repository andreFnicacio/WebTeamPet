<?php
/**
 * Created by PhpStorm.
 * User: Breno
 * Date: 27/04/2021
 * Time: 14:21
 */

namespace App\Helpers\API\Getnet;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Log;
use Mpdf\QrCode\Output\Png;
use Mpdf\QrCode\QrCode;

class Service
{
    /**
     * @var Token|null
     */
    protected $token = null;
    /**
     * @var Configuration|null
     */
    protected $configuration = null;
    protected $http = null;

    public function __construct()
    {
        $this->configuration = $config = new Configuration();
        $this->token = Token::build($config);

        $this->http = new Client([
            'headers' => [
                'Content-Type' => 'application/json; charset=utf-8',
                'Authorization' => 'Bearer ' . $this->token,
                //'seller_id' => Configuration::SELLER_ID,
            ]
        ]);
    }

    /**
     * @param int $amount Valor da transação em centavos
     * @param $order_id
     * @param $customer_id
     * @param string $currency
     * @return mixed
     */
    public function makePix(int $amount, $order_id, $customer_id, string $currency = 'BRL')
    {
        $endpoint = '/v1/payments/qrcode/pix';
        try {
            $pix = $this->post($endpoint, [
                'body' => json_encode([
                    'amount' => $amount,
                    'order_id' => $order_id,
                    'customer_id' => (string) $customer_id,
                    'currency' => $currency,
                ])
            ]);


            $qr = new QrCode($pix->additional_data->qr_code);
            $png = (new Png())->output($qr,400);
            $pix->png = base64_encode($png);
            return $pix;
        } catch (ClientException $e) {
            abort(422, $e->getResponse()->getBody()->getContents());
            Log::error(sprintf($e->getResponse()->getBody()->getContents()));
        } catch (\Exception $e) {
            abort(500, $e->getMessage());
            Log::error(sprintf($e->getMessage()));
        }

        return null;
    }

    private function get($uri, $params = []) {
        $response = $this->http->get($this->configuration->url($uri), $params);
        return json_decode($response);
    }

    private function post($uri, $params = []) {
        $response = $this->http->post($this->configuration->url($uri), $params);
        return json_decode($response->getBody());
    }
}