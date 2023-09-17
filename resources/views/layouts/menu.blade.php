@php
    $emergencia = \App\Http\Controllers\AppBaseController::isEmergencia();
@endphp

@if(\Entrust::hasRole(['CLINICAS']))

    @php
        $clinica = (new \Modules\Clinics\Entities\Clinicas)->where('id_usuario', Auth::user()->id)->first();
    @endphp

    @if ($clinica->aceite_urh)
        <li class="nav-item start {{ Request::is('home*') ? 'active' : '' }}">
            <a href="/home" class="nav-link ">
                <i class="fa fa-home"></i>
                <span class="title">Home</span>
            </a>
        </li>
    @else
        <li class="nav-item start {{ Request::is('home*') ? 'active' : '' }}">
            <a href="/home" class="nav-link ">
                <i class="fa fa-home"></i>
                <span class="title">Manual</span>
            </a>
        </li>
    @endif
@endif

@if(\Entrust::hasRole(['ADMINISTRADOR']))
    <li class="nav-item start {{ (Request::is('promocoes')) ? 'active' : '' }}">
        <a href="javascript:;" class="nav-link nav-toggle">
            <i class="fa fa-newspaper-o"></i>
            <span class="title">Promoções</span>
            <span class="arrow"></span>
        </a>
        <ul class="sub-menu">
            <li class="nav-item start ">
                <a href="{!! route('promocoes.index') !!}" class="nav-link ">
                    <i class="icon-bar-chart"></i>
                    <span class="title">Listar</span>
                </a>
            </li>

            <li class="nav-item start ">
                <a href="{!! route('promocoes.create') !!}" class="nav-link ">
                    <i class="fa fa-newspaper-o"></i>
                    <span class="title">Cadastrar</span>
                </a>
            </li>

        </ul>
    </li>
@endif

@include('guides::menu')

@permission('list_clientes')
<!-- Clientes -->
<li class="nav-item start {{ Request::is('clientes*') ? 'active' : '' }}">
    <a href="javascript:;" class="nav-link nav-toggle">
        <i class="fa fa-users"></i>
        <span class="title">Clientes</span>
        <span class="arrow"></span>
    </a>
    <ul class="sub-menu">
        <li class="nav-item start ">
            <a href="{!! route('clientes.index') !!}" class="nav-link ">
                <i class="icon-bar-chart"></i>
                <span class="title">Listar</span>
            </a>
        </li>
        @permission('create_clientes')
        <li class="nav-item start ">
            <a href="{!! route('clientes.create') !!}" class="nav-link ">
                <i class="ion-android-people"></i>
                <span class="title">Cadastrar</span>
            </a>
        </li>
        @endpermission
    </ul>
</li>
@endpermission

@permission('list_pets')
<!-- Pets -->
<li class="nav-item start {{ Request::is('pets*') ? 'active' : '' }}">
    <a href="javascript:;" class="nav-link nav-toggle">
        <i class="ion-ios-paw"></i>
        <span class="title">Pets</span>
        <span class="arrow"></span>
    </a>
    <ul class="sub-menu">
        <li class="nav-item start ">
            <a href="{!! route('pets.index') !!}" class="nav-link ">
                <i class="icon-bar-chart"></i>
                <span class="title">Listar</span>
            </a>
        </li>
        @permission('create_pets')
        <li class="nav-item start ">
            <a href="{!! route('pets.create') !!}" class="nav-link ">
                <i class="ion-ios-paw"></i>
                <span class="title">Cadastrar</span>
            </a>
        </li>
        @endpermission
        {{--        <li class="nav-item start ">--}}
        {{--            <a href="{!! route('renovacao.index') !!}" class="nav-link ">--}}
        {{--                <i class="fa fa-refresh"></i>--}}
        {{--                <span class="title">Validar renovações</span>--}}
        {{--            </a>--}}
        {{--        </li>--}}
        {{--        <li class="nav-item start ">--}}
        {{--            <a href="{!! route('renovacao.controle') !!}" class="nav-link ">--}}
        {{--                <i class="fa fa-columns"></i>--}}
        {{--                <span class="title">Controle de renovações</span>--}}
        {{--            </a>--}}
        {{--        </li>--}}
    </ul>
