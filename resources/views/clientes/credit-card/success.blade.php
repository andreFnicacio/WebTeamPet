@extends('layouts.lifepet-para-todos')

@section('css')
    @parent

@endsection

@section('content')
    <div class="container">
        <h1>Obrigado!</h1>
        <br>
        <p class="jumbotron">
            Tudo certo! 🐾<br>
            Acabamos de incluir o seu cartão. <br>
            Agora seus pagamentos poderão ser processados no novo método de pagamento para seu maior conforto. <br>
            <br>
            <br>
            Em caso de dúvidas entre em contato com nosso atendimento: <a href="mailto:atendimento@lifepet.com.br">atendimento@lifepet.com.br</a>
        </p>
    </div>
@endsection

@section('scripts')
    @parent
    <script src="https://cdnjs.cloudflare.com/ajax/libs/imask/3.4.0/imask.min.js"></script>
    <script src="https://lifepet.com.br/wp-content/themes/lifepet2020/assets/js/jquery.mask.js"></script>
@endsection
