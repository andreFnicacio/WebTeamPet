<?php
namespace App\Helpers\API\LifeQueueClient; 

require 'Environment.php';
require 'LifeQueueClient.php';
require 'LifeQueueClientException.php';
require 'Request.php';

$client = new LifeQueueClient(Environment::PRODUCTION);

$email = [];

$email[] = [
    'name' => 'André Rainaud',
    'email' => 'andrerayd@gmail.com'
];

try {
    $client->email(
        'E-mail teste',
        '<html>
        <meta charset="UTF-8" />
        <head>
        </head>
        <body>
        <p><strong>fila</strong><p/>
        <a href="https://lifepet.com.br">Clique aqui para acessar o site da Lifepet</a>
        </body>
        </html>',
        $email
    );

    //$client->push('dCYu1XEdMTQ:APA91bFzlWE3vyOSb3cm7Vd5cfreWz3gWllUmle3O6FEwRmrh8FQVXCAQegaGqxFClR_F8nLO_WMabPhD0fw1BUnGZs9G1JJtLyB8vVu6Fj2ROlm_zzRxUYlGMNpOX8kvcTBWlIRl_4f','Seu filho de patas bem cuidado!', 'Lifepet: Conosco o seu pet está seguro e você fica mais tranquilo para enfrentar esse momento tão complicado! Obrigada por confiar em nós!');
    
    $client->toQueue();
    echo 'Queue ID: ',$client->response()->message->id,"\n";
}
catch (LifeQueueClientException $e){
    echo $e->getMessage();
}