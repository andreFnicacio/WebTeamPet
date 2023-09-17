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
                <th> Vendedor </th>
                <th> Plano </th>
                <th> Valor </th>
                <th> Comissão </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                @foreach($vendas as $v)
                    <tr>
                        <td> {{ $v->id }} </td>
                        <td> {{ $v->nome }} </td>
                        <td> {{ $v->plano }} </td>
                        <td> {{ \App\Helpers\Utils::money($v->valor) }} </td>
                        <td> {{ \App\Helpers\Utils::money($v->comissao) }} </td>
                        @php
                            $total += $v->comissao;
                        @endphp
                @endforeach
            </tr>
            @unless($exportar)
            <tr>
                <td colspan="6" class="text-center">
                    Comissão Total: {{ \App\Helpers\Utils::money($total) }}
                </td>
            </tr>
            @endunless
        </tbody>
    </table>

