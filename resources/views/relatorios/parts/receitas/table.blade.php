@php
    if(!isset($exportar)) {
        $exportar = false;
    }

    $shouldDisplayDescription = isset($params) && array_key_exists('tipoReceita', $params);
@endphp
<strong>{{ count($receitas) }} resultados</strong>
<table class="table table-striped table-hover order-column datatables" >
    <thead>
        <tr>
            <th> Nome do Cliente </th>
            <th> Nome do Pet </th>
            <th> ID do Pet </th>
            <th> Nome do Plano </th>
            <th> ID do Plano </th>
            <th> Competência </th>
            <th> Data de Pagamento </th>
            <th> Valor Pago </th>
            @if($shouldDisplayDescription && $params['tipoReceita'] !== 'Fatura')
                <th> Descrição </th>
            @endif
        </tr>
    </thead>
    <tbody>
        @foreach($receitas as $receita)
            <tr>
                <td> {{ $receita->nome_cliente }} </td>
                <td> {{ $receita->nome_pet }} </td>
                <td> {{ $receita->id_pet }} </td>
                <td> {{ $receita->nome_plano }} </td>
                <td> {{ $receita->id_plano }} </td>
                <td> {{ $receita->competencia }} </td>
                <td> {{ $receita->data_pagamento->format("d/m/Y") }} </td>
                <td> {{ \App\Helpers\Utils::money($receita->valor_pago) }} </td>
                @if($shouldDisplayDescription && $params['tipoReceita'] !== 'Fatura')
                    <td> {{ $receita->complemento }} </td>
                @endif
            </tr>
        @endforeach
    </tbody>
</table>