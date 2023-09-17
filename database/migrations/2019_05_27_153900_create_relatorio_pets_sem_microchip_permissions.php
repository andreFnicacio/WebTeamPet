<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRelatorioPetsSemMicrochipPermissions extends Migration
{
    use \App\Helpers\CreateRolesPermissions;

    public static $roles = [
        [
            'name' => 'ADMINISTRADOR',
            'permissions' => [
                [
                    'name' => 'relatorio_pets_sem_microchip',
                    'display_name' => 'Relatório de Pets sem microchip',
                    'description' => '',
                    'menu' => 'relatórios'
                ]
            ]
        ]
    ];
}
