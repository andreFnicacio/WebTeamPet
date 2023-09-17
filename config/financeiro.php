<?php
/**
 * Configurações para conexão com sistema financeiro
 */
return [
    'api' => [
        'url' => env('FINANCEIRO_API_URL', 'https://financeiro-api.lifepet.com.br'),
        'secret_id' => env('FINANCEIRO_API_SECRET_ID', '13277c2c15388107af72c6560a248'),
        'app_id' => env('FINANCEIRO_API_APP_ID', '060d7f4be93a21ee31df')
    ]
];