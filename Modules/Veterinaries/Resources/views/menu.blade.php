@permission('list_prestadores')
<!-- Prestadores -->
<li class="nav-item start {{ Request::is('prestadores*') ? 'active' : '' }}">
    <a href="javascript:;" class="nav-link nav-toggle">
        <i class="fa fa-stethoscope"></i>
        <span class="title">Veterinários</span>
        <span class="arrow"></span>
    </a>

    <ul class="sub-menu">
        <li class="nav-item start ">
            <a href="{!! route('prestadores.index') !!}" class="nav-link ">
                <i class="icon-bar-chart"></i>
                <span class="title">Listar</span>
            </a>
        </li>
        @permission('create_prestadores')
        <li class="nav-item start ">
            <a href="{!! route('prestadores.create') !!}" class="nav-link ">
                <i class="fa fa-stethoscope"></i>
                <span class="title">Cadastrar</span>
            </a>
        </li>
        @endpermission
    </ul>
</li>
@endpermission