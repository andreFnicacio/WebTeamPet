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
    @foreach($guias->groupBy('id_cliente') as $guiasCliente)
        <table class="table table-striped table-hover order-column datatables" >
            <tbody>
                <tr>
                    <td colspan="7" class="text-left">
                        {{ $guiasCliente->first()->nome_cliente }}
                    </td>
                </tr>
                <tr>
                    <th> Guia </th>
                    <th> Pet </th>
                    <th> Clínica </th>
                    <th class="text-center"> Status </th>
                    <th> Procedimento </th>
                    @if(Entrust::hasRole(['ADMINISTRADOR']))
                        <th>Valor</th>
                    @endif
                    <th> Data da Solicitação </th>
                    {{--<th> Ações </th>--}}
                </tr>
                @foreach($guiasCliente as $guia)
                    <tr class="even gradeX">
                        {{--<td>--}}
                        {{--<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">--}}
                        {{--<input type="checkbox" class="checkboxes" value="1" />--}}
                        {{--<span></span>--}}
                        {{--</label>--}}
                        {{--</td>--}}
                        <td> {{ $guia->numero_guia }} </td>
                        <td>
                            @php
                                $pet = $guia->pet()->first();
                            @endphp
                            <span>{{ $pet->nome_pet }}</span>
                        </td>
                        <td>
                            {{ $guia->clinica()->first()->nome_clinica }}
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
                            @if(Entrust::hasRole(['ADMINISTRADOR']))
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
                        @if(Entrust::hasRole(['ADMINISTRADOR']))
                            <td>
                                {{ App\Helpers\Utils::money($guia->valor_momento) }}
                            </td>
                        @endif
                        <td class="center">
                            @if(!$exportar)
                                <span class="hide">{{ $guia->created_at->format('YmdHis') }}</span>
                            @endif
                            {{ $guia->created_at->format('d/m/Y H:i') }}
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="2">Total:</td>
                    <td colspan="5" class="text-right">
                        @php
                            $totalGuia = $guiasCliente->sum('valor_momento');
                            $totalRelatorio += $totalGuia;
                        @endphp
                        {{ App\Helpers\Utils::money($totalGuia) }}
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