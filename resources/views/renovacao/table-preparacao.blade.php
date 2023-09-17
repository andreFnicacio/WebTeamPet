<div class="row-fluid">
    <div class="col-sm-12">
        <strong>{{ count($dados) }} resultados</strong>
    </div>
</div>

<table class="table table-striped table-hover order-column datatables" >
    <thead>
    <tr>
        <th> ID Pet </th>
        <th> Nome do Pet </th>
        <th> Nome do Tutor </th>
        <th> Plano </th>
        <th> Valor </th>
        <th> Mês de reajuste </th>
        <th> Data de contrato </th>
        <th> Regime </th>
        <th> Valor Faturado / Ano </th>
        <th> Valor Utilizado </th>
        <th> Valor Original do Plano (Mensalmente) </th>
        <th> Desconto </th>
        <th> Parcelas </th>
        <th> Valor Mensal </th>
        <th> Valor Anual </th>
        <th> Relação de Uso </th>
        <th> Reajuste </th>
        <th> Ações </th>
    </tr>
    </thead>
    <tbody>
    @foreach($dados as $i => $d)
        <tr class="{{ $d['renovacao'] ? 'renovacao-' . $d['renovacao']->status : '' }}">
            <td> {{ $d['pet']->id }} </td>
            <td>
                <a href="{{ route('pets.edit', ['id' => $d['pet']->id]) }}" target="_blank">
                    {{ $d['pet']->nome_pet }}
                </a>
            </td>
            <td>
                <a href="{{ route('clientes.edit', ['id' => $d['cliente']->id]) }}" target="_blank">
                    {{ $d['cliente']->nome_cliente }}
                </a>
            </td>
            <td> {{ $d['plano']->nome_plano }} </td>
            <td> {{ $d['pet']->petsPlanosAtual()->first() ? \App\Helpers\Utils::money($d['pet']->petsPlanosAtual()->first()->valor_momento) : 'PET SEM PLANO' }} </td>
            <td> {{ $d['pet']->mes_reajuste }} </td>
            <td> {{ $d['pet']->petsPlanosAtual()->first() ? $d['pet']->petsPlanosAtual()->first()->data_inicio_contrato->format(\App\Helpers\Utils::BRAZILIAN_DATE) : 'PET SEM PLANO' }} </td>
            <td> {{ $d['pet']->regime }} / {{ $d['pet']->participativo ? 'PARTICIPATIVO' : 'INTEGRAL' }} </td>
            <td> {{ \App\Helpers\Utils::money($d['valorPago']) }} </td>
            <td> {{ \App\Helpers\Utils::money($d['valorUtilizado']) }} </td>
            <td>

                <input type="number" {{ $d['renovacao'] ? "value=" . $d['renovacao']->valor : '' }} {{ $d['renovacao'] ? 'disabled' : '' }} min="0" class="form-control calculo-renovacao" name="valor_base" data-index="{{$i}}" id="valor_base-{{$i}}"/>

                <input type="hidden" name="regime" data-index="{{$i}}" id="regime-{{$i}}" value="{{ $d['pet']->regime }}">
                <input type="hidden" name="id_pet" data-index="{{$i}}" id="id_pet-{{$i}}" value="{{ $d['pet']->id }}">
            </td>
            <td>
                <input type="number" {{ $d['renovacao'] ? 'disabled' : '' }} min="0" class="form-control calculo-renovacao" name="desconto" value="0" data-index="{{$i}}" id="desconto-{{$i}}"/>
            </td>
            <td>
                <input type="number" min="1" max="12" class="form-control" name="parcelas" {{ $d['renovacao'] ? ($d['renovacao']->link ? "value=" . $d['renovacao']->link->parcelas : '')  : '' }} {{ $d['renovacao'] ? 'disabled' : '' }} value="1" data-index="{{$i}}" id="parcelas-{{$i}}"/>
            </td>
            <td>
                <input type="text"  class="form-control valor-renovacao" name="valor_mensal" value="0" data-index="{{$i}}" id="valor_mensal-{{$i}}" readonly/>
            </td>
            <td>
                <input type="text"  class="form-control valor-renovacao" name="valor_anual" value="0" data-index="{{$i}}" id="valor_anual-{{$i}}" readonly/>
            </td>
            <td> {{ $d['relacao_uso'] }}% </td>
            <td>
                {{ $d['reajuste'] }}
                <input type="hidden" name="reajuste" data-index="{{ $i }}" id="reajuste-{{$i}}" value="{{ $d['reajuste'] }}">
            </td>
            <td>
                <div class="btn-group">
                    @unless($d['renovacao'])
                    <a href="javascript:;" data-original-title="Confirmar e abrir processo de renovação." class="btn btn-circle green confirmar-renovacao" data-index="{{ $i }}" data-toggle="tooltip">
                        <i class="fa fa-check"></i>
                    </a>
                    @endunless
                    @if(isset($d['renovacao']))
                        @if($d['renovacao']->status != \App\Models\Renovacao::STATUS_PAGO)
                            <a target="_blank" href="{{ route('renovacao.edit', ['id' => $d['renovacao']->id]) }}" data-original-title="Editar renovação." class="btn btn-circle blue" data-toggle="tooltip">
                                <i class="fa fa-edit"></i>
                            </a>
                        @else
                            <span class="badge badge-success"> PAGO - {{ $params['ano'] }}</span>
                        @endif
                    @endif
                    @unless($d['renovacao'])
                    <a href="javascript:;" data-original-title="Não optar pelo reajuste." class="btn btn-circle red excluir-renovacao" data-index="{{ $i }}" data-toggle="tooltip">
                        <i class="fa fa-times" ></i>
                    </a>
                    @endunless
                </div>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>