@permission('list_clinicas')
<!-- Clínicas -->
<li class="nav-item start {{ Request::is('clinicas*') ? 'active' : '' }}">
    <a href="javascript:;" class="nav-link nav-toggle">
        <i class="fa fa-hospital-o"></i>
        <span class="title">Clínicas</span>
        <span class="arrow"></span>
    </a>
    <ul class="sub-menu">
        <li class="nav-item start ">
            <a href="{!! route('clinicas.index') !!}" class="nav-link ">
                <i class="icon-bar-chart"></i>
                <span class="title">Listar</span>
            </a>
        </li>
        @permission('create_clinicas')
        <li class="nav-item start ">
            <a href="{!! route('clinicas.create') !!}" class="nav-link ">
                <i class="fa fa-hospital-o"></i>
                <span class="title">Cadastrar</span>
            </a>
        </li>
        @endpermission
    </ul>

</li>
@endpermission