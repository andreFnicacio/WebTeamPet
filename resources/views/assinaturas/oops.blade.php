@extends('layouts.lifepet-para-todos')
@section('css')
    <style>
        #form-conclusao-cadastro {
            padding-bottom: 3rem;
        }
    </style>
    @parent
@endsection
@section('title')
    @parent
    Oops! Encontramos um problema!
@endsection
@section('content')
    <div class="container">
            <div class="content">
                <div class="jumbotron">
                    <h3>Não foi possível recuperar seus dados para o cadastro!</h3><br>
                    <h4>Na tentativa de obter seus dados encontramos algum problema que impossibilitou a continuidade. Entre em contato com nosso
                    suporte que iremos te ajudar! </h4><br><br>
                    <div class="table text-center">
                        <a href="https://lifepet.com.br/sac" class="btn btn-primary">Fale com nosso suporte</a>
                    </div>
                    {{--<p>Canais de Atendimento<br />Telefone | 4007-2441<br />Whatsapp | 99664-0065<br />E-mail | atendimento@lifepet.com.br</p>--}}
                </div>
            </div>
    </div>
@endsection