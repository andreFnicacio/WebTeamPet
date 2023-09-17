<?php
/**
 * Created by PhpStorm.
 * User: Breno
 * Date: 24/08/2020
 * Time: 15:53
 */

namespace App\Helpers\API\LifeQueueClient;

require __DIR__. '/../../../../bootstrap/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Http\Kernel;
use App\Models\Clientes;

require 'Environment.php';
require 'LifeQueueClient.php';
require 'LifeQueueClientException.php';
require 'Request.php';

$app = require_once __DIR__.'/../../../../bootstrap/app.php';
$app->make('db');

$queque = new LifeQueueClient(Environment::PRODUCTION);
$file =  __DIR__ . "\\..\\..\\..\\..\\" . "storage\csv\clientes-push-20200824.csv";

$clients = \App\Helpers\Utils::csvToArray($file, ";");
$clientsElegible = [];
foreach($clients as $client) {
    $c = Clientes::where('celular', $client['Celular'])
                 ->whereNotNull('token_firebase')->first();
    if($c) {
        $clientsElegible[] = $c;
    }
    $c = null;
}

echo "Token: {$clientsElegible[0]->token_firebase} \n";

$title = "Atenção";
$message = "Pague a mensalidade hoje (24/08) e garanta 10% de desconto. Acesse o app e confira a fatura!";
try {
    foreach($clients as $client) {
        $queque->push($client->token_firebase, $title, $message);
        $queque->toQueue();
        echo 'Queue ID: ',$queque->response()->message->id,"\n";
    }

    //$client->push('dCYu1XEdMTQ:APA91bFzlWE3vyOSb3cm7Vd5cfreWz3gWllUmle3O6FEwRmrh8FQVXCAQegaGqxFClR_F8nLO_WMabPhD0fw1BUnGZs9G1JJtLyB8vVu6Fj2ROlm_zzRxUYlGMNpOX8kvcTBWlIRl_4f','Seu filho de patas bem cuidado!', 'Lifepet: Conosco o seu pet está seguro e você fica mais tranquilo para enfrentar esse momento tão complicado! Obrigada por confiar em nós!');
}
catch (LifeQueueClientException $e){
    echo $e->getMessage();
}

//$token = "cIfsb9K9K6c:APA91bF4jVaeIRO49bMKuITDU8LGqAHvpl47NrJdNoMWAP-y7ruAerOzdR4zOxU5DNnmq4QM--kzn1lsDwoCRS8KInvQKkYM1DW5-MmwTxkxM2A1FpEHH2z4qgCZllKS3FrMZ_xqsO_U";
//$title = "Atenção!";
//$message = "Pague a mensalidade hoje (24/08) e garanta 10% de desconto. Acesse o app e confira a fatura!";
//
//try {
//    $queque->push($token, $title, $message);
//    $queque->toQueue();
//    echo 'Queue ID: ',$queque->response()->message->id,"\n";
//}catch (LifeQueueClientException $e){
//    echo $e->getMessage();
//}