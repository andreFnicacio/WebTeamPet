@extends('layouts.app')

@section('title')
    @parent
    Emissor de guias - Nova guia
@endsection

@section('css')
    @parent
    <style>
        .tipo_atendimento .label {
            display: inline-block;
            margin-top: 8px;
        }

        .field-group.disabled {
            pointer-events: none;
            opacity: 50%;
        }

        .select2-container .select2-results__option[aria-disabled=true] {
            display: none;
        }

        .btn.picpay {
            background-color: #11c76f;
            margin-top: 20px;
        }

        .btn.cancel-picpay {
            margin-top: 20px;
            background-color: #e7505a;
            border-color: #e7505a !important;
        }

        .btn.cancel-picpay:focus, .btn.cancel-picpay:active {
            background-color: #a2484e;
        }

        .alert-picpay {
            margin-bottom: 5px;
        }

    </style>

@endsection

@php
    $selecionaveis = \Modules\Clinics\Entities\Clinicas::where('estado', $clinica->estado)
                                            ->where('selecionavel', 1)
                                            ->where('ativo', 1)
                                            ->orderBy('nome_clinica', 'asc');

    //VET MEDICAL CENTER VITORIA E PRESTADORES
    if(!in_array($clinica->id, [225, 268, 233, 265, 258, 88, 241])) {
        $selecionaveis->where('id', '!=', 270);
    }

    $selecionaveis = $selecionaveis->get();

    if(\Entrust::hasRole('GRUPO_HOSPITALAR')) {
        $grupo = \App\Models\GrupoHospitalar::where('id_usuario', Auth::id())->first();
        $vinculados = $grupo->clinicas()->get();
        $vinculadosSomados = $selecionaveis->merge($vinculados);
    }
@endphp

