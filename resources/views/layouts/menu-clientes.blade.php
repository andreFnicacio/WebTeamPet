<li class="nav-item start {{ Request::is('cliente.dados') ? 'active' : '' }}">
    <a href="{!! route('cliente.dados') !!}" class="nav-link" id="menu-dados-cliente">
        <i class="icon-home"></i>
        <span class="title">Dados cadastrais</span>
        <span class="arrow"></span>
    </a>
</li>
<li class="nav-item start {{ Request::is('cliente.pets') ? 'active' : '' }}">
    <a href="{!! route('cliente.pets') !!}" class="nav-link nav-toggle" id="menu-pets-cliente">
        <i class="ion-ios-paw"></i>
        <span class="title">Pets</span>
        <span class="arrow"></span>
    </a>
    <ul class="sub-menu">
        @php
            $user = Auth::user();
            $cliente = \App\Models\Clientes::where('id_usuario', $user->id)->first();
            if(!$cliente) {
                $pets = [];
            } else {
                $pets = $cliente->pets()->get();
            }

        @endphp
        @foreach($pets as $pet)
            <li class="nav-item start ">
                <a href="{{ route('cliente.pet', $pet->id) }}" class="nav-link ">
                    <span class="title">{{ $pet->nome_pet }}</span>
                </a>
            </li>
        @endforeach
    </ul>
</li>
<li class="nav-item start {{ Request::is('cliente.financeiro') ? 'active' : '' }}">
    <a href="{!! route('cliente.financeiro') !!}" class="nav-link" id="menu-financeiro-cliente">
        <i class="fa fa-money"></i>
        <span class="title">Histórico Financeiro</span>
        <span class="arrow"></span>
    </a>
</li>
<li class="nav-item start {{ Request::is('cliente.documentos') ? 'active' : '' }}">
    <a href="{!! route('cliente.documentos') !!}" class="nav-link" id="menu-documentos-cliente">
        <i class="fa fa-file"></i>
        <span class="title">Documentos</span>
        <span class="arrow"></span>
    </a>
</li>