</li>
@endpermission

@permission('list_planos')
<!-- Planos -->
<li class="nav-item start {{ Request::is('planos*') ? 'active' : '' }}">
    <a href="javascript:;" class="nav-link nav-toggle">
        <i class="fa fa-book"></i>
        <span class="title">Planos</span>
        <span class="arrow"></span>
    </a>
    <ul class="sub-menu">
        <li class="nav-item start ">
            <a href="{!! route('planos.index') !!}" class="nav-link ">
                <i class="icon-bar-chart"></i>
                <span class="title">Listar</span>
            </a>
        </li>
        @permission('create_planos')
        <li class="nav-item start ">
            <a href="{!! route('planos.create') !!}" class="nav-link ">
                <i class="fa fa-book"></i>
                <span class="title">Cadastrar</span>
            </a>
        </li>
        @endpermission
    </ul>
</li>
@endpermission

@permission('list_procedimentos')
<!-- Procedimentos -->
<li class="nav-item start {{ Request::is('procedimentos*') ? 'active' : '' }}">
    <a href="javascript:;" class="nav-link nav-toggle">
        <i class="fa fa-cube"></i>
        <span class="title">Procedimentos</span>
        <span class="arrow"></span>
    </a>
    <ul class="sub-menu">
        <li class="nav-item start ">
            <a href="{!! route('procedimentos.index') !!}" class="nav-link ">
                <i class="icon-bar-chart"></i>
                <span class="title">Listar</span>
            </a>
        </li>
        @permission('create_procedimentos')
        <li class="nav-item start ">
            <a href="{!! route('procedimentos.create') !!}" class="nav-link ">
                <i class="fa fa-cube"></i>
                <span class="title">Cadastrar</span>
            </a>
        </li>
        @endpermission
    </ul>
</li>
@endpermission

@include('clinics::menu')
@include('veterinaries::menu')

@permission('list_tabelas_referencia')
<!-- Tabelas de referência -->
<li class="nav-item start {{ Request::is('tabelasReferencias*') ? 'active' : '' }}">
    <a href="javascript:;" class="nav-link nav-toggle">
        <i class="fa fa-money"></i>
        <span class="title">Tabelas de Ref.</span>
        <span class="arrow"></span>
    </a>
    <ul class="sub-menu">
        <li class="nav-item start ">
            <a href="{!! route('tabelasReferencias.index') !!}" class="nav-link ">
                <i class="icon-bar-chart"></i>
                <span class="title">Listar</span>
            </a>
        </li>
        @permission('create_tabelas_referencia')
        <li class="nav-item start ">
            <a href="{!! route('tabelasReferencias.create') !!}" class="nav-link ">
                <i class="fa fa-money"></i>
                <span class="title">Cadastrar</span>
            </a>
        </li>
        @endpermission
    </ul>
</li>
@endpermission
@permission('list_grupos')
<!-- Grupos -->
<li class="nav-item start {{ Request::is('grupos*') ? 'active' : '' }}">
    <a href="javascript:;" class="nav-link nav-toggle">
        <i class="fa fa-cubes"></i>
        <span class="title">Grupos</span>
        <span class="arrow"></span>
    </a>
    <ul class="sub-menu">
        <li class="nav-item start ">
            <a href="{!! route('grupos.index') !!}" class="nav-link ">
                <i class="icon-bar-chart"></i>
                <span class="title">Listar</span>
            </a>
        </li>
        @permission('create_grupos')
        <li class="nav-item start ">
            <a href="{!! route('grupos.create') !!}" class="nav-link ">
                <i class="fa fa-cubes"></i>
                <span class="title">Cadastrar</span>
            </a>
        </li>
        @endpermission
    </ul>
