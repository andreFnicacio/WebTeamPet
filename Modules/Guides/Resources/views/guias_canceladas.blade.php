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
                        <span class="caption-subject bold uppercase">Guias à cancelar</span>
                    </div>

                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <form action="{{ route(Route::getCurrentRoute()->getName()) }}" method="GET" class="form">
                                <div class="row">
                                    <div class="col-sm-4">
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
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <h4 class="filter-label">Termo</h4>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="input-group input-large">
                                                        <input type="text" class="form-control" name="termo"
                                                               value="{{ $params['termo'] }}">
                                                        <span class="input-group-btn">
                                                            <button class="btn btn-secondary flat" type="submit">
                                                                <i class="fa fa-search"></i>
                                                            </button>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
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
                    <table class="table table-hover table-checkable order-column datatables-guias responsive table-responsive">
                        <thead>
                        <tr>
                            {{--<th>--}}
                            {{--<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">--}}
                            {{--<input type="checkbox" class="group-checkable" data-set="#sample_1 .checkboxes" />--}}
                            {{--<span></span>--}}
                            {{--</label>--}}
                            {{--</th>--}}
                            <th> Guia</th>
                            <th width="20%"> Pet</th>
                            <th width="20%"> Clínica</th>
                            <th width="2%"> Status</th>
                            <th width="2%"> Tipo</th>
                            <th width="2%">
                                @if(\Entrust::hasRole(['ADMINISTRADOR', 'AUDITORIA']))
                                    Aut.
                                @endif
                            </th>
                            <th> Data da Solicitação</th>
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
                                <td> {{ $guia->numero_guia }} </td>
                                <td>
                                    @php
                                        $pet = $guia->pet()->first();
                                    @endphp
                                    @if(Entrust::can('edit_pets'))
                                        <a href="{{ route('pets.edit', $pet->id) }}"
                                           target="_blank"> {{ $pet->nome_pet }} </a>
                                    @else
                                        <span>{{ $pet->nome_pet }}<span>
                                    @endif
                                </td>
                                <td class="text-uppercase">
                                    @if(isset($clinica))
                                        <span>{{ $clinica->nome_clinica }}</span>
                                    @else
                                        <span>{{ $guia->clinica()->first()->nome_clinica }}</span>
                                    @endif

                                </td>
                                <td class="text-center">
                                    @if($guia->status === 'LIBERADO')
                                        @if(!$guia->realizado_em)
                                            <span class="label label-sm bg-green-meadow" data-toggle="tooltip"
                                                  title="AUTORIZADO"> AUT </span>
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
                                <td class="guia_acoes">
                                    <div class="btn-group">
                                        <button class="btn btn-xs green dropdown-toggle" type="button"
                                                data-toggle="dropdown" aria-expanded="false"> Ações
                                            <i class="fa fa-angle-down"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-acoes-guia pull-left" role="menu">

                                            <li>
                                                @if($guia->status === 'LIBERADO' || Entrust::hasRole(['AUTORIZADOR', 'ADMINISTRADOR', 'AUDITORIA']))
                                                    <a target="_blank"
                                                       href="{{ route('autorizador.verGuia', $guia->numero_guia) }}"
                                                    >
                                                        <i class="icon-tag"></i> Ver guia
                                                    </a>
                                                @endif
                                            </li>
                                            <li>
                                                @if(!empty($guia->laudo))
                                                    <a href="#laudo_guia_{{ $guia->numero_guia }}" data-toggle="modal">
                                                        <i class="icon-user"></i> Ver laudo
                                                    </a>

                                                @endif
                                            </li>
                                            @permission('autorizar_guia')
                                            <li class="divider"></li>
                                            <li>
                                                <a href="javascript:;" class="atualizar-guia"
                                                   data-guia="{{ $guia->numero_guia }}" data-action="recusar">
                                                    <i class="fa fa-thumbs-down "></i> Recusar
                                                </a>
                                            </li>
                                            @endpermission
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
                <form action="{{ route('autorizador.adicionarLaudo') }}" method="POST">
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
                                <hr>Justificativa: </hr>
                                <p>
                                    {{ $guia->justificativa }}
                                </p>
                            @endif
                            @if(!empty($guia->cancelamento) && \Entrust::hasRole(['ADMINISTRADOR', 'AUDITORIA']))
                                <hr> Solicitação de Cancelamento:
                                <p>
                                    {{ $guia->cancelamento }}
                                </p>
                            @endif

                        </div>
                        <div class="modal-footer">
                            <button type="button" data-dismiss="modal" class="btn dark btn-outline">Fechar</button>
                        </div>
                    </div>
                </form>
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