@php
    if(!isset($exportar)) {
        $exportar = false;
    }
    if(!isset($layout)) {
        $layout = 'CLIENTES';
    }
@endphp

@if($layout === 'CLIENTES')
    @php
        $totalRelatorio = 0;
    @endphp
    <table class="table table-striped table-hover order-column datatables" >
        <tbody>
            {{-- <tr>
                <td colspan="15" class="text-center">
                    <h3><strong>{{ $guiasClinica->first()->clinica->nome_clinica }}</strong></h3>
                    <p>{{ $guiasClinica->first()->clinica->aceite_urh ? "URH: " . $guiasClinica->first()->clinica->urh->nome_urh . ' ' . App\Helpers\Utils::money($guiasClinica->first()->clinica->urh->valor_urh) : "URH: Sem URH (R$ 1,00)" }}</p>
                </td>
            </tr> --}}
            <tr>
                <th> Guia </th>
                <th> ID Pet </th>
                <th> Pet </th>
                <th> Espécie </th>
                <th> Plano </th>
                <th> Tutor </th>
                <th> Celular </th>
                <th> Email </th>
                <th class="text-center"> Status </th>
                <th> Procedimento </th>
                @if(Entrust::hasRole(['ADMINISTRADOR', 'AUDITORIA']))
                    <th>Valor</th>
                @endif
                <th> Prestador Solicitante </th>
                <th> Solicitante </th>
                <th> Prestador </th>
                <th> Data (Solicitação/Realização) </th>
                <th> Hora </th>
                <th> Credenciado </th>
                <th> URH </th>
                {{--<th> Ações </th>--}}
            </tr>
        @foreach($guias->groupBy('id_clinica') as $guiasClinica)
            @php
                $totalGamificationClinica = 0;
            @endphp
            @foreach($guiasClinica as $guia)
                @php
                    if (!$guia->plano) {
                        continue;
                    }
                @endphp
                <tr class="even gradeX">
                    <td> {{ $guia->numero_guia }} </td>
                    <td> {{ $guia->pet ? $guia->pet->id : ' - ' }} </td>
                    <td> {{ $guia->pet ? $guia->pet->nome_pet : ' - ' }} </td>
                    <td> {{ $guia->pet ? $guia->pet->tipo : ' - '}} </td>
                    <td> {{ $guia->plano->id . ' - ' . $guia->plano->nome_plano }} </td>
                    <td> {{ $guia->pet ? $guia->pet->cliente->nome_cliente : ' - '}} </td>
                    <td> {{ $guia->pet ? $guia->pet->cliente->celular : ' - '}} </td>
                    <td> {{ $guia->pet ? $guia->pet->cliente->email : ' - '}} </td>
                    <td class="text-center" width="10%">
                        @if($guia->status === 'LIBERADO')
                            <span class="label label-sm bg-green-meadow">{{ $exportar ? "LIBERADO" : "L" }}</span>
                        @elseif($guia->status === 'RECUSADO')
                            <span class="label label-sm label-danger">{{ $exportar ? "NEGADO" : "N" }}</span>
                        @else
                            <span class="label label-sm label-info">{{ $exportar ? "AGUARDANDO" : "A" }}</span>
                        @endif
                        {!! $exportar ? ", " : "&nbsp;"  !!}
                        @if(Entrust::hasRole(['ADMINISTRADOR', 'AUDITORIA']))
                            @if($guia->autorizacao === 'AUTOMATICA')
                                <span class="label label-sm label-info">{{ $exportar ? "AUTOMÁTICA" : "AT" }}</span>
                            @elseif($guia->autorizacao === 'AUDITORIA')
                                <span class="label label-sm label-success">{{ $exportar ? "AUDITORIA" : "A" }}</span>
                            @else
                                <span class="label label-sm label-warning">{{ $exportar ? "FORÇADA" : "F" }}</span>
                            @endif
                        @endif
                        {!! $exportar ? ", " : "&nbsp;"  !!}
                        @if($guia->glosado == '1' || $guia->glosado == '3')
                            <span class="label label-sm label-danger">{{ $exportar ? "GLOSADO" : "G" }}</span>
                        @endif
                    </td>
                    <td>
                        {{ $guia->procedimento->id }} - {{ $guia->procedimento->nome_procedimento }}
                        @if($guia->glosado == '1' || $guia->glosado == '3')
                            - <span class="label label-sm label-danger">Glosado</span>
                        @endif
                    </td>
                    @if(Entrust::hasRole(['ADMINISTRADOR', 'AUDITORIA']))
                        <td class="text-nowrap">
                            @if($guia->clinica->aceite_urh)
                                {{ $exportar ? number_format(round($guia->valor_momento) * $guia->clinica->urh->valor_urh, 2, ',', '') : App\Helpers\Utils::money(round($guia->valor_momento) * $guia->clinica->urh->valor_urh) }}
                            @else
                                {{ $exportar ? number_format($guia->valor_momento, 2, ',', '') : App\Helpers\Utils::money($guia->valor_momento) }}
                            @endif
                            {{-- {{ $exportar ? number_format($guia->valor_momento, 2, ',', '') : App\Helpers\Utils::money($guia->valor_momento) }} --}}
                        </td>
                    @endif
                    <td class="text-center">
                        {{ $guia->prestador_solicitante ? $guia->prestador_solicitante->nome : '' }}
                    </td>
                    <td class="text-center">
                        {{ $guia->solicitante ? $guia->solicitante->nome_clinica : '' }}
                    </td>
                    <td class="text-center">
                        {{ $guia->prestador ? $guia->prestador->nome : '' }}
                    </td>
                    <td class="center">
                        @if(!$exportar)
                            <span class="hide">{{ $guia->data->format('YmdHis') }}</span>
                        @endif
                        {{ $guia->data->format('d/m/Y') }}
                    </td>
                    <td class="center">
                        @if(!$exportar)
                            <span class="hide">{{ $guia->data->format('YmdHis') }}</span>
                        @endif
                        {{ $guia->data->format('H:i') }}
                    </td>
                    <td> {{ $guia->clinica->nome_clinica }} </td>
                    <td>
                        <p>{{ $guiasClinica->first()->clinica->aceite_urh ? App\Helpers\Utils::money($guiasClinica->first()->clinica->urh->valor_urh) : "R$ 1,00" }}</p>
                    </td>
                </tr>
                @php
                    $movimentacoes = (new \App\Models\MovimentacoesCredenciados())->where('id_guia_consulta', $guia->id)->get();
                @endphp
                @foreach($movimentacoes as $mov)
                    <tr class="bg-default">
                        <td> {{ $guia->numero_guia }} </td>
                        <td>
                            <span>{{ $guia->pet ? $guia->pet->nome_pet : ' - ' }}</span>
                        </td>
                        <td> {{ $guia->pet ? $guia->pet->tipo : ' - ' }} </td>
                        <td> {{ $guia->plano->id . ' - ' . $guia->plano->nome_plano }} </td>
                        <td>
                            {{ $guia->pet ? $guia->pet->cliente->nome_cliente : ' - ' }}
                        </td>
                        <td class="text-center">
                            Gamification
                            {{-- <i class="fa fa-2x fa-long-arrow-right"></i> --}}
                        </td>
                        <td class="bold" colspan="1">
                            <strong>{!! $mov->descricao !!}</strong>
                        </td>
                        <td class="text-nowrap bold">
                            <strong>
                                {{ $exportar ? number_format($mov->valor, 2, ',', '') : App\Helpers\Utils::money($mov->valor) }}
                            </strong>
                        </td>
                        <td class="text-center">
                            {{ $guia->prestador_solicitante ? $guia->prestador_solicitante->nome : '' }}
                        </td>
                        <td class="text-center">
                            {{ $guia->solicitante ? $guia->solicitante->nome_clinica : '' }}
                        </td>
                        <td class="text-center">
                            {{ $guia->prestador ? $guia->prestador->nome : '' }}
                        </td>
                        <td class="center">
                            @if(!$exportar)
                                <span class="hide">{{ $guia->data->format('YmdHis') }}</span>
                            @endif
                            {{ $guia->data->format('d/m/Y') }}
                        </td>
                        <td class="center">
                            @if(!$exportar)
                                <span class="hide">{{ $guia->data->format('YmdHis') }}</span>
                            @endif
                            {{ $guia->data->format('H:i') }}
                        </td>
                        <td> {{ $guia->clinica->nome_clinica }} </td>
                        <td>
                            <p>{{ $guiasClinica->first()->clinica->aceite_urh ? App\Helpers\Utils::money($guiasClinica->first()->clinica->urh->valor_urh) : "R$ 1,00" }}</p>
                        </td>
                    </tr>
                    @php
                        $totalGamificationClinica += $mov->valor;
                    @endphp
                @endforeach
            @endforeach

            @php
                // Filtrando por guias não glosadas
                // $guiasClinica = $guiasClinica->whereIn('glosado', ['0', '2']);
                if($guia->clinica->aceite_urh) {
                    $totalGuia = $guiasClinica->sum('valor_momento') * $guiasClinica->first()->clinica->urh->valor_urh;
                } else {
                    $totalGuia = $guiasClinica->sum('valor_momento');
                }
                $totalGuia += $totalGamificationClinica;
                // $totalGuia = $guiasClinica->sum('valor_momento');
                $totalRelatorio += $totalGuia;
            @endphp
            {{-- <tr class="total">
                <td colspan="15" class="text-center bold">Total
                    @if($exportar)
                        ({{ $guiasClinica->first()->clinica()->first()->nome_clinica  }})
                    @endif
                    :
                    {{ $exportar ? number_format($totalGuia, 2, ',', '') : App\Helpers\Utils::money($totalGuia) }}
                </td>
            </tr> --}}
        @endforeach
        </tbody>
    </table>
    @if(!$exportar)
        <table class="table table-striped table-bordered table-hover table-checkable order-column datatables">
            <thead>
            <tr>
                <th class="bold">Total:</th>
            </tr>
            </thead>
            <tbody>
            <td class="bold">{{ App\Helpers\Utils::money($totalRelatorio) }}</td>
            </tbody>
        </table>
    @endif
@endif
