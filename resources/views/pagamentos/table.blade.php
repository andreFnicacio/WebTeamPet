<table class="table table-responsive" id="pagamentos-table">
    <thead>
        <th>Cobrança</th>
        <th>Data Pagamento</th>
        <th>Complemento</th>
        <th>Valor Pago</th>
        <th>Forma Pagamento</th>
        <th colspan="3">Action</th>
    </thead>
    <tbody>
    @foreach($pagamentos as $p)
        <tr>
            <td>{!! $p->id_cobranca . ' - ' . $p->cobranca()->first()->competencia !!}</td>
            <td>{!! $p->data_pagamento !!}</td>
            <td>{!! $p->complemento !!}</td>
            <td>{!! number_format($p->valor_pago, 2, ',', '.') !!}</td>
            <td>{!! $p->forma_pagamento !!}</td>
            <td>
                {!! Form::open(['route' => ['pagamentos.destroy', $p->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('pagamentos.edit', [$p->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>