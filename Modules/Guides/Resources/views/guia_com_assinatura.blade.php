<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Guia de solicitação - {{ $guia->numero_guia }}</title>

    <!-- Normalize or reset CSS with your favorite library -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/3.0.3/normalize.css">

    <!-- Load paper.css for happy printing -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paper-css/0.2.3/paper.css">

    <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet"
          type="text/css"/>
    <link href="{{ url('/') }}/assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="{{ url('/') }}/assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="{{ url('/') }}/assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="{{ url('/') }}/assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet"
          type="text/css"/>
    <!-- END GLOBAL MANDATORY STYLES -->
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <link href="{{ url('/') }}/assets/global/css/components-md.min.css" rel="stylesheet" id="style_components"
          type="text/css"/>
    <link href="{{ url('/') }}/assets/global/css/plugins-md.min.css" rel="stylesheet" type="text/css"/>

    <!-- END PAGE LEVEL PLUGINS -->
    <!-- BEGIN THEME GLOBAL STYLES -->
    <link href="{{ url('/') }}/assets/global/css/components-md.min.css" rel="stylesheet" id="style_components"
          type="text/css"/>
    <link href="{{ url('/') }}/assets/global/css/plugins-md.min.css" rel="stylesheet" type="text/css"/>
    <!-- END THEME GLOBAL STYLES -->
    <!-- BEGIN THEME LAYOUT STYLES -->
    <link href="{{ url('/') }}/assets/layouts/layout2/css/layout.min.css" rel="stylesheet" type="text/css"/>
    <link href="{{ url('/') }}/assets/layouts/layout2/css/themes/light.min.css" rel="stylesheet" type="text/css"
          id="style_color"/>
    <link href="{{ url('/') }}/assets/layouts/layout2/css/custom.min.css" rel="stylesheet" type="text/css"/>


    <!-- Set page size here: A5, A4 or A3 -->
    <!-- Set also "landscape" if you need -->
    <style>
        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
            }

            body {
                -webkit-print-color-adjust: exact !important;
            }
        }

        @page {
            size: A4 landscape;
            -webkit-print-color-adjust: exact;
        }

        .a25 {
            width: 25%;
        }

        .a33 {
            width: 33%;
        }

        .a40 {
            width: 40%;
        }

        .a65 {
            width: 65%;
        }

        .a60 {
            width: 60%;
        }

        .a50 {
            width: 50%;
        }

        .a20 {
            width: 20%;
        }

        .a100 {
            width: 100%;
        }

        th.guia-sem-valor {
            text-align: center;
            font-size: 22px;
            padding-top: 25px;
            padding-bottom: 25px;
            background: #ededed;
        }
    </style>

    <style>
        /* .input-ghost {
            background-color: transparent;
            border-color: transparent;
            color: transparent;
            -webkit-appearance: none;
        }

        .input-wrapper {
            display: inline-block;
            position: relative;
        }

        .input-wrapper input {
            margin: 0;
        }

        .input-digits-wrapper {
            height: 35px;
        }

        .input-digits-wrapper input:focus {
            outline: none;
        }

        .input-digits-wrapper .input-digits {
            height: 100%;
            position: absolute;
            width: 100%;
        }

        .input-digits-wrapper .input-digit {
            height: 100%;
            margin-right: 10px;
            text-align: center;
            width: 50px;
        }

        .input-digits-wrapper .input-digit.focus {
            border-color: blue;
        }

        .input-digits-wrapper .input-digit:last-of-type {
            margin-right: 0;
        } */
    </style>

    <style type="text/css">
        -webkit-print-color-adjust: exact

        ;
        .tg {
            border-collapse: collapse;
            border-spacing: 0;
        }

        .tg .tg-yw4l {
            vertical-align: top
        }

        p {
            font-size: 14px !important;
            font-weight: 200;
        }

        .backcinza {
            background-color: #ededed;
            font-size: 20px;
            border: 1px solid #777;
            padding: 10px;
        }

        i.icone-guia {
            padding: 10px;
            background-color: #ededed;
            border-radius: 50px;
            display: block;
            margin-right: 5px;
        }

        /* i.sign{padding: 10px;color: #26c281;background-color: transparent;display: block !important;margin: 0 auto;} */

        .assinatura-digital * {
            color: #26c281;
        }

        .assinatura-digital .icone-assinatura {
            float: left;
        }

        .assinatura-digital .icone-assinatura i {
            padding: 10px;
            background-color: transparent;
            display: block !important;
            margin: 10px auto;
            font-size: 40px;
        }

        .assinatura-digital .dados-assinatura {
            float: left;
        }

        .assinatura-digital .dados-assinatura h5 {
            margin: 0 0 5px;
        }

        .swal2-container {
            z-index: 999999 !important;
        }

        #senha_cliente {
            -webkit-text-security: disc;
            -moz-text-security: circle;
            text-security: circle;
        }

        #senha_cliente::-webkit-inner-spin-button,
        #senha_cliente::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
    </style>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css"/>

