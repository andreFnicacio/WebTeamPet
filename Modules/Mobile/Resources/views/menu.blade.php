@if(\Entrust::hasRole(['ADMINISTRADOR']))
    <!-- Clientes -->
    <li class="nav-item start {{ Request::is('pusher*') ? 'active' : '' }}">
        <a href="javascript:;" class="nav-link nav-toggle">
            <i class="fa fa-mobile"></i>
            <span class="title">APP Cliente</span>
            <span class="arrow"></span>
        </a>
        <ul class="sub-menu">
            <li class="nav-item start ">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="fa fa-warning"></i>
                    <span class="title">Pusher</span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item start ">
                        <a href="{{ route('mobile.pusher.index') }}" class="nav-link nav-toggle">
                            <i class="fa fa-list"></i>
                            <span class="title">Listar</span>
                        </a>
                    </li>
                    <li class="nav-item start ">
                        <a href="{{ route('mobile.pusher.create') }}" class="nav-link nav-toggle">
                            <i class="fa fa-plus-circle"></i>
                            <span class="title">Criar</span>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </li>
@endif