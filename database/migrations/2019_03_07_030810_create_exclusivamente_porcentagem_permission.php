<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExclusivamentePorcentagemPermission extends Migration
{
    use \App\Helpers\CreateRolesPermissions;

    public static $roles = [
        [
            'name' => 'ATENDIMENTO',
            'permissions' => [
                [
                    'name' => 'exclusivamente_porcentagem',
                    'display_name' => 'Permite ver dados de dashboard apenas em porcentagem',
                    'description' => '',
                    'menu' => 'dashboard'
                ]
            ]
        ]
    ];
}
