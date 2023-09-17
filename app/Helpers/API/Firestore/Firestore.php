<?php

namespace App\Helpers\API\Firestore;

use App\Helpers\Utils;
use App\Models\Clientes;
use Carbon\Carbon;
use MrShan0\PHPFirestore\Fields\FirestoreArray;
use MrShan0\PHPFirestore\Fields\FirestoreObject;
use MrShan0\PHPFirestore\Fields\FirestoreReference;
use MrShan0\PHPFirestore\Fields\FirestoreTimestamp;
use MrShan0\PHPFirestore\FirestoreClient;

class Firestore
{
    public $firestore_key = "AIzaSyAaDyy3GlJwUMSjYmG5hoCvHuoGO6skXgo";
    public $project_id = "lifepet-11504";
    public $id_notificacao = null;

    public function __construct($id_notificacao)
    {
        $this->firestoreClient = new FirestoreClient($this->project_id, $this->firestore_key, [
            'database' => '(default)',
        ]);
        $this->id_notificacao = $id_notificacao;
    }

    public function addNotification(Clientes $cliente, $title, $body, $payloadData) {

        $this->atualizaCliente($cliente, [
            'nome_cliente' => $cliente->nome_cliente,
            'token_firebase' => $cliente->token_firebase
        ]);

        $this->firestoreClient->addDocument('users/'.$cliente->id.'/notifications', [
            'id_notificacao' => $this->id_notificacao,
            'token_firebase' => $cliente->token_firebase,
            'data' => new FirestoreObject($payloadData),
            'data_exclusao' => null,
            'isDeleted' => false,
            'data_envio' => new FirestoreTimestamp(),
            'isRead' => false,
            'data_leitura' => null,
            'data_label' =>  Utils::getWeekName((new Carbon())->dayOfWeek) . ' • ' . (new Carbon())->format('d') . ' de ' . Utils::getMonthName((new Carbon())->month) . ' às ' . (new Carbon())->format('H:i'),
        ], $this->id_notificacao);
        return true;

    }

    public function atualizaCliente(Clientes $cliente, $dados){
        $this->firestoreClient->updateDocument('users/'.$cliente->id, $dados);
    }

    public function getNotificacoes($document){
        $notificacoes = [];
        if (isset($document['notificacoes'])) {
            foreach ($document['notificacoes'] as $not) {
                $oldNotification = $not->getData()[0];
                $oldNotification['notification'] = $oldNotification['notification']->getData()[0];
                $notificacoes[] = new FirestoreObject([
                    'id_notificacao' => isset($oldNotification['id_notificacao']) ? $oldNotification['id_notificacao'] : null,
                    'data_exclusao' => isset($oldNotification['data_exclusao']) ? $oldNotification['data_exclusao'] : null,
                    'data_leitura' => isset($oldNotification['data_leitura']) ? $oldNotification['data_leitura'] : null,
                    'data_envio' => isset($oldNotification['data_envio']) ? $oldNotification['data_envio'] : null,
                    'data_label' => isset($oldNotification['data_label']) ?  $oldNotification['data_label'] : null,
                    'notification' => new FirestoreObject([
                        'title' => isset($oldNotification['notification']['title']) ? $oldNotification['notification']['title'] : null,
                        'body' => isset($oldNotification['notification']['body']) ? $oldNotification['notification']['body'] : null,
                        'priority' => isset($oldNotification['notification']['priority']) ? $oldNotification['notification']['priority'] : 'high',
                    ])
                ]);
            }
        }
        return $notificacoes;
    }

    public function getDocument(Clientes $cliente){
        $document = $this->firestoreClient->getDocument('users/'.$cliente->id);
        return $document = $document->toArray();
    }

}
