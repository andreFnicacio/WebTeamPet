@extends('layouts.lifepet-para-todos')
@section('css')
    <style>
        #form-conclusao-cadastro {
            padding-bottom: 3rem;
        }
    </style>
    <style type="text/css">
        .box-shadow {
            box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.05);
        }
        .box-pagamento {
            padding: 1.5rem;
            background-color:#fff;
            border-radius:15px;
            margin-bottom:25px;
        }
        .box-pagamento h4 {
            margin-bottom: 1rem;
        }
        #submit-button {
            margin-top: 1rem;
            background: #009bf2;
            padding: 15px 15px;
            height: unset;
        }
        div#main-container {
            margin-top: 0rem;
            margin-bottom: 3rem;
        }
        select[readonly] {
            background: #eee;
            pointer-events: none;
            touch-action: none;
        }

        .heading h2 {
            color: #0090F1;
            font-weight: 700;
            margin-bottom: 10px;
        }
    </style>
    <script src="https://www.google.com/recaptcha/api.js?render={{ getenv('RECAPTCHA_SITE_KEY') }}"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css">
    <link href="{{ url('/') }}/assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ url('/') }}/assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />

    {{--Purple theme--}}
    <style>
        .topbar {
            background: #6e64ec;
        }
        .heading h2 {
            color: #6e64ec;
        }
        .btn-primary {
            background-color: #6e64ec;
        }
        .btn-primary:hover {
            background-color: #5550b1;
        }
        .elementor-13880 .elementor-element.elementor-element-ffe2750:not(.elementor-motion-effects-element-type-background), .elementor-13880 .elementor-element.elementor-element-ffe2750 > .elementor-motion-effects-container > .elementor-motion-effects-layer {
            background-color: #5550b1;
        }
        .elementor-social-icon {
            background-color: #ff0087 !important;
        }
    </style>
    <style>
        @media screen and (max-width: 520px) {
            .box-pagamento h4 {
                font-size: 1.8rem;
                text-align: left !important;
                margin-bottom: 1.5rem;
                opacity: .8;
            }
        }

    </style>
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-W3M5XVV');</script>
    <!-- End Google Tag Manager -->
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
                <div style="">
                    <div class="container" id="main-container">
                        <div class="heading text-center">
                            <h2 class="elementor-heading-title elementor-size-default" style="margin-bottom: 3rem;">Finalizar contratação</h2>
                            <div class="elementor-text-editor elementor-clearfix"></div>
                        </div>
                        <form action="#" method="POST" id="form-pagamento">
                            {{ csrf_field() }}
                            <input type="hidden" name="pets" value="{{ $pets }}">
                            <input type="hidden" name="amount" value="{{ $pets }}">
                            <input type="hidden" name="id_plano" value="{{ $plano->id }}">
                            <div class="row">
                                <div class="col-md-4" id="dados">
                                    <div class="box-pagamento box-shadow">
                                        <h4 class="text-center">Seus dados</h4>
                                        <div class="form-group">
                                            <label for="cep">Nome Completo:</label>
                                            <input type="text" class="form-control" id="name" value="{{ $nome }}" readonly="readonly" required="required" name="name" placeholder="">
                                        </div>
                                        <div class="form-group">
                                            <label for="email">Email:</label>
                                            <input type="email" class="form-control" value="{{ $email }}" id="email" readonly="readonly" required="required" name="email" placeholder="">
                                        </div>
                                        <div class="form-group">
                                            <label for="celular">Celular:</label>
                                            <input type="text" class="form-control" value="{{ $celular }}" id="celular" required="required" readonly="readonly" name="celular" placeholder="">
                                        </div>

                                        <div class="form-group">
                                            <label for="cpf">CPF:</label>
                                            <input type="text" class="form-control" id="cpf" value="" required="required" name="cpf" placeholder="">
                                        </div>
                                        <div class="form-group">
                                            <label for="cep">CEP:</label><div class="row">
                                                <div class="col-8">

                                                    <input type="text" class="form-control" id="cep" required="required" name="cep" value="" placeholder="" maxlength="8" onblur="loadCEPInfo()">
                                                </div>
                                                <div class="col-4">
                                                    <a href="javascript:;" onclick="loadCEPInfo()" class="btn btn-primary" id="loadCEPButton">
                                                        <i class="fa fa-search"></i>
                                                    </a>
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
                                                        <option value=""></option>
                                                        <option value="ES">ES</option>
                                                        <option value="SP">SP</option>
                                                        <option value="RO">RO</option>
                                                        <option value="AC">AC</option>
                                                        <option value="AM">AM</option>
                                                        <option value="RR">RR</option>
                                                        <option value="PA">PA</option>
                                                        <option value="AP">AP</option>
                                                        <option value="TO">TO</option>
                                                        <option value="MA">MA</option>
                                                        <option value="PI">PI</option>
                                                        <option value="CE">CE</option>
                                                        <option value="RN">RN</option>
                                                        <option value="PB">PB</option>
                                                        <option value="PE">PE</option>
                                                        <option value="AL">AL</option>
                                                        <option value="SE">SE</option>
                                                        <option value="BA">BA</option>
                                                        <option value="MG">MG</option>
                                                        <option value="RJ">RJ</option>
                                                        <option value="PR">PR</option>
                                                        <option value="SC">SC</option>
                                                        <option value="RS">RS</option>
                                                        <option value="MS">MS</option>
                                                        <option value="MT">MT</option>
                                                        <option value="GO">GO</option>
                                                        <option value="DF">DF</option>
                                                    </select>
                                                    <input type="hidden" id="ibge" name="ibge">
                                                    <!-- <input type="text" class="form-control" readonly="readonly" id="state" required="required" name="state" placeholder=""> -->
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <div class="form-group">
                                                    <label for="city">Cidade:</label>
                                                    <input type="text" class="form-control" id="city" required="required" value="" name="city" placeholder="">
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="form-group">
                                                    <label for="neighbourhood">Bairro:</label>
                                                    <input type="text" class="form-control" id="neighbourhood" required="required" value="" name="neighbourhood" placeholder="">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <div class="form-group">
                                                    <label for="street">Rua:</label>
                                                    <input type="text" class="form-control" id="street" required="required" value="" name="street" placeholder="">
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="form-group">
                                                    <label for="address_number">Número:</label>
                                                    <input type="text" class="form-control" id="address_number" required="required" name="address_number" value="" placeholder="">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4" id="pagamento1">
                                    <div class="box-pagamento box-shadow">
                                        <h4 class="text-center">Pagamento</h4>
                                        <div class="form-group hide-on-total-discount">
                                            <label for="cep">Forma de pagamento:</label>
                                            <input type="text" class="form-control" id="payment_type" required="required" name="payment_type" placeholder="" value="CARTÃO DE CRÉDITO" readonly="readonly">
                                        </div>
                                        <div class="form-group hide-on-total-discount">
                                            <label for="cpf" >Número do cartão:</label>
                                            <input type="text" class="form-control card-data" id="card_number" required="required" name="card_number" placeholder="">
                                        </div>
                                        <div class="form-group hide-on-total-discount" >
                                            <label for="brand">Bandeira:</label>
                                            <select required="required" name="brand" id="brand" class="form-control card-data">
                                                <option value="mastercard">MASTERCARD</option>
                                                <option value="visa">VISA</option>
                                                <option value="diners">DINERS</option>
                                                <option value="amex">AMEX</option>
                                                <option value="elo">ELO</option>
                                            </select>
                                        </div>
                                        <div class="form-group hide-on-total-discount">
                                            <label for="holder">Nome como escrito no cartão:</label>
                                            <input type="text" class="form-control card-data" id="holder" required="required" name="holder" placeholder="">
                                        </div>
                                        <div class="row  hide-on-total-discount">
                                            <div class="col">
                                                <div class="form-group">
                                                    <label for="expires_in">Validade:</label>
                                                    <input type="text" class="form-control card-data" id="expires_in" required="required" name="expires_in" placeholder="00/00">
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="form-group">
                                                    <label for="cvv">CVV:</label>
                                                    <input type="text" class="form-control card-data" id="ccv" name="ccv" required="required" placeholder="000" maxlength="4">
                                                </div>


                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <div class="form-group">

                                                    <label for="cvv">Cupom de desconto:</label>
                                                    <input type="text" class="form-control" id="cupom" name="cupom" placeholder="" style="text-transform: uppercase" value="{{ $cupom ? $cupom : '' }}">
                                                </div>

                                                <img src="https://lifepet.com.br/wp-content/uploads/2020/03/fundossl-300x72.png" alt="" width="175" height="42">
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-md-4" id="resumo">
                                    <div class="box-pagamento box-shadow">
                                        <input type="hidden" id="id_cupom" name="id_cupom">
                                        <input type="hidden" name="regime" value="{{ $regime }}">
                                        <h4 class="text-center">Resumo do pedido</h4>
                                        <table class="table" id="resume">
                                            <tbody>
                                            <tr>
                                                <td>
                                                    <span id="client_name">{{ $nome }}</span><br>
                                                    <small>{{ $plano->nome_plano }}</small><br>
                                                    <small class="badge badge-info">{{ $pets }} PET(S)</small><br>
                                                    <small>Regime: <span style="text-transform: lowercase;">{{ $regime }}</span> (pré-pago)</small>
                                                </td>
                                                <td id="preco_plano" data-original-value="{{ number_format($preco, 2, '.', '') }}">{{ \App\Helpers\Utils::money($preco) }}</td>
                                            </tr>
                                            @if($parcelas > 1)
                                            <tr>
                                                <td colspan="2">
                                                    <label for="parcelas">Parcelamento:</label>
                                                    <select name="parcelas" id="parcelas" class="form-control" data-quantity="{{ $parcelas }}">
                                                        @for($i = 0; $i < $parcelas; $i++)
                                                            <option value="{{ $i+1 }}">{{ $i+1 }}x de R$ {{ number_format($preco/($i+1.00), 2, ',', '.') }}</option>
                                                        @endfor
                                                    </select>
                                                </td>
                                            </tr>
                                            @endif
                                            </tbody>
                                        </table>
                                    </div>
                                    <button type="submit" id="submit-button" style="background-color: #ff0087; border: 0px;" class="btn btn-primary form-control text-center">Comprar</button>
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
    <script src="https://lifepet.com.br/wp-content/themes/lifepet2020/assets/js/jquery.mask.js"></script>
    <!-- Facebook Pixel Code -->
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)}(window, document,'script',
            'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '1066802027112521');
        fbq('track', 'PageView');
    </script>
    <noscript>
        <img height="1" width="1" style="display:none"
             src="https://www.facebook.com/tr?id=1066802027112521&ev=PageView&noscript=1"/>
    </noscript>
    <!-- End Facebook Pixel Code -->

    <script src="https://raw.githubusercontent.com/RobinHerbots/Inputmask/5.x/dist/jquery.inputmask.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css" />
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-85146807-1"></script>

    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-W3M5XVV"
                      height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'UA-85146807-1');
    </script>
    <script>
        window.gTagController = {
            models: {
                purchase: {
                    transaction_id: 13,
                    value: 19.9,
                    plano: {
                        id: 64,
                        nome: "Lifepet Para Todos 2021/1",
                        regime: "Mensal",
                        pets: 1,
                        valor: 19.9
                    }
                }
            },
            online: function() {
                if(typeof gtag !== 'function') {
                    console.warning("Não foi possível encontrar a função da gTag. Verifique os scripts.");
                    return false;
                }

                return true;
            },
            purchase: function(data) {
                if(!this.online()) {
                    console.warning('Evento de "compra" não registrado devido a não haver script de gTag instalado.');
                    return;
                }

                return gtag('event', 'purchase', {
                  "transaction_id": data.transaction_id,
                  "affiliation": "Lifepet - Para Todos",
                  "value": data.value,
                  "currency": "BRL",
                  "tax": 0,
                  "shipping": 0,
                  "items": [
                    {
                      "id": data.plano.id,
                      "name": data.plano.nome,
                      "list_name": "Planos",
                      "brand": "Lifepet",
                      "category": "Para Todos",
                      "variant": data.plano.regime,
                      "list_position": 1,
                      "quantity": data.pets,
                      "price": data.plano.valor
                    }
                  ]
               });
            }, 
            addToCart: function(data) {
                if(!this.online()) {
                    console.warning('Evento de "adição ao carrinho." não registrado devido a não haver script de gTag instalado.');
                    return;
                }

                return gtag('event', 'add_to_cart', {
                  "items": [
                    {
                      "id": data.plano.id,
                      "name": data.plano.nome,
                      "list_name": "Planos",
                      "brand": "Lifepet",
                      "category": "Para Todos",
                      "variant": data.plano.regime,
                      "list_position": 1,
                      "quantity": data.pets,
                      "price": data.plano.valor
                    }
                  ]
                });
            }
        };

        function clearUserData() {
            jQuery("#name").val('');
            jQuery("#email").val('');
            jQuery("#celular").val('');
            jQuery("#cpf").val('');
        }

        function clearAddress() {
            jQuery('#street').val('');
            jQuery('#address_number').val('');
            jQuery('#neighbourhood').val('');
            jQuery('#city').val('');
            jQuery('#state').val('');
            jQuery('#ibge').val('');
        }

        function clearCreditCard() {
            jQuery('#card_number').val('');
            jQuery('#brand').val('');
            jQuery('#holder').val('');
            jQuery('#expires_in').val('');
            jQuery('#ccv').val('');
        }

        function desabilitarCartao() {
            $('.card-data').prop('disabled', true);
            $('.hide-on-total-discount').fadeOut();
        }

        function loadCEPInfo() {
            jQuery('#loadCEPButton').addClass('disabled');
            jQuery('#loadCEPButton').html('...');

            jQuery.get('https://viacep.com.br/ws/'+jQuery('#cep').val()+'/json', function (data) {
                window.cep.loaded = true;
                window.cep.data = data;

                jQuery('#street').val(data.logradouro);
                jQuery('#neighbourhood').val(data.bairro);
                jQuery('#city').val(data.localidade);
                jQuery('#state').val(data.uf);
                jQuery("#ibge").val(data.ibge);
            }).fail(function() {
                window.cep.loaded = false;
                window.cep.data = null;

                clearAddress();

                swal({
                    type: 'info',
                    title: 'CEP não reconhecido',
                    text: "O CEP informado não correspondeu a nenhum endereço. Tente novamente.",
                    showConfirmButton: true,
                    confirmButtonText: 'Ok',
                    allowOutsideClick: false
                });
            }).always(function() {
                jQuery('#loadCEPButton').removeClass('disabled');
                jQuery('#loadCEPButton').html('<i class="fa fa-search"></i>');
            });
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

        function validarNome(nome) {
            if(nome === '' || nome == null) {
                return false;
            }

            if(nome.length <= 2) {
                return false
            }

            var nomes = nome.split(' ');
            if(nomes.length < 2) {
                return false;
            }

            return true;
        }

        function validarCupom($input) {
            if(!$input.val() || window.cupom.loaded) {
                return;
            }

            jQuery.ajax({
                url: '{!! route('lifepet-para-todos.codigos-promocionais.validar') !!}',
                method: 'GET',
                data: {
                    id_plano: {{ $plano->id }},
                    codigo: $input.val(),
                    regime: '{{ $regime }}'
                }
            }).done(function (data) {
                if(data.status) {
                    swal({
                        type: 'info',
                        title: 'Cupom ativado!',
                        text: "Aproveite essa chance de proteger seus pets! 😊💜",
                        showConfirmButton: true,
                        confirmButtonText: 'Ok',
                        allowOutsideClick: false
                    });
                    let $novoPreco = jQuery('#preco_plano').data('original-value');
                    if(data.tipo_desconto == 'percentual') {
                        $novoPreco = $novoPreco - ($novoPreco * data.desconto / 100);
                        if($novoPreco < 0) {
                            $novoPreco = 0;
                        }
                    } else {
                        $novoPreco = $novoPreco - data.desconto;
                        if($novoPreco < 0) {
                            $novoPreco = 0;
                        }
                    }
                    if($novoPreco == 0) {
                        desabilitarCartao();
                    }

                    jQuery('#id_cupom').val(data.id);
                    $novoPreco = parseFloat($novoPreco.toFixed(2));
                    //Criar parcelas:
                    var parcelamento = $('select#parcelas');
                    if(parcelamento.length) {
                        //Atualizar
                        parcelamento.find('option').remove();
                        let $parcelas = parseInt(parcelamento.data('quantity'));
                        for(i = 1; i <= $parcelas; i++) {
                            parcelamento.append($('<option>', {
                                value: i,
                                text: i + "x de " + parseFloat(($novoPreco / i).toFixed(2)).toLocaleString('pt-BR')
                            }));
                        }
                    }
                    $novoPreco = 'R$ ' + $novoPreco.toLocaleString('pt-BR');
                    jQuery('#preco_plano').html($novoPreco);
                    jQuery('#cupom').attr('readonly', 'readonly');
                    window.cupom.loaded = true;
                } else {
                    swal({
                        type: 'warning',
                        title: 'Oops!',
                        text: "O código informado é inválido.",
                        showConfirmButton: true,
                        confirmButtonText: 'Ok',
                        allowOutsideClick: false
                    });
                }

            })
                .fail(function() {
                    swal({
                        type: 'warning',
                        title: 'Oops!',
                        text: "O código informado é inválido.",
                        showConfirmButton: true,
                        confirmButtonText: 'Ok',
                        allowOutsideClick: false
                    });
                }).always(function() {

            });
        }

        jQuery(document).ready(function() {
            window.cep = {
                'number': null,
                'loaded': false,
                'data': null
            };

            window.cupom = {
                'loaded': false
            };

            var $cupom = jQuery('#cupom');
            if($cupom.val()) {
                validarCupom($cupom);
            }
            // jQuery('#name').blur(function(e) {
            //   var self = jQuery(this);
            //   var firstname = self.val().split(' ')[0];
            //   if(firstname.length > 0) {
            //     jQuery('#client_name').html(firstname);
            //     jQuery('#resume').show();
            //   } else {
            //     jQuery('#client_name').html('');
            //     jQuery('#resume').hide();
            //   }
            // });
            // 
            window.purchaseData = {
                transaction_id: null,
                value: {{ number_format($preco, 2, '', '.') }},
                plano: {
                    id: {{ $plano->id }},
                    nome: "{{ $plano->nome_plano }}",
                    regime: "{{ $regime }}",
                    pets: {{ $pets }},
                    valor: {{ number_format($preco, 2, '', '.') }}
                }
            }

            gTagController.addToCart(purchaseData);

            jQuery('#cpf').blur(function(e) {
                var $cpf = jQuery(this).val();
                if($cpf === '') {
                    return;
                }

                if(!cpf($cpf)) {
                    //CPF Inválido
                    swal({
                        type: 'warning',
                        title: 'CPF Inválido',
                        text: "O CPF inserido não é válido. Tente novamente.",
                        showConfirmButton: true,
                        confirmButtonText: 'Ok',
                        allowOutsideClick: false
                    });

                    jQuery(this).val('');
                    return false;
                }

                jQuery.ajax({
                    url: '{{ url('/') }}' + '/api/v1/assinaturas/cliente/'+$cpf,
                    dataType: 'json',
                    type: 'GET',
                    success: function(data) {
                        if(data.exists) {
                            swal({
                                type: 'info',
                                title: 'Informação',
                                text: "Você já é cadastrado em nosso sistema. Pedimos para que entre em contato com a nossa equipe de atendimento" +
                                    " para realizar seu cadastro com o Lifepet Para Todos!",
                                showConfirmButton: true,
                                confirmButtonText: 'Ok',
                                allowOutsideClick: false
                            });
                            setTimeout(function() {
                                window.location = "https://wa.me/551149502299";
                            }, 2000);
                        }
                        console.log(data);
                    },
                    error: function(data, textStatus) {
                        console.log(data);
                    },
                });
            });

            jQuery('#form-pagamento').submit(function(e) {
                e.preventDefault();
                const $form = $(this);

                grecaptcha.execute('{{ getenv('RECAPTCHA_SITE_KEY') }}', {action: 'assinatura_lpt'}).then(function(token) {
                    $form.prepend('<input type="hidden" name="recaptcha" value="' + token + '">');

                    let $submit = $('#submit-button');
                    $submit.html('Processando pagamento...');
                    $submit.attr('disabled','disabled');
                    //Verifica CPF novamente.
                    if(!cpf(jQuery('#cpf').val())) {
                        swal({
                            type: 'warning',
                            title: 'CPF Inválido',
                            text: "O CPF inserido não é válido. Tente novamente.",
                            showConfirmButton: true,
                            confirmButtonText: 'Ok',
                            allowOutsideClick: false
                        });
                        return false;
                    }

                    //Verifica se o nome está completo
                    if(!validarNome(jQuery('#name').val())) {
                        swal({
                            type: 'warning',
                            title: 'Nome incompleto',
                            text: "Precisamos que você preencha o seu nome completo. Tente novamente.",
                            showConfirmButton: true,
                            confirmButtonText: 'Ok',
                            allowOutsideClick: false
                        });
                        return false;
                    }

                    var $fields = $form.serialize();
                    jQuery.ajax({
                        url: '{{ url('/') }}' + '/api/v1/assinaturas/assinar',
                        dataType: 'json',
                        type: 'POST',
                        data: $fields,
                        beforeSend: function() {

                            let $submit = $('#submit-button');
                            $submit.html('Processando pagamento...');
                            $submit.attr('disabled','disabled');

                            let loader = "<div class=\"inner\" style=\"width: 145px; height: 145px; margin: 0 auto;\"><svg xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" style=\"margin:auto;background:#fff;display:block;\" width=\"145px\" height=\"145px\" viewBox=\"0 0 100 100\" preserveAspectRatio=\"xMidYMid\">\n" +
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
                        },
                        complete: function() {
                            jQuery("#submit-button").prop('disabled', false);
                            let $submit = $('#submit-button');
                            $submit.html('<i class="fas fa-lock"></i> Comprar agora');
                            $submit.removeAttr('disabled');
                            clearCreditCard();
                        },
                        success: function(data, textStatus) {
                            swal.close();
                            if(!data.status) {
                                swal({
                                    type: 'error',
                                    title: 'Erro',
                                    text: data.message,
                                    showConfirmButton: true,
                                    confirmButtonText: 'Ok',
                                    allowOutsideClick: false
                                }).then(function() {
                                    swal({
                                        type: 'info',
                                        title: 'Aguarde',
                                        text: 'Estamos recarregando seus dados para tentar novamente. Aguarde a atualização da página.',
                                        showConfirmButton: true,
                                        confirmButtonText: 'Ok',
                                        allowOutsideClick: false
                                    });

                                    setTimeout(function() {
                                        location.reload();
                                    }, 2000);
                                });
                            } else {
                                purchaseData.transaction_id = 'lpt_' + data.hash;
                                gTagController.purchase(purchaseData);

                                swal({
                                    type: 'success',
                                    title: 'Parabéns!',
                                    text: data.message,
                                    showConfirmButton: true,
                                    confirmButtonText: 'Ok',
                                    allowOutsideClick: false
                                }).then(function() {
                                    let end = '{{ $plano->id == 65 ? 'telemedicina-sucesso' : 'paratodos-sucesso' }}';
                                    window.location = 'https://lifepet.com.br/' + end + '/?hash=' + data.hash;
                                });
                            }
                            console.log(data);
                        },
                        error: function(data, textStatus) {
                            swal({
                                type: 'error',
                                title: 'Erro',
                                text: 'Encontramos um erro ao tentar concluir seu pagamento. Por favor tente novamente mais tarde.',
                                showConfirmButton: true,
                                confirmButtonText: 'Ok',
                                allowOutsideClick: false
                            }).then(function() {
                                swal({
                                    type: 'info',
                                    title: 'Aguarde',
                                    text: 'Estamos recarregando seus dados para tentar novamente. Aguarde a atualização da página.',
                                    showConfirmButton: true,
                                    confirmButtonText: 'Ok',
                                    allowOutsideClick: false
                                });

                                setTimeout(function() {
                                    location.reload();
                                }, 2000);
                            });

                            console.log(data);
                        },
                    });
                });
            });

            jQuery('#cupom').blur(function(e) {
                $input = jQuery(this);
                validarCupom($input);
            });

            jQuery.ajax({
                url: 'https://www.rdstation.com.br/api/1.2/conversions',
                method: 'POST',
                data: {
                    token_rdstation: "0eb70ce4d806faa1a1a23773e3d174d4",
                    identificador: 'lead_LPT',
                    plano: {{ $plano->id }},
                    email: '{{ $email }}',
                    nome: '{{ $nome }}',
                    celular: '{{ $celular }}'
                }
            });

            (function(){
                jQuery('#ccv').maskLife('0000');
                jQuery('#expires_in').maskLife('00/00');
                jQuery('#cep').maskLife('00000000');
                jQuery('#cpf').maskLife('00000000000');
                jQuery('#number').maskLife('0000000000000000');
            })();
        });
    </script>

    <script type="text/javascript">
        var getUrlParameter = function getUrlParameter(sParam) {
            var sPageURL = window.location.search.substring(1),
            sURLVariables = sPageURL.split('&'),
            sParameterName,
            i;

            for (i = 0; i < sURLVariables.length; i++) {
                sParameterName = sURLVariables[i].split('=');

                if (sParameterName[0] === sParam) {
                    return typeof sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
                }
            }
            return false;
        };

        jQuery('#parcelas option[value="12"]').attr("selected", "selected");

        var cupom4 = getUrlParameter('cupom');

        if(cupom4 === "1anofree") {
           jQuery('#pagamento1').hide();
           jQuery('#dados').addClass( "col-md-6" );
           jQuery('#resumo').addClass( "col-md-6" );
           jQuery("#submit-button").html("Aceitar convite");
        } else {
            cupomfinal="";
        }
    </script>
    <script src="https://h.online-metrix.net/fp/tags.js?org_id=k8vif92e&session_id=<?= $_SESSION['fingerprint_session'] ?>"></script>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-85146807-6"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'UA-85146807-6');
    </script>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-QGEFL8PGFE"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-QGEFL8PGFE');
    </script>
    <!-- Global site tag (gtag.js) - Google Ads: 869040570 -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=AW-869040570"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'AW-869040570');
    </script>
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)}(window, document,'script',
            'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '1066802027112521');
        fbq('track', 'PageView');
        fbq('track', 'Lead');
        fbq('track', 'InitiateCheckout');
    </script>
    <noscript>
        <img height="1" width="1" style="display:none"
               src="https://www.facebook.com/tr?id=1066802027112521&ev=PageView&noscript=1"/>
    </noscript>
@endsection