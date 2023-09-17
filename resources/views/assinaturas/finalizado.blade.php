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
    Assinatura finalizada!
@endsection
@section('content')
    <div class="container">
            <div class="content">
                <div class="jumbotron">
                    <h3>Seus dados já foram cadastrados com sucesso.<br><br> Agora é só usar seu plano!</h3>
                    <br>
                    <h4>Confira seu email. Em breve você receberá todos os detalhes de como usar nosso aplicativo.</h4>
                    <br>
                    <div class="table text-center">
                        <a href="https://lifepet.com.br" class="btn btn-primary">Voltar ao site</a>
                    </div>
                </div>
            </div>
    </div>
@endsection