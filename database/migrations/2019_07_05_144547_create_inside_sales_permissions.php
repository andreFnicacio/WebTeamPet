<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInsideSalesPermissions extends Migration
{
    use \App\Helpers\CreateRolesPermissions;

    public static $roles = [
        [
            'name' => 'INSIDE_SALES',
            'display_name' => 'Inside Sales',
            'description' => 'Módulo interno de vendas',
            'permissions' => [
                [
                    'name' => 'inside_sales_cadastro',
                    'display_name' => 'Cadastro de novo cliente',
                    'description' => '',
                    'menu' => 'comercial'
                ]
            ]
        ]
    ];
}
