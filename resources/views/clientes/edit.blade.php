@extends('layouts.app')
@section('css')
    <script>
        window.idCliente = "{{ $cliente->id }}";
    </script>
    <script>
        window.cobrancas = {!! json_encode($cliente->cobrancas()->where('status', 1)->whereNull('cancelada_em')->orderBy('competencia', 'DESC')->get()->map(function($c) {
            $c->texto = $c->id . ": " . str_replace("-", "/", $c->competencia) . " (" . App\Helpers\Utils::money($c->valor_original) . ")";
            return $c;
        })) !!};
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

        tr.cancelada td:before {
            content: " ";
            position: absolute;
            top: 50%;
            left: 0;
            border-bottom: 1px dashed rgba(173, 80, 80, 0.48);
            width: 100%;
        }

        tr.cancelada td {
            position: relative;
            padding: 5px 10px;
        }

        .badges {
            position: fixed;
            top: 170px;
            right: 0px;
        }

        .badges .lifepet-badge {
            padding: 3px 5px 3px 8px;
            box-shadow: 1px 1px 1px #ccc;
            border-radius: 3px 0 0 3px;
            text-align: center;
            cursor: pointer;
            display: table;
            float: right;
            clear: both;
            width: 40px;
        }
        .badges .lifepet-badge:hover {
            padding-left: 12px;
            width: 55px;
            transition: 0.1s;
        }
        .swal2-container.swal2-fade.swal2-shown {
            z-index: 999999;
        }
    </style>
@endsection
@section('title')
    @parent
    Clientes - Editar - {{ $cliente->nome_cliente }}
@endsection
@section('content')

@php
    if(!empty($cliente->id_externo)) {
        try {
            $financeiro = new App\Helpers\API\Financeiro\Financeiro();
            $info = $financeiro->get('/customer/refcode/'.$cliente->id_externo);
        }
        catch (\Exception $e){

        }
    }
