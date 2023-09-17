<?php
/**
 * Created by PhpStorm.
 * User: desenvolvimento
 * Date: 03/12/18
 * Time: 17:56
 */

namespace App\Helpers;


trait CreateRolesPermissions
{
    public function setupPermissions()
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

    public function rollbackPermissions()
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