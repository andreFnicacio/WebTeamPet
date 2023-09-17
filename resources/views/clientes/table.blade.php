<div class="portlet">
    <div class="portlet-body">
        <div class="table-wrapper">
            <table class="table table-responsive datatable table-hover responsive" id="clientes-table">
                <thead>
                <th>ID</th>
                <th>Estado</th>
                <th>Contrato</th>
                <th>Nome</th>
                <th>CPF</th>
                <th>Email</th>
                <th>Status</th>
                <th>Sexo</th>
                <th>Qtd Pets</th>
                <th>Tipo do Pet</th>
                <th>Ano de Adesão</th>
                <th colspan="3">Ações</th>
                </thead>
                <tbody>
                @foreach($clientes as $clientes)
                    <tr>
                        <td>{{ $clientes->id }}</td>
                        <td>{{ $clientes->estado }}</td>
                        <td>{{ $clientes->numero_contrato }}</td>
                        <td>{{ $clientes->nome_cliente }}</td>
                        <td>{{ $clientes->cpf }}</td>
                        <td>{{ $clientes->email }}</td>
                        <td>{!! $clientes->ativo ? '<span class="badge badge-success"> Ativo </span>' : '<span class="badge badge-warning">Inativo</span>' !!}</td>
                        <td>{{ $clientes->sexo == 'F' ? 'Feminino' : 'Masculino' }}</td>
                        <td>{{ $clientes->pets->count() }}</td>
                        <td>{{ $clientes->getTiposPets() }}</td>
                        <td>{{ $clientes->created_at->format("Y") }}</td>
                        <td>
                            {!! Form::open(['route' => ['clientes.destroy', $clientes->id], 'method' => 'delete']) !!}
                            <div class='btn-group'>
                                <a href="{!! route('clientes.edit', [$clientes->id]) !!}" class='btn btn-default btn-xs btn-circle edit'>
                                    <i class="fa fa-pencil"></i>
                                </a>
                            </div>
                            {!! Form::close() !!}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            @include('common.pagination', ['route' => route('clientes.index')])
        </div>
    </div>
</div>

