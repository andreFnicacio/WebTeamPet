<div class="portlet">
    <div class="portlet-body">
        <table class="table table-responsive" id="especialidades-table">
    <thead>
        <th>Nome</th>
        <th colspan="3">Ações</th>
    </thead>
    <tbody>
    @foreach($especialidades as $especialidades)
        <tr>
            <td>{!! $especialidades->nome !!}</td>
            <td>
                <div class='btn-group'>
                    <a href="{!! route('especialidades.edit', [$especialidades->id]) !!}" class='btn btn-default btn-xs btn-circle edit'>
                        <i class="fa fa-pencil"></i>
                    </a>
                </div>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
    </div>
</div>