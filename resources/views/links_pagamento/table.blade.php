<table class="table table-responsive table-striped table-hover" id="linkPagamentos-table">
    <thead>
        <th>Cliente</th>
        <th>Valor</th>
        <th>Parcelas</th>
        <th>Expira em</th>
        <th>Tag</th>
        <th>Descricao</th>
        <th>Status</th>
        <th colspan="3">Ações</th>
    </thead>
    <tbody>
    @foreach($linksPagamento as $l)
        @php
            if (!$l->cliente) {
                continue;
            }
        @endphp
        <tr>
            <td><a href="{{ route('clientes.edit', $l->cliente->id) }}" target="_blank">{!! $l->cliente->nome_cliente !!}</a></td>
            <td>R$ {!! number_format($l->valor, 2, ',', '.') !!}</td>
            <td>{!! $l->parcelas !!}x</td>
            <td>{!! $l->expires_at->format('d/m/Y') !!}</td>
            <td>{!! str_replace(';', ', ', $l->tags) !!}</td>
            <td>{!! $l->descricao !!}</td>
            <td>{!! $l->status !!}</td>
            <td>
                {!! Form::open(['route' => ['links-pagamento.destroy', $l->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('links-pagamento.form-pagamento', [$l->hash]) !!}" class='btn btn-default btn-xs' target="_blank"><i class="fa fa-link"></i></a>
                    <a href="{!! route('links-pagamento.edit', [$l->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
{{--                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Tem certeza que deseja excluir?')"]) !!}--}}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>