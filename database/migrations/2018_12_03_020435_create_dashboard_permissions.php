<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDashboardPermissions extends Migration
{
    private static $roles = [
        [
            'name' => 'ADMINISTRADOR',
            'permissions' => [
                [
                    'name' => 'dashboard_vidas_ativas',
                    'display_name' => 'Ver total de vidas ativas',
                    'description' => '',
                    'menu' => 'dashboard'
                ],
                [
                    'name' => 'dashboard_vidas_ativas_mensais',
                    'display_name' => 'Ver total de vidas ativas mensais',
                    'description' => '',
                    'menu' => 'dashboard'
                ],
                [
                    'name' => 'dashboard_vidas_ativas_anuais',
                    'display_name' => 'Ver total de vidas ativas anuais',
                    'description' => '',
                    'menu' => 'dashboard'
                ],
                [
                    'name' => 'dashboard_vidas_inativas',
                    'display_name' => 'Ver total de vidas inativas',
                    'description' => '',
                    'menu' => 'dashboard'
                ],
                [
                    'name' => 'dashboard_vendas',
                    'display_name' => 'Ver total de vendas do período',
                    'description' => '',
                    'menu' => 'dashboard'
                ],
                [
                    'name' => 'dashboard_cancelamentos',
                    'display_name' => 'Ver total de cancelamentos',
                    'description' => '',
                    'menu' => 'dashboard'
                ],
                [
                    'name' => 'dashboard_sinistralidade_diaria',
                    'display_name' => 'Ver total de sinistralidade gerada no dia',
                    'description' => '',
                    'menu' => 'dashboard'
                ],
                [
                    'name' => 'dashboard_sinistralidade_mensal',
                    'display_name' => 'Ver total de sinistralidade gerada no mês',
                    'description' => '',
                    'menu' => 'dashboard'
                ],
                [
                    'name' => 'dashboard_atraso_mensal',
                    'display_name' => 'Ver total de atrasos do mês',
                    'description' => '',
                    'menu' => 'dashboard'
                ],
                [
                    'name' => 'dashboard_faturamento_mensal',
                    'display_name' => 'Ver total de faturamento mensal',
                    'description' => '',
                    'menu' => 'dashboard'
                ],
                [
                    'name' => 'dashboard_faturamento_mensal_previsto',
                    'display_name' => 'Ver total de faturamento mensal previsto',
                    'description' => '',
                    'menu' => 'dashboard'
                ],
                [
                    'name' => 'dashboard_media_recorrente_mensal',
                    'display_name' => 'Ver a média de recorrência mensal',
                    'description' => '',
                    'menu' => 'dashboard'
                ],
                [
                    'name' => 'dashboard_grafico_cancelamentos',
                    'display_name' => 'Ver gráfico de cancelamentos',
                    'description' => '',
                    'menu' => 'dashboard'
                ],
                [
                    'name' => 'dashboard_grafico_novas_vidas',
                    'display_name' => 'Ver gráfico de novas vidas',
                    'description' => '',
                    'menu' => 'dashboard'
                ],
                [
                    'name' => 'dashboard_grafico_sinistralidade_por_credenciada',
                    'display_name' => 'Ver gráfico de sinistralidade por credenciada',
                    'description' => '',
                    'menu' => 'dashboard'
                ],
                [
                    'name' => 'dashboard_tabela_pets_aniversariantes',
                    'display_name' => 'Ver tabela de pets aniversariantes',
                    'description' => '',
                    'menu' => 'dashboard'
                ],
                [
                    'name' => 'dashboard_tabela_clientes_aniversariantes',
                    'display_name' => 'Ver tabela de clientes aniversariantes',
                    'description' => '',
                    'menu' => 'dashboard'
                ],
                [
                    'name' => 'dashboard_tabela_pets_ativos_por_bairro',
                    'display_name' => 'Ver tabela de pets ativos por bairro',
                    'description' => '',
                    'menu' => 'dashboard'
                ],
                [
                    'name' => 'dashboard_tabela_pets_inativos_por_bairro',
                    'display_name' => 'Ver tabela de pets inativos por bairro ',
                    'description' => '',
                    'menu' => 'dashboard'
                ],
                [
                    'name' => 'dashboard_tabela_pets_ativos_por_cidade',
                    'display_name' => 'Ver tabela de pets ativos por cidade',
                    'description' => '',
                    'menu' => 'dashboard'
                ],
                [
                    'name' => 'dashboard_tabela_pets_inativos_por_cidade',
                    'display_name' => 'Ver tabela de pets inativos por cidade',
                    'description' => '',
                    'menu' => 'dashboard'
                ],
                [
                    'name' => 'dashboard_grafico_caes',
                    'display_name' => 'Ver gráfico de cães',
                    'description' => '',
                    'menu' => 'dashboard'
                ],
                [
                    'name' => 'dashboard_grafico_gatos',
                    'display_name' => 'Ver gráfico de gatos',
                    'description' => '',
                    'menu' => 'dashboard'
                ],
                [
                    'name' => 'dashboard_tabela_sinistralidade_por_prestador',
                    'display_name' => 'Ver tabela de sinistralidade por prestador',
                    'description' => '',
                    'menu' => 'dashboard'
                ],
                [
                    'name' => 'dashboard_grafico_participativos_versus_integrais',
                    'display_name' => 'Ver gráfico de participativos versus integrais',
                    'description' => '',
                    'menu' => 'dashboard'
                ],
                [
                    'name' => 'dashboard_grafico_pets_por_plano',
                    'display_name' => 'Ver gráfico de pets por plano',
                    'description' => '',
                    'menu' => 'dashboard'
                ],
                [
                    'name' => 'dashboard_grafico_pets_por_idade',
                    'display_name' => 'Ver gráfico de pets por idade',
                    'description' => '',
                    'menu' => 'dashboard'
                ],
                [
                    'name' => 'dashboard_grafico_ranking_vendedores',
                    'display_name' => 'Ver ranking de vendedores',
                    'description' => '',
                    'menu' => 'dashboard'
                ],
                [
                    'name' => 'dashboard_tabela_controle_vacinas',
                    'display_name' => 'Ver tabela de controle de vacinas',
                    'description' => '',
                    'menu' => 'dashboard'
                ],
                [
                    'name' => 'dashboard_tabela_vencimento_vacinas',
                    'display_name' => 'Ver tabela de vencimento de vacinas',
                    'description' => '',
                    'menu' => 'dashboard'
                ],
                [
                    'name' => 'dashboard_rentabilidade_de_plano',
                    'display_name' => 'Ver tabela de rentabilidade de plano',
                    'description' => '',
                    'menu' => 'dashboard'
                ]
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
