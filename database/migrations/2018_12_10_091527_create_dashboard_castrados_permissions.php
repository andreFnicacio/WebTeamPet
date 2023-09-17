<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDashboardCastradosPermissions extends Migration
{
    use \App\Helpers\CreateRolesPermissions;

    public static $roles = [
        [
            'name' => 'ADMINISTRADOR',
            'permissions' => [
                [
                    'name' => 'dashboard_grafico_castracao',
                    'display_name' => 'Ver gráfico de relação de pets castrados e não castrados',
                    'description' => '',
                    'menu' => 'dashboard'
                ]
            ]
        ]
    ];
}
