<table class="table table-striped table-hover order-column datatables" >
    <thead>
    <tr>
        <th> ID Pet </th>
        <th> Início do contrato </th>
        <th> Plano </th>
        <th> Regime </th>
        <th> Pet </th>
        <th> Tutor </th>
        <th> Celular </th>
        <th> Competencia </th>
        <th> Encerramento do contrato </th>
    </tr>
    </thead>
    <tbody>

    @foreach($results as $r)
        <tr>
            <td>{{ $r->id }}</td>
            <td>{{ App\Helpers\Utils::dateToBrazilianDate($r->inicio_do_contrato) }}</td>
            <td>{{ $r->plano }}</td>
            <td>{{ $r->regime }}</td>
            <td>{{ $r->pet }}</td>
            <td><a href="{{ route('clientes.edit', $r->id_tutor) }}" target="_blank">{{ $r->tutor }}</a></td>
            <td>{{ $r->celular }}</td>
            <td>{{ $competencia }}</td>
            <td>{{ $r->data_encerramento ? App\Helpers\Utils::dateToBrazilianDate($r->data_encerramento) : '-' }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
