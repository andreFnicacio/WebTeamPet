@permission('listar_guias')
<!-- Especialidades -->
<li class="nav-item start notificable {{ Request::is('especialidades*') ? 'active' : '' }}">
    @php
        $start = new \Carbon\Carbon('first day of this month');
        $end = new \Carbon\Carbon('last day of this month');
        $waiting = \Modules\Guides\Entities\HistoricoUso::where('status', 'AVALIANDO')->whereBetween('created_at', [$start, $end])->count();
    @endphp
    @if($waiting)
        <div class="notification-group" style="display:none">
            @if($waiting)
                <span class="notification bg-blue" data-toggle="tooltip" data-placement="bottom"
                      title="Guias aguardando avaliação">
                    {{ $waiting }}
                </span>
            @endif
        </div>
    @endif
    <a href="javascript:;" class="nav-link nav-toggle">
        <i class="fa fa-tags"></i>
        <span class="title">Guias</span>
        <span class="arrow"></span>
    </a>
    <ul class="sub-menu">
        @permission('emitir_guia')
        <li class="nav-item start ">
            <a href="javascript:;" class="nav-link nav-toggle">
                <i class="fa fa-tag"></i>
                <span class="title">Emitir guia</span>
                <span class="arrow"></span>
            </a>
            <ul class="sub-menu">
                <li class="nav-item start ">
                    <a href="{{ $emergencia ? route('autorizador.home'). "?tipo_atendimento=EMERGENCIA" : 'javascript:;' }}"
                       class="nav-link link-disabled"
                       data-toggle="tooltip"
                       data-placement="right"
                       data-original-title="Disponível apenas em horário emergencial."
                    >
                        <i class="fa fa-warning"></i>
                        <span class="title">Guia emergencial</span>
                    </a>
                </li>
                <li class="nav-item start ">
                    <a href="{{ $emergencia ? 'javascript:;' : route('autorizador.home') . "?tipo_atendimento=NORMAL"}}"
                       class="nav-link link-disabled"
                       data-toggle="tooltip"
                       data-placement="right"
                       data-original-title="Após às 20:00h somente guias emergenciais são permitidas."
                    >
                        <i class="fa fa-tag"></i>
                        <span class="title">Guia comum</span>
                    </a>
                </li>
                <li class="nav-item start ">
                    <a href="{!! route('autorizador.home') !!}?tipo_atendimento=ENCAMINHAMENTO"
                       class="nav-link link-disabled" data-toggle="tooltip" data-placement="right">
                        <i class="fa fa-forward"></i>
                        <span class="title">Guia de encaminhamento</span>
                    </a>
                </li>
            </ul>
        </li>
        @endpermission
        @permission('listar_guias')
        <li class="nav-item start ">
            <a href="{!! route('autorizador.verGuias') !!}" class="nav-link ">
                <i class="fa fa-tags"></i>
                <span class="title">Listar guias</span>
            </a>
        </li>
        @endpermission
        @role(['AUTORIZADOR', 'ADMINISTRADOR', 'CLINICAS', 'GRUPO_HOSTPITALAR'])
        <li class="nav-item start ">
            <a href="{!! route('autorizador.verGuiasGlosadas') !!}" class="nav-link ">
                <i class="fa fa-exclamation-circle"></i>
                <span class="title">Guias glosadas</span>
            </a>
        </li>
        @endrole
        @permission('listar_guias_encaminhamento')
        <li class="nav-item start ">
            <a href="{!! route('autorizador.guiasEncaminhamento') !!}" class="nav-link ">
                <i class="fa fa-tags"></i>
                <span class="title">Listar guias de encaminhamento</span>
            </a>
        </li>
        @endpermission

        @if(\Entrust::hasRole(['CLINICAS']))
            @if ($clinica)
                <li class="nav-item start ">
                    <a href="{!! route('autorizador.buscarEncaminhamento') !!}" class="nav-link ">
                        <i class="fa fa-search"></i>
                        <span class="title">Buscar guia de encaminhamento</span>
                    </a>
                </li>
            @endif
        @endif

        @permission('listar_guias_cancelar')
        <li class="nav-item start ">
            <a href="{!! route('autorizador.guiasCancelar') !!}" class="nav-link ">
                <i class="fa fa-tags"></i>
                <span class="title">Listar guias à cancelar</span>
            </a>
        </li>
        @endpermission
    </ul>
</li>
@endpermission