</li>
@endpermission
@permission('list_especialidades')
<!-- Especialidades -->
<li class="nav-item start {{ Request::is('especialidades*') ? 'active' : '' }}">
    <a href="javascript:;" class="nav-link nav-toggle">
        <i class="ion-erlenmeyer-flask"></i>
        <span class="title">Especialidades</span>
        <span class="arrow"></span>
    </a>
    <ul class="sub-menu">
        <li class="nav-item start ">
            <a href="{!! route('especialidades.index') !!}" class="nav-link ">
                <i class="icon-bar-chart"></i>
                <span class="title">Listar</span>
            </a>
        </li>
        @permission('create_especialidades')
        <li class="nav-item start ">
            <a href="{!! route('especialidades.create') !!}" class="nav-link ">
                <i class="ion-erlenmeyer-flask"></i>
                <span class="title">Cadastrar</span>
            </a>
        </li>
        @endpermission
    </ul>
</li>
@endpermission

@if(\Entrust::hasRole(['GERENCIAL','TIMESHEET_ADMIN','ADMINISTRADOR','FINANCEIRO','AUDITORIA','CADASTRO','RELATORIOS', 'RELATORIOS_CX', 'CONSULTORIA_FINANCEIRA']))
    <li class="nav-item start {{ Request::is('relatorios*') ? 'active' : '' }}">
        <a href="javascript:;" class="nav-link nav-toggle">
            <i class="icon-bar-chart"></i>
            <span class="title">Relatórios</span>
            <span class="arrow"></span>
        </a>
        <ul class="sub-menu">
            @permission('relatorio_clientes')
            <li class="nav-item start ">
                <a href="{!! route('internal-network-lp') !!}" class="nav-link ">
                    <i class="fa fa-users"></i>
                    <span class="title">Rede Credenciada - Planos Antigos</span>
                </a>
            </li>
            @endpermission

            @permission('relatorio_sinistralidade')
            <li class="nav-item start ">
                <a href="{!! route('relatorios.sinistralidade') !!}" class="nav-link ">
                    <i class="fa fa-bolt"></i>
                    <span class="title">Sinistralidade</span>
                </a>
            </li>
            <li class="nav-item start ">
                <a href="{!! route('relatorios.sinistralidadeGrupos') !!}" class="nav-link ">
                    <i class="fa fa-bolt"></i>
                    <span class="title">Sinistralidade de Grupos Hospitalares</span>
                </a>
            </li>
            @endpermission
            @permission('relatorio_participativo')
            <li class="nav-item start ">
                <a href="{!! route('relatorios.participativo') !!}?status%5B%5D=LIBERADO" class="nav-link ">
                    <i class="fa fa-percent"></i>
                    <span class="title">Participativos</span>
                </a>
            </li>
            @endpermission
            @permission('relatorio_timesheet')
            <li class="nav-item start ">
                <a href="{!! route('relatorios.timesheets') !!}" class="nav-link ">
                    <i class="fa fa-clock-o"></i>
                    <span class="title">Timesheets</span>
                </a>
            </li>
            @endpermission
            @permission('relatorio_timesheet')
            <li class="nav-item start ">
                <a href="{!! route('relatorios.vendedores') !!}" class="nav-link ">
                    <i class="fa fa-money"></i>
                    <span class="title">Vendedores</span>
                </a>
            </li>
            @endpermission
            @permission('relatorio_reajustes')
            <li class="nav-item start ">
                <a href="{!! route('relatorios.reajustes') !!}" class="nav-link ">
                    <i class="fa fa-money"></i>
                    <span class="title">Reajuste</span>
                </a>
            </li>
            @endpermission

            @permission('relatorio_reajustes')
            <li class="nav-item start ">
                <a href="{!! route('relatorios.receitas') !!}" class="nav-link ">
                    <i class="fa fa-money"></i>
                    <span class="title">Receitas</span>
                </a>
            </li>
            @endpermission

            @permission('relatorio_reajustes')
            <li class="nav-item start ">
                <a href="{!! route('relatorios.receitas-picpay') !!}" class="nav-link ">
                    <i class="fa fa-money"></i>
                    <span class="title">Receitas - Picpay</span>
                </a>
            </li>
            @endpermission

            @permission('relatorio_reajustes')
            <li class="nav-item start ">
                <a href="{!! route('relatorios.receitas-link-pagamento') !!}" class="nav-link ">
                    <i class="fa fa-money"></i>
                    <span class="title">Receitas - Links de Pagamento</span>
                </a>
            </li>
            @endpermission

            @permission('relatorio_cancelamento')
            <li class="nav-item start ">
                <a href="{!! route('relatorios.cancelamento') !!}" class="nav-link ">
                    <i class="fa fa-times"></i>
                    <span class="title">Cancelamento</span>
                </a>
            </li>
            @endpermission

            @permission('relatorio_clientes')
            <li class="nav-item start ">
                <a href="{!! route('relatorios.clientes') !!}" class="nav-link ">
                    <i class="fa fa-users"></i>
                    <span class="title">Clientes</span>
                </a>
            </li>
            @endpermission

            @permission('relatorio_clientes')
            <li class="nav-item start ">
                <a href="{!! route('relatorios.inadimplentes') !!}" class="nav-link ">
                    <i class="fa fa-users"></i>
                    <span class="title">Inadimplentes</span>
                </a>
            </li>
            @endpermission

            @permission('relatorio_clientes')
            <li class="nav-item start ">
                <a href="{!! route('relatorios.clientesSemFaturaCompetencia') !!}" class="nav-link ">
                    <i class="fa fa-user-times"></i>
                    <span class="title">Clientes sem fatura</span>
                </a>
            </li>
            @endpermission

            @permission('relatorio_clientes')
            <li class="nav-item start ">
                <a href="{!! route('relatorios.pets') !!}" class="nav-link ">
                    <i class="ion-ios-paw"></i>
                    <span class="title">Pets</span>
                </a>
            </li>
            @endpermission

            @permission('relatorio_pets_sem_microchip')
            <li class="nav-item start ">
                <a href="{!! route('relatorios.pets_sem_microchip') !!}" class="nav-link ">
                    <i class="ion-ios-paw"></i>
                    <span class="title">Pets sem microchip</span>
                </a>
            </li>
            @endpermission

            @permission('relatorio_clientes')
            <li class="nav-item start ">
                <a href="{!! route('relatorios.indicacoes') !!}" class="nav-link ">
                    <i class="fa fa-exchange"></i>
                    <span class="title">Indicações</span>
                </a>
            </li>
            @endpermission
            @permission('relatorio_clientes')
            <li class="nav-item start ">
                <a href="{!! route('relatorios.compraRapida') !!}" class="nav-link ">
                    <i class="fa fa-area-chart"></i>
                    <span class="title">E-commerce</span>
                </a>
            </li>
            @endpermission
        </ul>
    </li>
