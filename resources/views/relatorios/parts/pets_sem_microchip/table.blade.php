@php
    if(!isset($exportar)) {
        $exportar = false;
    }
@endphp
    <strong>{{ count($pets) }} resultados</strong>
    <table class="table table-striped table-hover order-column datatables" >
        <thead>
            <tr>
                <th> Microchip </th>
                <th> Nome Pet </th>
                <th> Plano </th>
                <th> Início do Contrato </th>
                <th> Cliente </th>
                <th> Cidade </th>
                <th> Estado </th>
                <th> Email </th>
                <th> Celular </th>
                <th> Guias Liberadas </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                @foreach($pets as $p)
                    <tr>
                        <td>{{ $p->numero_microchip }}</td>
                        <td><a target="_blank" href="{{ route('pets.edit', $p->id) }}">{{ $p->pet }}</a></td>
                        <td> {{ $p->plano }} </td>
                        <td> {{ \App\Helpers\Utils::dateTime(\Carbon\Carbon::createFromFormat('Y-m-d', $p->data_inicio_contrato), 'd/m/Y') }} </td>
                        <td> {{ $p->cliente }} </td>
                        <td> {{ $p->cidade }} </td>
                        <td> {{ $p->estado }}</td>
                        <td> {{ $p->email }} </td>
                        <td> {{ $p->celular }} </td>
                        <td> {{ $p->historicoUsos()->where('status', 'LIBERADO')->count() }} </td>
                @endforeach
            </tr>
        </tbody>
    </table>
