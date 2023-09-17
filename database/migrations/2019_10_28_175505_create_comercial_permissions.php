<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComercialPermissions extends Migration
{
    use \App\Helpers\CreateRolesPermissions;

    public static $roles = [
        [
            'name' => 'ADMINISTRADOR_COMERCIAL',
            'display_name' => 'Administrador do comercial',
            'description' => 'Papel da liderança do comercial, permite a gestão dos vendedores',
            'permissions' => [
            ]
        ]
    ];
}
