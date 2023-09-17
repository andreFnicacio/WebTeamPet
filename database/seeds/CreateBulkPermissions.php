<?php

use Illuminate\Database\Seeder;

class CreateBulkPermissions extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createPermissions();
        $this->assignPermissions();
    }
    private function assignPermissions() {
        $mappings = [
            'ADMINISTRADOR' => [
                'clientes' => [
                    'edit',
                    'list',
                    'create',
                    'delete'
                ],
                'pets' => [
                    'edit',
                    'list',
                    'create',
                    'delete'
                ],
                'planos' => [
                    'edit',
                    'list',
                    'create',
                    'delete'
                ],
                'procedimentos' => [
                    'edit',
                    'list',
                    'create',
                    'delete'
                ],
                'clinicas' => [
                    'edit',
                    'list',
                    'create',
                    'delete'
                ],
                'prestadores' => [
                    'edit',
                    'list',
                    'create',
                    'delete'
                ],
                'tabelas_referencia' => [
                    'edit',
                    'list',
                    'create',
                    'delete'
                ],
                'grupos' => [
                    'edit',
                    'list',
                    'create',
                    'delete'
                ],
                'especialidades' => [
                    'edit',
                    'list',
                    'create',
                    'delete'
                ]
            ],
            'ATENDIMENTO' => [
                'clientes' => [
                    'edit',
                    'list',
                    'create',
                ],
                'pets' => [
                    'edit',
                    'list',
                    'create'
                ],
                'planos' => [
                    'list'
                ],
                'procedimentos' => [
                    'list'
                ],
                'clinicas' => [
                    'list'
                ],
                'prestadores' => [
                    'list'
                ],
                'tabelas_referencia' => [
                    'list'
                ],
                'grupos' => [
                    'list'
                ],
                'especialidades' => [
                    'list'
                ]
            ],
            'AUTORIZADOR' => [
                'clientes' => [
                    'edit',
                    'list',
                    'create',
                ],
                'pets' => [
                    'edit',
                    'list',
                    'create',
                ],
                'planos' => [
                    'edit',
                    'list',
                    'create',
                ],
                'procedimentos' => [
                    'edit',
                    'list',
                    'create',
                ],
                'clinicas' => [
                    'edit',
                    'list',
                    'create',
                ],
                'prestadores' => [
                    'edit',
                    'list',
                    'create',
                ],
                'tabelas_referencia' => [
                    'edit',
                    'list',
                    'create',
                ],
                'grupos' => [
                    'list',
                ],
                'especialidades' => [
                    'list',
                ]
            ],
            'CLINICAS' => [

            ],
            'FINANCEIRO' => [
                'clientes' => [
                    'edit',
                    'list',
                    'create',
                ],
                'pets' => [
                    'edit',
                    'list',
                    'create',
                ],
            ]
        ];

        foreach ($mappings as $roleName => $functionalities) {
            foreach($functionalities as $functionality => $abilities) {
                $role = \App\Models\Role::where('name', $roleName)->first();
                if($role) {
                    foreach ($abilities as $ability) {
                        $permission = \App\Models\Permission::where("name", $ability . "_" . $functionality)->first();
                        if($permission) {
                            $found = \Illuminate\Support\Facades\DB::table('permission_role')->where('permission_id', $permission->id)->where('role_id', $role->id)->first();
                            if(!$found) {
                                dump("Assign ({$permission->name}) to ($role->name)");
                                \Illuminate\Support\Facades\DB::table('permission_role')->insert([
                                    'permission_id' => $permission->id,
                                    'role_id' => $role->id,
                                ]);
                            }
                            $found = null;
                        }
                        $permission = null;
                    }
                }
            }
        }
    }
    private function createPermissions() {
        $functionalities = [
            'clientes' => "Clientes",
            'pets' => "Pets",
            'planos' => "Planos",
            'procedimentos' => "Procedimentos",
            'clinicas' => "Clínicas",
            'prestadores' => "Prestadores",
            'tabelas_referencia' => "Tabelas de Referência",
            'grupos' => "Grupos",
            'especialidades' => "Especialidades"
        ];
        $capabilities = [
            'create' => "Criar",
            'list' => "Listar",
            'edit' => "Editar",
            'delete' => "Excluir"
        ];

        foreach ($functionalities as $f => $name) {
            foreach ($capabilities as $capability => $display) {
                $permissionName = $capability . "_" . $f;
                if(!\App\Models\Permission::where('name', $permissionName)->first()) {
                    $permission = \App\Models\Permission::create([
                        'name' => $permissionName,
                        'display_name' => $display . ": " . $name
                    ]);
                    dump($permission);
                }
            }
        }
    }
}
