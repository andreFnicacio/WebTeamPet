<div class="portlet">
    <div class="portlet-body">
        <table class="table table-responsive" id="prestadores-table">
            <thead>
                <th>Nome</th>
                <th>Email</th>
                <th>Telefone</th>
                <th>CRMV</th>
                <th>Especialista</th>
                <th>Especialidade</th>
                <th width="15%" class="text-center">Ações</th>
            </thead>
            <tbody>
                @foreach($prestadores as $prestador)
                    <tr>
                        <td>{!! $prestador->nome !!}</td>
                        <td>{!! $prestador->email !!}</td>
                        <td>{!! $prestador->telefone !!}</td>
                        <td>{!! $prestador->crmv !!}</td>
                        <td class="text-center">
                            @if($prestador->especialista)
                                <i class="fa fa-circle" data-toggle="tooltip" title="SIM"></i>
                            @else
                                <i class="fa fa-circle-o" data-toggle="tooltip" title="NÃO"></i>
                            @endif
                        </td>
                        <td>{!! $prestador->nome_especialidade ? $prestador->nome_especialidade : '-'!!}</td>
                        <td class="text-center" width="15%">
                            {!! Form::open(['route' => ['prestadores.destroy', $prestador->id], 'method' => 'delete']) !!}
                            <div class='btn-group'>
                                <a href="{!! route('prestadores.edit', [$prestador->id]) !!}"  class='btn btn-default btn-xs btn-circle edit'>
                                    <i class="fa fa-pencil"></i>
                                </a>
                                @permission('delete_prestadores')
                                {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs btn-circle edit', 'onclick' => "return confirm('Você tem certeza?')"]) !!}
                                @endpermission
                            </div>
                            {!! Form::close() !!}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @include('common.pagination', ['route' => route('prestadores.index')])
    </div>
</div>