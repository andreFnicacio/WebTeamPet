<table class="table table-striped table-hover order-column datatables" >
    <thead>
    <tr>
        <th> Data de cadastro </th>
        <th> Nome </th>
        {{-- <th> ID Cliente (Sistema Financeiro) </th> --}}
        <th> E-mail </th>
        <th> Celular </th>
        <th> CPF </th>
        {{-- <th> Plano </th> --}}
        <th> Pets </th>
        <th> Plano </th>
        <th> Localidade </th>
        <th> Pagamento confirmado? </th>
        <th> Concluído? </th>
        <th> Cupom </th>
        <th> Link </th>
    </tr>
    </thead>
    <tbody>

    @foreach($compras as $c)

        <tr>
            <td>{{ $c->created_at->format('d/m/Y H:i:s') }}</td>
            <td>{{ $c->nome }}</td>
            <td>{{ $c->email }}</td>
            <td>{{ $c->celular }}</td>
            <td>{{ $c->cpf }}</td>
            <td>{{ $c->pets }}</td>
            <td> {{ $c->plano ? $c->plano->id .  ' - ' . $c->plano->nome_plano : ' - ' }} </td>
            <td>{{ $c->cidade . '/' . $c->estado }}</td>
            <td>{{ is_null($c->pagamento_confirmado) ? ' - ' : ($c->pagamento_confirmado ? 'SIM' : 'NÃO') }}</td>
            <td class="{{ $c->concluido ? 'font-green' : 'font-red' }}">{{ $c->concluido ? 'SIM' : 'NÃO' }}</td>
            <td>{{ $c->cupom ? strtoupper($c->cupom->codigo) : ' - ' }}</td>
            <td><a class="btn btn-default {{ $c->concluido ? 'disabled' : '' }}" target="_blank" href="{{ route('api.assinaturas.concluir', ['hash' => $c->hash]) }}"><span class="fa fa-external-link"></span></a></td>
        </tr>
    @endforeach
    </tbody>
</table>
