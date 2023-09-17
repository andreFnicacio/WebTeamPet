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
                    <th> Data de cadastro </th>
                    <th> Nome Cliente </th>
                    <th> CPF/CNPJ </th>
                    {{-- <th> ID Cliente (Sistema Financeiro) </th> --}}
                    <th> E-mail </th>
                    <th> Telefone </th>
                    <th> Telefone alternativo </th>
                    {{-- <th> Plano </th> --}}
                    <th> Status Financeiro do cliente </th>
                    <th> Dia do vencimento </th>
                    <th> Forma de pagamento </th>
                    <th> Quantidade de pets </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    @foreach($dados as $c)
        
                        <tr>
                            <td>{{ $c['id'] }}</td>
                            <td>{{ $c['data_cadastro'] }}</td>
                            <td><a target="_blank" href="{{ route('clientes.edit', $c['id']) }}">{{ $c['nome'] }}</a></td>
                            {{-- <td>{{ $c['id_externo'] ? $c['id_externo'] : '-' }}</td> --}}
                            <td> {{ $c['cpf_cnpj'] }} </td>
                            <td> {{ $c['email'] }} </td>
                            <td> {{ $c['celular'] }} </td>
                            <td> {{ $c['telefone_fixo'] ?? '-' }} </td>
                            {{-- <td> {!! $c['plano'] !!} </td> --}}
                            <td> {{ $c['status_financeiro'] }} </td>
                            <td> {{ $c['dia_vencimento'] ?? '-' }} </td>
                            <td> {{ $c['forma_pagamento'] == 'cartao' ? 'CARTÃO' : 'BOLETO' }} </td>
                            <td> {{ $c['quantidade_pets'] }} </td>

                    @endforeach
                </tr>
            </tbody>
        </table>
    </div>
