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
    <link href="{{ url('/') }}/assets/pages/css/search.min.css" rel="stylesheet" type="text/css"/>
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

    <section class="content-header text-center">
        <h1 class="title">Buscar Guia</h1>
    </section>

    {{-- <div class="row">

       <div class="col-md-12"> --}}
    <div class="search-page search-content-3">
        <div class="row">
            <div class="col-lg-4">
                <div class="search-filter" style="padding-bottom: 10px;">

                    <div class="search-label uppercase">Número da Guia</div>

                    <form action="" method="GET">
                        {{-- {{ csrf_field() }} --}}
                        <div class="input-icon right">
                            <i class="icon-magnifier"></i>
                            <input type="text" name="numero_guia" class="form-control"
                                   value="{{ $numero_guia ? $numero_guia : '' }}" placeholder="Número da Guia" required>
                        </div>

                        <button class="btn btn-lg green bold uppercase btn-block margin-bottom-15">Buscar</button>
                    </form>

                </div>
            </div>
            <div class="col-lg-8">
                <div class="portlet light">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-tag font-blue"></i>
                            <span class="caption-subject bold font-blue uppercase"> Guia </span>
                        </div>
                    </div>
                    <div class="portlet-body">
                        @if($guia)
                            <form action="{{ route('autorizador.realizarEncaminhamento') }}" id="realizarEncaminhamento"
                                  class="form" method="POST">
                                {{ csrf_field() }}
                                <input type="hidden" name="id_clinica" value="{{ $clinica->id }}" required>
                                <input type="hidden" name="numero_guia" value="{{ $guia->numero_guia }}" required>
                                <h4><strong>Guia #{{ $guia->numero_guia }}</strong></h4>
                                <br>
                                <p><strong>Encaminhado por</strong>: {{ $guia->clinica->nome_clinica }}</p>
                                <p><strong>Pet</strong>: {{ $guia->pet->nome_pet }}</p>
                                <p><strong>Tutor</strong>: {{ $guia->pet->cliente->nome_cliente }}</p>
                                <p>
                                    <strong>Liberação</strong>: {{ $guia->data_liberacao ? $guia->data_liberacao->format('d/m/Y H:i') : 'Esta guia não foi liberada para realização ainda!' }}
                                </p>
                                <p><strong>Procedimentos</strong>:</p>
                                <ul>
                                    @foreach($procedimentos as $proc)
                                        <li>{{ $proc->nome_procedimento }}</li>
                                    @endforeach
                                </ul>
                                <div class="form-group margin-top-40 margin-bottom-40">
                                    <div class="row">
                                        <div class="col-sm-6 col-sm-offset-3">
                                            <label for="prestadores">Qual Prestador irá realizar este(s)
                                                procedimento(s)?</label>
                                            <select name="id_prestador" id="prestadores" class="form-control select2"
                                                    required>
                                                <option value=""></option>
                                                @foreach(\Modules\Veterinaries\Entities\Prestadores::all() as $prestador)
                                                    <option value="{{ $prestador->id }}">{{ $prestador->nome }}
                                                        - {{ $prestador->getCRMV() }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            @if($guia->data_liberacao <= (new \Carbon\Carbon()))
                                <button class="btn btn-lg green-meadow center-block margin-top-20"
                                        style="min-width: 150px;" id="btnRealizarEncaminhamento">Realizar esta guia!
                                </button>
                            @else
                                <div data-toggle="tooltip" title="Verifique a data e a hora de liberação">
                                    <button class="btn btn-lg disabled center-block margin-top-20"
                                            style="min-width: 150px">Guia aguardando liberação
                                    </button>
                                </div>
                            @endif
                        @else
                            <h3 class="text-center">Busque uma guia de encaminhamento através do número informado pelo
                                tutor</h3>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- </div>
</div> --}}
@endsection

@section('scripts')
    @parent

    <script>
        $(document).ready(function () {
            $('#btnRealizarEncaminhamento').click(function (e) {
                if (!$('input[name="id_clinica"]').val()) {
                    swal('Erro!', 'Esta guia não pode ser realizada!', 'error');
                    return false;
                } else if (!$('select[name="id_prestador"]').val()) {
                    swal('Atenção!', 'Selecione um prestador!', 'warning');
                    return false;
                } else {
                    swal({
                        title: 'Atenção!',
                        text: "Ao vincular uma guia, você estará assumindo que o atendimento foi realizado por você na data de hoje. Não se esqueça de imprimir e coletar a assinatura do cliente.",
                        type: 'warning',
                        showCancelButton: true,
                        cancelButtonText: 'Cancelar',
                        confirmButtonText: 'Sim, desejo realizar!'
                    }).then((result) => {
                        $('#realizarEncaminhamento').submit();
                    })
                }
                return true;
            });
        });
    </script>

    {{--<script src="{{ url('/') }}/assets/global/scripts/datatable.js" type="text/javascript"></script>--}}
    <script src="{{ url('/') }}/assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
    <script src="{{ url('/') }}/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js"
            type="text/javascript"></script>
@endsection
