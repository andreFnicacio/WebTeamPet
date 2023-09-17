<table class="table table-responsive" id="cobrancas-table">
    <thead>
        <th>Id Cliente</th>
        <th>Competencia</th>
        <th>Valor Original</th>
        <th>Data Vencimento</th>
        <th>Complemento</th>
        <th>Status</th>
        <th colspan="3">Ações</th>
    </thead>
    <tbody>
    @foreach($cobrancas as $c)
        <tr>
            <td>{!! $c->id_cliente !!}</td>
            <td>{!! $c->competencia !!}</td>
            <td>{!! \App\Helpers\Utils::money($c->valor_original) !!}</td>
            <td>{!! $c->data_vencimento->format('d/m/Y') !!}</td>
            <td>{!! $c->complemento !!}</td>
            <td>{!! $c->status !!}</td>
            <td>
                {!! Form::open(['route' => ['cobrancas.destroy', $c->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('cobrancas.edit', [$c->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {{--{!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}--}}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>