@section('content')
    @include('common.swal')

    <div class="portlet light portlet-fit portlet-form ">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-money font-green-jungle"></i>
                <span class="caption-subject font-green-jungle sbold uppercase">Pagamento Direto
                </span>
            </div>
            <div class="actions">
                <div class="btn-group btn-group-devided" data-toggle="buttons">

                </div>
            </div>


        </div>
        <div class="portlet-body" id="pagamentoDireto">
            <!-- BEGIN FORM-->
            <form action="{{ route('autorizador.confirmarPagamentoDireto', ['numeroGuia' => $numeroGuia]) }}"
                  id="form_pagamentoDireto" class="form-horizontal" method="POST">
                {{ csrf_field() }}
                <input type="hidden" name="payment_method" value="picpay">
                <div class="form-body">
                    {{--                    <div class="alert alert-warning alert-picpay">--}}
                    {{--                        <button class="close" data-close="alert"></button>--}}
                    {{--                        Devido a uma falha, não foi possível realizar a cobrança automaticamente da coparticipação do cliente. <br>--}}
                    {{--                        Mas não se preocupe. Habilitamos o 'recebimento direto' pela nossa <b>Carteira Digital</b> e pelo <b>PicPay.</b>--}}
                    {{--                    </div>--}}
                    <div class="alert alert-warning alert-picpay">
                        <button class="close" data-close="alert"></button>
                        Pergunte agora ao cliente se ele possui nossa <b>PIX</b> ou <b>PicPay</b>. Se ele não possuir,
                        sugira que baixe e se ele não puder baixar, peça para procurar o suporte.
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-12" style="margin-bottom: 20px;">
                            <h4 class="block" style="margin-top: 0px; padding-left: 0">Dados de revisão:</h4>

                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Número da Guia</th>
                                    <th>Procedimento</th>
                                    <th>Valor</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php
                                    $participacaoTotal = 0;
                                @endphp
                                @foreach($historicos as $historico)
                                    <tr>
                                        <td>#{{ $numeroGuia }}</td>
                                        <td>{{ $historico->procedimento->nome_procedimento }}</td>
                                        @php
                                            $participacaoProcedimento = $historico->procedimento->valorParticipacao($historico->plano);
                                            $participacaoTotal += $participacaoProcedimento;
                                        @endphp
                                        <td>{{ \App\Helpers\Utils::money($participacaoProcedimento) }}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="2">Total:</td>
                                    <td class="bold">{{ \App\Helpers\Utils::money($participacaoTotal) }}</td>
                                </tr>
                                </tbody>
                            </table>
                            <input type="hidden" name="total_procedimentos" value="{{ $participacaoTotal }}">
                            <br>

                            <div>

                                <!-- Nav tabs -->
                                <ul class="nav nav-tabs" role="tablist">
                                    <li role="presentation"><a href="#tab-pix" aria-controls="pix" role="tab"
                                                               data-toggle="tab" data-payment-method="pix">Pix</a></li>
                                    <li role="presentation"><a href="#tab-picpay" aria-controls="tab-picpay" role="tab"
                                                               data-toggle="tab" data-payment-method="picpay"
                                                               data-form-required="['usuario_picpay', 'id_transacao_picpay']">Picpay</a>
                                    </li>
                                    {{--<li role="presentation"><a href="#tab-wallet" aria-controls="wallet" role="tab" data-toggle="tab" data-payment-method="wallet">Carteira Lifepet</a></li>--}}
                                </ul>

                                <!-- Tab panes -->
                                <div class="tab-content">
                                    <div role="tabpanel" class="tab-pane" id="tab-pix">
                                        <div class="text-center">

                                            <p class="alert alert-info"
                                               style="width: 50%; margin: 0 auto; margin-bottom: 20px">
                                                Não atualize nem feche a página. Aguarde o pagamento.<br>
                                                O pagamento será reconhecido automáticamente.
                                            </p>

                                            <p id="pix-loading">Aguarde. Carregando QRCode PIX ...</p>

                                            <input type="hidden" name="acquirer_transaction_id"
                                                   id="pix-acquirer-transaction-id">
                                            <img src="" alt="" id="pix-qrcode">
                                            <div id="pix-copia-e-cola-wrapper" style="width: 40%; margin: 0 auto">
                                                <label for="pix-copia-e-cola">PIX Copia e Cola:</label>
                                                <input type="text" id="pix-copia-e-cola" class="form-control"
                                                       placeholder="PIX Copia e Cola">
                                            </div>


                                            <div class="timer" id="pix-timer"> -</div>
                                            <button type="button" class="btn btn-accent" id="pix-reloader"
                                                    style="display: none">Recarregar
                                            </button>
                                        </div>
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="tab-picpay">
                                        <div class="text-center">
                                            <img src="{{ asset('picpay/picpay_lifepet.jpg') }}" class="img-responsive"
                                                 style="max-width: 300px; display: inline-block;">
                                            <div>
                                                <input type="text" id="usuario_picpay" class="form-control"
                                                       name="usuario_picpay" required
                                                       placeholder="Usuário do Picpay do pagante."
                                                       style="width: 270px; margin: 0 auto; display: block">
                                                <input type="text" id="id_transacao_picpay" class="form-control"
                                                       name="id_transacao_picpay" required
                                                       placeholder="ID da Transação."
                                                       style="width: 270px; margin: 0 auto; display: block">
                                            </div>
                                        </div>
                                    </div>
                                    {{--<div role="tabpanel" class="tab-pane" id="tab-wallet">--}}
                                    {{--@if($charge)--}}
                                    {{--<div class="text-center">--}}
                                    {{--<img src="{{ $charge->image }}" alt="">--}}
                                    {{--</div>--}}
                                    {{--@else--}}
                                    {{--<p class="panel panel-info">Indisponível</p>--}}
                                    {{--@endif--}}
                                    {{--</div>--}}
                                </div>
                            </div>

                            <div class="text-center" id="form-buttons">
                                <a class="btn cancel cancel-picpay btn-success" id="cancel-picpay">Não foi possível
                                    receber.</a>
                                <button type="submit" id="submit-picpay" class="btn picpay btn-success">Confirmar
                                    recebimento.
                                </button>
                            </div>

                        </div>
                    </div>

                </div>
            </form>

            <!-- END FORM-->
        </div>
    </div>
    <form action="{{ route('autorizador.cancelarPagamentoDireto', ['numeroGuia' => $numeroGuia]) }}"
          id="form_cancelarPagamentoDireto" class="form-horizontal" method="POST">
        {{ csrf_field() }}
        <input type="hidden" name="total_procedimentos" value="{{ $participacaoTotal }}">
    </form>
@endsection

