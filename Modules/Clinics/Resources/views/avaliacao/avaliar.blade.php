<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1, maximum-scale=0.85">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <title>Lifepet Saúde -
        Avaliação de atendimento
    </title>
    <style>
        @font-face {
            font-family: 'Source Sans Pro';
            font-style: normal;
            font-weight: 400;
            font-display: swap;
            src: local('Source Sans Pro Regular'), local('SourceSansPro-Regular'), url(https://fonts.gstatic.com/s/sourcesanspro/v12/6xK3dSBYKcSV-LCoeQqfX1RYOo3qOK7g.ttf) format('truetype');
        }

        html {
            height: 100%;
        }

        body {
            background-color: #ffffff;
            background-image: url('https://www.lifepet.com.br/wp-content/uploads/2017/04/simule6.jpg');
            background-position: center center;
            background-repeat: no-repeat;
            padding-top: 25px;
            padding-right: 20px;
            padding-bottom: 85px;
            padding-left: 20px;
            margin-bottom: 0px;
            margin-top: 0px;
            -webkit-background-size: cover;
            -moz-background-size: cover;
            -o-background-size: cover;
            background-size: cover;
        }

        .box-avaliacao {
            max-width: 300px;
            width: 100%;
            margin: 0 auto 50px;
            font-family: "Source Sans Pro";
            text-align: center;
            border-radius: 20px;
            box-shadow: 0px 3px 4px 2px #d8d8d8;
        }

        .box-avaliacao .box-avaliacao strong {
            font-weight: 700;
        }

        .box-avaliacao div.img-wrapper {
            background: white;
            padding-top: 10px;
            border-radius: 20px 20px 0 0;
            padding-bottom: 10px;
            /* margin-bottom: -25px; */
        }

        .box-avaliacao div.avaliacao-wrapper {
            background: #009be3;
            padding-bottom: 20px;
            padding-left: 15px;
            padding-right: 15px;
            border-radius: 0px 0px 20px 20px;
            color: white;
        }

        .box-avaliacao img.logo {
            text-align: center;
            display: block;
            margin: 0 auto;
            max-width: 240px;
            width: 90%;
        }

        .box-avaliacao h1 {
            padding: 20px 0 10px;
            line-height: 0.9em;
        }

        .box-avaliacao h5 {
            font-size: 0.9rem;
        }

        .box-avaliacao ul.avaliacao-nota {
            list-style: none;
            padding-left: 0px;
            padding-bottom: 10px;
            padding-top: 10px;
        }

        .box-avaliacao ul.avaliacao-nota li {
            display: inline-block;
            width: 18%;
        }

        .box-avaliacao ul.avaliacao-nota li a {
            color: #ccc;
        }

        .box-avaliacao ul.avaliacao-nota li a i {
            font-size: 40px;
            /* letter-spacing: 18px; */
        }

        .box-avaliacao ul.avaliacao-nota li.selected a {
            color: #ffc100 !important;
        }

        .box-avaliacao ul.avaliacao-nota li.active a {
            color: #ffc100 !important;
        }

        .box-avaliacao ul.avaliacao-nota li.hover.active a {
            color: #ccc !important;
        }

        .box-avaliacao ul.avaliacao-nota li.hover.selected a {
            color: #ffc100 !important;
        }

        .box-avaliacao .comentario {
            /* width: 100%; */
            /* border: none; */
            resize: none;
            /* padding: 0; */
        }

        .box-avaliacao .btn_enviar {
            color: #009be3;
            background-color: #ffffff;
            font-weight: bold;
            letter-spacing: 1px;
        }

    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css">
</head>
<body>
<div class="box-avaliacao">
    <div class="img-wrapper">
        <img src="https://www.lifepet.com.br/wp-content/uploads/2019/07/logo.png" alt="" class="logo">
    </div>
    <div class="avaliacao-wrapper">
        @if($avaliacaoInvalida)
            <h1>Avaliação Indisponível</h1>
            <h5>
                A guia selecionada não foi liberada ou a guia selecionada já foi avaliada.
            </h5>
        @else
            <h1>Avaliação de atendimento</h1>
            <h5>
                Ajude-nos a melhorar e avalie o atendimento recebido.
                Como você avalia o atendimento do(a) prestador(a)
                <strong>{{ $guia->prestador->nome }}</strong>
                no dia
                @if($guia->tipo_atendimento == \Modules\Guides\Entities\HistoricoUso::TIPO_ENCAMINHAMENTO)
                    {{ (new \Carbon\Carbon())->parse($guia->realizado_em)->format('d/m/Y') }}
                @else
                    {{ (new \Carbon\Carbon())->parse($guia->created_at)->format('d/m/Y') }}
                @endif
                para o(a) pet
                <strong>{{ $guia->pet->nome_pet }}?</strong>
            </h5>
            <form action="{{ route('api.credenciados.avaliacao.avaliar') }}" id="form-avaliacao" method="POST">
                <input type="hidden" name="nota" id="nota" value="1">
                <input type="hidden" name="numero_guia" value="{{$guia->numero_guia}}">
                <ul class="avaliacao-nota">
                    <li class="nota" data-nota="1">
                        <a href="#"><i class="fa fa-star"></i></a>
                    </li>
                    <li class="nota" data-nota="2">
                        <a href="#"><i class="fa fa-star"></i></a>
                    </li>
                    <li class="nota" data-nota="3">
                        <a href="#"><i class="fa fa-star"></i></a>
                    </li>
                    <li class="nota" data-nota="4">
                        <a href="#"><i class="fa fa-star"></i></a>
                    </li>
                    <li class="nota" data-nota="5">
                        <a href="#"><i class="fa fa-star"></i></a>
                    </li>
                </ul>
                <div class="form-group">
                    <textarea name="comentario" class="comentario form-control" rows="3"
                              placeholder="Deixe suas observações sobre o atendimento"></textarea>
                </div>
                <div class="form-group">
                    <button class="btn btn_enviar form-control">Enviar</button>
                </div>
            </form>
        @endif

    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script>
    $(document).ready(function () {
        var notas = $('ul').find('li.nota');
        notas.hover(function (e) {
            $('ul').find('li.nota').addClass('hover');
            $(this).addClass('selected');
            $(this).prevAll().addClass('selected');
            $(this).nextAll().removeClass('selected');
        }, function (e) {
            $('ul').find('li.nota').removeClass('hover');
            $(this).removeClass('selected');
            $(this).prevAll().removeClass('selected');
        });

        notas.click(function (e) {
            // e.preventDefault();
            $('#form-avaliacao').find('input#nota').val($(this).data('nota'));

            // $(this).removeClass('selected');
            // $(this).prevAll().removeClass('selected');
            console.log($(this));
            $(this).addClass('active');
            $(this).prevAll().addClass('active');
            $(this).nextAll().removeClass('active');

            // $('#form-avaliacao').submit();
        })
    })
</script>
</body>
</html>