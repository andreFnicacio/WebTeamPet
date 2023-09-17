<div class="portlet">
    <div class="portlet-body">
        <table class="table table-responsive" id="papels-table">
    <thead>
        <th>Nome</th>
        <th>Papel</th>
        <th>Descrição</th>
        <th colspan="3">Ações</th>
    </thead>
    <tbody>
    @foreach($roles as $papel)
        <tr>
            <td>{!! $papel->display_name !!}</td>
            <td>{!! $papel->name !!}</td>
            <td>{!! $papel->description !!}</td>
            <td>
                {!! Form::open(['route' => ['papeis.destroy', $papel->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('papeis.edit', [$papel->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
    </div>
</div>