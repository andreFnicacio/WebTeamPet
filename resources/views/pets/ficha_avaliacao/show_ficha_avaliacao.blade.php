@extends('layouts.app')

@section('title')
    @parent
    Pets - Ficha de Avaliação
@endsection

@section('css')
    <style>
        .assinatura-digital {
            overflow: hidden;
            height: 60px;
            margin-top: 30px;
        }
        .assinatura-digital * {
            color: #26c281;
        }
        .assinatura-digital .icone-assinatura {
            float:left;
        }
        .assinatura-digital .icone-assinatura i {
            padding: 10px;
            background-color: transparent;
            display: block !important;
            margin: 10px auto;
            font-size: 40px;
        }
        .assinatura-digital .dados-assinatura {
            float:left;
        }
        .assinatura-digital .dados-assinatura h5 {
            margin: 0 0 5px;
        }
        #senha_cliente {
            -webkit-text-security: disc;
            -moz-text-security:circle;
            text-security:circle;
        }
        #senha_cliente::-webkit-inner-spin-button,
        #senha_cliente::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
    </style>
@endsection

@section('content')
    <!-- BEGIN VALIDATION STATES-->
    <div class="portlet light portlet-fit portlet-form ">
        <div class="portlet-title">
            <div class="caption">
                <i class="icon-settings font-green-jungle"></i>
                <span class="caption-subject font-green-jungle sbold uppercase">
                    Ficha de Avaliação
                </span>
            </div>
        </div>
        <div class="portlet-body">
            <form id="form-ficha" method="post" class="form-horizontal" role="form">
                <div class="form-body">

                    <div class="row">
                        <div class="col-xs-12 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">

                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="portlet blue box">
                                        <div class="portlet-title">
                                            <div class="caption">
                                                <i class="fa fa-paw"></i>
                                                Dados do Pet
                                            </div>
                                        </div>
                                        <div class="portlet-body">
                                            <div class="row">
                                                <div class="col-xs-12 col-sm-6 col-md-4">
                                                    <label class="bold">Pet:</label>
                                                    <p>{!! $ficha->pet->nome_pet !!}</p>
                                                </div>
                                                <div class="col-xs-12 col-sm-6 col-md-4">
                                                    <label class="bold">Espécie:</label>
                                                    <p>{!! $ficha->pet->tipo == "CACHORRO" ? "Canina" : "Felina" !!}</p>
                                                </div>
                                                <div class="col-xs-12 col-sm-6 col-md-4">
                                                    <label class="bold">Raça:</label>
                                                    <p>{!! $ficha->pet->raca->nome !!}</p>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-xs-12 col-sm-6 col-md-4">
                                                    <label class="bold">Tutor:</label>
                                                    <p>{!! $ficha->pet->cliente->nome_cliente !!}</p>
                                                </div>
                                                <div class="col-xs-12 col-sm-6 col-md-4">
                                                    <label class="bold">Nascimento:</label>
                                                    <p>{!! $ficha->pet->data_nascimento->format('d/m/Y') !!}</p>
                                                </div>
                                                <div class="col-xs-12 col-sm-6 col-md-4">
                                                    <label class="bold">Data:</label>
                                                    <p>{!! (new \Carbon\Carbon())->today()->format('d/m/Y') !!}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="portlet blue box">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class="fa fa-info-circle"></i>
                                        Informações Adicionais
                                    </div>
                                </div>
                                <div class="portlet-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="bold">Porte <span class="font-red">*</span></label>
                                            <p>{{ $ficha->porte }}</p>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="bold">Pelagem <span class="font-red">*</span></label>
                                            <p>{{ $ficha->pelagem }}</p>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="bold">Microchip <span class="font-red">*</span></label>
                                            <p>{{ $ficha->numero_microchip }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="portlet blue box">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class="fa fa-info-circle"></i>
                                        Anamnese
                                    </div>
                                </div>
                                <div class="portlet-body">

                                    @php
                                        $j = 0;
                                    @endphp
                                    @foreach($categorias as $categoria)

                                        <h5 class="margin-bottom-40"><strong>{!! $categoria !!}</strong></h5>

                                        @foreach($respostas[$categoria] as $resposta)
                                            <div class="form-group">
                                                <div class="col-xs-12">
                                                    <label>{!! $resposta->pergunta->nome_pergunta !!}</label>
                                                    <div class="row">
                                                        <div class="col-xs-12 col-sm-3">
                                                            <div class="mt-radio-inline">
                                                                <label class="mt-radio">
                                                                    <input type="radio" value="1" {{ $resposta->resposta == 1 ? 'checked' : '' }} disabled> Sim
                                                                    <span></span>
                                                                </label>
                                                                <label class="mt-radio">
                                                                    <input type="radio" value="0" {{ $resposta->resposta == 0 ? 'checked' : '' }} disabled> Não
                                                                    <span></span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-12 col-sm-9">
                                                            <div class="form-group form-md-line-input ">
                                                                <input type="text" class="form-control" value="{{ $resposta->descricao }}" disabled>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        @endforeach

                                        @php
                                            $j++;
                                        @endphp

                                        @if($j < count($categorias))
                                            <hr>
                                        @endif

                                    @endforeach
                                </div>
                            </div>

                            <div class="portlet blue box">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class="fa fa-pencil"></i>
                                        Assinaturas Eletrônicas
                                    </div>
                                </div>
                                <div class="portlet-body">
                                    <div class="row">
                                        <div class="col-xs-12 col-md-6">
                                            <div class="assinatura-digital">
                                                <div class="icone-assinatura">
                                                    <i class="icon-check"></i>
                                                </div>
                                                <div class="dados-assinatura">
                                                    <h5 class="bold">Prestador</h5>
                                                    <h5>10/10/2010</h5>
                                                    <h5>ffb89639179a011bc7e8dc500f67d816</h5>
                                                </div>
                                            </div>
                                            <hr class="border-blue-dark  margin-bottom-10 margin-top-10">
                                            <p class="bold text-center margin-bottom-10">Prestador</p>
                                        </div>
                                        <div class="col-xs-12 col-md-6">
                                            <div class="assinatura-digital">
                                                <button class="btn btn-lg blue center-block margin-top-10" data-toggle="modal" data-target="#modal-cliente-assinatura">Assinar</button>
                                            </div>
                                            <hr class="border-blue-dark  margin-bottom-10 margin-top-10">
                                            <p class="bold text-center margin-bottom-10">Cliente</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="modal-cliente-assinatura">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="text-center">Assinatura Eletrônica</h3>
                </div>
                <div class="modal-body">
                    <form action="{{ route('autorizador.assinarCliente') }}" method="POST" id="form-assinarCliente">
                        {{ csrf_field() }}
                        <input type="hidden" name="id_ficha" value="{{ $ficha->id }}}">
                        <div class="row">
                            <div class="col-md-12">
                                <h3 class="text-center">SENHA</h3>
                            </div>
                            <div class="col-md-6 col-md-offset-3">
                                <input class="form-control text-center" name="senha_plano" id="senha_cliente" type="text" autocomplete="off" placeholder="Senha de 4 dígitos" required>
                            </div>
                        </div>
                        <button class="btn btn-lg blue center-block margin-top-20" disabled id="btn_assinar_cliente">Assinar</button>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-prestador-assinatura">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="text-center">Assinatura Eletrônica</h3>
                </div>
                <div class="modal-body">
                    <form action="{{ route('autorizador.assinarPrestador') }}" method="POST" id="form-assinarPrestador">
                        {{ csrf_field() }}
                        <input type="hidden" name="id_ficha" value="{{ $ficha->id }}}">
                        <div class="row">
                            <div class="col-md-12">
                                <h3 class="text-center">CRMV</h3>
                            </div>
                            <div class="col-md-6 col-md-offset-3">
                                <input class="form-control text-center" name="senha_prestador" id="senha_prestador" type="text" placeholder="Ex.: 1234ES">
                                <small class="helper">Apenas números e as duas letras do estado</small>
                            </div>
                        </div>
                        <button class="btn btn-lg blue center-block margin-top-20" id="btn_assinar_prestador">Assinar</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
    <!-- END VALIDATION STATES-->
@endsection

@section('scripts')
    <script>
        $('#form-ficha').on('submit', function (e) {
            e.preventDefault();
            return false;
        });
    </script>
    <script>
        $(document).ready(function () {

            $("#modal-cliente-assinatura #senha_cliente").mask("NNNN", {
                translation: {
                    'N': {pattern: /[0-9]/},
                }
            });

            $('#modal-cliente-assinatura').on('show.bs.modal', function (e) {
                setTimeout(function() { $('#modal-cliente-assinatura #senha_cliente').focus() }, 200);
            });
            $('#modal-cliente-assinatura').on('hide.bs.modal', function (e) {
                $('#modal-cliente-assinatura #senha_cliente').removeClass('focus').val('');
            });

            $('#modal-cliente-assinatura #senha_cliente').on('keyup', function(event) {
                var length = $(this).val().length;
                $('#btn_assinar_cliente').prop('disabled', true);
                if (length == 4) {
                    $('#btn_assinar_cliente').prop('disabled', false);
                }
            });
        });
    </script>
@endsection
