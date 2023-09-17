<div class="portlet">
    <div class="portlet-body">
        <table class="table table-responsive" id="grupos-table">
            <thead>
                <th>Nome</th>
                <th>E-mail</th>
                <th colspan="3">Ações</th>
            </thead>
            <tbody>
            @foreach($usuarios as $user)
                <tr>
                    <td>{!! $user->name !!}</td>
                    <td>{!! $user->email !!}</td>
                    <td>
                        {!! Form::open(['route' => ['usuarios.destroy', $user->id], 'method' => 'delete']) !!}
                        <div class='btn-group'>
                            <a href="{!! route('usuarios.edit', [$user->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                            @permission('delete_usuarios')
                            {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Você tem certeza?')"]) !!}
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