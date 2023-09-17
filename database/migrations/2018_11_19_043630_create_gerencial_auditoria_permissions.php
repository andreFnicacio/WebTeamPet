<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGerencialAuditoriaPermissions extends Migration
{
    private static $roles = [
        [
            'name' => 'GERENCIAL',
            'display_name' => 'Gerencial',
            'description' => 'Permite a emissão de relatórios',
            'permissions' => [
                [
                    'name' => 'relatorio_participativo',
                    'display_name' => 'Relatório Participativo',
                    'description' => 'Permite a emissão de Relatório Participativo',
                    'menu' => 'relatórios'
                ],
                [
                    'name' => 'relatorio_sinistralidade',
                    'display_name' => 'Relatório de Sinistralidade',
                    'description' => 'Permite a emissão de Relatório de Sinistralidade',
                    'menu' => 'relatórios'
                ],
            ]
        ],
        [
            'name' => 'TIMESHEET_ADMIN',
            'display_name' => 'Administrador do Timesheet',
            'description' => 'Torna o usuário um administrador do timesheet',
            'permissions' => [
                [
                    'name' => 'relatorio_timesheet',
                    'display_name' => 'Relatório de Timesheet',
                    'description' => 'Permite a emissão de Relatório de Timesheet',
                    'menu' => 'relatórios'
                ],
            ]
        ],
        [
            'name' => 'AUDITORIA',
            'display_name' => 'Administrador do Timesheet',
            'description' => 'Torna o usuário um administrador do timesheet',
            'permissions' => [
                [
                    'name' => 'listar_guias_cancelar',
                    'display_name' => 'Listar guias a cancelar',
                    'description' => 'Permite listar guias a cancelar',
                    'menu' => 'guias'
                ],
                [
                    'name' => 'listar_guias_encaminhamento',
                    'display_name' => 'Listar guias de encaminhamento',
                    'description' => 'Permite listar guias de encaminhamento',
                    'menu' => 'guias'
                ],
            ]
        ]
    ];

    private function setupPermissions()
    {
        foreach(self::$roles as $role) {
            $r = \App\Models\Role::where('name', $role['name'])->first();
            if(!$r) {
                $r = new \App\Models\Role();
                $r->name = $role['name'];
                $r->display_name = $role['display_name'];
                $r->description = $role['description'];
                $r->save();
            }

            foreach($role['permissions'] as $permission) {
                $p = \App\Models\Permission::where('name', $permission['name'])->first();
                if(!$p) {
                    $p = new \App\Models\Permission();
                    $p->name = $permission['name'];
                    $p->display_name = $permission['display_name'];
                    $p->description = $permission['description'];
                    $p->menu = $permission['menu'];
                    $p->save();
                }

                $r->attachPermission($p);
            }
        }
    }

    private function rollbackPermissions()
    {
        foreach(self::$roles as $role) {
            $r = \App\Models\Role::where('name', $role['name'])->first();
            if($r) {
                foreach($role['permissions'] as $permission) {
                    $p = \App\Models\Permission::where('name', $permission['name'])->first();
                    if($p) {
                        $r->detachPermission($p);
                    }
                    //$p->forceDelete();
                }
                //$r->forceDelete();
            }
        }
    }

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
