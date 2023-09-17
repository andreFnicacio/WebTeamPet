<?php
/**
 * Created by PhpStorm.
 * User: bsamp
 * Date: 27/09/2018
 * Time: 03:26
 */

namespace App\Helpers\API\Zenvia;


class Message
{
    public $to;
    public $msg;
    public $from;
    public $schedule;
    public $id;
    public $callbackOption;
    public $aggregateId;
    public $flashSms;
    public $celularCliente;

    //const URL = 'https://api-rest.zenvia.com/services';
    //const KEY = 'bGlmZXBldC5yZXN0OjZXN0J5cGxBVWY=';
    const URL = '';
    const KEY = '';

    /**
     * Message constructor.
     * @param $to
     * @param $msg
     * @param string $from
     * @param null $schedule
     * @param null $id
     * @param string $callbackOption
     * @param null $aggregateId
     * @param bool $flashSms
     */
    public function __construct($to, $msg, $from = 'Lifepet', $schedule = null, $id = null, $callbackOption = 'NONE', $aggregateId = null, $flashSms = false)
    {
        $this->to = self::formatTelephone($to);
        $this->msg = $msg;
        $this->from = $from;
        $this->schedule = $schedule;
        $this->id = $id;
        $this->callbackOption = $callbackOption;
        $this->aggregateId = $aggregateId;
        $this->flashSms = $flashSms;
        $this->celularCliente = $to;
    }

    /**
     * @param null $finalidade
     * @param null $numeroGuia
     * @param bool $onlyApp
     * @return \Modules\Mobile\Services\PushNotificationService|mixed|null
     */
    public function send($finalidade = null, $numeroGuia = null, $onlyApp = false) {

        $cliente = (new \App\Models\Clientes())->where('celular', $this->celularCliente)->first();

        // Envio de Push Notification, caso o cliente possua token firebase
        if ($cliente->token_firebase) {
            $pushNotification = (new \Modules\Mobile\Services\PushNotificationService($cliente, "Lifepet", $this->msg, []));
            $pushNotification->send();
            return $pushNotification;
        }

        if($onlyApp) {
            return null;
        }

        $sms = \App\Sms::create([
            'mobile' => $this->to,
            'body'   => $this->msg,
            'numero_guia' => $numeroGuia,
            'finalidade' => $finalidade
        ]);

        $ch = new Curl();
        $body = new \stdClass();
        $body->sendSmsRequest = new \stdClass();
        $body->sendSmsRequest->to = $this->to;
        $body->sendSmsRequest->msg = $this->msg;
        $body->sendSmsRequest->from = $this->from;
        $body->sendSmsRequest->schedule = $this->schedule;
        $body->sendSmsRequest->callbackOption = $this->callbackOption;
        $body->sendSmsRequest->aggregateId = $this->aggregateId;
        $body->sendSmsRequest->flashSms = $this->flashSms;
        $body->sendSmsRequest->id = $sms->id;

        $ch->postData($body);
        $ch->postDefaults(self::URL . '/send-sms');
        $result = $ch->execute();
        $ch->close();

        return $result;
    }

    /**
     * @param $telephone
     * @return string
     */
    public static function formatTelephone($telephone) {
        $phone = preg_replace("/[^0-9]/", "", $telephone);

        return "55" . $phone;
    }
}
