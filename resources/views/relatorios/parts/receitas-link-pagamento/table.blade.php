@php
    if(!isset($exportar)) {
        $exportar = false;
    }
    if(!isset($layout)) {
        $layout = 'CLIENTES';
    }
    $total = 0;
@endphp
    <strong>{{ count($receitas) }} resultados</strong>
    <table class="table table-striped table-hover order-column datatables" >
        <thead>
            <tr>
                <th style="width:30%"> Nome do Cliente </th>
                <th style="width:10%"> Data de Pagamento </th>
                <th style="width:10%"> Valor Pago </th>
                <th style="width:10%"> Tag </th>
                <th style="width:25%"> Descrição </th>
            </tr>
        </thead>
        <tbody>
            @foreach($receitas as $receita)
                <tr>
                    <td> {{ $receita->nome_cliente }} </td>
                    <td> {{ (new \Carbon\Carbon($receita->updated_at))->format('d/m/Y') }} </td>
                    <td> {{ \App\Helpers\Utils::money($receita->valor) }} </td>
                    <td> {{ $receita->tags }} </td>
                    <td> {{ $receita->descricao }} </td>
                </tr>
            @endforeach
        </tbody>
    </table>

