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
    @foreach($guias->groupBy('id_clinica') as $guiasClinica)
        <table class="table table-striped table-hover order-column datatables" >
            <tbody>
                <tr>
                    <th> Guia </th>
                    <th> ID Pet </th>
                    <th> Pet </th>
                    <th> Tutor </th>
                    <th class="text-center"> Status </th>
                    <th> Procedimento </th>
                    @if(Entrust::hasRole(['ADMINISTRADOR', 'AUDITORIA']))
                        <th>Valor</th>
                    @endif
                    <th> Clínica </th>
                    <th> Data (Solicitação/Realização) </th>
                    {{--<th> Ações </th>--}}
                </tr>
                @foreach($guiasClinica as $guia)
                    <tr class="even gradeX">
                        {{--<td>--}}
                        {{--<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">--}}
                        {{--<input type="checkbox" class="checkboxes" value="1" />--}}
                        {{--<span></span>--}}
                        {{--</label>--}}
                        {{--</td>--}}
                        <td> {{ $guia->numero_guia }} </td>
                        @php
                            $pet = $guia->pet()->first();
                        @endphp
                        <td>
                            <span>{{ $pet->id }}</span>
                        </td>
                        <td>
                            <span>{{ $pet->nome_pet }}</span>
                        </td>
                        <td>
                            {{ $pet->cliente()->first()->nome_cliente }}
                        </td>
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
                        </td>
                        <td>
                            @php
                                $procedimento = $guia->procedimento()->first();
                            @endphp
                            {{ $procedimento->id }} - {{ $procedimento->nome_procedimento }}
                        </td>
                        @if(Entrust::hasRole(['ADMINISTRADOR', 'AUDITORIA']))
                            <td>
                                @if($guia->glosado == '1' || $guia->glosado == '3')
                                    <span class="label label-sm label-danger">Glosado</span>
                                @else
                                    {{ $exportar ? number_format($guia->valor_momento, 2, ',', '') : App\Helpers\Utils::money($guia->valor_momento) }}
                                @endif
                            </td>
                        @endif
                        <td class="text-center">
                            {{ $guia->clinica->nome_clinica }}
                        </td>
                        <td class="center">
                            @if(!$exportar)
                                <span class="hide">{{ $guia->data->format('YmdHis') }}</span>
                            @endif
                            {{ $guia->data->format('d/m/Y H:i') }}
                        </td>
                    </tr>
                @endforeach
                <tr class="total">
                    <td colspan="2">Total
                        @if($exportar)
                            ({{ $guiasClinica->first()->clinica()->first()->nome_clinica  }})
                        @endif
                            :
                    </td>
                    <td colspan="5" class="text-right">
                        @php
                            // Filtrando por guias não glosadas
                            $guiasClinica = $guiasClinica->whereIn('glosado', ['0', '2']);

                            $totalGuia = $guiasClinica->sum('valor_momento');
                            $totalRelatorio += $totalGuia;
                        @endphp
                        {{ $exportar ? number_format($totalGuia, 2, ',', '') : App\Helpers\Utils::money($totalGuia) }}
                    </td>
                </tr>
            </tbody>
        </table>
    @endforeach
    @if(!$exportar)
    <table class="table table-striped table-bordered table-hover table-checkable order-column datatables">
        <thead>
            <tr>
                <th>Total:</th>
            </tr>
        </thead>
        <tbody>
            <td>{{ App\Helpers\Utils::money($totalRelatorio) }}</td>
        </tbody>
    </table>
    @endif
@endif