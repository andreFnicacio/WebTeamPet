@php
    if(!isset($exportar)) {
        $exportar = false;
    }
    if(!isset($layout)) {
        $layout = 'CLIENTES';
    }
    $total = 0;
@endphp
    <table class="table table-striped table-hover order-column datatables" >
        <thead>
            <tr>
                <th> ID </th>
                <th> Usuário </th>
                <th> Tarefa </th>
                <th> Projeto </th>
                <th> Departamento </th>
                <th> Duração Total </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                @foreach($sheets as $s)
                    <tr>
                        <td> {{ $s->id }} </td>
                        <td> {{ $s->username }} </td>
                        <td> {{ $s->tarefa }} </td>
                        <td> {{ $s->projeto }} </td>
                        <td> {{ $s->departamento }} </td>
                        <td> {{ \App\Helpers\Utils::secondsToFormattedHours($s->duracao_total) }} </td>
                        @php
                            $total += $s->duracao_total;
                        @endphp
                @endforeach
            </tr>
            @unless($exportar)
            <tr>
                <td colspan="6" class="text-center">
                    Total: {{ \App\Helpers\Utils::secondsToFormattedHours($total) }}
                </td>
            </tr>
            @endunless
        </tbody>
    </table>

