<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConsultoresPermissions extends Migration
{
    use \App\Helpers\CreateRolesPermissions;

    public static $roles = [
        [
            'name' => 'ADMINISTRADOR',
            'permissions' => [
                [
                    'name' => 'consultores_listar',
                    'display_name' => 'Consultores Listar',
                    'description' => '',
                    'menu' => 'consultores'
                ],
                [
                    'name' => 'consultores_perfil',
                    'display_name' => 'Consultores Perfil',
                    'description' => '',
                    'menu' => 'consultores'
                ],
                [
                    'name' => 'consultores_materiais_listar',
                    'display_name' => 'Consultores Materiais Listar',
                    'description' => '',
                    'menu' => 'consultores'
                ],
                [
                    'name' => 'consultores_materiais_criar',
                    'display_name' => 'Consultores Materiais Criar',
                    'description' => '',
                    'menu' => 'consultores'
                ],
                [
                    'name' => 'consultores_materiais_editar',
                    'display_name' => 'Consultores Materiais Editar',
                    'description' => '',
                    'menu' => 'consultores'
                ],
                [
                    'name' => 'consultores_materiais_excluir',
                    'display_name' => 'Consultores Materiais Excluir',
                    'description' => '',
                    'menu' => 'consultores'
                ],
            ]
        ]
    ];
}
