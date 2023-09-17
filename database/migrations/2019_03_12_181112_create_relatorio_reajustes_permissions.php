<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRelatorioReajustesPermissions extends Migration
{
    use \App\Helpers\CreateRolesPermissions;

    public static $roles = [
        [
            'name' => 'ADMINISTRADOR',
            'permissions' => [
                [
                    'name' => 'relatorio_reajustes',
                    'display_name' => 'Relatório de Reajustes',
                    'description' => '',
                    'menu' => 'relatórios'
                ],
                [
                    'name' => 'relatorio_reajustes_download',
                    'display_name' => 'Relatório de Reajustes Download',
                    'description' => '',
                    'menu' => 'relatórios'
                ],
            ]
        ],
        [
            'name' => 'FINANCEIRO',
            'permissions' => [
                [
                    'name' => 'relatorio_reajustes',
                    'display_name' => 'Relatório de Reajustes',
                    'description' => '',
                    'menu' => 'relatórios'
                ],
                [
                    'name' => 'relatorio_reajustes_download',
                    'display_name' => 'Relatório de Reajustes Download',
                    'description' => '',
                    'menu' => 'relatórios'
                ],
            ]
        ],
        [
            'name' => 'ATENDIMENTO',
            'permissions' => [
                [
                    'name' => 'relatorio_reajustes',
                    'display_name' => 'Relatório de Reajustes',
                    'description' => '',
                    'menu' => 'relatórios'
                ],
                [
                    'name' => 'relatorio_reajustes_download',
                    'display_name' => 'Relatório de Reajustes Download',
                    'description' => '',
                    'menu' => 'relatórios'
                ],
            ]
        ],
    ];
}
