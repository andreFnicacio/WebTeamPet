<div class="portlet">
    <div class="portlet-body">
        <div class="table-wrapper">
            <table class="table table-responsive datatable table-hover responsive" id="atas-table">
                <thead>
                <th>ID</th>
                <th>Título</th>
                <th>Realização</th>
                <th>Ações</th>
                </thead>
                <tbody>
                @foreach($comunicados_credenciados as $comunicado)
                    <tr>
                        <td>{{ $comunicado->id  }}</td>
                        <td>{{ $comunicado->titulo }}</td>
                        <td>
                            {{ \Carbon\Carbon::parse($comunicado->published_at)->format('d/m/Y H:i') }}
                        </td>
                        <td>
                            <a href="#" target="_blank" class="btn btn-danger btn-xs btn-circle edit">
                                <i class="fa fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

        </div>
    </div>
</div>

