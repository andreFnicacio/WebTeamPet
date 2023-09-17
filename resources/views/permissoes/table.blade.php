<div class="portlet">
    <div class="portlet-body">
        <table class="table table-responsive responsive" id="permissoes-table">
            <thead>
            <th>Nome</th>
            <th>Permissão</th>
            <th>Menu</th>
            <th>Descrição</th>
            <th colspan="3">Ações</th>
            </thead>
            <tbody>
            @foreach($permissoes as $permissoes)
                <tr>
                    <td>{!! $permissoes->display_name !!}</td>
                    <td>{!! $permissoes->name !!}</td>
                    <td>{!! $permissoes->menu !!}</td>
                    <td>{!! $permissoes->description !!}</td>
                    <td>
                        {!! Form::open(['route' => ['permissoes.destroy', $permissoes->id], 'method' => 'delete']) !!}
                        <div class='btn-group'>
                            @permission('delete_permissoes')
                                {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Confirma?')"]) !!}
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