@section('scripts')
    @parent
    <script>
        Number.prototype.pad = function (size) {
            var s = String(this);
            while (s.length < (size || 2)) {
                s = "0" + s;
            }
            return s;
        }

        function loadPix() {
            //Get pix
            const numeroGuia = '{{ $numeroGuia }}';
            const valor = '{{ intval($participacaoTotal * 100) }}';

            $("#pix-copia-e-cola-wrapper").hide();
            $('#pix-qrcode').hide();
            $('#pix-loading').show();
            $('#pix-timer').html(' - ');
            $('#pix-reloader').hide();
            $('#pix-acquirer-transaction-id').val('');

            $.get("/getnet/pix/atendimento/" + numeroGuia + '/' + valor).success(function (response) {
                $('#pix-loading').hide();
                $('#pix-qrcode').attr('src', 'data:image/png;base64, ' + response.pix.rendered);
                $('#pix-qrcode').show();
                $('#pix-reloader').hide()
                $('#pix-qrcode').css("-webkit-filter", "");
                $('#pix-acquirer-transaction-id').val(response.pix.transaction_id);
                $("#pix-copia-e-cola-wrapper").show();
                $("#pix-copia-e-cola").val(response.pix.qr_code);
                $('#pix-copia-e-cola').removeAttr('disabled');
                $('#pix-copia-e-cola').show();

                const countDownDate = new Date(response.pix.expiration_date_qrcode).getTime();
                //Start countdown;
                var countdown = setInterval(function () {
                    let now = new Date().getTime();
                    let distance = countDownDate - now;
                    let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    let seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    if (distance >= 0) {
                        let time = minutes.pad() + ':' + seconds.pad();
                        $('#pix-timer').html(time);
                    }

                    if (distance < 0) {
                        clearInterval(countdown);
                        $('#pix-timer').html(' - ');
                        $('#pix-reloader').show();
                        $('#pix-qrcode').css("-webkit-filter", "blur(10px)");
                        $("#pix-copia-e-cola-wrapper").hide();
                    }
                }, 1000);

                //Set payment checker
                var paymentChecker = setInterval(function () {
                    $.get("/getnet/pix/atendimento/" + numeroGuia).success(function (response) {
                        if (response.approved) {
                            clearInterval(paymentChecker);
                            //Automatic redirect to index.
                            swal({
                                'title': 'Pagamento confirmado!',
                                'type': 'success',
                                'html': 'Recebemos a confirmação do pagamento e estamos lhe redirecionando para a listagem de guias.'
                            });

                            return setTimeout(function () {
                                location.href = '{{ route('autorizador.verGuias') }}';
                            }, 1000);
                        }
                    }).error(function (response) {
                        console.log("Não foi possível ler o pagamento." + response);
                    });
                }, 8000);

            });
        }


        $(document).ready(function () {
            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                const paymentMethod = $(this).data('payment-method');
                var required = $(this).data('form-required');
                $('[name=payment_method]').val(paymentMethod);

                $('#form-buttons').show();
                if (paymentMethod === 'pix') {
                    $("#form-buttons").hide();
                    loadPix();
                }

                //Disable all tabs inputs
                $('#form_pagamentoDireto').find('input[type=text]').each(function (index) {
                    $element = $(this);
                    $element.hide();
                    $element.attr('disabled', 'disabled');
                });

                if (required) {
                    required = eval(required);
                    if (typeof required === 'object') {
                        for (i = 0; i < required.length; i++) {
                            $element = $('#' + required[i]);
                            if ($element[0]) {
                                $element.show();
                                $element.attr('required', 'required');
                                $element.removeAttr('disabled');
                            }
                        }
                    }
                }
            });

            $('#form_pagamentoDireto').submit(function (e) {
                if ($(this)[0].checkValidity()) {
                    $('#submit-picpay, #cancel-picpay').attr('disabled', 'disabled');
                }
            });

            $('#cancel-picpay').click(function (e) {
                e.preventDefault();
                e.stopPropagation();
                if ($(this).attr('disabled')) {
                    return false;
                }

                swal({
                    title: 'Atenção.',
                    html: "Não foi possível confirmar a coparticipação do cliente, portanto a guia permanece indisponível para atendimento.<br>Peça para o cliente entrar em contato com o suporte.",
                    type: 'warning',
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Confirmar',
                    confirmButtonClass: 'btn btn-info',
                    cancelButtonClass: 'btn info',
                    cancelButtonText: 'Voltar',
                    showCancelButton: true,
                    buttonsStyling: true
                }).then(function (confirm) {
                    if (confirm) {
                        //POST CANCEL
                        //location.href = '{{ route('autorizador.verGuias') }}';
                        $('#form_cancelarPagamentoDireto').submit();
                        $('#submit-picpay, #cancel-picpay').attr('disabled', 'disabled');
                    }
                });
            });

            $('#myTabs a').click(function (e) {
                e.preventDefault()
                $(this).tab('show')
            });


            $('#pix-reloader').on('click', function (e) {
                const button = $(this);
                if (button.attr('disabled')) {
                    e.preventDefault();
                    return false;
                }

                loadPix();
            });

            $('.nav-tabs a[href="#tab-pix"]').tab('show');
        });
    </script>

@endsection