@endphp
    <!-- BEGIN VALIDATION STATES-->
    <div class="portlet light portlet-fit portlet-form ">
        <div class="portlet-title">
            <div class="caption">
                <i class="icon-settings font-green-jungle"></i>
                <span class="caption-subject font-green-jungle sbold uppercase">
                  Clientes
                </span>

                <form action="{{ route('cliente.criarUsuario', ['id' => $cliente->id]) }}" style="display: inline; margin-left: 10px" method="post">
                    {{ csrf_field() }}
                    <button type="submit" class="btn  btn-xs btn-circle tooltips {{ $cliente->hasUser() ? 'disabled grey' : 'blue' }}" data-placement="top" data-original-title="Criar usuário">
                        <i class="fa fa-user"></i>
                    </button>
                </form>
                <form action="{{ route('cliente.resetarSenhaCliente', ['id' => $cliente->id]) }}" style="display: inline; margin-left: 10px" method="post" id="resetarSenhaCliente">
                    {{ csrf_field() }}
                    <button type="button" id="resetarSenha" class="btn  btn-xs btn-circle tooltips {{ !$cliente->hasUser() ? 'disabled grey' : 'blue' }}" data-placement="top" data-original-title="Resetar senha do usuário do cliente.">
                        <i class="fa fa-refresh small"></i>&nbsp;
                        <i class="fa fa-lock"></i>
                    </button>
                </form>


            </div>
            @permission('edit_clientes')
            <div class="actions" data-target="#Clientes">
                <div class="btn-group btn-group-devided">
                    <a href="#" data-toggle="modal" data-target="#modal-dados-cartao" class="btn blue font-white btn-solicitar-cartao">Solicitar dados de cartão</a>
                    <a href="imprimir" target="_blank" class=" btn blue font-white"><i class="fa fa-print"></i>Imprimir</a>
                    <button type="submit" id="save" class="btn green-jungle">Salvar</button>
                    <button type="submit" id="cancel" class="btn red-sunglo">Cancelar</button>
                </div>
            </div>
            @endpermission
        </div>
        <div class="portlet-body">
            <!-- BEGIN FORM-->
            {!! Form::model($cliente, [
                                'route' => [
                                    'clientes.update',
                                    $cliente->id
                                ],
                                'method' => 'patch',
                                'class' => 'form-horizontal',
                                'id' => 'Clientes'
                            ]);
            !!}
            <div class="form-body">

                <div class="alert alert-danger display-hide">
                    <button class="close" data-close="alert"></button> Verifique se você preencheu todos os campos.
                </div>
                <div class="alert alert-success display-hide">
                    <button class="close" data-close="alert"></button> Validado com sucesso.
                </div>
                <div class="col-md-12" style="margin-bottom: 20px;">
                    <h3 class="block" style="margin-top: 0px;">Dados Gerais</h3>
                </div>
                @include('clientes.fields')
            </div>
            {!! Form::close() !!}
        </div>
        <!-- END FORM-->
    </div>

    

    <!-- END VALIDATION STATES-->
    <!-- BEGIN VALIDATION STATES-->
    <div class="portlet  light  portlet-form ">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-address-book-o font-red-sunglo"></i>
                <span class="caption-subject font-red-sunglo sbold uppercase">Assinaturas</span>
            </div>
            <div class="actions">
                <div class="row">
                    <div class="col-md-offset-0 col-md-12">
                        @permission('create_pets')
                        <a class="btn  green-jungle btn-outline sbold" href="#" ><i class="icon-plus red-sunglo"></i> Novo</a>
                        @endpermission
                    </div>
                </div>
            </div>
        </div>
        <div class="portlet-body">
            <div class="table-responsive">
                <table class="table col-md-12">
                    <thead>
                    <tr>
                        <th> ID </th>
                        <th> Nome do pet </th>
                        <th> Tipo </th>
                        <th> Raça </th>
                        <th> Plano </th>
                        <th> Regime </th>
                        <th> Valor </th>
                        <th> Pagamento </th>
                        <th> Status Plano </th>
                        <th> Cancelamento agendado? </th>
                        <th>  </th>
                    </tr>
                    </thead>
                    <tbody>
                    @php
                        /**
                         * @var $pets \App\Models\Pets[]
                         */
                        $pets = $cliente->pets()->get();
                    @endphp
                    @foreach($pets as $pet)
                        <tr>
                            <td> {{ $pet->id }} </td>
                            <td>
                                <a href="{{ route('pets.edit', $pet->id) }}" target="_blank">{{ $pet->nome_pet }}</a>
                            </td>
                            <td> {{ $pet->tipo }} </td>
                            <td> {{ $pet->raca->nome }} </td>
                            <td> {{ $pet->plano() ? $pet->plano()->nome_plano : '-' }}</td>
                            <td> {{ $pet->regime }}</td>
                            <td> {{ $pet->petsPlanosAtual()->first() ? \App\Helpers\Utils::money($pet->petsPlanosAtual()->first()->valor_momento) : ' - ' }}</td>
                            <td>  <span class="label label-sm label-success"> {{ $pet->statusPagamento() }} </span></td>
                            @php
                                $status = $pet->ativo ? "Ativo" : "Inativo";
                            @endphp
                            <td>
                                <span class="badge bg-{{ $pet->ativo ? "green-jungle" : "yellow-saffron" }}">{{ ucwords($status) }}</span>
                            </td>
                            <td>
                                {{ $pet->cancelamentoAgendado() ? 'SIM' : 'NÃO' }}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- END VALIDATION STATES-->
    <input type="hidden" id="id_cliente" value="{{ $cliente->id_cliente }}">
    <!-- BEGIN VALIDATION STATES :: Histórico Financeiro -->
    @permission('ver_historico_financeiro')
    <div class="portlet  light  portlet-form ">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-money font-red-sunglo"></i>
                <span class="caption-subject font-red-sunglo sbold uppercase">Histórico Financeiro</span>
            </div>
            <div class="actions">
                <div class="row">
                    <div class="col-md-offset-0 col-md-12">
                        @permission('edit_cobrancas')
                        <a class="btn  red-sunglo btn-outline sbold" href="#nova_baixa" data-toggle="modal">
                            <i class="fa fa-arrow-circle-down big"></i> BAIXAR
                        </a>

                        @if(isset($info))
                        <a class="btn green-jungle btn-outline sbold" href="#boleto_avulso" data-toggle="modal"><i class="fa fa-barcode"></i> Novo Boleto Avulso</a>
                        @endif

                        <a class="btn blue-steel btn-outline sbold" href="#cobranca_manual" data-toggle="modal"><i class="fa fa-handshake-o"></i> Registro Manual</a>
                        @endpermission

                    </div>
                </div>
            </div>
        </div>
        <div class="portlet-body">
            <div class="table-responsive">
                <table class="table col-md-12 historico-financeiro">
                    <thead>
                    <tr>
                        <th> # </th>
                        <th> Competência </th>
                        <th> Vencimento </th>
                        <th> Data Pagamento </th>
                        <th> Valor </th>
                        <th> Status </th>
                        <th> Gerado em </th>
                        <th></th>
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
                        <tr class="{{ $rowClass }} {{ "cancelada tooltips" }}" data-placement="top" data-original-title="{{ $c->justificativa . " | Cancelada em: " . $c->cancelada_em->format('d/m/Y H:i:s') }}">
                        @else
                        <tr class="{{ $rowClass }}">
                        @endif
                            <td>
                                {{ $c->id }}
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
                                @if( ($status == 'Em aberto' || $status == 'Em atraso') && empty($c->hash_boleto) && empty($c->id_financeiro))
                                    @php
                                        $rotaAtualizacao = route('api.superlogica.v2.webhooks.cobranca.sincronizar');
                                        if($c->driver === \App\Models\Cobrancas::DRIVER__SUPERLOGICA_V1) {
                                            $rotaAtualizacao = route('api.superlogica.cobranca.sincronizar');
                                        }
                                    @endphp
                                    <form action="{{ $rotaAtualizacao }}" method="POST">
                                        {{ csrf_field() }}
                                        <input type="hidden" name="id_cobranca" value="{{ $c->id }}">
                                        <input type="hidden" name="id_superlogica" value="{{ $c->id_superlogica }}">
                                        <input type="hidden" name="old_superlogica_id" value="{{ $c->old_superlogica_id }}">

                                        <span class="badge bg-{{ $c->status ? "green-jungle" : "yellow-saffron" }}">{{ ucwords($status) }}</span>
                                        <button type="submit" class="btn btn-circle btn-icon-only btn-xs blue btn-sync-cobranca" data-toggle="tooltip" data-title="Sincronizar Cobrança" data-placement="right">
                                            <i class="fa fa-refresh"></i>
                                        </button>
                                    </form>
                                @else
                                    <span class="badge bg-{{ $c->status ? "green-jungle" : "yellow-saffron" }}">{{ ucwords($status) }}</span>
                                @endif
                                @if( !empty($c->hash_boleto) && $status != 'Pago')
                                    <a target="blank" class="badge bg-info" href="https://financeiro.lifepet.com.br/boletos/segundavia/{{$c->hash_boleto}}">2ª via</a>
                                @endif

                                @if( empty($c->hash_boleto) && $status != 'Pago' && !empty($c->id_financeiro) && $c->driver !== \App\Models\Cobrancas::DRIVER_VINDI)
                                    <a class="badge bg-red btn-forca-debito" href="javascript:;"
                                       data-url="{{ route('clientes.forcarDebito', ['id' => $c->id_financeiro]) }}"
                                       rel="{{$c->id_financeiro}}"
                                       data-id="{{ $c->id }}"
                                       data-id-financeiro="{{ $c->id_financeiro }}"
                                       data-vencimento="{{ $c->data_vencimento->format('d/m/Y') }}"
                                       data-competencia="{{ str_replace('-', '/', $c->competencia) }}"
                                       data-valor-original="{{ \App\Helpers\Utils::money($c->valor_original) }}"
                                    >Forçar Débito</a>
                                @endif

                                @if ($c->driver == \App\Models\Cobrancas::DRIVER_VINDI && $c->complemento)
                                    <a class="badge bg-info" href="{{$c->complemento}}" id="btn_copy_payment_link">Copiar Link Pagamento</a>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-green-jungle">{{ $c->driver ? ucwords($c->driver) : ' - ' }}</span>
                            </td>
                            <td>
                                <a class="btn btn-xs blue font-white tooltips" data-placement="top" data-original-title="Visualizar"
                                   href="{{ route('clientes.verFatura', ['id' => $cliente->id, 'id_cobranca' => $c->id]) }}"
                                   target="_blank">
                                    <span  class="fa fa-eye tooltips"></span>
                                </a>
                            </td>
                        </tr>
                        @foreach($pagamentos as $p)
                            <tr class="pagamento {{ $rowClass }} {{ $cancelada ? "cancelada" : "" }}" style="font-size: 12px; font-style: italic;">
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
                                <td>

                                </td>
                                <td>

                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL BOLETO AVULSO -->
    <div class="modal fade" id="boleto_avulso" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Novo boleto avulso</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <form action="{{ route('clientes.boletoAvulso', ['id' => $cliente->id]) }}" id="form-boleto-avulso" name="boleto_avulso" method="post">
            <div class="modal-body">
            
                <div class="form-body">
                {{ csrf_field() }}
                @if(isset($info))
                <input type="hidden" id="customer_id" name="customer_id" value="{{$info->data->id}}" />
                @endif
                    <div class="form-group col-sm-12">
                        <label class="control-label col-md-3">Valor
                            <span class="required"> * </span>
                        </label>
                        <div class="col-md-9">
                            <input type="text" class="money form-control" required name="valor" class="form-control" />
                        </div>
                    </div>
    
                    @php
                      $vencimento = date('d/m/Y');  
                    @endphp
    
                    <div class="form-group col-sm-12">
                        <label class="control-label col-md-3">Data de Vencimento
                            <span class="required"> * </span>
                        </label>
                        <div class="col-md-9">
                            <div class="input-group input-medium date date-picker"  data-date-format="dd/mm/yyyy">
                                <input type="text" name="vencimento" value="{{$vencimento}}" class="form-control" readonly required>
                                <span class="input-group-btn">
                                    <button class="btn default" type="button">
                                        <i class="fa fa-calendar"></i>
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div>
    
                    <div class="form-group col-sm-12">
                        <label class="control-label col-md-3">Multa
                            <span class="required"> * </span>
                        </label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" required name="multa" value="0.02" />
                        </div>
                    </div>
    
                    <div class="form-group col-sm-12">
                        <label class="control-label col-md-3">Juros
                            <span class="required"> * </span>
                        </label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" required name="juros" value="0.00033" />
                        </div>
                    </div>
    
                    <div class="form-group col-sm-12">
                        <label class="control-label col-md-3">Receber após vencimento?
                            <span class="required"> * </span>
                        </label>
                        <div class="col-md-9">
                            <select class="form-control" required name="limite_pagamento">
                                <option value="1">SIM</option>
                                <option value="2">NÃO</option>
                            </select>
                        </div>
                    </div>
    
                    <div class="form-group col-sm-12">
                        <label class="control-label col-md-3">Motivo
                            <span class="required"> * </span>
                        </label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" required name="obs" placeholder="Informe aqui o motivo do boleto" />
                        </div>
                    </div>
                </div>
    
            
            </div>
            <div class="modal-footer">
                <button type="submit" id="btn-gerar-boleto" class="btn btn-primary">Gerar Boleto</button>
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            </div>
        </form>
          </div>
        </div>
      </div>
    <!-- MODAL BOLETO AVULSO -->
    <!-- MODAL COBRANÇA MANUAL-->
    <div class="modal fade" id="cobranca_manual" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Registrar cobrança manual</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('clientes.cobrancas.manual', ['id' => $cliente->id]) }}" id="form-cobranca-manual" name="form-cobranca-manual" method="post">
                    <div class="modal-body">

                        <div class="form-body">
                            {{ csrf_field() }}
                            @if(isset($info))
                                <input type="hidden" id="customer_id" name="customer_id" value="{{$info->data->id}}" />
                            @endif
                            <div class="form-group col-sm-12">
                                <label class="control-label col-md-3">Competência:
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-3">
                                    <select class="form-control" required name="competencia_ano">
                                        @for($i = 2; $i >= 0; $i--)
                                            <option {{ now()->year === now()->subYear($i)->year ? 'selected' : ''  }}>{{ now()->subYear($i)->year }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <span style="display: block;float: left;font-size: 18pt;font-weight: 100;"> / </span>
                                <div class="col-md-2">
                                    <select class="form-control" required name="competencia_mes">
                                        @for($i = 1; $i <= 12; $i++)
                                            <option {{ now()->month === $i ? 'selected' : ''  }}>{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>


                            <div class="form-group col-sm-12">
                                <label class="control-label col-md-3">Valor
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-5">
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <span class="input-group-text" id="basic-addon1">R$</span>
                                        </div>
                                        <input type="text" class="money form-control" required name="valor" class="form-control" />
                                    </div>

                                </div>
                            </div>

                            @php
                                $vencimento = date('d/m/Y');
                            @endphp

                            <div class="form-group col-sm-12">
                                <label class="control-label col-md-3">Data de Vencimento
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-9">
                                    <div class="input-group input-medium date date-picker"  data-date-format="dd/mm/yyyy">
                                        <input type="text" name="vencimento" value="{{$vencimento}}" class="form-control" readonly required>
                                        <span class="input-group-btn">
                                        <button class="btn default" type="button">
                                            <i class="fa fa-calendar"></i>
                                        </button>
                                    </span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-sm-12">
                                <label class="control-label col-md-3">Data de Pagamento
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-9">
                                    <div class="input-group input-medium date date-picker"  data-date-format="dd/mm/yyyy">
                                        <input type="text" name="pagamento" value="{{$vencimento}}" class="form-control" readonly required>
                                        <span class="input-group-btn">
                                        <button class="btn default" type="button">
                                            <i class="fa fa-calendar"></i>
                                        </button>
                                    </span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-sm-12">
                                <label class="control-label col-md-3">Tags
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-9">
                                    <div class="input-group" id="cobranca-manual--tags-container" >
                                        <select name="tags[]" class="select2-tags form-control" id="" multiple></select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-sm-12">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="control-label col-md-4">Incluir pagamento?
                                            <span class="required"> * </span>
                                        </label>
                                        <label class="control-label col-md-8" v-if="incluir_pagamento">Descrição do pagamento:
                                            <span class="required"> * </span>
                                        </label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="col-md-4">
                                            <input type="checkbox" value="1" class="make-switch" id="incluir_pagamento" name="incluir_pagamento" data-on-color="success" data-off-color="danger" data-on-text="Sim" data-off-text="Não" onchange="vueCobrancaManual.incluir_pagamento = !vueCobrancaManual.incluir_pagamento"  v-model="incluir_pagamento">
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" required="required" v-if="incluir_pagamento" name="descricao_pagamento" class="form-control" placeholder="Baixa manual de pagamento não sincronizado...">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-sm-12" v-show="incluir_pagamento">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="control-label col-md-12">Registrar pagamento no SF?
                                            <span class="required"> * </span>
                                        </label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="col-md-4">
                                            <input type="checkbox" class="make-switch" value="1" id="incluir_sf" name="incluir_sf" data-on-color="success" data-off-color="danger" data-on-text="Sim" data-off-text="Não" onchange="vueCobrancaManual.incluir_sf = !vueCobrancaManual.incluir_sf"  v-model="incluir_sf">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group col-sm-12">
                                <label class="control-label col-md-3">Autor
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-12">
                                    <input type="text" readonly class="form-control" value="{{ Auth::user()->name }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="btn-gerar-boleto" class="btn btn-primary blue-steel">Registrar</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- MODAL COBRANÇA MANUAL-->
    <!-- CARTÕES -->
    @if ($cliente->forma_pagamento == "cartao")
    <div class="portlet  light  portlet-form ">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-credit-card font-red-sunglo"></i>
                <span class="caption-subject font-red-sunglo sbold uppercase">Cartões Cadastrados</span>
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
                <table class="table col-md-12 historico-financeiro">
                    <thead>
                    <tr>
                        <th> # </th>
                        <th> Bandeira </th>
                        <th> Final </th>
                        <th> Vencimento </th>
                        <th> Principal </th>
                        <th> Status </th>
                        <th> Ações </th>
                    </tr>
                    </thead>
                    <tbody>

                        @if(isset($info))
                    
                    @foreach($info->data->cards as $c)
                        <tr>
                            <td>{{ $c->card_id }} </td>
                            <td>
                                {{ $c->brand }}
                            </td>
                            <td>
                               {{$c->number}}
                            </td>
                            <td>
                                {{ $c->expire_in}}
                            </td>
                            <td>
                                @if($c->default)
                                    <span class="fa fa-star font-yellow-gold"></span>
                                @endif
                            </td>
                            <td>
                               @if($c->status == 'A')
                               <span class="badge badge-success">Ativo</span>
                               @else
                               <span class="badge badge-danger">Inativo</span>
                               @endif
                            </td>
                            <td>
                                @permission('cliente_cartao_credito_excluir')
                                <a href="javascript:;" data-card-id="{{ $c->card_id }}" data-customer-id="{{ $info->data->id }}" class="button btn-sm btn-circle bg-red tooltips excluirCartao" data-original-title="Excluir cartão"><i class="fa fa-trash font-white"></i></a>
                                @endpermission
                                @permission('cliente_cartao_credito_principal')
                                <a href="javascript:;" data-card-id="{{ $c->card_id }}" data-customer-id="{{ $info->data->id }}" class="button btn-sm btn-circle bg-blue tooltips cartaoPrincipal" data-original-title="Definir cartão como principal"><i class="fa fa-star font-yellow-gold"></i></a>
                                @endpermission
                            </td>
                        </tr>
                        
                    @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
    <!-- CARTÕES -->



    @endpermission
    <!-- END VALIDATION STATES-->
    @permission('ver_notas_clientes')
    <div class="portlet  light  portlet-form " id="notas">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-file-text-o font-red-sunglo"></i>
                <span class="caption-subject font-red-sunglo sbold uppercase">Notas</span>
            </div>
            <div class="actions">
                <div class="row">
                    <div class="col-md-offset-0 col-md-12">

                        <a class="btn  green-jungle btn-outline sbold" href="#nova_nota" data-toggle="modal"><i class="icon-plus red-sunglo"></i> Novo</a>

                    </div>
                </div>
            </div>
        </div>
        <div class="portlet-body">
            <div class="table-responsive">
                <table class="table col-md-12 historico-financeiro">
                    <thead>
                    <tr>
                        <th> </th>
                        <th width="20%"> Data </th>
                        <th width="50%"> Corpo </th>
                        <th width="20%"> Autor </th>
                        <th width="10%"> Ações </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="n in notas">
                        <td></td>
                        <td width="20%">@{{ n.created_at }}</td>
                        <td width="50%">@{{ n.corpo }}</td>
                        <td width="20%">@{{ n.autor }}</td>
                        <td width="10%">
                            <a class="button btn-sm btn-circle bg-red tooltips" data-placement="top" data-original-title="Excluir a nota." @click="excluirNota(n)">
                                <i class="fa fa-trash font-white"></i>
                            </a>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div id="nova_nota" class="modal fade" tabindex="-1" data-replace="true" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">Nova nota</h4>
                    </div>
                    <div class="modal-body">
                        <form class="form">
                            <div class="form-body">
                                <div class="form-group col-sm-12">
                                    <label class="control-label col-md-3">Corpo
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-12">
                                        <textarea name="corpo" id="corpo" rows="3" v-model="nota.corpo" class="form-control"></textarea>
                                    </div>
                                </div>
                                <div class="form-group col-sm-12">
                                    <label class="control-label col-md-3">Autor
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-12">
                                        <input type="text" readonly class="form-control" value="{{ Auth::user()->name }}">
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>
                    <div class="modal-footer">
                        <button type="button" data-dismiss="modal" class="btn dark btn-outline">Fechar</button>
                        <button type="button" id="salvar_nota" class="btn green-jungle btn-outline" @click="salvar()">Salvar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endpermission
    <!-- END VALIDATION STATES-->
    @permission('edit_clientes')
   
    <!-- Modal -->
<div class="modal fade" id="modal-dados-cartao" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Solicitar dados de cartão</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body text-center">
        @if(isset($info))
            <a target="blank" href="https://financeiro.lifepet.com.br/clientes/cartao/{{$info->data->hash}}" class="btn btn-primary">Link de captura</a>
          
          <p>
          - OU -
          </p>
          <form id="rd-captura-cartao" name="rd-captura-cartao">
              <input type="hidden" value="0eb70ce4d806faa1a1a23773e3d174d4" name="token_rdstation" id="token_rdstation" />
              <input type="hidden" value="envia-convite-cartao" name="identificador" id="identificador" />
              <input type="hidden" value="{{$cliente->email}}" name="email" id="rd-email" />
              <input type="hidden" value="{{$cliente->nome_cliente}}" name="name" id="rd-name" />
              <input type="hidden" value="{{$info->data->hash}}" name="custom_fields[1111021]" id="rd-link" />
          </form>
          <a href="javascript:;" class="btn btn-primary btn-captura-cartao">Solicitar por e-mail</a>
        @else
          Não foi possível obter o HASH no sistema financeiro.
        @endif
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
        </div>
      </div>
    </div>
  </div>


    @include('clientes.documentos')

    <div class="portlet  light  portlet-form " id="arquivos">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-folder-open-o font-green-sharp"></i>
                <span class="caption-subject font-green-sharp sbold uppercase">Arquivos</span>
            </div>
            <div class="actions">
                <div class="row">
                    <div class="col-md-offset-0 col-md-12">

                        <a class="btn  green-jungle btn-outline sbold tooltips" data-placement="top" data-original-title="Carregar" href="#novo_upload" data-toggle="modal"><i class="fa fa-upload red-sunglo"></i></a>

                    </div>
                </div>
            </div>
        </div>
        <div class="portlet-body">
            <div class="table-responsive">
                <table class="table col-md-12 historico-financeiro">
                    <thead>
                    <tr>
                        <th> </th>
                        <th > Criação </th>
                        <th > Descrição </th>
                        <th > Tamanho </th>
                        <th > Autor </th>
                        <th >  </th>
                    </tr>
                    </thead>
                    <tbody>

                    @foreach($cliente->getPropostas() as $key => $file)
                        <tr>
                            <td>
                                @php
                                @endphp
                            </td>
                            <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d', $file->data_proposta)->format('d/m/Y') }}</td>
                            <td>Proposta Digital</td>
                            <td>--</td>
                            <td>
                                @php
                                    $filePets = collect($file->pets)->toArray();
                                    ksort($filePets);
                                    $pet = head($filePets);
                                    $id_vendedor = $pet->plano->id_vendedor;
                                    $vendedor = \App\Models\Vendedores::find($id_vendedor);
                                @endphp
                                @if($vendedor)
                                    {{ $vendedor->nome }}
                                @else
                                    Não identificado
                                @endif
                            </td>
                            <td >
                                <a class="btn btn-xs blue font-white tooltips" data-placement="top" data-original-title="Visualizar" href="{{ route('clientes.proposta',['id'=>$cliente->id,'numProposta'=>$key]) }}" target="_blank">
                                    <span  class="fa fa-eye tooltips"></span>
                                </a>
                            </td>
                        </tr>
                    @endforeach

                    @foreach($cliente->uploads()->orderBy('created_at', 'DESC')->get() as $file)
                        <tr>
                            <td></td>
                            <td>{{ $file->created_at->format('d/m/Y H:i:s') }}</td>
                            <td>{{ nl2br($file->description) }}</td>
                            <td>{{ number_format($file->size/1024, 2, ",", ".") }}KB</td>
                            <td>{{ $file->user()->exists() ? $file->user()->first()->name : 'Sem Usuário' }}</td>
                            <td >
                                <a class="btn btn-xs blue font-white tooltips" data-placement="top" data-original-title="Visualizar" href="{{ url('/') }}/{{ $file->path }}" target="_blank">
                                    <span  class="fa fa-eye tooltips"></span>
                                </a>
                                <a class="btn btn-xs blue font-white tooltips" data-placement="top" data-original-title="Baixar" href="{{ url('/') }}/{{ $file->path }}" type="download" download="{{ $file->original_name }}" >
                                    <span  class="fa fa-download tooltips"></span>
                                </a>

                                @role(['ADMINISTRADOR', 'CADASTRO'])
                                <button class="btn btn-xs red font-white tooltips" data-toggle="modal" data-target="#deleteUpload-{{ $file->id }}" data-placement="top" data-original-title="Excluir">
                                    <span  class="fa fa-trash tooltips"></span>
                                </button>
                                <div id="deleteUpload-{{ $file->id }}" class="modal fade" data-replace="true" style="display: none;">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                                <h4 class="modal-title">Excluir Arquivo: {{ nl2br($file->description) }}</h4>
                                            </div>
                                            <form id="form-deleteUpload-{{ $file->id }}" class="form" action="{{ route('clientes.deleteUpload') }}" method="POST" >
                                                <div class="modal-body">
                                                    <h5>Para excluir este arquivo, informe a senha e justifique o motivo:</h5>
                                                    {{ csrf_field() }}
                                                    <input type="hidden" name="id_upload" value="{{ $file->id }}" required>
                                                    <div class="form-body">
                                                        <div class="row">
                                                            <div class="form-group col-sm-12">
                                                                <label class="control-label col-md-5">Senha:
                                                                    <span class="required"> * </span>
                                                                </label>
                                                                <div class="col-md-12">
                                                                    <input type="password" name="senha" class="form-control" placeholder="Confirme sua senha" required>
                                                                </div>
                                                            </div>
                                                            <div class="form-group col-sm-12">
                                                                <label class="control-label col-md-3">Justificativa
                                                                    <span class="required"> * </span>
                                                                </label>
                                                                <div class="col-md-12">
                                                                    <textarea type="text" required name="justificativa" class="form-control" placeholder="Justificativa para a exclusão"></textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" data-dismiss="modal" class="btn dark btn-outline">Fechar</button>
                                                    <button type="button" class="btn green-jungle btn-outline btn-deleteUpload">Salvar</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @endrole
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div id="novo_upload" class="modal fade" tabindex="-1" data-replace="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Novo upload</h4>
                </div>
                <div class="modal-body">
                    <form id="form_upload" class="form" action="{{ route('clientes.upload', $cliente->id) }}" method="POST" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="form-body">
                            <div class="form-group col-sm-12">
                                <label class="control-label col-md-5">Selecione o arquivo
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-12">
                                    <input type="file" class="form-control" name="file" accept="image/x-png,.tiff,image/bmp,image/jpeg,application/pdf,.csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required>
                                </div>
                            </div>
                            <div class="form-group col-sm-12">
                                <label class="control-label col-md-3">Descrição

                                </label>
                                <div class="col-md-12">
                                    <input type="text" name="description" class="form-control" value="" placeholder="Descrição do arquivo">
                                </div>
                            </div>
                            <div class="form-group col-sm-12">
                                <label class="control-label col-md-3">Visibilidade
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-12">
                                    {{ Form::hidden('publico',0) }}
                                    <input type="checkbox" name="publico" class="make-switch" data-on-color="success" data-off-color="danger" data-on-text="Público" data-off-text="Privado" value="1">
                                </div>
                            </div>
                            <div class="form-group col-sm-12">
                                <label class="control-label col-md-3">Autor
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-12">
                                    <input type="text" readonly class="form-control" value="{{ Auth::user()->name }}">
                                </div>
                            </div>
                        </div>



                    </form>

                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn dark btn-outline">Fechar</button>
                    <button type="button" id="salvar_upload" data-target="#form_upload" class="btn green-jungle btn-outline">Salvar</button>
                </div>
            </div>
        </div>
    </div>
    @endpermission
    @permission('edit_cobrancas')
    <div id="nova_baixa" class="modal fade" tabindex="-1" data-replace="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Nova baixa</h4>
                </div>
                <form id="form_nova_baixa" class="form" action="{{ route('cobrancas.cancelar') }}" method="POST">
                    <div class="modal-body">

                        {{ csrf_field() }}
                        <div class="form-body">
                            <div class="form-group col-sm-12">
                                <label class="control-label col-md-5">Selecione as parcelas
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-12">
                                    @php
                                        $cobrancas = $cliente->cobrancas()->where('status', 1)->orderBy('competencia', 'DESC')->get();
                                    @endphp
                                    <select name="cobrancas[]" id="cobrancas" multiple="true" class="select2" required onchange="vueCancelamentoCobrancas.selectedCobrancas = $('#cobrancas').val(); vueCancelamentoCobrancas.refreshSelect();">
                                        <option :value="c.id" v-for="c in cobrancas">@{{ c.texto }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-sm-12">
                                <label class="control-label col-md-3">Justificativa
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-12">
                                    <textarea type="text" v-model="justificativa" required name="justificativa" class="form-control" placeholder="Justificativa para a baixa"></textarea>
                                </div>
                            </div>
                            <div class="form-group col-sm-12">


                                <div class="row">
                                    <div class="col-md-12">
                                        <label class="control-label col-md-4">É acordo?
                                            <span class="required"> * </span>
                                        </label>
                                        <label class="control-label col-md-8" v-if="is_acordo">Parcela substitutiva:
                                            <span class="required"> * </span>
                                        </label>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="col-md-4">
                                            <input type="checkbox" class="make-switch" id="is_acordo" data-on-color="success" data-off-color="danger" data-on-text="Sim" data-off-text="Não" onchange="vueCancelamentoCobrancas.is_acordo = !vueCancelamentoCobrancas.is_acordo"  v-model="is_acordo">
                                        </div>
                                        <div class="col-md-8" v-if="is_acordo">
                                            <select name="acordo" v-model="acordo" id="acordo" class="select2 form-control" required>
                                                <option :value="c.id" v-for="c in cobrancasNaoSelecionadas">@{{ c.texto }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>


                            </div>
                            <div class="form-group col-sm-12">
                                <label class="control-label col-md-3">Autor
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-12">
                                    <input type="text" readonly class="form-control" value="{{ Auth::user()->name }}">
                                </div>
                            </div>
                        </div>





                    </div>
                    <div class="modal-footer">
                        <button type="button" data-dismiss="modal" class="btn dark btn-outline">Fechar</button>
                        <button type="submit" id="salvar_baixa" data-target="#form_nova_baixa" class="btn green-jungle btn-outline">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div id="forcar_debito" class="modal fade" tabindex="-1" data-replace="true" style="">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Forçar débito</h4>
            </div>
            <form id="form_forcar_debito" class="form" action="#" method="POST" data-url="{{ route('clientes.forcarDebito') }}">
                <div class="modal-body">
                    {{ csrf_field() }}
                    <div class="form-body">
                        <div class="form-group col-sm-12">
                            <label class="control-label col-md-5">Parcela:

                            </label>
                            <input type="hidden" name="id_cobranca" id="id_financeiro">
                            <div class="col-md-12">
                                <ul>
                                    <li>ID: <span id="forcar-debito--parcela--id"></span></li>
                                    <li>Competência: <span id="forcar-debito--parcela--competencia"></span></li>
                                    <li>Valor: <span id="forcar-debito--parcela--valor"></span></li>
                                    <li>Vencimento: <span id="forcar-debito--parcela--vencimento"></span></span></li>
                                </ul>
                            </div>
                        </div>
                        <div class="form-group col-sm-12">
                            <label class="control-label col-md-5">Selecione o cartão:
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-12">
                                @if(isset($info))
                                <select name="card_id" id="card" class="form-control">
                                    @foreach($info->data->cards as $c)
                                        <option value="{{ $c->card_id }}">{{ $c->brand }} - {{ $c->number }}</option>
                                    @endforeach
                                </select>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn dark btn-outline">Fechar</button>
                    <button type="submit" data-target="#form_forcar_debito" class="btn green-jungle btn-outline">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>
    @endpermission
    <!-- END VALIDATION STATES-->

    @include('informacoes_adicionais.vinculo', ['tabelaVinculada' => 'clientes', 'id' => $cliente->id])
@endsection

@section('scripts')
    @parent
    <script>



        function copyToClipboard(paymentLink) {
            event.preventDefault();
            navigator.clipboard.writeText(paymentLink).then(() => {
                swal("Sucesso!", 'Link de pagamento copiado para a area de tranferencia.', 'success');
            });
        }

        $(document).ready(function() {

            $('#btn_copy_payment_link').on('click', function(e) {
                e.preventDefault();
                let paymentUrl = $(this).attr('href');
                navigator.clipboard.writeText(paymentUrl).then(() => {
                    swal("Sucesso!", 'Link de pagamento copiado para a area de tranferencia.', 'success');
                });
            });

            $('.btn-forca-debito').on('click', function(){
                let id = $(this).data('id');
                let id_financeiro = $(this).data('id-financeiro');
                let competencia = $(this).data('competencia');
                let vencimento = $(this).data('vencimento');
                let valor = $(this).data('valor-original');

                $("#forcar_debito span#forcar-debito--parcela--id").html('');
                $("#forcar_debito span#forcar-debito--parcela--competencia").html('');
                $("#forcar_debito span#forcar-debito--parcela--vencimento").html('');
                $("#forcar_debito span#forcar-debito--parcela--valor").html('');
                $("#forcar_debito #id_financeiro").val(null);

                $("#forcar_debito span#forcar-debito--parcela--id").html("#" + id);
                $("#forcar_debito span#forcar-debito--parcela--competencia").html(competencia);
                $("#forcar_debito span#forcar-debito--parcela--vencimento").html(vencimento);
                $("#forcar_debito span#forcar-debito--parcela--valor").html(valor);
                $("#forcar_debito #id_financeiro").val(id_financeiro);

                $('#forcar_debito').modal('show');
            });

            $('#form_forcar_debito').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    url: '{{ route('clientes.forcarDebito') }}',
                    data: $(this).serialize(),
                    method: 'POST',
                    success: function (response) {
                        swal("Sucesso!", 'O pagamento foi realizado com sucesso!', 'success');
                        //location.reload();
                    },
                    error: function () {
                        swal('Oops!', "Não foi possível realizar o pagamento da parcela.", 'error');
                    }
                });
            });

            $('#form-boleto-avulso').on('submit', function(e){
                e.preventDefault();
                $("#btn-gerar-boleto").text('Gerando, aguarde...');
                $("#btn-gerar-boleto").attr('disabled', true);
                $.post("{{ route('clientes.boletoAvulso', ['id' => $cliente->id]) }}", $(this).serialize(), function(response){
                    console.log(response);
                    if(response.error){

                        $("#btn-gerar-boleto").text('Gerar boleto');
                        $("#btn-gerar-boleto").removeAttr('disabled');

                        swal("Não foi possível gerar o boleto avulso", (response.error.description ? response.error.description : ''), 'error');
                    }
                    else {
                        $("#btn-gerar-boleto").text('Gerar boleto');
                        $("#btn-gerar-boleto").removeAttr('disabled');

    
                        swal({
                            title: "Boleto gerado com sucesso!",
                            type: "success"
                        }).then(okay => {
                            if (okay) {
                                window.location.reload();
                            }
                        }); 
                    
                    }
                },'json').fail(function(response) {
                    var resp = response.responseJSON;
                    console.log(resp);
                    $("#btn-gerar-boleto").text('Gerar boleto');
                    $("#btn-gerar-boleto").removeAttr('disabled');

                    swal((resp.error && resp.error.description ? resp.error.description : "Não foi possível gerar o boleto avulso"), '', 'error');
                });
            })


            $('.btn-captura-cartao').on('click', function(){
                $('.btn-captura-cartao').text('Enviando... Aguarde!');
                $.post('https://www.rdstation.com.br/api/1.2/conversions',$('#rd-captura-cartao').serialize(), function(res){
                
                }).always(function() {
                    $('.btn-captura-cartao').text('Solicitação enviada!');
                });
            });
            var $actions = $('.actions[data-target="#Clientes"]');

            $actions.find('#save').click(function() {
                var target = $actions.attr('data-target');
                if(target != '') {
                    $(target).submit();
                }
            });
            $actions.find('#cancel').click(function() {
                var target = $actions.attr('data-target');
                location.href = "{!! route('clientes.index') !!}";
                return;
            });

            //Handle upload

            $('#salvar_upload').click(function(e) {
                e.preventDefault();
                var $self = $(this);
                $self.addClass('disabled');
                var $target = $($self.data('target'));
                $target.submit();
            });

            $('.btn-deleteUpload').click(function (e) {
                e.preventDefault();
                var modal = $(this).closest('.modal');
                var form = $(this).closest('form');
                var formData = {};
                var closestTr = form.closest('tr');

                $(form.serializeArray()).each(function(i, field){
                    formData[field.name] = field.value;
                });

                var id_upload = formData.id_upload;
                var senha = formData.senha;
                var justificativa = formData.justificativa;

                if (senha === false || justificativa === false) {
                    return false;
                } else {
                    if (senha === "" || justificativa === "") {
                        if (senha === "") {
                            swal("A senha é obrigatória!", '', 'error');
                            return false
                        }
                        if (justificativa === "") {
                            swal("A justificativa é obrigatória!", '', 'error');
                            return false
                        }
                    } else {

                        $.post(form.attr('action'), {
                            _token: '{{ csrf_token() }}',
                            id_upload: id_upload,
                            senha: senha,
                            justificativa: justificativa,
                        }, function (data) {
                            swal({
                                title: data.msg.title,
                                text: data.msg.text,
                                type: data.msg.type
                            });
                            if (data.msg.type !== 'error') {
                                modal.modal('hide');
                                closestTr.hide();
                            }
                        });

                    }
                }
                return false;
            });

            $('#resetarSenha').click(function(e) {
                var prompted = prompt('Digite RESETAR para executar a ação.');
                if(prompted.toUpperCase() === 'RESETAR') {
                    $('form#resetarSenhaCliente').submit();
                    return true;
                } else {
                    e.preventDefault();
                    alert('Operação cancelada.');
                    return false;
                }
            });

            $('#excluirNota').click(function(e) {
                var prompted = prompt('Digite EXCLUIR para executar a ação.');
                if(prompted.toUpperCase() === 'EXCLUIR') {
                    $('form#excluirNota').submit();
                    return true;
                } else {
                    e.preventDefault();
                    alert('Operação cancelada.');
                    return false;
                }
            });

            $('.excluirCartao').click(function(e) {
                var $card_id = $(this).data('card-id');

                var prompted = prompt('Digite EXCLUIR para executar a ação.');
                if(prompted.toUpperCase() === 'EXCLUIR') {
                    $.ajax({
                        url: '{{ route('clientes.cartoes.excluir') }}',
                        method: 'POST',
                        data: {
                            'card_id' : $card_id,
                            '_token': '{{ csrf_token() }}',
                        },
                        success: function(data) {
                            alert('Excluindo cartão: ' + card_id);
                            location.reload();
                        },
                        error: function(data) {
                            console.log(data);
                            alert('Houve um erro ao tentar excluir o cartão.');
                        }
                    });

                    return true;
                } else {
                    e.preventDefault();
                    alert('Operação cancelada.');
                    return false;
                }
            });

            $('.cartaoPrincipal').click(function(e) {
                var $card_id = $(this).data('card-id');
                var $customer_id = $(this).data('customer-id');
                var prompted = confirm('Confirma o cartão #'+ $card_id + ' como cartão principal?');
                if(prompted) {
                    $.ajax({
                        url: '{{ route('clientes.cartoes.principal') }}',
                        method: 'POST',
                        data: {
                            'card_id' : $card_id,
                            'customer_id' : $customer_id,
                            '_token': '{{ csrf_token() }}',
                        },
                        success: function(data) {
                            alert('O cartão #' + $card_id + ' agora é o principal.');
                            location.reload();
                        },
                        error: function(data) {
                            console.log(data);
                            alert('Houve um erro ao tentar definir o cartão como principal');
                        }
                    });
                } else {
                    e.preventDefault();
                    alert('Operação cancelada.');
                    return false;
                }
            });

            // $('#salvar_baixa').click(function(e) {
            //     e.preventDefault();
            //     var $self = $(this);
            //     $self.addClass('disabled');
            //     var $target = $($self.data('target'));
            //     $target.submit();
            // });

            $('[data-modal]').click(function(e) {
                var modal = $(this).data('modal');
                var $modal = $(modal);
                $modal.modal('show');
            })

            $('.select2-tags').select2({
                tags: true,
                dropdownParent: $('#cobranca-manual--tags-container')
            })
        });


    </script>
@endsection
