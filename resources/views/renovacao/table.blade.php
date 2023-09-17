<h3>Com reajuste</h3>
<br>
<div class="tableFixHead">
    <table class="table table-responsive table-hover " id="renovacao-table">
        <thead>
            <th>ID Pet</th>
            <th>Nome pet</th>
            <th>Tutor</th>
            <th>Plano</th>
            <th>Valor antes da renovação</th>
            <th>Reajuste</th>
            <th>Valor reajustado sem desconto</th>
            <th>Desconto</th>
            <th>Valor reajustado com desconto</th>
            <th>Relação de uso</th>
            <th>Início do contrato</th>
            <th>Link de pagamento</th>
            <th>Pagamento</th>
            <th>Status</th>
            <th>Regime</th>
            <th>Competência</th>
            <th>Data de criação</th>
            <th colspan="3">Editar</th>
        </thead>
        <tbody>
        @foreach($renovacoes_optantes as $renovacao)
            <tr>
                <td>{!! $renovacao->id_pet !!}</td>
                <td><a target="_blank" href="{{ route('pets.edit', $renovacao->id_pet) }}">{!! $renovacao->pet->nome_pet !!}</a></td>
                <td><a target="_blank" href="{{ route('clientes.edit', $renovacao->id_cliente) }}">{!! $renovacao->cliente->nome_cliente !!}</a></td>
                <td>{!! $renovacao->plano->nome_plano !!}</td>
                <td>R$ {!! number_format($renovacao->valor_original, 2, ',', '.') !!}</td>
                <td>{!! number_format($renovacao->reajuste, 2, ',', '.') !!}%</td>
                <td>R$ {!! number_format($renovacao->valor/(1 - floatval($renovacao->desconto/100)), 2, ',', '.') !!}</td>
                <td>{!! number_format($renovacao->desconto, 2, ',', '.') !!}%</td>
                <td>R$ {!! number_format($renovacao->valor, 2, ',', '.') !!}</td>
                <td>R$ {{ number_format($renovacao->uso, 2, ',', '.') }}</td>
                <td>{{ $renovacao->data_inicio_contrato->format(\App\Helpers\Utils::BRAZILIAN_DATE) }}</td>
                <td>
                    @if($renovacao->link ? $renovacao->link->indisponivel : false)
                    <a target="blank" href="{!! $renovacao->link->link() !!}">LINK</a>
                    @endif
                </td>
                <td>{!! $renovacao->paid_at ? $renovacao->paid_at->format('d/m/Y H:i:s') : '- -' !!}</td>
                <td>
                    @if($renovacao->status == \App\Models\Renovacao::STATUS_PAGO)
                        <span class="badge bg-blue">{!! $renovacao->status !!}</span>
                    @elseif($renovacao->status == \App\Models\Renovacao::STATUS_NOVO)
                        <span class="badge bg-green">{!! $renovacao->status !!}</span>
                    @elseif($renovacao->status == \App\Models\Renovacao::STATUS_EM_NEGOCIACAO)
                        <span class="badge bg-yellow">{!! $renovacao->status !!}</span>
                    @elseif($renovacao->status == \App\Models\Renovacao::STATUS_CANCELADO)
                        <span class="badge bg-red">{!! $renovacao->status !!}</span>
                    @elseif($renovacao->status == \App\Models\Renovacao::STATUS_AGENDADO)
                        <span class="badge bg-orange">{!! $renovacao->status !!}</span>
                    @elseif($renovacao->status == \App\Models\Renovacao::STATUS_ATUALIZADO)
                        <span class="badge bg-purple">{!! $renovacao->status !!}</span>
                    @elseif($renovacao->status == \App\Models\Renovacao::STATUS_CONVERTIDO)
                        <span class="badge bg-light">{!! $renovacao->status !!}</span>
                    @endif
                </td>
                <td>{!! $renovacao->regime !!}</td>
                <td>{!! $renovacao->competencia_mes !!}/{!! $renovacao->competencia_ano !!}</td>
                <td>{{ $renovacao->created_at->format(\App\Helpers\Utils::BRAZILIAN_DATETIME) }}</td>
                <td>
                    @unless($renovacao->paid_at)
                    <div class='btn-group'>
                        <a href="{!! route('renovacao.edit', [$renovacao->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    </div>
                    @endunless
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
<h3>Sem reajuste</h3>
<br>
<table class="table table-responsive table-hover" id="renovacao-table">
    <thead>
    <th>ID Pet</th>
    <th>Nome pet</th>
    <th>Tutor</th>
    <th>Plano</th>
    <th>Valor original</th>
    <th>Valor de pagamento</th>
    <th>Link de pagamento</th>
    <th>Pagamento</th>
    <th>Status</th>
    <th>Regime</th>
    <th>Competência</th>
    <th>Data de criação</th>
    <th colspan="3">Editar</th>
    </thead>
    <tbody>
    @foreach($renovacoes_nao_optantes as $renovacao)
        <tr>
            <td>{!! $renovacao->id_pet !!}</td>
            <td>{!! $renovacao->pet->nome_pet !!}</td>
            <td>{!! $renovacao->cliente->nome_cliente !!}</td>
            <td>{!! $renovacao->plano->nome_plano !!}</td>
            <td>{!! number_format($renovacao->valor_original, 2, ',', '.') !!}</td>
            <td>{!! number_format($renovacao->valor, 2, ',', '.') !!}</td>
            <td>-</td>
            <td>{!! $renovacao->paid_at ? $renovacao->paid_at->format('d/m/Y H:i:s') : '- -' !!}</td>
            <td>
                @if($renovacao->status == \App\Models\Renovacao::STATUS_PAGO)
                    <span class="badge bg-blue">{!! $renovacao->status !!}</span>
                @elseif($renovacao->status == \App\Models\Renovacao::STATUS_NOVO)
                    <span class="badge bg-green">{!! $renovacao->status !!}</span>
                @elseif($renovacao->status == \App\Models\Renovacao::STATUS_EM_NEGOCIACAO)
                    <span class="badge bg-yellow">{!! $renovacao->status !!}</span>
                @elseif($renovacao->status == \App\Models\Renovacao::STATUS_CANCELADO)
                    <span class="badge bg-red">{!! $renovacao->status !!}</span>
                @endif
            </td>
            <td>{!! $renovacao->regime !!}</td>
            <td>{!! $renovacao->competencia_mes !!}/{!! $renovacao->competencia_ano !!}</td>
            <td>{{ $renovacao->created_at->format(\App\Helpers\Utils::BRAZILIAN_DATETIME) }}</td>
            <td>
                @unless($renovacao->paid_at)
                    <div class='btn-group'>
                        <a href="{!! route('renovacao.edit', [$renovacao->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    </div>
                @endunless
            </td>
        </tr>
    @endforeach
    </tbody>
</table>