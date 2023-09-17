<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSolicitacoesReembolsoPermissions extends Migration
{
    use \App\Helpers\CreateRolesPermissions;

    public static $roles = [
        [
            'name' => 'ADMINISTRADOR',
            'permissions' => [
                [
                    'name' => 'list_solicitacoes_reembolso',
                    'display_name' => 'Listar Solicitações de Reembolso',
                    'description' => '',
                    'menu' => 'solicitações de reembolso'
                ]
            ]
        ],
        [
            'name' => 'FINANCEIRO',
            'permissions' => [
                [
                    'name' => 'list_solicitacoes_reembolso',
                    'display_name' => 'Listar Solicitações de Reembolso',
                    'description' => '',
                    'menu' => 'solicitações de reembolso'
                ]
            ]
        ],
        [
            'name' => 'AUTORIZADOR',
            'permissions' => [
                [
                    'name' => 'list_solicitacoes_reembolso',
                    'display_name' => 'Listar Solicitações de Reembolso',
                    'description' => '',
                    'menu' => 'solicitações de reembolso'
                ]
            ]
        ],
        [
            'name' => 'AUDITORIA',
            'permissions' => [
                [
                    'name' => 'list_solicitacoes_reembolso',
                    'display_name' => 'Listar Solicitações de Reembolso',
                    'description' => '',
                    'menu' => 'solicitações de reembolso'
                ]
            ]
        ],
        [
            'name' => 'ATENDIMENTO',
            'permissions' => [
                [
                    'name' => 'list_solicitacoes_reembolso',
                    'display_name' => 'Listar Solicitações de Reembolso',
                    'description' => '',
                    'menu' => 'solicitações de reembolso'
                ]
            ]
        ],
    ];
}