@endif
@role('ADMINISTRADOR')
<li class="nav-item start {{ (Request::is('permissoes*') || Request::is('roles*') || Request::is('users*')) ? 'active' : '' }}">
    <a href="javascript:;" class="nav-link nav-toggle">
        <i class="fa fa-gears"></i>
        <span class="title">Configurações</span>
        <span class="arrow"></span>
    </a>
    <ul class="sub-menu">
        <li class="nav-item start {{ Request::is('permissoes*') ? 'active' : '' }}">
            <a href="javascript:;" class="nav-link nav-toggle">
                <i class="fa fa-gear"></i>
                <span class="title">Permissões</span>
                <span class="arrow"></span>
            </a>
            <ul class="sub-menu">
                <li class="nav-item start ">
                    <a href="{!! route('permissoes.create') !!}" class="nav-link ">
                        <i class="fa fa-plus-square"></i>
                        <span class="title">Criar</span>
                    </a>
                </li>
                <li class="nav-item start ">
                    <a href="{!! route('permissoes.index') !!}" class="nav-link ">
                        <i class="fa fa-list-alt"></i>
                        <span class="title">Listar</span>
                    </a>
                </li>
            </ul>
        </li>
        <li class="nav-item start {{ Request::is('roles*') ? 'active' : '' }}">
            <a href="javascript:;" class="nav-link nav-toggle">
                <i class="fa fa-sitemap"></i>
                <span class="title">Papéis</span>
                <span class="arrow"></span>
            </a>
            <ul class="sub-menu">
                <li class="nav-item start ">
                    <a href="{!! route('papeis.create') !!}" class="nav-link ">
                        <i class="fa fa-plus-square"></i>
                        <span class="title">Criar</span>
                    </a>
                </li>
                <li class="nav-item start ">
                    <a href="{!! route('papeis.index') !!}" class="nav-link ">
                        <i class="fa fa-list-alt"></i>
                        <span class="title">Listar</span>
                    </a>
                </li>
            </ul>
        </li>
        <li class="nav-item start {{ Request::is('usuarios*') ? 'active' : '' }}">
            <a href="javascript:;" class="nav-link nav-toggle">
                <i class="fa fa-users"></i>
                <span class="title">Usuários</span>
                <span class="arrow"></span>
            </a>
            <ul class="sub-menu">
                <li class="nav-item start ">
                    <a href="{!! route('usuarios.create') !!}" class="nav-link ">
                        <i class="fa fa-user-plus"></i>
                        <span class="title">Criar</span>
                    </a>
                </li>
                <li class="nav-item start ">
                    <a href="{!! route('usuarios.index') !!}" class="nav-link ">
                        <i class="fa fa-search"></i>
                        <span class="title">Buscar</span>
                    </a>
                </li>
            </ul>
        </li>
        @permission('list_informacoes_adicionais')
        <li class="nav-item start {{ Request::is('informacoesAdicionais*') ? 'active' : '' }}">
            <a href="javascript:;" class="nav-link nav-toggle">
                <i class="fa fa-info-circle"></i>
                <span class="title">Info. adicionais</span>
                <span class="arrow"></span>
            </a>
            <ul class="sub-menu">
                @permission('create_informacoes_adicionais')
                <li class="nav-item start ">
                    <a href="{!! route('informacoesAdicionais.create') !!}" class="nav-link ">
                        <i class="fa fa-user-plus"></i>
                        <span class="title">Criar</span>
                    </a>
                </li>
                @endpermission
                <li class="nav-item start ">
                    <a href="{!! route('informacoesAdicionais.index') !!}" class="nav-link ">
                        <i class="fa fa-search"></i>
                        <span class="title">Buscar</span>
                    </a>
                </li>
            </ul>
        </li>
        @endpermission
        <li class="nav-item start {{ Request::is('parametros*') ? 'active' : '' }}">
            <a href="javascript:;" class="nav-link nav-toggle">
                <i class="fa fa-info-circle"></i>
                <span class="title">Parâmetros</span>
                <span class="arrow"></span>
            </a>
            <ul class="sub-menu">
                <li class="nav-item start ">
                    <a href="{!! route('parametros.create') !!}" class="nav-link ">
                        <i class="fa fa-user-plus"></i>
                        <span class="title">Criar</span>
                    </a>
                </li>
                <li class="nav-item start ">
                    <a href="{!! route('parametros.index') !!}" class="nav-link ">
                        <i class="fa fa-search"></i>
                        <span class="title">Buscar</span>
                    </a>
                </li>
            </ul>
        </li>

    </ul>
