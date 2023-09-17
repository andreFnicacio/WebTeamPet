<table class="table table-responsive table-striped" id="lptCodigosPromocionais-table">
    <thead>
        <th>#</th>
        <th>Plano</th>
        <th>Pets</th>
        <th>Preço</th>
    </thead>
    <tbody>
    @foreach($tabelas as $tabela)
        <tr>
            <td>{!! $tabela->id !!}</td>
            <td>{{ $tabela->id_plano }} - {!! $tabela->plano->nome_plano !!} &nbsp; <span class="badge {{ $tabela->regime == 'ANUAL' ? 'badge-info' : 'badge-default' }}">{{ $tabela->regime }}</span></td>
            <td>{!! $tabela->pets !!}</td>
            <td>{!! \App\Helpers\Utils::money($tabela->preco) !!}</td>
        </tr>
    @endforeach
    </tbody>
</table>