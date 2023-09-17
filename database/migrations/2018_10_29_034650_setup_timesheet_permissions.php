<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SetupTimesheetPermissions extends Migration
{
    private static $roles = [
        [
            'name' => 'TIMESHEET',
            'display_name' => 'Timesheet',
            'description' => 'Torna o usuário permitido a usar o timesheet',
            'permissions' => [
                [
                    'name' => 'create_timesheet_projeto',
                    'display_name' => 'Criar projeto do Timesheet',
                    'description' => 'Permite a criação de um projeto no Timesheet',
                    'menu' => 'timesheet'
                ],
                [
                    'name' => 'create_timesheet_tarefa',
                    'display_name' => 'Criar tarefa do Timesheet',
                    'description' => 'Permite a criação de uma tarefa no Timesheet',
                    'menu' => 'timesheet'
                ]
            ]
        ],
        [
            'name' => 'TIMESHEET_ADMIN',
            'display_name' => 'Administrador do Timesheet',
            'description' => 'Torna o usuário um administrador do timesheet',
            'permissions' => [
                [
                    'name' => 'edit_timesheet',
                    'display_name' => 'Editar dados do timesheet',
                    'description' => 'Permite que os horários sejam modificados',
                    'menu' => 'timesheet'
                ],
                [
                    'name' => 'delete_timesheet',
                    'display_name' => 'Excluir um timesheet',
                    'description' => 'Permite excluir registros do timesheet',
                    'menu' => 'timesheet'
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