</li>
@endrole
@permission('ver_documentos_internos')
<li class="nav-item start {{ (Request::is('documentos_internos')) ? 'active' : '' }}">
    <a href="javascript:;" class="nav-link nav-toggle">
        <i class="fa fa-folder-o"></i>
        <span class="title">Docs Internos</span>
        <span class="arrow"></span>
    </a>
    <ul class="sub-menu">
        <li class="nav-item start ">
            <a href="{!! route('documentos_internos.index') !!}" class="nav-link ">
                <i class="fa fa-list"></i>
                <span class="title">Listar</span>
            </a>
        </li>
    </ul>
</li>
@endrole

@if(\Entrust::hasRole(['ADMINISTRADOR', 'INSIDE_SALES', 'ADMINISTRADOR_COMERCIAL']))
    <!-- Clientes -->
    <li class="nav-item start {{ Request::is('comercial*') || Request::is('vendedores*') ? 'active' : '' }}">
        <a href="javascript:;" class="nav-link nav-toggle">
            <i class="fa fa-line-chart"></i>
            <span class="title">Comercial</span>
            <span class="arrow"></span>
        </a>
        <ul class="sub-menu">
            @permission('inside_sales_cadastro')
            <li class="nav-item start ">
                <a href="{!! route('comercial.inside_sales') !!}" class="nav-link ">
                    <i class="fa fa-line-chart"></i>
                    <span class="title">Inside Sales</span>
                </a>
            </li>
            @endpermission
            @permission('list_vendedores')
            <!-- Clínicas -->
            <li class="nav-item start {{ Request::is('vendedores*') ? 'active' : '' }}">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-smile-o"></i>
                    <span class="title">Vendedores</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item start ">
                        <a href="{!! route('vendedores.index') !!}" class="nav-link ">
                            <i class="icon-bar-chart"></i>
                            <span class="title">Listar</span>
                        </a>
                    </li>
                    @permission('create_vendedores')
                    <li class="nav-item start ">
                        <a href="{!! route('vendedores.create') !!}" class="nav-link ">
                            <i class="fa fa-hospital-o"></i>
                            <span class="title">Cadastrar</span>
                        </a>
                    </li>
                    @endpermission
                </ul>
            </li>
            @endpermission
            @permission('list_vendedores')
            <!-- Clínicas -->
            <li class="nav-item start {{ Request::is('links-pagamento*') ? 'active' : '' }}">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-link"></i>
                    <span class="title">Links de Pagamento</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item start ">
                        <a href="{!! route('links-pagamento.index') !!}" class="nav-link ">
                            <i class="icon-bar-chart"></i>
                            <span class="title">Listar</span>
                        </a>
                    </li>
                    @permission('create_vendedores')
                    <li class="nav-item start ">
                        <a href="{!! route('links-pagamento.create') !!}" class="nav-link ">
                            <i class="fa fa-hospital-o"></i>
                            <span class="title">Cadastrar</span>
                        </a>
                    </li>
                    @endpermission
                </ul>
            </li>
            @endpermission
        </ul>
    </li>
