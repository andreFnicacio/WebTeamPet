<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDashboardRentabilidadePlanoPermissions extends Migration
{
    use \App\Helpers\CreateRolesPermissions;

    public static $roles = [
        [
            'name' => 'ADMINISTRADOR',
            'permissions' => [
                [
                    'name' => 'dashboard_rentabilidade_de_plano',
                    'display_name' => 'Carregar a rentabilidade de cada plano.',
                    'description' => '',
                    'menu' => 'dashboard'
                ]
            ]
        ]
    ];
}
