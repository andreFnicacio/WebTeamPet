@extends('layouts.app')

@php
    if(Entrust::hasRole(['CLINICAS']) && !Entrust::hasRole(['ADMINISTRADOR', 'AUDITORIA'])) {
        $clinica = \Modules\Clinics\Entities\Clinicas::where('id_usuario', Auth::user()->id)->first();
    }
@endphp

@section('css')
    @parent
    <link href="{{ url('/') }}/assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css"/>
    <link href="{{ url('/') }}/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css"
          rel="stylesheet" type="text/css"/>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code&display=swap" rel="stylesheet">
    <style>
        .portlet.light > .portlet-title > .caption > .caption-subject {
            font-size: 29px;
            font-family: 'Roboto', sans-serif;
            font-weight: 100 !important;
            letter-spacing: 0.67px;
            float: none;

        }

        .portlet.light > .portlet-title > .caption {
            float: none;
            margin-bottom: 20px;
        }

        ul.dropdown-menu.dropdown-acoes-guia {
            overflow: auto;
            position: relative;
            min-width: unset;
        }

        .select2-container--bootstrap {
            width: auto !important;
        }

        .badge-enc {
            font-size: 10px !important;
            margin-bottom: 3px;
        }

        .badge-enc i {
            line-height: 11px !important;
        }

        .font-numeric {
            font-family: 'Fira Code', monospace !important;
            font-weight: bold !important;
            font-size: 11px !important;
            text-transform: uppercase;
        }

        @media screen and (min-width: 1400px) {
            .font-numeric {
                font-family: 'Fira Code', monospace !important;
                font-weight: bold !important;
                font-size: 13px !important;
                text-transform: uppercase;
            }
        }

        .table-checkable tr > td:first-child, .table-checkable tr > th:first-child {
            max-width: 200px;
            min-width: 100px;
        }
    </style>
@endsection

@section('title')
    @parent
    Guias
@endsection

