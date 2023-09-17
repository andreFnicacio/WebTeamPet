<div class="portlet">
    <div class="portlet-body">
        <table class="table table-responsive" id="procedimentos-table">
            <thead>
                <th>#</th>
                <th>Título</th>
                <th>Mensagem</th>
                <th>Clientes</th>
                <th>Status</th>
                <th>Progresso</th>
            </thead>
            <tbody>
            @foreach($pushes as $push)
                <tr>
                    <td>{{ $push->id }}</td>
                    <td>{{ $push->title }}</td>
                    <td>{{ $push->message }}</td>
                    <td>{{ $push->count }}</td>
                    <td>{{ $push->status }}</td>
                    <td>{{ $push->progress }} / {{ $push->count }} ({!! number_format(($push->progress/$push->count) * 100, 2) + 0 !!}%)</td>
                    <td>{{ $push->created_at->format(\App\Helpers\Utils::BRAZILIAN_DATETIME) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>