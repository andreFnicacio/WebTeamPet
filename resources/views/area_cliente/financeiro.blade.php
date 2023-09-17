@extends('layouts.app')
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
    </style>
@endsection
@section('title')
    @parent
    Histórico Financeiro
@endsection
@section('content')
    <input type="hidden" id="id_cliente" value="{{ $cliente->id_cliente }}">
    <div class="portlet  light  portlet-form ">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-money font-red-sunglo"></i>
                <span class="caption-subject font-red-sunglo sbold uppercase">Histórico Financeiro</span>
            </div>
            <div class="actions">
                <div class="row">
                    <div class="col-md-offset-0 col-md-12">



                    </div>
                </div>
            </div>
        </div>
        <div class="portlet-body">
            <div class="table-responsive">
                <table class="table col-md-12 responsive historico-financeiro">
                    <thead>
                    <tr>
                        <th> </th>
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
                        $cobrancas = $cliente->cobrancas()->orderBy('competencia', 'DESC')->get();
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
                        @endphp
                        <tr class="{{ $rowClass }}">
                            <td>
                                {{--<span class="fa fa-arrow-down text-danger"></span> --}}
                            </td>
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
                                <span class="badge bg-{{ $c->status ? "green-jungle" : "yellow-saffron" }}">{{ ucwords($status) }}</span>
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
                                <td>
                                </td>
                                <td></td>
                                <td></td>

                                <td>
                                    <small>{{ empty($p->complemento) ? "Valor recebido" : $p->complemento }}</small>
                                </td>

                                <td>
                                    <small>{{ \App\Helpers\Utils::money($p->valor_pago) }}</small>
                                </td>
                                <td></td>
                            </tr>
                        @endforeach
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection