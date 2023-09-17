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
    Guias Glosadas
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
                        <span class="caption-subject bold uppercase">Guias Glosadas</span>
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
                                <td> {{ $guia->numero_guia }} </td>
                                <td>
                                    @php
                                        $pet = $guia->pet()->first();
                                    @endphp
                                    @if(Entrust::can('edit_pets'))
                                        <a href="{{ route('pets.edit', $pet->id) }}"
                                           target="_blank"> {{ $pet->nome_pet }} </a>
                                    @else
                                        <span>{{ $pet->nome_pet }}</span>
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
                                    <button class="btn btn-default bg-blue font-white"
                                            href="#glosa_{{ $guia->numero_guia }}" data-toggle="modal">
                                        Ver Glosa
                                    </button>
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
        <div id="glosa_{{ $guia->numero_guia }}" class="modal fade" tabindex="-1" data-replace="true"
             style="display: none;">
            <div class="modal-dialog">
                @php
                    $glosa = \Modules\Guides\Entities\GuiaGlosa::where('id_historico_uso', $guia->id)->first();
                @endphp

                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">Glosa da Guia #{{ $guia->numero_guia }}</h4>
                    </div>
                    <div class="modal-body">
                        <div class="note note-success">
                            <small class="badge badge-info"
                                   style="float: right;">{{ Carbon\Carbon::parse($glosa->created_at)->format('d/m/Y H:i') }}</small>
                            <h4 class="bold">Justificativa da glosa: </h4>
                            <p> {{ $glosa->justificativa }} </p>
                        </div>
                        <div class="note note-warning">
                            @if($glosa->defesa)

                                <small class="badge badge-info"
                                       style="float: right;">{{ Carbon\Carbon::parse($glosa->data_defesa)->format('d/m/Y H:i') }}</small>
                                <h4 class="bold ">Defesa: </h4>
                                <p>{{ $glosa->defesa }}</p>
                                @if($glosa->getArquivo())
                                    <a href="{{ url('/') }}/{{ $glosa->getArquivo()->path }}"
                                       class="btn blue margin-top-10" type="download"
                                       download="{{ $glosa->getArquivo()->original_name }}">
                                        <i class="fa fa-paperclip"></i> Baixar Arquivo
                                    </a>
                                @endif

                            @else

                                @role(['AUTORIZADOR', 'ADMINISTRADOR', 'AUDITORIA'])
                                <h4 class="bold">Ainda não há defesa para esta glosa.</h4>
                                @endrole

                                @php
                                    $created = new \Carbon\Carbon($glosa->created_at);
                                    $diff = $created->diff(\Carbon\Carbon::now())->days;
                                @endphp

                                @if($diff <= 30)
                                    @role('CLINICAS')
                                    <form action="{{ route("autorizador.defenderGlosa") }}" method="post"
                                          enctype="multipart/form-data" id="defesa-form-{{ $glosa->id }}">
                                        {{ csrf_field() }}
                                        <h4 class="bold">Ainda não há defesa para esta glosa.</h4>
                                        <input type="hidden" name="id" value="{{ $glosa->id }}">
                                        <div class="form-group">
                                            <textarea class="form-control custom-control" name="defesa" rows="3"
                                                      style="resize:none"
                                                      placeholder="Insira uma defesa para esta glosa..."></textarea>
                                        </div>
                                        <div class="form-group">
                                            <input type="file" name="arquivo_defesa"
                                                   class="form-control arquivo-defesa">
                                        </div>
                                    </form>
                                    @endrole
                                @else
                                    <h4 class="bold">O tempo de defesa expirou.</h4>
                                @endif

                            @endif
                        </div>

                        @if($glosa->justificativa_confirmacao)
                            <div class="note note-success">
                                <small class="badge badge-info"
                                       style="float: right;">{{ Carbon\Carbon::parse($glosa->data_confirmacao)->format('d/m/Y H:i') }}</small>
                                <h4 class="bold">Justificativa da confirmação: </h4>
                                <p> {{ $glosa->justificativa_confirmacao }} </p>
                            </div>
                        @endif
                        <hr>

                        @if($glosa->defesa)
                            @role(['AUTORIZADOR', 'ADMINISTRADOR', 'AUDITORIA'])
                            <div class="center-block">
                                <h4 class="text-center">O que deseja fazer?</h4>
                                <div class="text-center">
                                    @if(!$glosa->justificativa_confirmacao)
                                        <button class="btn blue btn-primary tooltips confirmar-glosa"
                                                data-dismiss="modal"
                                                data-guia="{{ $guia->numero_guia }}"
                                                data-glosa="{{ $glosa->id }}">
                                            <i class="fa fa-check"></i> Confirmar
                                        </button>
                                    @endif
                                    <button class="btn green-meadow btn-primary tooltips reverter-glosa"
                                            data-dismiss="modal"
                                            data-guia="{{ $guia->numero_guia }}">
                                        <i class="fa fa-undo"></i> Reverter
                                    </button>
                                </div>
                            </div>
                            @endrole
                            @role('CLINICAS')
                            @if($glosa->justificativa_confirmacao)
                                <h4 class="text-center">A glosa desta guia foi confirmada.</h4>
                            @else
                                <h4 class="text-center">A defesa está sob análise, aguarde...</h4>
                            @endif
                            @endrole
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" data-dismiss="modal" class="btn dark btn-outline">Fechar</button>
                        @if(!$glosa->defesa)
                            @role('CLINICAS')
                            <button class="btn blue btn-primary" form="defesa-form-{{ $glosa->id }}">Enviar</button>
                            @endrole
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection

@section('scripts')
    @parent
    <script>
        $(document).ready(function () {
            $('.reverter-glosa').click(function () {
                var numero_guia = $(this).data('guia');

                swal({
                    title: 'Deseja reverter a glosa?',
                    type: 'question',
                    showCancelButton: true,
                    cancelButtonText: 'Não',
                    confirmButtonText: 'Sim',
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
            $('.confirmar-glosa').click(function () {
                var numero_guia = $(this).data('guia');
                var id_glosa = $(this).data('glosa');

                swal({
                    title: 'Deseja confirmar a glosa?',
                    type: 'question',
                    input: 'textarea',
                    showCancelButton: true,
                    cancelButtonText: 'Não',
                    confirmButtonText: 'Sim',
                    buttonsStyling: true,
                    preConfirm: function (justificativa_confirmacao) {
                        return new Promise(function (resolve, reject) {
                            if (justificativa_confirmacao === '') {
                                reject('Uma justificativa é necessária.')
                            }
                            $.ajax({
                                url: '{{ route('autorizador.confirmarGlosa') }}',
                                type: 'POST',
                                data: {
                                    justificativa_confirmacao: justificativa_confirmacao,
                                    numero_guia: numero_guia,
                                    id_glosa: id_glosa,
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
                }).then(function () {
                    swal({
                        type: 'success',
                        title: 'A glosa foi confirmada com sucesso!'
                    }).then(function () {
                        location.reload();
                    })
                });
            });
        });

    </script>
    <script src="{{ url('/') }}/assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
    <script src="{{ url('/') }}/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js"
            type="text/javascript"></script>
@endsection