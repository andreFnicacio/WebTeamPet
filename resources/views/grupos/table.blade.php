<div class="portlet">
    <div class="portlet-body">
        <table class="table table-responsive" id="grupos-table">
            <thead>
                <th>Nome</th>
                <th class="text-center">Procedimentos</th>
                <th class="text-center" colspan="3">Ações</th>
            </thead>
            <tbody>
            @foreach($grupos as $grupo)
                <tr>
                    <td>{!! $grupo->nome_grupo !!}</td>
                    <td class="text-center">
                        <span class="number">
                            {!! \App\Models\Procedimentos::where('id_grupo', $grupo->id)->count() !!}
                        </span>
                    </td>
                    <td class="text-center">
                        {!! Form::open(['route' => ['grupos.destroy', $grupo->id], 'method' => 'delete']) !!}
                        <div class='btn-group'>
                            {{--<a href="{!! route('grupos.show', [$grupos->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>--}}
                            <a href="{!! route('grupos.edit', [$grupo->id]) !!}" class='btn btn-default btn-xs btn-circle edit'><i class="fa fa-pencil"></i></a>
                            {{--{!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}--}}
                        </div>
                        {!! Form::close() !!}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>