@section('content')
    @include('common.swal')
    <div class="row">

        <div class="col-md-12">
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet light ">
                <div class="portlet-title text-center">
                    <div class="caption font-blue">
                        {{--<i class="fa fa-tags font-blue"></i>--}}
                        <span class="caption-subject bold uppercase">Guias</span>
                    </div>
                    {{--<div class="actions">--}}
                    {{--<div class="btn-group btn-group-devided" data-toggle="buttons">--}}
                    {{--<div class="btn-group pull-right">--}}
                    {{--<button class="btn green  btn-outline dropdown-toggle" data-toggle="dropdown">Opções--}}
                    {{--<i class="fa fa-angle-down"></i>--}}
                    {{--</button>--}}
                    {{--<ul class="dropdown-menu pull-right">--}}
                    {{--<li>--}}
                    {{--<a href="javascript:;">--}}
                    {{--<i class="fa fa-print"></i> Imprimir </a>--}}
                    {{--</li>--}}
                    {{--<li>--}}
                    {{--<a href="javascript:;">--}}
                    {{--<i class="fa fa-file-pdf-o"></i> Salvar em PDF </a>--}}
                    {{--</li>--}}
                    {{--<li>--}}
                    {{--<a href="javascript:;">--}}
                    {{--<i class="fa fa-file-excel-o"></i> Exportar para Excel </a>--}}
                    {{--</li>--}}
                    {{--</ul>--}}
                    {{--</div>--}}
                    {{--</div>--}}
                    {{--</div>--}}
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <form action="{{ route(Route::getCurrentRoute()->getName()) }}" method="GET" class="form">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <h4 class="filter-label">Período</h4>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="input-group input-large date-picker input-daterange"
                                                         data-date="{{ $params['start'] }}"
                                                         data-date-format="dd/mm/yyyy">
                                                        <input type="text" class="form-control" name="start"
                                                               value="{{ $params['start'] }}">
                                                        <span class="input-group-addon"> até </span>
                                                        <input type="text" class="form-control" name="end"
                                                               value="{{ $params['end'] }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <h4 class="filter-label">Termo</h4>
                                            <input type="text" class="form-control" name="termo"
                                                   value="{{ $params['termo'] }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <h4 class="filter-label">Status</h4>
                                            <select name="status" id="status" class="form-control">
                                                <option value=""></option>
                                                <option value="LIBERADO"
                                                        {{ $params['status'] === 'LIBERADO' ? 'selected' : '' }}>
                                                    AUTORIZADO
                                                </option>
                                                <option value="RECUSADO"
                                                        {{ $params['status'] === 'RECUSADO' ? 'selected' : '' }}>
                                                    NÃO AUTORIZADO
                                                </option>
                                                <option value="AVALIANDO"
                                                        {{ $params['status'] === 'AVALIANDO' ? 'selected' : '' }}>
                                                    AGUARDANDO
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <h4 class="filter-label">Autorização</h4>
                                            <select name="autorizacao" id="status" class="form-control">
                                                <option value=""></option>
                                                <option value="AUTOMATICA"
                                                        {{ $params['autorizacao'] === 'AUTOMATICA' ? 'selected' : '' }}>
                                                    AUTOMÁTICA
                                                </option>
                                                <option value="AUDITORIA"
                                                        {{ $params['autorizacao'] === 'AUDITORIA' ? 'selected' : '' }}>
                                                    AUDITORIA
                                                </option>
                                                <option value="FORCADO"
                                                        {{ $params['autorizacao'] === 'FORCADO' ? 'selected' : '' }}>
                                                    FORÇADA
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <button type="submit" class="btn blue margin-top-30">
                                                <span>Pesquisar</span> <span class="fa fa-search"></span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="table-toolbar">
                        <div class="row">
                            <div class="col-md-6">

                            </div>
                            <div class="col-md-6">

                            </div>
                        </div>
                    </div>
                    <table data-order='[[ {{ \Entrust::hasRole(['ADMINISTRADOR', 'AUDITORIA']) ? 7 : 5 }}, "desc" ]]'
                           class="table table-hover table-checkable order-column datatables-guias responsive table-responsive">
                        <thead>
                        <tr>
                            {{--<th>--}}
                            {{--<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">--}}
                            {{--<input type="checkbox" class="group-checkable" data-set="#sample_1 .checkboxes" />--}}
                            {{--<span></span>--}}
                            {{--</label>--}}
                            {{--</th>--}}
                            <th width="15%"> Guia</th>
                            <th width="10%"> Pet</th>
                            @if(\Entrust::hasRole(['ADMINISTRADOR', 'AUDITORIA']))
                                <th width="20%"> Veterinário</th>
                                <th width="20%"> Clínica</th>
                            @endif
                            <th width="2%"> Status</th>
                            <th width="2%"> Tipo</th>
                            <th width="2%">
                                @if(\Entrust::hasRole(['ADMINISTRADOR', 'AUDITORIA']))
                                    Aut.
                                @endif
                            </th>
                            <th> Data da Solicitação</th>
                            {{--<th> Data da Liberação </th>--}}
                            <th> Ações</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($guias as $guia)
                            <tr class="even gradeX">
                                {{--<td>--}}
                                {{--<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">--}}
                                {{--<input type="checkbox" class="checkboxes" value="1" />--}}
                                {{--<span></span>--}}
                                {{--</label>--}}
                                {{--</td>--}}
                                <td class="font-numeric">
                                    {{ $guia->numero_guia }}
                                    @if($guia->isGlosado())
                                        <span class="badge-enc badge badge-danger" data-toggle="tooltip"
                                              title="Glosada">G</span>
                                    @endif
                                </td>
                                <td>

                                    @php
                                        $dataLiberada = $guia->dataLiberada();
                                    @endphp

                                    @if(Entrust::can('edit_pets'))
                                        <a href="{{ route('pets.edit', $guia->pet->id) }}"
                                           target="_blank"> {{ $guia->pet->nome_pet }} </a>
                                    @else
                                        <span>{{ $guia->pet->nome_pet }}<span>
                                        @endif

                                                @if(!$guia->realizado_em)
                                                    @if($guia->tipo_atendimento === 'ENCAMINHAMENTO')

                                                        @if($guia->id_clinica == $guia->id_solicitador)
                                                            @if($guia->status == \Modules\Guides\Entities\HistoricoUso::STATUS_LIBERADO)
                                                                <br>
                                                                <span class="badge-enc badge badge-primary bg-blue-madison-opacity">
                                                        VOCÊ MESMO DEVERÁ REALIZAR ESTA GUIA
                                                    </span>
                                                            @else
                                                                <br>
                                                                <span class="badge-enc badge badge-primary bg-blue-madison-opacity">
                                                        VOCÊ ENCAMINHOU ESSA GUIA
                                                    </span>
                                                            @endif
                                                        @elseif($guia->id_solicitador)
                                                            <br>
                                                            <span class="badge-enc badge badge-primary bg-blue-madison-opacity">
                                                    ENCAMINHADO POR: {!! $guia->solicitante()->first()->nome_clinica !!}
                                                </span>
                                                        @else
                                                            <br>
                                                            <span class="badge-enc badge badge-primary bg-blue-madison-opacity">
                                                    GUIA ABERTA PELA AUDITORIA
                                                </span>
                                                        @endif

                                                        @if($guia->status == \Modules\Guides\Entities\HistoricoUso::STATUS_LIBERADO && $guia->data_liberacao)
                                                            @if($dataLiberada)
                                                                <br>
                                                                <span class="badge-enc badge badge-success"
                                                                      data-toggle="tooltip"
                                                                      title="Você já atingiu a data mínima e está autorizado a agendar e realizar o procedimento com o cliente.">
                                                            LIBERADO PARA REALIZAÇÃO
                                                                <i class="fa fa-info-circle"></i>
                                                        </span>
                                                            @else
                                                                <br>
                                                                <span class="badge-enc badge badge-success">
                                                            LIBERADO A PARTIR DE: {{ \Carbon\Carbon::parse($guia->data_liberacao)->format('d/m/Y H:i') }}
                                                        </span>
                                                            @endif
                                                        @elseif($guia->status == \Modules\Guides\Entities\HistoricoUso::STATUS_RECUSADO)
                                                            <br>
                                                            <span class="badge-enc badge badge-danger"
                                                                  data-toggle="tooltip"
                                                                  title="Essa guia foi recusada pela auditoria e não poderá ser executada.">
                                                GUIA NÃO AUTORIZADA
                                                    <i class="fa fa-info-circle"></i>
                                                </span>
                                                        @else
                                                            <br>
                                                            <span class="badge-enc badge badge-warning bg-yellow-saffron"
                                                                  data-toggle="tooltip"
                                                                  title="Após a análise da Auditoria, essa guia, caso não tenha sido encaminhada para você mesmo, sairá da sua lista e passará a compor a lista da credenciada escolhida.">
                                                GUIA EM ANÁLISE PELA AUDITORIA
                                                    <i class="fa fa-info-circle"></i>
                                                </span>
                                    @endif

                                    @endif
                                    @endif
                                </td>
                                @if(\Entrust::hasRole(['ADMINISTRADOR', 'AUDITORIA']))
                                    <td class="text-uppercase">
                                        <span>{{ $guia->prestador ? $guia->prestador->nome : "-" }}</span>
                                    </td>
                                    <td class="text-uppercase">
                                        @if(isset($clinica))
                                            <span>{{ $clinica->nome_clinica }}</span>
                                        @else
                                            <span>{{ $guia->clinica->nome_clinica }}</span>
                                        @endif
                                    </td>
                                @endif
                                <td class="text-center">
                                    @if($guia->status === 'LIBERADO')
                                        @if(!$guia->realizado_em)
                                            @if($guia->tipo_atendimento === \Modules\Guides\Entities\HistoricoUso::TIPO_ENCAMINHAMENTO)
                                                <span class="label label-sm bg-blue-soft" data-toggle="tooltip"
                                                      title="AGUARDANDO AGENDAMENTO"> AGD </span>
                                            @else
                                                <span class="label label-sm bg-green-meadow" data-toggle="tooltip"
                                                      title="AUTORIZADO"> AUT </span>
                                            @endif
                                        @else
                                            <span class="label label-sm bg-green-steel bg-font-green-steel"
                                                  data-toggle="tooltip" data-title="REALIZADO">RZ</span>
                                        @endif
                                    @elseif($guia->status === 'RECUSADO')
                                        <span class="label label-sm label-danger" data-toggle="tooltip"
                                              title="NÃO AUTORIZADO"> NA </span>
                                    @else
                                        <span class="label label-sm label-info" data-toggle="tooltip"
                                              title="AGUARDANDO"> AG </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($guia->tipo_atendimento === 'NORMAL')
                                        <span class="label label-sm label-info" data-toggle="tooltip"
                                              title="NORMAL"> NR </span>
                                    @elseif($guia->tipo_atendimento === 'ENCAMINHAMENTO')
                                        <span class="label label-sm bg-purple-studio bg-font-purple-studio"
                                              data-toggle="tooltip" title="ENCAMINHAMENTO"> EN </span>
                                    @else
                                        <span class="label label-sm label-warning" data-toggle="tooltip"
                                              title="EMERGÊNCIA"> EM </span>
                                    @endif
                                </td>

                                <td class="text-center">
                                    @if(\Entrust::hasRole(['ADMINISTRADOR', 'AUDITORIA']))
                                        @if($guia->autorizacao === 'AUTOMATICA')
                                            <span class="label label-sm label-info" data-toggle="tooltip"
                                                  title="AUTOMÁTICA"> AT </span>
                                        @elseif($guia->autorizacao === 'AUDITORIA')
                                            <span class="label label-sm label-success" data-toggle="tooltip"
                                                  title="AUDITORIA"> AD </span>
                                        @else
                                            <span class="label label-sm label-warning" data-toggle="tooltip"
                                                  title="FORÇADA"> F </span>
                                        @endif
                                    @endif
                                </td>
                                <td class="center"><span
                                            class="hide">{{ $guia->created_at->format('YmdHis') }}</span> {{ $guia->created_at->format('d/m/Y H:i') }}
                                </td>
                                {{--<td class="center">--}}
                                {{--<span class="hide">{{ $guia->data_liberacao ? $guia->data_liberacao->format('YmdHis') : " - " }}</span> {{ $guia->data_liberacao ? $guia->data_liberacao->format('d/m/Y H:i') : " - " }}--}}
                                {{--</td>--}}
                                <td class="guia_acoes">
                                    <div class="btn-group">
                                        <button class="btn btn-xs green dropdown-toggle" type="button"
                                                data-toggle="dropdown" aria-expanded="false"> Ações
                                            <i class="fa fa-angle-down"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-acoes-guia pull-left" role="menu">

                                            <li class="{{ $dataLiberada ? "" : 'disabled' }}">
                                                @if($guia->tipo === "ENCAMINHAMENTO")
                                                    @if($guia->status === 'REALIZADO' || Entrust::hasRole(['AUTORIZADOR', 'ADMINISTRADOR', 'AUDITORIA']))
                                                        <a target="_blank"
                                                           href="{{ $dataLiberada ? route('autorizador.verGuia', $guia->numero_guia) : "" }}"
                                                        >
                                                            <i class="icon-tag"></i> Ver
                                                            guia {{ $dataLiberada ? "" : "({$guia->data_liberacao->format('d/m/Y H:i')})"}}
                                                        </a>
                                                    @endif
                                                @else
                                                    @if($guia->status === 'LIBERADO' || Entrust::hasRole(['AUTORIZADOR', 'ADMINISTRADOR', 'AUDITORIA']))
                                                        <a target="_blank"
                                                           href="{{ $dataLiberada ? route('autorizador.verGuia', $guia->numero_guia) : "" }}"
                                                        >
                                                            <i class="icon-tag"></i> Ver guia
                                                        </a>
                                                    @endif
                                                @endif
                                            </li>
                                            <li>
                                                @if(!empty($guia->laudo))
                                                    <a href="#laudo_guia_{{ $guia->numero_guia }}" data-toggle="modal">
                                                        <i class="icon-user"></i> Ver laudo
                                                    </a>
                                                @endif
                                            </li>
                                            @if(\Entrust::hasRole(['ADMINISTRADOR', 'AUDITORIA']))
                                                <li>
                                                    <a href="#anexos_guia_{{ $guia->numero_guia }}" data-toggle="modal">
                                                        <i class="fa fa-file"></i> Anexos
                                                    </a>
                                                </li>
                                            @endif
                                            <li>
                                                <a data-toggle="modal" href="#detalhes_guia_{{ $guia->numero_guia }}">
                                                    <i class="fa fa-info-circle"></i> Detalhar
                                                </a>
                                            </li>
                                            @if($guia->tipo_atendimento === 'ENCAMINHAMENTO' && !$guia->realizado_em && $guia->status === 'LIBERADO' && $dataLiberada)
                                                <li>
                                                    <a href="{{ route('autorizador.formRealizar', $guia->numero_guia) }}">
                                                        <i class="fa fa-check"></i> Realizar
                                                    </a>
                                                </li>
                                            @endif
                                            @permission('autorizar_guia')
                                            <li class="divider"></li>
                                            <li>
                                                <a href="javascript:;" class="atualizar-guia"
                                                   data-guia="{{ $guia->numero_guia }}" data-action="liberar">
                                                    <i class="fa fa-thumbs-up "></i> Liberar
                                                </a>
                                            </li>

                                            <li>
                                                <a href="javascript:;" class="atualizar-guia"
                                                   data-guia="{{ $guia->numero_guia }}" data-action="recusar">
                                                    <i class="fa fa-thumbs-down "></i> Recusar
                                                </a>
                                            </li>
                                            @if($guia->tipo_atendimento === 'ENCAMINHAMENTO')
                                                <li class="divider"></li>
                                                <li>
                                                    <a href="javascript:;" class="atualizar-guia"
                                                       data-guia="{{ $guia->numero_guia }}" data-action="agendar">
                                                        <i class="fa fa-calendar"></i> Agendar
                                                    </a>
                                                </li>
                                            @endif
                                            @endpermission

                                            @if(!$guia->status === 'REALIZADO' || Entrust::hasRole(['AUTORIZADOR', 'ADMINISTRADOR', 'AUDITORIA']))
                                                <li class="divider"></li>
                                                <li>
                                                    <a href="javascript:;"
                                                       class="{{ $guia->glosa() ? 'reverter-glosa' : 'glosar-guia' }}"
                                                       data-guia="{{ $guia->numero_guia }}">
                                                        <i class="fa fa-exclamation-circle"></i> Glosar Guia
                                                    </a>
                                                </li>
                                            @endif

                                            @if($guia->canPagamentoAlternativo())
                                                <li class="divider"></li>
                                                <li>
                                                    <a href="{{ Route('autorizador.pagamentoDireto', $guia->numero_guia) }}"
                                                       data-guia="{{ $guia->numero_guia }}">
                                                        <i class="fa fa-barcode"></i> Pagamento Alternativo
                                                    </a>
                                                </li>
                                            @endif
                                            <li class="divider"></li>
                                            <li>
                                                <a href="javascript:;" class="cancelar-guia"
                                                   data-guia="{{ $guia->numero_guia }}" data-action="cancelar">
                                                    <i class="fa fa-trash"></i> Cancelar
                                                </a>
                                            </li>

                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div class="row">
                        <div class="col-sm-12">
                            {{ $guias->appends($params)->links() }}
                        </div>
                    </div>

                </div>
            </div>
            <!-- END EXAMPLE TABLE PORTLET-->
        </div>
    </div>
@endsection

@section('scripts')
    @parent
    @php
        $prestadores = \Modules\Veterinaries\Entities\Prestadores::all();
        $clinicas = \Modules\Clinics\Entities\Clinicas::all();
    @endphp
    @foreach($guias as $guia)
        <div id="laudo_guia_{{ $guia->numero_guia }}" class="modal fade" tabindex="-1" data-replace="true"
             style="display: none;">
            <div class="modal-dialog">
                @php

                    $laudoEditavel = (new \Carbon\Carbon())->lte($guia->created_at->addHours(\Modules\Guides\Entities\HistoricoUso::HORAS_LAUDO_EDITAVEL));
                @endphp

                <form action="{{ route('autorizador.adicionarLaudo') }}" method="POST" enctype="multipart/form-data">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                            <h4 class="modal-title">Laudo ({{ $guia->numero_guia }})</h4>
                        </div>
                        <div class="modal-body">
                            <p>
                                {!! nl2br($guia->laudo) !!}
                            </p>
                            @if(!empty($guia->justificativa) && \Entrust::hasRole(['ADMINISTRADOR', 'AUDITORIA']))
                                <br>
                                <hr>Justificativa:
                                <p>
                                    {{ $guia->justificativa }}
                                </p>
                            @endif

                            @if($laudoEditavel)
                                {{ csrf_field() }}
                                <input type="hidden" name="numero_guia" value="{{ $guia->numero_guia }}">
                                <textarea name="laudo_adicional" rows="4" class="form-control"
                                          placeholder="Laudo adicional"></textarea>
                                <div class="form-group" style="margin-top: 10px;">
                                    <label class="control-label">Selecione laudos ou imagens, se existirem:</label>
                                    <input type="file" class="form-control" name="file[]"
                                           accept="image/png,image/jpeg,application/pdf" multiple>
                                </div>
                            @endif
                        </div>
                        <div class="modal-footer">
                            @if($laudoEditavel)
                                <button type="submit" class="btn btn-outline btn-success">Alterar</button>
                            @endif
                            <button type="button" data-dismiss="modal" class="btn dark btn-outline">Fechar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div id="anexos_guia_{{ $guia->numero_guia }}" class="modal fade" tabindex="-1" data-replace="true"
             style="display: none;">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">Anexos ({{ $guia->numero_guia }})</h4>
                    </div>
                    <div class="modal-body">

                        <form action="{{ route('autorizador.adicionarAnexo') }}" method="POST"
                              enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="numero_guia" value="{{ $guia->numero_guia }}">
                            <div class="row">
                                <div class="col-md-10">
                                    <div class="form-group" style="margin-top: 10px;">
                                        <label class="control-label">Selecione o arquivo:</label>
                                        <input type="file" class="form-control" name="file[]"
                                               accept="image/png,image/jpeg,application/pdf" multiple required>
                                    </div>
                                </div>
                                <div class="col-md-2" style="margin-top: 35px">
                                    <button type="submit" class="btn btn-outline btn-success">Salvar</button>
                                </div>
                            </div>
                        </form>

                        @if(\Entrust::hasRole(['ADMINISTRADOR', 'AUDITORIA']))
                            <br>
                            <hr>Anexos:

                            <table class="table">
                                <tr>
                                    <th>Anexo</th>
                                    <th>Usuário</th>
                                    <th>Data</th>
                                </tr>
                                @foreach($guia->anexos() as $indice => $anexo)
                                    <tr>
                                        <td><a href="{{ url('/') }}/autorizador/guia/{{ $anexo->path }}" type="download"
                                               download="{{ $anexo->original_name }}"> Anexo {{ ++$indice }} </a></td>
                                        <td>{{ $anexo->user->name }}</td>
                                        <td>{{ $anexo->created_at->format('d/m/Y H:i:s') }}</td>
                                    </tr>
                                @endforeach
                            </table>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" data-dismiss="modal" class="btn dark btn-outline">Fechar</button>
                    </div>
                </div>

            </div>
        </div>

        @if($guia->tipo_atendimento === 'ENCAMINHAMENTO')
            <div id="encaminhamento_guia_{{ $guia->numero_guia }}" class="modal fade" tabindex="-1" data-replace="true"
                 style="display: none;">
                <div class="modal-dialog">
                    <form action="{{ route('autorizador.agendar') }}" method="POST">
                        {{ csrf_field() }}
                        <input type="hidden" name="numero_guia" value="{{ $guia->numero_guia }}">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                <h4 class="modal-title">Guia de encaminhamento: {{ $guia->numero_guia }}</h4>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="col-sm-3">
                                            <label for="data_liberacao">Data da liberação:</label><br>
                                        </div>
                                        <div class="col-sm-8">
                                            <input type="datetime-local" required class="form-control"
                                                   name="data_liberacao" data-date-format="d/m/Y H:i">
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="col-sm-3">
                                            <label for="clinica_encaminhamento">Clínica:</label><br>
                                        </div>
                                        <div class="col-sm-8">
                                            <select name="clinica_encaminhamento" class="select2_modal form-control"
                                                    data-parent="encaminhamento_guia_{{ $guia->numero_guia }}" required>
                                                <option selected></option>
                                                @foreach($clinicas as $c)
                                                    <option value="{{ $c->id }}">{{ $c->id }}
                                                        - {{ $c->nome_clinica }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="col-sm-3">
                                            <label for="prestador_encaminhamento">Clínica:</label><br>
                                        </div>
                                        <div class="col-sm-8">
                                            <select name="prestador_encaminhamento" class="select2_modal form-control"
                                                    data-parent="encaminhamento_guia_{{ $guia->numero_guia }}" required>
                                                <option selected></option>
                                                @foreach($prestadores as $p)
                                                    <option value="{{ $p->id }}">{{ $p->id }} - {{ $p->nome }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-outline btn-success">Confirmar</button>
                                <button type="button" data-dismiss="modal" class="btn dark btn-outline">Fechar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endif
        <div id="detalhes_guia_{{ $guia->numero_guia }}" class="modal fade" tabindex="-1" data-replace="true"
             style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">Detalhes da Guia #{{ $guia->numero_guia }}</h4>
                    </div>
                    <div class="modal-body">
                        <ul>
                            <li>Número: {{ $guia->numero_guia }}</li>
                            <li>Pet: {{ $guia->pet->nome_pet }}</li>
                            <li>Tutor: {{ $guia->pet->cliente->nome_cliente }}</li>
                            <li>Atendimento: {{ $guia->tipo_atendimento }}</li>
                            {{--<li>Status: {{ $guia->status }}</li>--}}
                            <li>Emissão: {{ \App\Helpers\Utils::shortDate($guia->created_at)}}</li>
                            @if($guia->tipo_atendimento === \Modules\Guides\Entities\HistoricoUso::TIPO_ENCAMINHAMENTO)
                                <li>
                                    Liberação: {{ $guia->data_liberacao ? \App\Helpers\Utils::dateTime($guia->data_liberacao) : "Não liberado" }}</li>
                                <li>
                                    Realização: {{ $guia->realizado_em ? \App\Helpers\Utils::dateTime($guia->realizado_em) : "Não realizado" }}</li>
                            @endif
                            <li>
                                Procedimentos
                                <ul>
                                    @foreach(\App\Models\Procedimentos::byHistoricoUso($guia) as $procedimento)
                                        <li>{{ $procedimento->nome_procedimento }}</li>
                                    @endforeach
                                </ul>
                            </li>
                        </ul>
                    </div>
                    <div class="modal-footer">

                        <button type="button" data-dismiss="modal" class="btn dark btn-outline">Fechar</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
    <script>
        $(document).ready(function () {
            $('.select2_modal').each(function (k, v) {
                $(v).select2({
                    tags: true,
                    dropdownParent: $("#" + $(v).data('parent'))
                });
            });
            $('#button_emitirGuia').click(function () {
                swal({
                    title: 'Deseja forçar a autorização?',
                    text: "O procedimento estará sujeito a glosa.",
                    type: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Forçar',
                    cancelButtonText: 'Aguardar liberação',
                    confirmButtonClass: 'btn btn-danger',
                    cancelButtonClass: 'btn btn-info',
                    buttonsStyling: false
                }).then(function () {
                    $('#autorizacao').val('FORCADO');
                    $('#form_emitirGuia').find('button[type=submit]').click();
                }, function (dismiss) {
                    // dismiss can be 'cancel', 'overlay',
                    // 'close', and 'timer'
                    if (dismiss === 'cancel') {
                        $('#autorizacao').val('AUDITORIA');
                        $('#form_emitirGuia').find('button[type=submit]').click();
                    }
                });
            });

        @permission('autorizar_guia')
            $('.atualizar-guia').click(function () {
                var $action = $(this).data('action');
                var $guia = $(this).data('guia');
                var urlAutorizar = '{{ route("autorizador.autorizar") }}';
                var urlRecusar = '{{ route("autorizador.recusar") }}';

                if ($action === 'recusar') {
                    swal({
                        title: 'Deixe a justificativa da recusa.',
                        input: 'text',
                        showCancelButton: true,
                        confirmButtonText: 'Enviar',
                        showLoaderOnConfirm: true,
                        preConfirm: function (justificativa) {
                            return new Promise(function (resolve, reject) {
                                if (justificativa === '') {
                                    reject('Uma justificativa é necessária.')
                                }
                                $.ajax({
                                    url: urlRecusar,
                                    type: 'POST',
                                    data: {
                                        justificativa: justificativa,
                                        numero_guia: $guia,

                                    },
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    }
                                })
                                    .done(function () {
                                        resolve();

                                    })
                                    .fail(function (data) {
                                        reject('Houve um erro. Cheque o console.');
                                        console.log(data);
                                    });
                            });
                        },
                        allowOutsideClick: false
                    }).then(function (email) {
                        swal({
                            type: 'info',
                            title: 'Guia recusada!',
                            html: 'A guia foi recusada.'
                        }).then(function () {
                            location.reload();
                        })
                    });
                } else if ($action === 'liberar') {
                    $.ajax({
                        url: urlAutorizar,
                        type: 'POST',
                        data: {
                            numero_guia: $guia
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    })
                        .done(function () {
                            swal({
                                type: 'success',
                                title: 'Guia autorizada!',
                                html: 'A guia foi autorizada'
                            }).then(function () {
                                location.reload();
                            })
                        })
                        .fail(function (data) {
                            swal({
                                type: 'error',
                                title: 'Ocorreu um erro!'
                            });
                            console.log(data);
                        });
                } else {
                    //Quando a ação é de encaminhamento
                    $("#encaminhamento_guia_" + $guia).modal('show');
                    $('.select2_modal').each(function (k, v) {
                        $(v).select2({
                            tags: true,
                            dropdownParent: $("#" + $(v).data('parent'))
                        });
                    });
                }
            });
        @endpermission

            $('.cancelar-guia').click(function () {
                var $action = $(this).data('action');
                var $guia = $(this).data('guia');
                var urlCancelar = '{{ route("autorizador.solicitarCancelamento") }}';

                swal({
                    title: 'Deixe a justificativa para o cancelamento.',
                    input: 'text',
                    showCancelButton: true,
                    confirmButtonText: 'Solicitar',
                    showLoaderOnConfirm: true,
                    preConfirm: function (justificativa) {
                        return new Promise(function (resolve, reject) {
                            if (justificativa === '') {
                                reject('Uma justificativa é necessária.')
                            }
                            $.ajax({
                                url: urlCancelar,
                                type: 'POST',
                                data: {
                                    justificativa: justificativa,
                                    numero_guia: $guia,
                                },
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                }
                            }).done(function () {
                                resolve();
                            }).fail(function (data) {
                                reject('Houve um erro. Cheque o console.');
                                console.log(data);
                            });
                        });
                    },
                    allowOutsideClick: false
                }).then(function (email) {
                    swal({
                        type: 'warning',
                        title: 'Cancelamento solicitado!',
                        html: 'O cancelamento da guia será avaliado em breve.'
                    }).then(function () {
                        location.reload();
                    })
                });
            });

            $('.glosar-guia').click(function () {
                var guia = $(this).data('guia');
                var url = '{{ route("autorizador.glosar") }}';

                swal({
                    title: 'Deixe a justificativa para a glosa.',
                    input: 'textarea',
                    showCancelButton: true,
                    confirmButtonText: 'Glosar',
                    showLoaderOnConfirm: true,
                    preConfirm: function (justificativa) {
                        return new Promise(function (resolve, reject) {
                            if (justificativa === '') {
                                reject('Uma justificativa é necessária.')
                            }
                            $.ajax({
                                url: url,
                                type: 'POST',
                                data: {
                                    justificativa: justificativa,
                                    numero_guia: guia,
                                },
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                }
                            }).done(function () {
                                resolve();
                            }).fail(function (data) {
                                reject('Houve um erro. Cheque o console.');
                                console.log(data);
                            });
                        });
                    },
                    allowOutsideClick: false
                }).then(function (email) {
                    swal({
                        type: 'success',
                        title: 'Sucesso!',
                        html: 'A Guia foi glosada com sucesso.'
                    }).then(function () {
                        location.reload();
                    })
                });
            });

            $('.reverter-glosa').click(function () {
                var numero_guia = $(this).data('guia');

                swal({
                    title: 'Esta guia já foi revertida de uma glosa.',
                    text: 'Deseja reverter a glosa?',
                    type: 'warning',
                    showCancelButton: true,
                    cancelButtonColor: '#d33',
                    cancelButtonText: 'Não',
                    cancelButtonClass: 'btn bg-red',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Sim',
                    confirmButtonClass: 'btn bg-green-meadow',
                    reverseButtons: true,
                    buttonsStyling: true
                }).then(function () {
                    $.post('{{ route('autorizador.reverterGlosa') }}', {
                        numero_guia: numero_guia,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    }, function (response) {
                        swal({
                            type: 'success',
                            title: 'A glosa foi revertida com sucesso!'
                        }).then(function () {
                            location.reload();
                        })
                    });
                });
            });
        });

    </script>
@endsection

@section('scripts')
    @parent

    {{--<script src="{{ url('/') }}/assets/global/scripts/datatable.js" type="text/javascript"></script>--}}
    <script src="{{ url('/') }}/assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
    <script src="{{ url('/') }}/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js"
            type="text/javascript"></script>
@endsection