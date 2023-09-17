@php
    if(!isset($exportar)) {
        $exportar = false;
    }
@endphp
    <strong>{{ $total }} resultados</strong>
    <table class="table table-striped table-hover order-column datatables" >
        <thead>
            <tr>
                <th> Data do cancelamento </th>
                <th> Nome Cliente </th>
                <th> Status </th>
                <th> Celular </th>
                <th> E-mail </th>
                <th> ID Cliente (Sistema Financeiro) </th>
                <th> Nome Pet </th>
                <th> Status </th>
                <th> Plano </th>
                <th> Motivo do cancelamento </th>
                <th> Status Financeiro do cliente </th>
                <th> Regime </th>
                <th> Valor </th>
                <th> Sinistralidade </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                @foreach($cancelamentos as $c)
                    @php
                       
                    @endphp
                    <tr>
                        <td>{{ $c['data_cancelamento'] }}</td>
                        <td><a target="_blank" href="{{ $c['cliente'] ? route('clientes.edit', $c['cliente']['id']) : 'javascript:;' }}">{{ $c['cliente'] ? $c['cliente']['nome'] : '-' }}</a></td>
                        <td>{{ $c['cliente'] ? ($c['cliente']['ativo'] ? 'ATIVO' : 'INATIVO') : '-' }}</td>
                        <td>{{ $c['cliente'] ? $c['cliente']['celular'] : '-' }}</td>
                        <td>{{ $c['cliente'] ? $c['cliente']['email'] : '-' }}</td>
                        <td>{{ $c['cliente'] ? $c['cliente']['id_externo'] : '-' }}</td>

                        <td><a target="_blank" href="{{ $c['pet'] ? route('pets.edit', $c['pet']['id']) : 'javascript:;' }}">{{ $c['pet'] ? $c['pet']['nome'] : '-' }}</a></td>
                        <td> {{ $c['pet'] ? ($c['pet']['ativo'] ? 'ATIVO' : 'INATIVO') : '-' }} </td>
                        <td> {!! $c['plano'] !!} </td>
                        <td> {{ $c['motivo'] }} </td>
                        <td> {{ $c['status_financeiro'] }} </td>
                        <td> {{ $c['pet'] ? $c['pet']['regime'] : '-' }} </td>
                        <td> {{ $c['pet'] ? 'R$ ' . number_format($c['pet']['valor'], 2, ',', '.')  : '-' }} </td>
                        <td> {{ $c['pet'] ? 'R$ ' . number_format($c['sinistralidade'], 2, ',', '.')  : '-' }} </td>
                @endforeach
            </tr>
        </tbody>
    </table>
