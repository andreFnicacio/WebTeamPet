@php
    if(!isset($exportar)) {
        $exportar = false;
    }
@endphp
<strong>{{ $total }} resultados</strong>
<div class="table-responsive">
    <table class="table table-striped table-hover order-column datatables" >

        <thead>
        <tr>
            <th> ID </th>
            <th> Nome </th>
            <th> Plano </th>
            <th> Regime </th>
            <th> Valor </th>
            <th> Plano Atual</th>>
            <th> Valor momento</th>
            <th> Data de criação </th>
            <th> Ativo </th>
            <th> Data de início do contrato </th>
            <th> Dia do vencimento </th>
            <th> Forma de pagamento </th>
            <th> Tutor </th>
            <th> CPF/CNPJ</th>
            <th> Cidade </th>
            <th> UF </th>
            <th> E-mail </th>
            <th> Telefone </th>
        </tr>
        </thead>
        <tbody>
        @foreach($pets as $p)
            <tr>
                <td>{{ $p->id }}</td>
                <td>{{ $p->nome_pet }}</td>
                <td>{{ $p->nome_plano }}</td>
                <td>{{ $p->regime }}</td>
                <td>{{ \App\Helpers\Utils::money($p->valor_plano) }}</td>
                <td>{{ $p->plano()->nome_plano }}</td>
                <td>{{ \App\Helpers\Utils::money($p->valor_plano) }}</td>
                <td>{{   Carbon\Carbon::parse($p->data_criacao)->format('d/m/Y') }}</td>
                <td>{{ $p->ativo ? 'ATIVO' : 'INATIVO' }}</td>

                <td>{{ Carbon\Carbon::createFromFormat('Y-m-d', $p->inicio_contrato)->format('d/m/Y') }}</td>
                <td>{{ $p->dia_vencimento ?? '-' }}</td>
                <td>{{ $p->forma_pagamento == 'cartao' ? 'CARTÃO' : 'BOLETO' }}</td>
                <td>{{ $p->nome_cliente }}</td>
                <td>{{ $p->cpf }}</td>
                <td>{{ $p->cidade }}</td>
                <td>{{ $p->uf }}</td>
                <td>{{ $p->cliente ? $p->cliente->email : ' - ' }}</td>
                @if($p->cliente)
                    <td>{{ $p->cliente ? $p->cliente->celular : ' - ' }}{{ $p->cliente->telefone ? (" / " . $p->cliente->telefone) : ''  }}</td>
                @else
                    <td> - </td>
                @endif
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
