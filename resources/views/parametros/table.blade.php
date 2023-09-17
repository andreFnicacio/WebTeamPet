<div class="portlet">
    <div class="portlet-body">
        <div class="table-wrapper">
            <table class="table table-responsive datatable table-hover responsive" id="parametros-table">
                <thead>
                    <th>Tipo</th>
                    <th>Chave</th>
                    <th>Valor</th>
                    <th>Descricao</th>
                    <th colspan="3">Ações</th>
                </thead>
                <tbody>
                @foreach($parametros as $parametros)
                    <tr>
                        <td>{!! $parametros->tipo !!}</td>
                        <td>{!! $parametros->chave !!}</td>
                        <td>{!! $parametros->valor !!}</td>
                        <td>{!! $parametros->descricao !!}</td>
                        <td>
                            {!! Form::open(['route' => ['parametros.destroy', $parametros->id], 'method' => 'delete']) !!}
                            <div class='btn-group'>
                                <a href="{!! route('parametros.edit', [$parametros->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                                {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                            </div>
                            {!! Form::close() !!}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>