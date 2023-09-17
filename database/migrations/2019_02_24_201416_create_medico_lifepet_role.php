<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMedicoLifepetRole extends Migration
{
    use \App\Helpers\CreateRolesPermissions;

    public static $roles = [
        [
            'name' => 'MEDICO_LIFEPET',
            'display_name' => 'Médico Lifepet',
            'description' => '',

            'permissions' => [
                [
                    'name' => 'editar_informacoes_medicas_pet',
                    'display_name' => 'Permite editar apenas informações médicas',
                    'description' => '',
                    'menu' => 'pets'
                ]
            ]
        ]
    ];
}
