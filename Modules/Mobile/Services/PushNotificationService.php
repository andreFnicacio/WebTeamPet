<?php

namespace Modules\Mobile\Services;

use App\Helpers\API\Firestore\Firestore;
use App\Models\Clientes;
use FCM;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;

class PushNotificationService
{
    public $cliente;
    public $notification;
    public $data = null;
    public $title;
    public $body;
    public $payloadData;

    public function __construct(Clientes $cliente, $title, $body, $payloadData = [])
    {

        $payloadData['id_notificacao'] = \Carbon\Carbon::now()->timestamp . str_pad(rand(), 6, STR_PAD_LEFT);
        $payloadData['title'] = $title;
        $payloadData['body'] = $body;

        $this->cliente = $cliente;
        $this->title = $title;
        $this->body = $body;
        $this->payloadData = $payloadData;

        $notificationBuilder = new PayloadNotificationBuilder($this->title);
        $notificationBuilder->setBody($this->body)->setSound('default');
        $this->notification = $notificationBuilder->build();

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData($payloadData);
        $this->data = $dataBuilder->build();
    }

    public function send($addFirestore = true)
    {
        if ($addFirestore) {
            $this->addNotificationFirestore();
        }
        return FCM::sendTo($this->cliente->token_firebase, null, $this->notification, $this->data);
    }

    public function addNotificationFirestore(){
        $payloadData = $this->data->toArray();
        $firestore = (new Firestore($payloadData['id_notificacao']));
        $firestore->addNotification($this->cliente, $this->title, $this->body, $this->payloadData);
    }
}
