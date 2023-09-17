@extends('layouts.metronic5')
@section('css')
    <script>
        window.idCliente = "{{ $cliente->id }}";
    </script>
    @parent
    <style>
        button.disabled {
            pointer-events: none;
        }
        tr.pagamento td {
            background: hsla(0, 0%, 90%, 0.3) !important;
            border: none !important;
            padding-bottom: 3px !important;
            padding-top: 3px !important;
        }
        .historico-financeiro tr.cancelado {
            color: hsla(0, 0%, 114%, 1);
            cursor: not-allowed;
            background: hsla(0, 0%, 55%, 0.5);
            -webkit-touch-callout: none; /* iOS Safari */
            -webkit-user-select: none; /* Safari */
            -khtml-user-select: none; /* Konqueror HTML */
            -moz-user-select: none; /* Firefox */
            -ms-user-select: none; /* Internet Explorer/Edge */
            user-select: none; /* Non-prefixed version, currently
                                  supported by Chrome and Opera */
        }
        table {
            border-collapse: collapse;
        }
    </style>
@endsection
@section('title')
    @parent
    Histórico Financeiro
@endsection
@section('content')
    <input type="hidden" id="id_cliente" value="{{ $cliente->id_cliente }}">
    <div class="m-portlet  light  portlet-form ">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
						<span class="m-portlet__head-icon">
							<i class="fa fa-calendar"></i>
						</span>
                    <h3 class="m-portlet__head-text">
                        Histórico Financeiro
                    </h3>
                </div>
            </div>
        </div>
        <div class="portlet-body">
            <div class="table-responsive">
                <table class="table table-hover table-checkable order-column">
                    <thead>
                    <tr>
                        <th> Competência </th>
                        <th> Vencimento </th>
                        <th> Data Pagamento </th>
                        <th> Valor </th>
                        <th> Status </th>
                        <th> 2ª Via </th>
                    </tr>
                    </thead>
                    <tbody>
                        @php
                            /**
                             * @var $pets \App\Models\Cobrancas[]
                             */
                            $cobrancas = $cliente->cobrancas()->where('status', 1)->orderBy('competencia', 'DESC')->get();
                        @endphp
                        @foreach($cobrancas as $c)
                            @php
                                $pagamentos = $c->pagamentos()->orderBy('data_pagamento', 'DESC')->get();
                                $status = $c->status ? "Em aberto" : "Cancelado";


                                if($c->status && count($pagamentos)) {
                                    $status = "Pago";
                                } else {
                                    if((new Carbon\Carbon())->gt($c->data_vencimento->addDays(2))) {
                                        $status = "Em atraso";
                                        $c->status = 0;
                                    }
                                }
                                $rowClass = str_replace(" ", "-", strtolower($status));
                                $cancelada = $c->cancelada_em;
                            @endphp
                            @if($cancelada)
                                <tr class="{{ $rowClass }}" data-toggle="tooltip" data-placement="top" data-original-title="{{ $c->justificativa . " | Cancelada em: " . $c->cancelada_em->format('d/m/Y H:i:s') }}">
                            @else
                            <tr class="{{ $rowClass }}">
                                @endif

                                <td>
                                    {{ str_replace('-', '/', $c->competencia) }}
                                </td>
                                <td>
                                    {{ $c->data_vencimento->format('d/m/Y') }}
                                </td>
                                <td>
                                    @if($pagamentos->count())
                                        {{ $pagamentos->first()->data_pagamento->format('d/m/Y') }}
                                    @endif
                                </td>
                                <td>
                                    {{ \App\Helpers\Utils::money($c->valor_original) }}
                                </td>

                                <td>
                                    @if($cancelada)
                                        <span class="badge bg-red">CANCELADA</span>
                                    @else
                                        <span class="badge bg-{{ $c->status ? "green-jungle" : "yellow-saffron" }}">{{ ucwords($status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($status == "Em aberto" || $status == "Em atraso")
                                        <a href="{{ $c->linkSegundaVia() }}" target="_blank">
                                            <i class="fa fa-external-link"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            @foreach($pagamentos as $p)
                                <tr class="pagamento {{ $rowClass }}" style="font-size: 12px; font-style: italic;">

                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>

                                    <td>
                                        <small>{{ empty($p->complemento) ? "Valor recebido" : $p->complemento }}</small>
                                    </td>

                                    <td>
                                        <small>{{ \App\Helpers\Utils::money($p->valor_pago) }}</small>
                                    </td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection