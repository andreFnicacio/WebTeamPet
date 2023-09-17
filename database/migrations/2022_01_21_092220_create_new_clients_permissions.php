<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewClientsPermissions extends \App\Helpers\Migrations\PermissionsMigration
{
    public static $roles = [
        [
            'name' => 'ADMINISTRADOR',
            'permissions' => [
                [
                    'name' => 'ver_historico_financeiro',
                    'display_name' => 'Visualizar histórico financeiro no cadastro',
                    'description' => 'Visualizar histórico financeiro no cadastro',
                    'menu' => 'clientes'
                ],
                [
                    'name' => 'ver_notas_clientes',
                    'display_name' => 'Visualizar notas de clientes',
                    'description' => 'Visualizar notas de clientes',
                    'menu' => 'clientes'
                ]
            ]
        ],
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->setupPermissions();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->rollbackPermissions();
    }
}
