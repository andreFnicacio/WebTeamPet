@extends('layouts.lifepet-para-todos')
@section('css')
    <style>
        #form-conclusao-cadastro {
            padding-bottom: 3rem;
        }
        div#main-container {
            margin-top: 0rem;
            margin-bottom: 3rem;
        }
        .box-pagamento {
            padding: 1.5rem;
            background-color: #fff;
            border-radius: 15px;
            margin-bottom: 25px;
        }
        .heading h2 {
            font-weight: 700;
            margin-bottom: 10px;
        }
        .elementor-heading-title {
            padding: 0;
            margin: 0;
            line-height: 1;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css">
    <link href="{{ url('/') }}/assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ url('/') }}/assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
    @parent
@endsection
@section('title')
    @parent
    Realizar Pagamento
@endsection
@section('content')
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row" style="padding-left: 20px">
                    <div class="container" id="main-container">
                        <div class="heading text-center">
                            <h2 class="elementor-heading-title elementor-size-default" style="margin-bottom: 3rem;">Finalizar pagamento</h2>
                            <div class="elementor-text-editor elementor-clearfix"></div>
                        </div>
                        <form action="{{ route('links-pagamento.pagar', ['hash' => $linkPagamento->hash]) }}" method="POST" id="form-pagamento">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="box-pagamento box-shadow">
                                        <h4 class="text-center">Seus dados</h4>
                                        <div class="form-group">
                                            <label for="cep">Nome Completo:</label>
                                            <input type="nome" class="form-control" id="name" value="<?= $linkPagamento->cliente->nome_cliente ?>" readonly="readonly" required="required" name="name" placeholder="">
                                        </div>
                                        <div class="form-group">
                                            <label for="email">Email:</label>
                                            <input type="email" class="form-control" value="<?= $linkPagamento->cliente->email ?>" id="email" readonly="readonly" required="required" name="email" placeholder="">
                                        </div>
                                        <div class="form-group">
                                            <label for="celular">Celular:</label>
                                            <input type="celular" class="form-control" value="<?= $linkPagamento->cliente->celular ?>" id="celular" required="required" readonly="readonly" name="celular" placeholder="">
                                        </div>

                                        <div class="form-group">
                                            <label for="cpf">CPF:</label>
                                            <input type="text" class="form-control" id="cpf" readonly value="<?= $linkPagamento->cliente->cpf ?>" required="required" name="cpf" placeholder="">
                                        </div>
                                        <div class="form-group">
                                            <label for="cep">CEP:</label><div class="row">
                                                <div class="col-6">

                                                    <input type="text" class="form-control" id="cep" required="required" name="cep" readonly value="{{ $linkPagamento->cliente->cep }}" placeholder="" maxlength="8">
                                                </div>
                                                <div class="col-4">
                                                    {{--<a href="javascript:;" onclick="loadCEPInfo()" class="btn btn-primary" id="loadCEPButton">BUSCAR</a>--}}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <div class="form-group">
                                                    <label for="country">País:</label>
                                                    <input type="text" class="form-control" readonly="readonly" id="country" required="required"  name="country" placeholder="" value="Brasil">
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="form-group">
                                                    <label for="state">Estado (UF):</label>
                                                    <select name="state" class="form-control" required="required" id="state" readonly>
                                                        <option value="ES" selected>ES</option>
                                                        <option value="SP">SP</option>
                                                    </select>
                                                    <!-- <input type="text" class="form-control" readonly="readonly" id="state" required="required" name="state" placeholder=""> -->
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <div class="form-group">
                                                    <label for="city">Cidade:</label>
                                                    <input type="text" class="form-control" readonly="readonly" id="city" required="required" value="{{ $linkPagamento->cliente->cidade }}" name="city" placeholder="">
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="form-group">
                                                    <label for="neighbourhood">Bairro:</label>
                                                    <input type="text" class="form-control" readonly="readonly" id="neighbourhood" required="required" value="{{ $linkPagamento->cliente->bairro }}" name="neighbourhood" placeholder="">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <div class="form-group">
                                                    <label for="street">Rua:</label>
                                                    <input type="text" class="form-control" readonly="readonly" id="street" required="required" value="{{ $linkPagamento->cliente->rua }}" name="street" placeholder="">
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="form-group">
                                                    <label for="address_number">Número:</label>
                                                    <input type="text" class="form-control" id="address_number" required="required" name="address_number" value="{{ $linkPagamento->cliente->numero_endereco }}" readonly placeholder="">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="box-pagamento box-shadow">
                                        <h4 class="text-center">Pagamento</h4>
                                        <div class="form-group">
                                            <label for="cep">Forma de pagamento:</label>
                                            <input type="text" class="form-control" id="payment_type" required="required" name="payment_type" placeholder="" value="CARTÃO DE CRÉDITO" readonly="readonly">
                                        </div>
                                        <div class="form-group">
                                            <label for="cpf">Número do cartão:</label>
                                            <input type="text" class="form-control" id="card_number" required="required" name="card_number" placeholder="">
                                        </div>
                                        <div class="form-group">
                                            <label for="brand">Bandeira:</label>
                                            <select required="required" name="brand" id="brand" class="form-control">
                                                <option value="mastercard">MASTERCARD</option>
                                                <option value="visa">VISA</option>
                                                <option value="diners">DINERS</option>
                                                <option value="amex">AMEX</option>
                                                <option value="elo">ELO</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="holder">Nome como escrito no cartão:</label>
                                            <input type="text" class="form-control" id="holder" required="required" name="holder" placeholder="">
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <div class="form-group">
                                                    <label for="expires_in">Validade:</label>
                                                    <input type="text" class="form-control" id="expires_in" required="required" name="expires_in" placeholder="00/00">
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="form-group">
                                                    <label for="cvv">CVV:</label>
                                                    <input type="text" class="form-control" id="ccv" name="ccv" required="required" placeholder="000" maxlength="4">
                                                </div>

                                                <img src="https://lifepet.com.br/wp-content/uploads/2020/03/fundossl-300x72.png" alt="" width="175" height="42">

                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="box-pagamento box-shadow">
                                        <h4 class="text-center">Resumo do pedido</h4>
                                        <table class="table" id="resume">
                                            <tr>
                                                <td><span id="client_name"><?= $linkPagamento->cliente->nome_cliente ?></span><br/></td>
                                                <td>R$ <?= number_format($linkPagamento->valor, 2, ',', '.') ?></td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    <select name="parcelas" id="parcelas" class="form-control">
                                                        @for($i = 0; $i < $linkPagamento->parcelas; $i++)
                                                            <option value="{{ $i+1 }}">{{ $i+1 }}x de R$ {{ number_format($linkPagamento->valor/($i+1.00), 2, ',', '.') }}</option>
                                                        @endfor
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    <p>{{ $linkPagamento->descricao }}</p>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <button type="submit" id="submit-button" class="btn btn-primary form-control text-center"><i class="fas fa-lock"></i> Pagar agora</button>

                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @parent
    <script src="{{ url('/') }}/assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
    <script src="{{ url('/') }}/assets/global/plugins/jquery-mask/jquery.mask.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2();



            (function(){
                //jQuery('#ccv').maskLife('000');
                jQuery('#expires_in').mask('00/00');
                jQuery('#cep').mask('00000000');
                jQuery('#cpf').mask('00000000000');
                jQuery('#card_number').mask('0000 0000 0000 0000');
            })();

            $('#form-pagamento').submit(function () {
                $('#submit-button').html('Processando pagamento...');
                $('#submit-button').attr('disabled','disabled');
                var loader = "<div class=\"inner\" style=\"width: 145px; height: 145px; margin: 0 auto;\"><svg xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" style=\"margin:auto;background:#fff;display:block;\" width=\"145px\" height=\"145px\" viewBox=\"0 0 100 100\" preserveAspectRatio=\"xMidYMid\">\n" +
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
                swal({
                    type: 'info',
                    title: 'Processando pagamento',
                    html: loader + '<br>Favor aguardar. Não atualize nem feche a página. Aguarde até a atualização automática.',
                    showConfirmButton: false,
                    allowOutsideClick: false,
                });
            });

        });
    </script>
@endsection