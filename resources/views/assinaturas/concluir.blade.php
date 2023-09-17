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
    Concluir Assinatura
@endsection
@section('content')
    <div class="container">

        @if(!$compraRapida->concluido)
        <form action="{{ route('api.assinaturas.salvar', ['hash' => $compraRapida->hash]) }}" method="POST" id="form-conclusao-cadastro">
            <input type="hidden" name="id_plano" value="{{ $compraRapida->id_plano }}">

            <div class="row">
                <div class="col-md-12">
                    <div class="box-cadastro-para-todos box-shadow">
                        <h4 class="text-center">Seus dados</h4>
                        <div class="form-group">
                            <div class="row">
                                <div class="col">
                                    <label for="name">Nome Completo:</label>
                                    <input type="text" class="form-control" readonly value="{{ $compraRapida->nome }}" id="name" required="required" name="name" placeholder="" minlength="1" oninput="removeWhiteSpace(this)">
                                </div>
                                <div class="col">
                                    <label for="email">Email:</label>
                                    <input type="email" class="form-control" readonly value="{{ $compraRapida->email }}" id="email" required="required" name="email" placeholder="">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col">
                                    <label for="cpf">CPF:</label>
                                    <input type="number" class="form-control" readonly id="cpf" value="{{ $compraRapida->cpf }}" required="required" name="cpf" placeholder="">
                                </div>
                                <div class="col">
                                    <label for="cep">CEP:</label>
                                    <input type="number" class="form-control" id="cep" value="{{ $compraRapida->cep }}" required="required" name="cep" placeholder="">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <div class="col">
                                    <label for="cpf">Data de nascimento:</label>
                                    <input type="date" class="form-control" id="birthdate" required="required" name="birthdate" placeholder="">
                                </div>
                                <div class="col">
                                    <label for="cep">Celular:</label>
                                    <input type="text" class="form-control" id="phone" value="{{ $compraRapida->celular }}" required="required" name="phone" placeholder="">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-6">
                                    <label for="sex">Sexo:</label>
                                    <select name="sex" id="sex" class="form-control">
                                        <option value="Masculino">Masculino</option>
                                        <option value="Feminino">Feminino</option>
                                        <option value="Outro">Outro</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="street">Rua:</label>
                                    <input type="text" class="form-control" readonly="readonly" value="{{ $compraRapida->rua }}"  id="street" required="required" name="street" placeholder="">
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="address_number">Número:</label>
                                    <input type="text" class="form-control" id="address_number" value="{{ $compraRapida->numero }}" required="required" name="address_number" placeholder="">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="neighbourhood">Bairro:</label>
                                    <input type="text" class="form-control" readonly="readonly" value="{{ $compraRapida->bairro }}" id="neighbourhood" required="required" name="neighbourhood" placeholder="">
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="city">Cidade:</label>
                                    <input type="text" class="form-control" readonly="readonly" value="{{ $compraRapida->cidade }}" id="city" required="required" name="city" placeholder="">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="state">Estado:</label>
                                    <input type="text" class="form-control" readonly="readonly" id="state" value="{{ $compraRapida->estado }}" required="required" name="state" placeholder="">
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="country">País:</label>
                                    <input type="text" class="form-control" readonly="readonly" id="country" value="Brasil" required="required" name="country" placeholder="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="box-cadastro-para-todos box-shadow">
                        <h4 class="text-center">Dados do(s) pet(s)</h4>
                        @for($i = 0; $i < $compraRapida->pets; $i++)
                        <h5>Pet {{ $i+1 }}:</h5>
                        <div class="form-group">
                            <div class="row">
                                <div class="col">
                                    <label for="name">Nome:</label>
                                    <input type="text" class="form-control" id="pets_{{$i}}_name" required="required" name="pets[{{$i}}][name]" placeholder="" minlength="1" oninput="removeWhiteSpace(this)">
                                </div>
                                <div class="col">
                                    <label for="email">Sexo:</label>
                                    <select name="pets[{{$i}}][sex]" id="pets_{{$i}}_sex" class="form-control">
                                        <option value="Macho">Macho</option>
                                        <option value="Fêmea">Fêmea</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-6">
                                    <label for="email">Espécie:</label>
                                    <select name="pets[{{$i}}][species]" id="pets_{{$i}}_species" class="form-control">
                                        <option value="Cão">Cão</option>
                                        <option value="Gato">Gato</option>
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <label for="email">Raça:</label>
                                    <select name="pets[{{$i}}][breed]" id="pets_{{$i}}_breed" class="form-control select2">
                                        @foreach($racas as $r)
                                            <option value="{{ $r->id }}">{{ $r->nome }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-6">
                                    <label for="cpf">Data de nascimento:</label>
                                    <input type="date" class="form-control" id="pets_{{$i}}_birthdate" required="required" name="pets[{{$i}}][birthdate]" placeholder="">
                                </div>
                            </div>
                        </div>
                        <br>
                        @endfor
                    </div>
                </div>
                <div class="col-md-4 offset-md-4">
                    <button type="submit" id="submit-button" class="btn btn-primary form-control text-center">Finalizar e começar a usar agora!</button>
                </div>
                <br><br>
            </div>
        </form>
        @else
            <div class="content" style="height: 50vh">
                <div class="jumbotron">
                    <h3>Seus dados já foram cadastrados com sucesso.<br> Agora é só usar seu plano!</h3>
                </div>
            </div>
        @endif
    </div>
@endsection

@section('scripts')
    <script src="{{ url('/') }}/assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2();
        });
        $("form").submit(function () {
            if ($(this).valid()) {
                $(this).submit(function () {
                    return false;
                });
                return true;
            }
            else {
                return false;
            }
        });

        function removeWhiteSpace(input) {
            if(/^\s/.test(input.value))
                input.value = '';
        }
    </script>
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
@endsection