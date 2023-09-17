@extends('layouts.lifepet-para-todos')
@section('css')
    <style>
        #form-conclusao-cadastro {
            padding-bottom: 3rem;
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
    <div class="container">
        <div class="content">
            <div class="jumbotron text-center">
                <h3>Seus dados já foram cadastrados com sucesso.<br><br> Estamos processando seu pagamento!</h3>
                <br>
                <div class="table text-center">
                    <a href="https://lifepet.com.br" class="btn btn-primary">Voltar ao site</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @parent
    <script src="{{ url('/') }}/assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
    <script src="https://lifepet.com.br/wp-content/themes/lifepet2020/assets/js/jquery.mask.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2();

            (function(){
                jQuery('#ccv').maskLife('000');
                jQuery('#expires_in').maskLife('00/00');
                jQuery('#cep').maskLife('00000000');
                jQuery('#cpf').maskLife('00000000000');
                jQuery('#card_number').maskLife('0000 0000 0000 0000');
            })();
        });
    </script>
@endsection