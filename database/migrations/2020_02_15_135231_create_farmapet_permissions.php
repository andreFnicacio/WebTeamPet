<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFarmapetPermissions extends Migration
{
    use \App\Helpers\CreateRolesPermissions;

    public static $roles = [
        [
            'name' => 'ADMINISTRADOR',
            'permissions' => [
                [
                    'name' => 'farmapet_clientes_listar',
                    'display_name' => 'Farmapet Clientes Listar',
                    'description' => '',
                    'menu' => 'farmapet'
                ],
                [
                    'name' => 'farmapet_clientes_vincular',
                    'display_name' => 'Farmapet Clientes Vincular',
                    'description' => '',
                    'menu' => 'farmapet'
                ],
                [
                    'name' => 'farmapet_solicitacoes',
                    'display_name' => 'Farmapet Solicitações',
                    'description' => '',
                    'menu' => 'farmapet'
                ],
            ]
        ]
    ];
}
