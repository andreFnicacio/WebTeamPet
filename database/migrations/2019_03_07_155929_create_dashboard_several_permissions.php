<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDashboardSeveralPermissions extends Migration
{
    use \App\Helpers\CreateRolesPermissions;

    public static $roles = [
        [
            'name' => 'ADMINISTRADOR',
            'permissions' => [
                [
                    'name' => 'dashboard_participacao_mensal',
                    'display_name' => 'Permite ver dados de participação mensal no dashboard',
                    'description' => '',
                    'menu' => 'dashboard'
                ],
                [
                    'name' => 'dashboard_upgrades',
                    'display_name' => 'Permite ver dados de upgrades no dashboard',
                    'description' => '',
                    'menu' => 'dashboard'
                ],
                [
                    'name' => 'dashboard_downgrades',
                    'display_name' => 'Permite ver dados de downgrades no dashboard',
                    'description' => '',
                    'menu' => 'dashboard'
                ],
                [
                    'name' => 'dashboard_renovacoes',
                    'display_name' => 'Permite ver dados de renovacoes no dashboard',
                    'description' => '',
                    'menu' => 'dashboard'
                ]
            ]
        ]
    ];
}
