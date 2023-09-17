@php
    if(!isset($exportar)) {
        $exportar = false;
    }
@endphp
    <strong>{{ $total }} resultados</strong>
    <div class="table-responsive">
        <table class="table table-hover order-column datatables" >
            <thead>
                <tr>
                    <th> Nome Cliente </th>
                    <th> CPF/CNPJ </th>
                    <th> Telefone </th>
                    <th> E-mail </th>
                    <th> Status </th>
                    <th> Competência </th>
                    <th> Vencimento </th>
                    <th> Valor </th>
                    <th> Status Financeiro </th>
                </tr>
            </thead>
            <tbody>
                @foreach($clientesInadimplentes as $inadimplente)
                    <tr>
                        <td class="col-md-2"><a target="_blank" href="{{ route('clientes.edit', $inadimplente['id_cliente']) }}">{{ $inadimplente['nome_cliente'] }}</a></td>
                        <td class="col-md-2"> {{ App\Helpers\Utils::formataCPF($inadimplente['cpf_cnpj']) }} </td>
                        <td class="col-md-2"> {{ App\Helpers\Utils::formataTelefone($inadimplente['telefone']) }} </td>
                        <td class="col-md-1"> {{ $inadimplente['email'] }} </td>
                        <td class="col-md-1">
                            <span class="badge {{ $inadimplente['status'] ? 'badge-success' : 'badge-danger' }}"> 
                                {{ $inadimplente['status'] ? 'ATIVO' : 'INATIVO'}} 
                            </span>
                        </td>
                        <td class="col-md-1"> {{ $inadimplente['competencia'] }} </td>
                        <td class="col-md-1"> {{ $inadimplente['data_vencimento']->format('d/m/Y') }} </td>
                        <td class="col-md-2"> {{ App\Helpers\Utils::money($inadimplente['valor']) }} </td>
                        <td class="col-md-1"> {{ $inadimplente['statusFinanceiro'] }} </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