@endif

@include('mobile::menu')

@if(\Entrust::hasRole(['ADMINISTRADOR']))
    <li class="nav-item start {{ Request::is('urh*') ? 'active' : '' }}">
        <a href="{!! route('urh.create') !!}" class="nav-link nav-toggle">
            <i class="fa fa-dollar"></i>
            <span class="title">URH</span>
        </a>
    </li>
@endif

@if(\Entrust::hasRole(['ADMINISTRADOR', 'AUDITORIA']))
    <li class="nav-item start {{ (Request::is('comunicados_credenciados')) ? 'active' : '' }}">
        <a href="javascript:;" class="nav-link nav-toggle">
            <i class="fa fa-microphone"></i>
            <span class="title">Comunicados</span>
            <span class="arrow"></span>
        </a>
        <ul class="sub-menu">
            <li class="nav-item start {{ Request::is('comunicados_credenciados*') ? 'active' : '' }}">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-hospital-o"></i>
                    <span class="title">Credenciados</span>
                    <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item start ">
                        <a href="{!! route('comunicados_credenciados.listar') !!}" class="nav-link ">
                            <i class="fa fa-list"></i>
                            <span class="title">Listar</span>
                        </a>
                    </li>
                    <li class="nav-item start ">
                        <a href="{!! route('comunicados_credenciados.criar') !!}" class="nav-link ">
                            <i class="fa fa-microphone"></i>
                            <span class="title">Cadastrar</span>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>

    </li>
@endif