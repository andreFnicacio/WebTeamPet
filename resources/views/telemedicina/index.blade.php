@extends('layouts.telemedicina')
@section('css')
    <style>
        body {
            height: 100vh;
            padding: 40px 30px;
            background-size: contain;
            background-position: center center;
            background-image: linear-gradient(
                    185deg
                    , rgba(141,125,250,1) 0%, rgba(233,189,250,1) 100%) !important;
            background-attachment: fixed;
        }
        .content {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        #foto {
            margin-top: 30px;
        }

        h4 {
            font-weight: bold;
            color: #8d2cff;
            font-size: 10pt;
            margin-bottom: 20px;
        }
        .input-circle {
            border-radius: 15px;
            margin-bottom: 10px;
            text-align: center;
        }

        p {
            color: #8d2cff;
            font-weight: bold;
            font-size: 18pt;
            margin-bottom: 30px;
        }

        button.continuar {
            background-color: #8d2cff;
            color: white;
            border-color: transparent;
            font-weight: bold;
        }
        button.continuar:focus {
            background-color: #8d2cff;
            color: white;
        }
        button.continuar[disabled] {
            background-color: #8d2cff;
            opacity: .5;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css">
    <link href="{{ url('/') }}/assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ url('/') }}/assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
    @parent
@endsection
@section('title')
    @parent
    Telemedicina Lifepet
@endsection
@section('content')
    <div class="content">
        <div class="container">
            <div class="row">
                <div class="offset-3 col-6 text-center">
                    <img src="https://lifepet.com.br/wp-content/themes/lifepet2020/assets/img/lifepet-logo.svg" alt="">
                </div>
            </div>
            <div class="row">
                <div class="offset-2 col-8 text-center">
                    <img src="{{ asset('_telemedicina/foto.png') }}" alt="" class="img-responsive img-fluid" id="foto">
                </div>
            </div>
            <div class="row">
                <div class="col-12 text-center">
                    <br>
                    <h4>BEM-VINDO(A)</h4>
                    <p>Para entrar,<br>
                    confirme seu CPF:</p>
                </div>
            </div>

            <div class="row">
                <div class=" col-12 text-center">
                    <input type="text" placeholder="XXX.XXX.XXX-XX" id="cpf" class="cpf form-control input-circle">
                </div>
            </div>
            <div class="row">
                <div class=" col-12 text-center">
                    <button id="validar-cpf" class="form-control input-circle continuar" disabled>CONTINUAR</button>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
    @parent
    <script src="{{ url('/') }}/assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
    <script src="https://lifepet.com.br/wp-content/themes/lifepet2020/assets/js/jquery.mask.js"></script>


    <script src="https://raw.githubusercontent.com/RobinHerbots/Inputmask/5.x/dist/jquery.inputmask.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css" />
    <script>

        function getLoader() {
            return "<div class=\"inner\" style=\"width: 145px; height: 145px; margin: 0 auto;\"><svg xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" style=\"margin:auto;background:#fff;display:block;\" width=\"145px\" height=\"145px\" viewBox=\"0 0 100 100\" preserveAspectRatio=\"xMidYMid\">\n" +
                "<g>\n" +
                "  <animateTransform attributeName=\"transform\" type=\"rotate\" values=\"360 50 50;0 50 50\" keyTimes=\"0;1\" dur=\"2s\" repeatCount=\"indefinite\" calcMode=\"spline\" keySplines=\"0.5 0 0.5 1\" begin=\"-0.2s\"></animateTransform>\n" +
                "  <circle cx=\"50\" cy=\"50\" r=\"39.891\" stroke=\"#539cd6\" stroke-width=\"14.4\" fill=\"none\" stroke-dasharray=\"0 300\">\n" +
                "    <animate attributeName=\"stroke-dasharray\" values=\"15 300;55.1413599195142 300;15 300\" keyTimes=\"0;0.5;1\" dur=\"2s\" repeatCount=\"indefinite\" calcMode=\"linear\" keySplines=\"0 0.4 0.6 1;0.4 0 1 0.6\" begin=\"-0.092s\"></animate>\n" +
                "  </circle>\n" +
                "  <circle cx=\"50\" cy=\"50\" r=\"39.891\" stroke=\"#ffffff\" stroke-width=\"7.2\" fill=\"none\" stroke-dasharray=\"0 300\">\n" +
                "    <animate attributeName=\"stroke-dasharray\" values=\"15 300;55.1413599195142 300;15 300\" keyTimes=\"0;0.5;1\" dur=\"2s\" repeatCount=\"indefinite\" calcMode=\"linear\" keySplines=\"0 0.4 0.6 1;0.4 0 1 0.6\" begin=\"-0.092s\"></animate>\n" +
                "  </circle>\n" +
                "  <circle cx=\"50\" cy=\"50\" r=\"32.771\" stroke=\"#727272\" stroke-width=\"1\" fill=\"none\" stroke-dasharray=\"0 300\">\n" +
                "    <animate attributeName=\"stroke-dasharray\" values=\"15 300;45.299378454348094 300;15 300\" keyTimes=\"0;0.5;1\" dur=\"2s\" repeatCount=\"indefinite\" calcMode=\"linear\" keySplines=\"0 0.4 0.6 1;0.4 0 1 0.6\" begin=\"-0.092s\"></animate>\n" +
                "  </circle>\n" +
                "  <circle cx=\"50\" cy=\"50\" r=\"47.171\" stroke=\"#727272\" stroke-width=\"1\" fill=\"none\" stroke-dasharray=\"0 300\">\n" +
                "    <animate attributeName=\"stroke-dasharray\" values=\"15 300;66.03388996804073 300;15 300\" keyTimes=\"0;0.5;1\" dur=\"2s\" repeatCount=\"indefinite\" calcMode=\"linear\" keySplines=\"0 0.4 0.6 1;0.4 0 1 0.6\" begin=\"-0.092s\"></animate>\n" +
                "  </circle>\n" +
                "</g>\n" +
                "\n" +
                "<g>\n" +
                "  <animateTransform attributeName=\"transform\" type=\"rotate\" values=\"360 50 50;0 50 50\" keyTimes=\"0;1\" dur=\"2s\" repeatCount=\"indefinite\" calcMode=\"spline\" keySplines=\"0.5 0 0.5 1\"></animateTransform>\n" +
                "  <path fill=\"#539cd6\" stroke=\"#727272\" d=\"M97.2,50.1c0,6.1-1.2,12.2-3.5,17.9l-13.3-5.4c1.6-3.9,2.4-8.2,2.4-12.4\"></path>\n" +
                "  <path fill=\"#ffffff\" d=\"M93.5,49.9c0,1.2,0,2.7-0.1,3.9l-0.4,3.6c-0.4,2-2.3,3.3-4.1,2.8l-0.2-0.1c-1.8-0.5-3.1-2.3-2.7-3.9l0.4-3 c0.1-1,0.1-2.3,0.1-3.3\"></path>\n" +
                "  <path fill=\"#539cd6\" stroke=\"#727272\" d=\"M85.4,62.7c-0.2,0.7-0.5,1.4-0.8,2.1c-0.3,0.7-0.6,1.4-0.9,2c-0.6,1.1-2,1.4-3.2,0.8c-1.1-0.7-1.7-2-1.2-2.9 c0.3-0.6,0.5-1.2,0.8-1.8c0.2-0.6,0.6-1.2,0.7-1.8\"></path>\n" +
                "  <path fill=\"#539cd6\" stroke=\"#727272\" d=\"M94.5,65.8c-0.3,0.9-0.7,1.7-1,2.6c-0.4,0.9-0.7,1.7-1.1,2.5c-0.7,1.4-2.3,1.9-3.4,1.3h0 c-1.1-0.7-1.5-2.2-0.9-3.4c0.4-0.8,0.7-1.5,1-2.3c0.3-0.8,0.7-1.5,0.9-2.3\"></path>\n" +
                "</g>\n" +
                "<g>\n" +
                "  <animateTransform attributeName=\"transform\" type=\"rotate\" values=\"360 50 50;0 50 50\" keyTimes=\"0;1\" dur=\"2s\" repeatCount=\"indefinite\" calcMode=\"spline\" keySplines=\"0.5 0 0.5 1\" begin=\"-0.2s\"></animateTransform>\n" +
                "  <path fill=\"#ffffff\" stroke=\"#727272\" d=\"M86.9,35.3l-6,2.4c-0.4-1.2-1.1-2.4-1.7-3.5c-0.2-0.5,0.3-1.1,0.9-1C82.3,33.8,84.8,34.4,86.9,35.3z\"></path>\n" +
                "  <path fill=\"#ffffff\" stroke=\"#727272\" d=\"M87.1,35.3l6-2.4c-0.6-1.7-1.5-3.3-2.3-4.9c-0.3-0.7-1.2-0.6-1.4,0.1C88.8,30.6,88.2,33,87.1,35.3z\"></path>\n" +
                "  <path fill=\"#539cd6\" stroke=\"#727272\" d=\"M82.8,50.1c0-3.4-0.5-6.8-1.6-10c-0.2-0.8-0.4-1.5-0.3-2.3c0.1-0.8,0.4-1.6,0.7-2.4c0.7-1.5,1.9-3.1,3.7-4l0,0 c1.8-0.9,3.7-1.1,5.6-0.3c0.9,0.4,1.7,1,2.4,1.8c0.7,0.8,1.3,1.7,1.7,2.8c1.5,4.6,2.2,9.5,2.3,14.4\"></path>\n" +
                "  <path fill=\"#ffffff\" d=\"M86.3,50.2l0-0.9l-0.1-0.9l-0.1-1.9c0-0.9,0.2-1.7,0.7-2.3c0.5-0.7,1.3-1.2,2.3-1.4l0.3,0 c0.9-0.2,1.9,0,2.6,0.6c0.7,0.5,1.3,1.4,1.4,2.4l0.2,2.2l0.1,1.1l0,1.1\"></path>\n" +
                "  <path fill=\"#ff9922\" d=\"M93.2,34.6c0.1,0.4-0.3,0.8-0.9,1c-0.6,0.2-1.2,0.1-1.4-0.2c-0.1-0.3,0.3-0.8,0.9-1 C92.4,34.2,93,34.3,93.2,34.6z\"></path>\n" +
                "  <path fill=\"#ff9922\" d=\"M81.9,38.7c0.1,0.3,0.7,0.3,1.3,0.1c0.6-0.2,1-0.6,0.9-0.9c-0.1-0.3-0.7-0.3-1.3-0.1 C82.2,38,81.8,38.4,81.9,38.7z\"></path>\n" +
                "  <path fill=\"#727272\" d=\"M88.5,36.8c0.1,0.3-0.2,0.7-0.6,0.8c-0.5,0.2-0.9,0-1.1-0.3c-0.1-0.3,0.2-0.7,0.6-0.8C87.9,36.3,88.4,36.4,88.5,36.8z\"></path>\n" +
                "  <path stroke=\"#727272\" d=\"M85.9,38.9c0.2,0.6,0.8,0.9,1.4,0.7c0.6-0.2,0.9-0.9,0.6-2.1c0.3,1.2,1,1.7,1.6,1.5c0.6-0.2,0.9-0.8,0.8-1.4\"></path>\n" +
                "  <path fill=\"#539cd6\" stroke=\"#727272\" d=\"M86.8,42.3l0.4,2.2c0.1,0.4,0.1,0.7,0.2,1.1l0.1,1.1c0.1,1.2-0.9,2.3-2.2,2.3c-1.3,0-2.5-0.8-2.5-1.9l-0.1-1 c0-0.3-0.1-0.6-0.2-1l-0.3-1.9\"></path>\n" +
                "  <path fill=\"#539cd6\" stroke=\"#727272\" d=\"M96.2,40.3l0.5,2.7c0.1,0.5,0.2,0.9,0.2,1.4l0.1,1.4c0.1,1.5-0.9,2.8-2.2,2.9h0c-1.3,0-2.5-1.1-2.6-2.4 L92.1,45c0-0.4-0.1-0.8-0.2-1.2l-0.4-2.5\"></path>\n" +
                "  <path fill=\"#727272\" d=\"M91.1,34.1c0.3,0.7,0,1.4-0.7,1.6c-0.6,0.2-1.3-0.1-1.6-0.7c-0.2-0.6,0-1.4,0.7-1.6C90.1,33.1,90.8,33.5,91.1,34.1z\"></path>\n" +
                "  <path fill=\"#727272\" d=\"M85.5,36.3c0.2,0.6-0.1,1.2-0.7,1.5c-0.6,0.2-1.3,0-1.5-0.6C83,36.7,83.4,36,84,35.8C84.6,35.5,85.3,35.7,85.5,36.3z\"></path>\n" +
                "\n" +
                "</g>\n" +
                "</svg></div>";
        }

        function cpf(cpf){
            cpf = cpf.replace(/\D/g, '');
            if(cpf.toString().length != 11 || /^(\d)\1{10}$/.test(cpf)) return false;
            var result = true;
            [9,10].forEach(function(j){
                var soma = 0, r;
                cpf.split(/(?=)/).splice(0,j).forEach(function(e, i){
                    soma += parseInt(e) * ((j+2)-(i+1));
                });
                r = soma % 11;
                r = (r <2)?0:11-r;
                if(r != cpf.substring(j, j+1)) result = false;
            });
            return result;
        }

        $(document).ready(function() {
            $('#validar-cpf').click(function() {
                var $cpf = jQuery("#cpf").val();
                if($cpf === '') {
                    swal({
                        type: 'warning',
                        title: 'Campo obrigatório!',
                        html: loader + 'Preencha o campo de CPF.',
                        showConfirmButton: false,
                        allowOutsideClick: false,
                    });
                }

                jQuery.ajax({
                    url: '{{ url('/') }}' + '/api/v1/telemedicina/cliente/'+$cpf,
                    dataType: 'json',
                    type: 'GET',
                    beforeSend: function() {
                        let loader = getLoader();

                        swal({
                            type: 'info',
                            title: 'Checando informações...',
                            html: loader + '<br>Favor aguardar. Não atualize nem feche a página. Aguarde até a atualização automática.',
                            showConfirmButton: false,
                            allowOutsideClick: false,
                        });
                    },
                    success: function(data) {
                        swal.close();
                        if(!data.exists) {
                            swal({
                                type: 'info',
                                title: 'Informação',
                                text: "Você ainda não é cadastrado em nosso sistema. Pedimos para que entre em contato com a nossa equipe de atendimento" +
                                    " para adquirir o serviço de Telemedicina Veterinária da Lifepet!",
                                showConfirmButton: true,
                                confirmButtonText: 'Ok',
                                allowOutsideClick: false
                            });
                        } else {
                            //Show form
                            if(!data.status) {
                                return swal({
                                    type: 'warning',
                                    title: 'Oops!',
                                    html: 'Seu acesso não está permitido. Entre em contato com o suporte para saber mais.',
                                    showConfirmButton: true,
                                    allowOutsideClick: false,
                                });
                            } else {
                                let loader = getLoader();

                                swal({
                                    type: 'success',
                                    title: 'Falta pouco!',
                                    html: loader + '<br>Aguarde enquanto te encaminhamos para a marcação de consultas.',
                                    showConfirmButton: false,
                                    allowOutsideClick: false,
                                });

                                window.location = "https://www.lifepet.com.br/tm-atendimento";
                            }
                        }
                    },
                    error: function(data, textStatus) {
                        swal({
                            type: 'error',
                            title: 'Erro',
                            text: 'Não foi possível verificar seu cadastro em nossos servidores.',
                            showConfirmButton: true,
                            confirmButtonText: 'Ok',
                            allowOutsideClick: false
                        });

                        console.log(data);
                    },
                });
            });

            function habilitarContinuar() {
                if($(this).val().length === 14) {
                    $('#validar-cpf').prop('disabled', false);
                } else {
                    $('#validar-cpf').prop('disabled', true);
                }
            }

            $('#cpf').on('keyup', habilitarContinuar);
            $('#cpf').on('blur', habilitarContinuar);
        });


        (function(){
            jQuery('#cpf').maskLife('000.000.000-00');
        })();
    </script>

@endsection