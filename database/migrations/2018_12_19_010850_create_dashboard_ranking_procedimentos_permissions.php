<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDashboardRankingProcedimentosPermissions extends Migration
{
    use \App\Helpers\CreateRolesPermissions;

    public static $roles = [
        [
            'name' => 'ADMINISTRADOR',
            'permissions' => [
                [
                    'name' => 'dashboard_tabela_ranking_procedimentos',
                    'display_name' => 'Ver tabela de relação de procedimentos com credenciados',
                    'description' => '',
                    'menu' => 'dashboard'
                ]
            ]
        ]
    ];
}
