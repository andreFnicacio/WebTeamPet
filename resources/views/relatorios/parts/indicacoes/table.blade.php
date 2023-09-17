<table class="table table-striped table-hover order-column datatables" >
    <thead>
    <tr>
        <th> Data de cadastro </th>
        <th> Nome </th>
        <th> E-mail </th>
        <th> Telefone </th>
        <th> Cliente que indicou </th>
        {{-- <th> Plano </th> --}}
    </tr>
    </thead>
    <tbody>

    @foreach($indicacoes as $c)
        <tr>
            <td>{{ $c->created_at->format('d/m/Y H:i:s') }}</td>
            <td>{{ $c->nome }}</td>
            <td>{{ $c->email }}</td>
            <td>{{ $c->telefone }}</td>
            <td>{{ $c->cliente->nome_cliente }}</td>
          </tr>
    @endforeach
    </tbody>
</table>