</head>

<!-- Set "A5", "A4" or "A3" for class name -->
<!-- Set also "landscape" if you need -->
<body class="A4 landscape login">

@include('common.swal')

<!-- Each sheet element should have the class "sheet" -->
<!-- "padding-**mm" is optional: you can set 10, 15, 20 or 25 -->
@if(count($historicos) == 0)
    <br>
    <br>
    <h1 class="text-center text-white">Guia inexistente ou não pertencente à sua clínica.</h1>
@else
    <section class="sheet" style=" margin:0 auto; padding: 9mm;">

        <!-- Write HTML just like a web page -->
        <div style="border: 1px solid #666;  padding: 2mm 10mm; height: 100%;">
            <div class="topolife row">
                <table class="" style="width: 100%;" border="0">
                    <tr style="border-bottom: 1px solid #c1c1c1;">
                        <th class="a20"><img class="login-logo login-6"
                                             src="{{ url('/') }}/assets/pages/img/logo-big-white.png"
                                             style="width: 120px;"/></th>
                        <th class="a60"><h4 style="font-weight: 700; text-align: center;"><small>GUIA LIFEPET</small>
                                <br/>SERVIÇO PROFISSIONAL / EXAMES / INTERNAÇÃO</h4></th>
                        <th class="a20" style="text-align: right;  padding-bottom: 35px !important;">
                            <h4 style="padding: 0px; margin-bottom:-5px;">Guia:</h4><br/>
                            <span class="backcinza">{{ $guia->numero_guia }}</span>
                        </th>
                    </tr>
                </table>

                <table class="" style="width: 100%;" border="0">
                    <tr style="border-bottom: 1px solid #c1c1c1;">
                        <th class="a20">
                            <p>
                                <i class="icone-guia fa fa-calendar"></i>

                                @if($guia->tipo_atendimento === \Modules\Guides\Entities\HistoricoUso::TIPO_ENCAMINHAMENTO)
                                    <b>Realizado
                                        em:</b> {{ $guia->realizado_em ? $guia->realizado_em->format('d/m/Y H:i:s') : 'NÃO REALIZADO' }}
                                @else
                                    <b>Emitido em:</b> {{ $guia->created_at->format('d/m/Y H:i:s')  }}
                                @endif
                            </p>
                        </th>
                        <th class="a40">
                            <h4 style="font-weight: 700; text-align: left;">
                                <p>
                                    <i class="icone-guia fa fa-paw"></i>
                                    <b> Pet:</b> {{ $guia->id_pet . " - " . $guia->pet()->first()->nome_pet  }}
                                </p>
                            </h4>
                        </th>
                        <th class="a40">
                            <p>
                                <i class="icone-guia fa fa-user"></i><b>Tutor:</b> {{ $guia->pet()->first()->cliente()->first()->nome_cliente }}
                            </p>
                        </th>

                    </tr>
                </table>

                <h3 style="margin-bottom: 0px;">Dados do solicitante:</h3>
                <table class="" style="width: 100%;" border="0">
                    <tr style="">
                        @php
                            $prestador = $guia->prestador()->first();
                            $prestador = $prestador ? $prestador : new \Modules\Veterinaries\Entities\Prestadores();
                        @endphp
                        <th class="a33"><p><i
                                        class="icone-guia fa fa-user-md"></i><b>Veterinário: </b>Dr(a) {{ $prestador->nome }}
                            </p></th>
                        <th class="a20">
                            <h4 style="font-weight: 700; text-align: left;"><p><i
                                            class="icone-guia fa fa fa-stethoscope"></i><b>
                                        CRMV:</b>{{ $prestador->crmv }} </p>
                        </th>
                        <th class="a40">
                            <p>
                                <i class="icone-guia fa fa fa-hospital-o "></i><b>Credenciado:</b> {{ $guia->clinica()->first()->nome_clinica }}
                            </p>
                        </th>
                    </tr>
                </table>

                <h3 style="margin-bottom: 10px; margin-top: 0px;">Procedimentos solicitados:</h3>
                <table class="" style="width: 100%;  border-radius:10px !important;" border="0">
                    <tr style="">
                        <th style="width: 70%; padding: 20px 25px; vertical-align: top; font-size: 12px; border: 1px solid #e1e1e1; height: 200px; ">
                            <table class="" style="width: 100%; font-size: 10px;" border="0">
                                @foreach($historicos as $historico)
                                    <tr style="">
                                        <th class="a100">{{ $historico->procedimento()->first()->id }}
                                            - {{ $historico->procedimento()->first()->nome_procedimento }}</th>
                                    </tr>
                                @endforeach
                            </table>
                        </th>
                        <th style="width: 70%; padding: 20px 25px; vertical-align: top; font-size: 12px; border: 1px solid #e1e1e1; height: 200px; ">
                            <table class="" style="width: 100%;" border="0">
                                <tr style="">
                                    <th class="a100">
                                        <h4>Observações:</h4>
                                        <p>{{ $guia->observacao }}</p>
                                    </th>
                                </tr>
                            </table>
                        </th>
                    </tr>
                </table>


                <table class="" style="width: 100%;  border-radius:10px !important;" border="0">
                    <tr>
                        @if(!$guia->semValor())
                            <th class="a33"><h3 style="margin-bottom: 0;margin-top: 10px;">Outros dados:</h3></th>

                            @php
                                $numero_guia = $guia->numero_guia;
                                $procedimentosInternacao = \App\Models\Procedimentos::whereIn('id_grupo', ['20100','99914','99917','99920'])->get()->pluck('id');
                                $internacao = \Modules\Guides\Entities\HistoricoUso::where('numero_guia', $numero_guia)->whereIn('id_procedimento', $procedimentosInternacao)->get();
                            @endphp
                            <th class="a33">Houve internação? <b
                                        style="font-weight: 700; text-decoration:underline;">{{ (count($internacao) > 0) ? "Sim" : "Não" }}</b>
                            </th>

                            @if(count($internacao) > 0)
                                <th class="a33">Dia de internações: <b
                                            style="font-weight: 700; text-decoration:underline;">{{ count($internacao) }}</b>
                                </th>
                            @endif

                            @if($guia->especialidade()->first())
                                <th class="a33">Especialidade: <b
                                            style="font-weight: 700; text-decoration:underline;">{{ $guia->especialidade()->first()->nome }}</b>
                                </th>
                            @endif

                        @else
                            <th class="guia-sem-valor"><b>GUIA DE VERIFICAÇÃO. SEM VALOR.</b></th>
                        @endif
                    </tr>
                </table>

                @if(!$guia->semValor())
                    <table class="" style="width: 100%;margin-top: 30px;border-radius:10px !important;" border="0">
                        <tbody>
                        <tr>
                            <td class="a33 cell-assinar" style="margin-right: 25px !important;width: 45%;float: left;">
                                @if($guia->assinatura_prestador)
                                    <div class="assinatura-digital">
                                        <div class="icone-assinatura">
                                            <i class="icon-check"></i>
                                        </div>
                                        <div class="dados-assinatura">
                                            <h5><strong>Dr(a) {{ $prestador->nome }}</strong></h5>
                                            <h5>{{ $guia->data_assinatura_prestador->format('d/m/Y') }}</h5>
                                            <h5>{{ $guia->assinatura_prestador }}</h5>
                                        </div>
                                    </div>
                                @else
                                    <button class="btn btn-lg blue center-block margin-bottom-10" data-toggle="modal"
                                            data-target="#modal-prestador-assinatura">Assinar
                                    </button>
                                @endif
                            </td>
                            <td class="a33 cell-assinar" style="margin-right: 25px !important;width: 45%;float: left;">
                                @if($guia->assinatura_cliente)
                                    <div class="assinatura-digital">
                                        <div class="icone-assinatura">
                                            <i class="icon-check"></i>
                                        </div>
                                        <div class="dados-assinatura">
                                            <h5><strong>{{ $guia->pet->cliente->nome_cliente }}</strong></h5>
                                            <h5>{{ $guia->data_assinatura_cliente->format('d/m/Y') }}</h5>
                                            <h5>{{ $guia->assinatura_cliente }}</h5>
                                        </div>
                                    </div>
                                @else
                                    <button class="btn btn-lg blue center-block margin-bottom-10" data-toggle="modal"
                                            data-target="#modal-cliente-assinatura">Assinar
                                    </button>
                                @endif
                            </td>
                        </tr>
                        <tr class="text-center">
                            <th class="a33 text-center"
                                style="border-top: 1px solid #000; margin-right: 25px !important; display: block; width: 45%; float: left; ">
                                Assinatura e carimbo do médico veterinário
                            </th>
                            <th class="a33 text-center"
                                style="border-top: 1px solid #000; margin-right: 25px !important; display: block; width: 45%;  float: left; ">
                                Assinatura do tutor/responsável
                            </th>
                        </tr>
                        </tbody>
                    </table>
                @endif
            </div>
        </div>

    </section>

    <div class="modal fade" id="modal-cliente-assinatura">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="text-center">Assinatura Eletrônica</h3>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <div class="inline-block">
                            <div class="dashed-box"
                                 style="padding: 15px;border: 2px dashed green;margin: 10px auto 20px;">
                                <h3 style="padding: 0;margin: 0;">Guia: #{{ $guia->numero_guia }}</h3>
                            </div>
                        </div>
                    </div>
                    <form action="{{ route('autorizador.assinarCliente') }}" method="POST" id="form-assinarCliente">
                        {{ csrf_field() }}
                        <input type="hidden" name="numero_guia" value="{{ $guia->numero_guia }}">
                        <div class="row">
                            <div class="col-md-12">
                                <h3 class="text-center">SENHA</h3>
                            </div>
                            <div class="col-md-6 col-md-offset-3">
                                <input class="form-control text-center" name="senha_plano" id="senha_cliente"
                                       type="text" autocomplete="off" placeholder="Senha de 4 dígitos" required>
                            </div>
                        </div>
                        <button class="btn btn-lg blue center-block margin-top-20" disabled id="btn_assinar_cliente">
                            Assinar
                        </button>
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
                    <div class="text-center">
                        <div class="inline-block">
                            <div class="dashed-box"
                                 style="padding: 15px;border: 2px dashed green;margin: 10px auto 20px;">
                                <h3 style="padding: 0;margin: 0;">Guia: #{{ $guia->numero_guia }}</h3>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('autorizador.assinarPrestador') }}" method="POST" id="form-assinarPrestador">
                        {{ csrf_field() }}
                        <input type="hidden" name="numero_guia" value="{{ $guia->numero_guia }}">
                        <div class="row">
                            <div class="col-md-12">
                                <h3 class="text-center">CRMV</h3>
                            </div>
                            <div class="col-md-6 col-md-offset-3">
                                <input class="form-control text-center" name="senha_prestador" id="senha_prestador"
                                       type="text" placeholder="Ex.: 1234ES">
                                <small class="helper">Apenas números e as duas letras do estado</small>
                            </div>
                        </div>
                        <button class="btn btn-lg blue center-block margin-top-20" id="btn_assinar_prestador">Assinar
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endif

