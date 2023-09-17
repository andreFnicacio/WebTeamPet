<div class="portlet">
    <div class="portlet-body">
        <div class="table-wrapper">
            <table class="table table-responsive responsive" id="pets-table">
                <thead>
                <th>ID</th>
                <th>Microchip</th>
                <th>Nome</th>
                <th>Espécie</th>
                <th>Raca</th>
                <th>Cliente</th>
                <th>Plano</th>
                <th>Tipo do plano</th>
                <th>Status</th>
                <th colspan="3">Ações</th>
                </thead>
                <tbody class="Pets List">
                @foreach($pets as $pets)
                    <tr>
                        <td>{!! $pets->id !!}</td>
                        <td><a title="Emitir Guia" href="{!! route('autorizador.home', ['microchip' => $pets->numero_microchip]) !!}">{!! $pets->numero_microchip !!}</a></td>
                        <td>{!! $pets->nome_pet !!}</td>
                        <td><span class="Item {{ ($pets->tipo == "GATO") ? "Cat" : "Dog" }}"></span></td>
                        <td>{!! $pets->nome_raca !!}</td>
                        <td>
                            <a href="{{ route('clientes.edit', $pets->id_cliente) }}" target="_blank">
                                {!! $pets->nome_cliente !!}
                            </a>
                        </td>

                        <td>{!! $pets->nome_plano !!}</td>
                        <td>
                            @php
                                $tipoPlano = $pets->familiar ? "familiar" : "individual";
                            @endphp
                            <span class="badge bg-{{ $pets->familiar ? "blue-steel" : "purple-sharp" }}">{{ ucwords($tipoPlano) }}</span>
                        </td>
                        <td>
                            @php
                                $status = $pets->ativo ? "Ativo" : "Inativo";
                            @endphp
                            <span class="badge bg-{{ $pets->ativo ? "green-jungle" : "yellow-saffron" }}">{{ ucwords($status) }}</span>
                        </td>
                        <td>
                            {!! Form::open(['route' => ['pets.destroy', $pets->id], 'method' => 'delete']) !!}
                            <div class='btn-group'>
                                <a href="{!! route('pets.edit', [$pets->id]) !!}" class='btn btn-default btn-xs btn-circle edit'>
                                    <i class="fa fa-pencil"></i>
                                </a>
                            </div>
                            {!! Form::close() !!}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            @include('common.pagination', ['route' => route('pets.index')])
        </div>
    </div>
</div>
