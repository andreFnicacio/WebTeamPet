<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SetupGerenciamentoCartoesPermissions extends Migration
{
    private static $roles = [
        [
            'name' => 'FINANCEIRO',
            'permissions' => [
                [
                    'name' => 'cliente_cartao_credito_excluir',
                    'display_name' => 'Excluir cartão de crédito do cliente.',
                    'description' => 'Excluir cartão de crédito do cliente',
                    'menu' => 'clientes'
                ],
                [
                    'name' => 'cliente_cartao_credito_principal',
                    'display_name' => 'Determinar o cartão do cliente como principal',
                    'description' => 'Determinar o cartão do cliente como principal',
                    'menu' => 'clientes'
                ]
            ]
        ],
    ];

    private function setupPermissions()
    {
        $admin = \App\Models\Role::where('name', 'ADMINISTRADOR')->first();
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
                $admin->attachPermission($p);
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
