@php
    if(!isset($exportar)) {
        $exportar = false;
    }
    if(!isset($layout)) {
        $layout = 'CLIENTES';
    }
    $total = 0;
@endphp
    <strong>{{ count($dados) }} resultados</strong>
    <table class="table table-striped table-hover order-column datatables" >
        <thead>
            <tr>
                <th> ID Pet </th>
                <th> Nome do Pet </th>
                <th> Nome do Tutor </th>
                <th> Plano </th>
                <th> Data Contrato </th>
                <th> Telefone </th>
                <th> Email </th>
                <th> Regime </th>
                <th> Valor Faturado / Ano </th>
                <th> Valor Utilizado </th>
                <th> Relação de Uso </th>
                <th> Reajuste </th>
            </tr>
        </thead>
        <tbody>
            @foreach($dados as $d)
                <tr>
                    <td> {{ $d['pet']->id }} </td>
                    <td> {{ $d['pet']->nome_pet }} </td>
                    <td> {{ $d['cliente']->nome_cliente }} </td>
                    <td> {{ $d['plano']->nome_plano }} </td>
                    <td> {{ $d['pet']->petsPlanosAtual()->first() ? $d['pet']->petsPlanosAtual()->first()->data_inicio_contrato->format(\App\Helpers\Utils::BRAZILIAN_DATE) : '-' }} </td>
                    <td> {{ $d['cliente']->celular }} </td>
                    <td> {{ $d['cliente']->email }} </td>
                    <td> {{ $d['pet']->regime }} / {{ $d['pet']->participativo ? 'PARTICIPATIVO' : 'INTEGRAL' }} </td>
                    <td> {{ \App\Helpers\Utils::money($d['valorPago']) }} </td>
                    <td> {{ \App\Helpers\Utils::money($d['valorUtilizado']) }} </td>
                    <td> {{ $d['relacao_uso'] }}% </td>
                    <td> {{ $d['reajuste'] }} </td>
                </tr>
            @endforeach
        </tbody>
    </table>

