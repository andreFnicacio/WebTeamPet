<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Lifepet - Getnet - Checkout</title>
</head>
<body>
@php
    $order_id = \Carbon\Carbon::now()->format('YmdHis');
@endphp
<a class="pay-button-getnet">Clique para finalizar</a>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script async src="https://checkout.getnet.com.br/loader.js"
        data-getnet-sellerid="{{ $seller_id }}"
        data-getnet-token="{!! $token !!}"
        data-getnet-amount="5.00"
        data-getnet-customerid="{{ $order_id }}"
        data-getnet-orderid="{{ $order_id }}"
        data-getnet-button-class="pay-button-getnet"
        data-getnet-installments="1"
        data-getnet-customer-first-name="Alexandre"
        data-getnet-customer-last-name="Moreira da Silva"
        data-getnet-customer-document-type="CPF"
        data-getnet-customer-document-number="35232329880"
        data-getnet-customer-email="alexandre.moreira@vixgrupo.com.br"
        data-getnet-customer-phone-number="27998898763"
        data-getnet-customer-address-street="Rua Itapemirim"
        data-getnet-customer-address-street-number="80"
        data-getnet-customer-address-complementary="APT 804"
        data-getnet-customer-address-neighborhood="Praia de Itaparica"
        data-getnet-customer-address-city="Vila Velha"
        data-getnet-customer-address-state="ES"
        data-getnet-customer-address-zipcode="29102090"
        data-getnet-customer-country="Brasil"
        data-getnet-url-callback="https://site.lifepet.com.br?lpt_sucesso=true&order_id={{ $order_id }}"
        data-getnet-items='[{"name": "Plano Lifepet Para Todos #59","description": "Plano de saúde animal", "value": 5, "quantity": 1,"sku": ""}]'
        data-getnet-payment-methods-disabled='["debito", "debito-autenticado", "debito-nao-autenticado", "boleto"]'>
</script>
<script>
    $(document).ready(function() {
        $(".pay-button-getnet").mouseup(function() {
            setTimeout(function() {
                $('#getnet-checkout')[0].addEventListener('load', ev => {
                    // Funções compatíveis com IE e outros navegadores
                    var eventMethod = (window.addEventListener ? 'addEventListener' : 'attachEvent');
                    var eventer = window[eventMethod];
                    var messageEvent = (eventMethod === 'attachEvent') ? 'onmessage' : 'message';

                    // Ouvindo o evento do loader
                    eventer(messageEvent, function iframeMessage(e) {
                        var data = e.data || '';

                        switch (data.status || data) {
                            // Corfirmação positiva do checkout.
                            case 'success':
                                console.log('Transação realizada.');
                                break;

                            // Notificação de que o IFrame de checkout foi fechado a aprovação.
                            case 'close':
                                console.log('Checkout fechado.');
                                break;

                            // Notificação que um boleto foi registrado com sucesso
                            case 'pending':
                                console.log('Boleto registrado e pendente de pagamento');
                                console.log(data.detail);
                                break;

                            // Notificação que houve um erro na tentativa de pagamento
                            case 'error':
                                console.warn(data.detail.message);
                                break;

                            // Ignora qualquer outra mensagem
                            default:
                                break;
                        }
                    }, false);
                });
            }, 1000);
        });
    });
</script>
</body>
</html>