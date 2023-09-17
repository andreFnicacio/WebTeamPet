<div class="portlet">
    <div class="portlet-body">
        <table class="table table-responsive" id="promocoes-table">
    <thead>
        <th>ID</th>
        <th>Nome</th>
        <th>Dt. Início</th>
        <th>Dt. Término</th>
        <th>Ativo</th>
        <th colspan="3" class="text-center">Ações</th>
    </thead>
    <tbody>
    @foreach($registros as $r)
        <tr>
            <td>
                {!! $r->id !!}    
                </td>
            <td>{!! $r->nome !!}</td>
            <td>{!! $r->dt_inicio->format('d/m/Y') !!}</td>
            <td>{!! $r->dt_termino ? $pets->dt_termino->format('d/m/Y') : "-" !!}</td>
            <td>
                @if($r->ativo)
                    <span class="badge badge-success">SIM</span>
                @else
                    <span class="badge badge-danger">NÃO</span>
                @endif
            </td>
            <td class="text-center" width="15%">
                {!! Form::open(['route' => ['promocoes.destroy', $r->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('promocoes.edit', [$r->id]) !!}" class='btn btn-default btn-xs btn-circle edit'>
                        <i class="fa fa-pencil"></i>
                    </a>
                    @permission('delete_clinicas')
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs btn-circle edit', 'onclick' => "return confirm('Você tem certeza?')"]) !!}
                    @endpermission
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
    </div>
</div>