<!-- BEGIN CORE PLUGINS -->
<script src="{{ url('/') }}/assets/global/plugins/jquery.min.js" type="text/javascript"></script>
<script src="{{ url('/') }}/assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="{{ url('/') }}/assets/global/plugins/js.cookie.min.js" type="text/javascript"></script>
<script src="{{ url('/') }}/assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js"
        type="text/javascript"></script>
<script src="{{ url('/') }}/assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="{{ url('/') }}/assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js"
        type="text/javascript"></script>
<!-- END CORE PLUGINS -->

<!-- BEGIN THEME GLOBAL SCRIPTS -->
<script src="{{ url('/') }}/assets/global/scripts/app.min.js" type="text/javascript"></script>
<!-- END THEME GLOBAL SCRIPTS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
{{-- <script src="{{ url('/') }}/assets/pages/scripts/dashboard.min.js" type="text/javascript"></script> --}}
<!-- END PAGE LEVEL SCRIPTS -->
<!-- BEGIN THEME LAYOUT SCRIPTS -->
<script src="{{ url('/') }}/assets/layouts/layout2/scripts/layout.min.js" type="text/javascript"></script>
<script src="{{ url('/') }}/assets/layouts/layout2/scripts/demo.min.js" type="text/javascript"></script>
<script src="{{ url('/') }}/assets/layouts/global/scripts/quick-sidebar.min.js" type="text/javascript"></script>
<script src="{{ url('/') }}/assets/layouts/global/scripts/quick-nav.min.js" type="text/javascript"></script>

<script src="{{ url('/') }}/assets/global/plugins/jquery-mask/jquery.mask.min.js" type="text/javascript"></script>

<script>
    $(document).ready(function () {

        $("#modal-cliente-assinatura #senha_cliente").mask("NNNN", {
            translation: {
                'N': {pattern: /[0-9]/},
            }
        });

        $('#modal-cliente-assinatura').on('show.bs.modal', function (e) {
            setTimeout(function () {
                $('#modal-cliente-assinatura #senha_cliente').focus()
            }, 200);
        });
        $('#modal-cliente-assinatura').on('hide.bs.modal', function (e) {
            $('#modal-cliente-assinatura #senha_cliente').removeClass('focus').val('');
        });

        $('#modal-cliente-assinatura #senha_cliente').on('keyup', function (event) {
            var length = $(this).val().length;
            $('#btn_assinar_cliente').prop('disabled', true);
            if (length == 4) {
                $('#btn_assinar_cliente').prop('disabled', false);
            }
        });
    });
</script>

@yield('scripts')

</body>

</html>
