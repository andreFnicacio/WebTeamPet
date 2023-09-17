@php
    if(!isset($exportar)) {
        $exportar = false;
    }
@endphp
    <table class="table table-striped table-hover order-column datatables" >

        <thead>
            <tr>
                <th> Plano </th>
                <th> ID Procedimento </th>
                <th> Procedimento </th>
                <th> Coparticipação </th>
            </tr>
        </thead>
        <tbody>
            @foreach($procedimentos as $procedimento)
                <tr>
                    <td>{{ $procedimento['nome_plano'] }}</td>
                    <td>{{ $procedimento['id_procedimento'] }}</td>
                    <td>{{ $procedimento['nome_procedimento'] }}</td>
                    <td>{{ $procedimento['coparticipacao'] ?? 'nulo' }}</td>
                 </tr>
            @endforeach
        </tbody>
    </table>
