<table class="table table-responsive table-striped" id="lptCodigosPromocionais-table">
    <thead>
        <th>Codigo</th>
        <th>Expira Em</th>
        <th>Desconto</th>
        <th>Plano</th>
        <th>Aplicabilidade</th>
        <th>Permanente?</th>
        <th colspan="3">Ações</th>
    </thead>
    <tbody>
    @foreach($codigosPromocionais as $codigoPromocional)
        <tr>
            <td>{!! $codigoPromocional->codigo !!}</td>
            <td>{!! $codigoPromocional->expira_em->format(\App\Helpers\Utils::BRAZILIAN_DATE) !!}</td>
            <td>{!! $codigoPromocional->tipo_desconto === 'fixo' ? \App\Helpers\Utils::money($codigoPromocional->desconto) : $codigoPromocional->desconto . '%' !!}</td>
            <td>{!! $codigoPromocional->plano->nome_plano !!}</td>
            <td>{!! $codigoPromocional->aplicabilidadeForHumans !!}</td>
            <td>{!! $codigoPromocional->permanente ? 'SIM' : 'NÃO' !!}</td>
            <td>

                {!! Form::open(['route' => ['lifepet-para-todos.codigos-promocionais.excluir', $codigoPromocional->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{{ route('lifepet-para-todos.codigos-promocionais.editar', ['id' => $codigoPromocional->id]) }}" class="btn btn-default btn-xs">
                        <i class="glyphicon glyphicon-pencil"></i>
                    </a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Tem certeza que gostaria de excluir?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>

        </tr>
    @endforeach
    </tbody>
</table>