<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, user-scalable=yes, initial-scale=1">

    <title>Proposta - {{ $dados_proposta->cliente->dados->nome_cliente }}</title>

    <!-- Normalize or reset CSS with your favorite library -->
    <link rel="stylesheet" href="{{ asset('_app_cadastro_cliente/proposta/inc/normalize.css') }}">

    <!-- Load paper.css for happy printing -->
    <link rel="stylesheet" href="{{ asset('_app_cadastro_cliente/proposta/inc/paper.css') }}">

    <link href="{{ asset('_app_cadastro_cliente/proposta/inc/css.css') }}" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" type="text/css">

{{--    <script src="{{ asset('_app_cadastro_cliente/proposta/inc/jquery-1.js') }}"></script>--}}
    {{--<script src="{{ asset('_app_cadastro_cliente/proposta/inc/jspdf.js') }}"></script>--}}

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.5/jspdf.debug.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script src="https://unpkg.com/sweetalert2@7.18.0/dist/sweetalert2.all.js"></script>

    <link rel="icon" href="{{ asset('_app_cadastro_cliente/proposta/img/logo-azul.jpg') }}" />
    <link rel="shortcut icon" href="{{ asset('_app_cadastro_cliente/proposta/img/logo-azul.jpg') }}" />

    <style>
        @media print {
            * {
                /*-webkit-print-color-adjust: exact !important;*/
            }

            body {
                /*-webkit-print-color-adjust: exact !important;*/
                color: #197db8;
            }
        }

        @page {
            size: A4 portrait;
            /*-webkit-print-color-adjust: exact;*/
        }

        * {
            -webkit-print-color-adjust: exact !important;   /* Chrome, Safari */
            color-adjust: exact !important;                 /*Firefox*/
        }

        body {
            background-color: #FFF;
        }

        .nome-assinatura {
            height: 30px;
            display: block;
            margin-top: 3px;
        }

        .page-break {
            page-break-after: always;
        }

        .a25 {
            width: 25%;
        }

        .a33 {
            width: 33%;
        }

        .a40 {
            width: 40%;
        }

        .a65 {
            width: 65%;
        }

        .a60 {
            width: 60%;
        }

        .a50 {
            width: 50%;
        }

        .a20 {
            width: 20%;
        }

        .a100 {
            width: 100%;
        }

        .dadosgerais {
            margin-top: 11px;
        }

        .dadosgerais td {
            padding: 5px 10px 5px 5px;
            border-bottom: 5px solid #fff !important;
        }

        li {
            color: #197db8;
            font-size: 14px;
            margin-bottom: 1px;
        }

        .titulo {
            font-size: 15px;
            color: #197db8;
            padding-right: 3px;
            text-align: right;
        }

        .assinatura {
            font-size: 15px;
            color: #197db8;
            vertical-align: bottom;
            text-align: center;
        }

        .texto {
            font-size: 15px;
            color: #666;
            font-weight: bold;
            background-color: #e1e8f399;
            border-radius: 10px;
        }

        p {
            font-weight: 900 !important;
            margin-bottom: 0px;
        }

        ul.check li {
            list-style: none;
            padding-left: 0px;
        }

        ul.check2 li {
            margin-bottom: 5px;
        }

        .doencaspet li {
            font-size: 12px;
        }
    </style>
    <style type="text/css">
        /*-webkit-print-color-adjust: exact;*/
        .tg {
            border-collapse: collapse;
            border-spacing: 0;
        }

        .tg .tg-yw4l {
            vertical-align: top
        }

        p {
            font-size: 14px !important;
            font-weight: 200;
        }

        .backcinza {
            background-color: #ededed;
            font-size: 20px;
            border: 1px solid #777;
            padding: 10px;
        }

        /*i {*/
            /*padding: 10px;*/
            /*background-color: #ededed;*/
            /*border-radius: 50px;*/
            /*display: block;*/
            /*margin-right: 5px;*/
        /*}*/

    </style>

    <style>
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #fff;
            opacity: 0.8;
            z-index: 9999;
        }

        .loading-overlay .spin-loader {
            height: 100px;
            margin: 50% auto 30px;
            background: url({{ asset('_app_cadastro_cliente/images/loader.gif') }}) no-repeat center center transparent;
            top: 25%;
        }

        .loading-overlay h2 {
            color: #000;
            height: 100px;
            text-align: center;
            font-size: 40px;
        }

        .btn-generatePdf {
            background-color: #f98403;
            color: white;
            text-align: center;
            display: block;
            margin: 30px auto 100px;
            padding: 15px 50px;
            border: 0;
            border-radius: 2px;
            box-shadow: 1px 1px 3px 0px black;
            width: 50%;
        }

        .aceite{
            /*margin: 0 auto 15px;*/
            text-align: center;
            width: 100%;
            position: fixed;
            bottom: 0;
        }
        .aceite .btn {
            background-color: rgb(68, 182, 174);
            color: #FFF;
            border: 0;
            padding: 25px 0;
            width: 100%;
            white-space: nowrap;
        }
        .aceite .btn p {
            font-size: 25px !important;
            margin: 10px 0;
        }
        .separador-aceite {
            margin-bottom: 100px;
        }
        #modal-aceite .btn-submit {
            background-color: rgb(68, 182, 174);
            color: #FFF;
            border: 0;
            padding: 25px 0;
            width: 100%;
            white-space: nowrap;
        }
        .wrapper-signature {
            margin: 0px auto 50px;
            width: 700px;
            height: 250px;
            -moz-user-select: none;
            -webkit-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
        .wrapper-signature .signature-pad {
            background-color: white;
            border: 1px solid black;
        }

        .wrapper-signature .btn {
            display: inline-block;
            margin: 0px 15px 0 0;
            padding: 10px 25px;
            cursor: pointer;
            color: #FFF !important;
            background-color: rgb(68, 182, 174);
        }
        #modal-aceite .btn.disabled {
            pointer-events: none;
            background-color: grey;
        }

        .box_anexos {
            padding: 40px 0;
        }

        .box_anexos label {

        }
        .box_anexos label#anexos {

        }
    </style>

    {{-- Bootstrap => Grid / Modal --}}
    <style>
        .checkbox, .radio {
            position: relative;
            display: block;
            margin-top: 10px;
            margin-bottom: 10px;
        }
        .checkbox label, .radio label {
            min-height: 20px;
            padding-left: 20px;
            font-weight: 400;
            cursor: pointer;
            display: inline-block;
            max-width: 100%;
            margin-bottom: 5px;
            text-align: left;
        }
        .checkbox input[type=checkbox], .checkbox-inline input[type=checkbox], .radio input[type=radio], .radio-inline input[type=radio] {
            position: absolute;
            margin-top: 4px\9;
            margin-left: -20px;
        }

        input[type=checkbox], input[type=radio] {
            margin: 4px 0 0;
            margin-top: 1px\9;
            line-height: normal;
        }
        .modal-open {
            overflow: hidden;
        }
        .modal {
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            z-index: 1050;
            display: none;
            overflow: hidden;
            -webkit-overflow-scrolling: touch;
            outline: 0;
        }
        .modal.fade .modal-dialog {
            -webkit-transform: translate(0, -25%);
            -ms-transform: translate(0, -25%);
            -o-transform: translate(0, -25%);
            transform: translate(0, -25%);
            -webkit-transition: -webkit-transform 0.3s ease-out;
            -o-transition: -o-transform 0.3s ease-out;
            transition: transform 0.3s ease-out;
        }
        .modal.in .modal-dialog {
            -webkit-transform: translate(0, 0);
            -ms-transform: translate(0, 0);
            -o-transform: translate(0, 0);
            transform: translate(0, 0);
        }
        .modal-open .modal {
            overflow-x: hidden;
            overflow-y: auto;
        }
        .modal-dialog {
            position: relative;
            width: auto;
            margin: 10px auto;
        }
        .modal-content {
            position: relative;
            background-color: #ffffff;
            -webkit-background-clip: padding-box;
            background-clip: padding-box;
            border: 1px solid #999999;
            border: 1px solid rgba(0, 0, 0, 0.2);
            border-radius: 6px;
            -webkit-box-shadow: 0 3px 9px rgba(0, 0, 0, 0.5);
            box-shadow: 0 3px 9px rgba(0, 0, 0, 0.5);
            outline: 0;
        }
        .modal-backdrop {
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            z-index: 1040;
            background-color: #000000;
        }
        .modal-backdrop.fade {
            filter: alpha(opacity=0);
            opacity: 0;
        }
        .modal-backdrop.in {
            filter: alpha(opacity=50);
            opacity: 0.5;
        }
        .modal-header {
            padding: 15px;
            border-bottom: 1px solid #e5e5e5;
        }
        .modal-header .close {
            margin-top: -2px;
        }
        .modal-title {
            margin: 0;
            line-height: 1.42857143;
        }
        .modal-body {
            position: relative;
            padding: 15px;
        }
        .modal-footer {
            padding: 15px;
            text-align: right;
            border-top: 1px solid #e5e5e5;
        }
        .modal-footer .btn + .btn {
            margin-bottom: 0;
            margin-left: 5px;
        }
        .modal-footer .btn-group .btn + .btn {
            margin-left: -1px;
        }
        .modal-footer .btn-block + .btn-block {
            margin-left: 0;
        }
        .modal-scrollbar-measure {

            position: absolute;
            top: -9999px;
            width: 50px;
            height: 50px;
            overflow: scroll;
        }
        /* Mobile */
        @media (max-width: 425px) {
            .aceite .btn p {
                font-size: 25px !important;
                margin: 40px 0;
            }
            .separador-aceite {
                margin-bottom: 150px;
            }
            #modal-aceite .checkbox input {
                height: 30px;
                width: 30px;
                margin-left: -40px;
            }
            #modal-aceite .checkbox label {
                font-size: 30px;
                padding-left: 40px;
            }
            #modal-aceite .btn-submit {
                font-size: 25px;
            }
            #modal-aceite .modal-header {
                font-size: 35px;
            }
            .wrapper-signature .btn {
                font-size: 25px;
            }
        }
        @media (min-width: 768px) {
            /*.modal-dialog {*/
                /*width: 600px;*/
                /*margin: 30px auto;*/
            /*}*/
            .modal-content {
                -webkit-box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
            }
            .modal-sm {
                width: 300px;
            }
            .aceite .btn p {
                font-size: 25px !important;
                margin: 10px 0;
            }
        }
        @media (min-width: 992px) {
            .modal-lg {
                width: 900px;
            }
        }
    </style>
</head>

<!-- Set "A5", "A4" or "A3" for class name -->
<!-- Set also "landscape" if you need -->
<body class="A4 portrait login" style="">

    <div class="loading-overlay" style="display: none;">
        <div class="spin-loader"></div>
        <h2 style="color: black;">
            Gerando a proposta...
            <br><br>
            Aguarde um instante!
        </h2>
    </div>

    <div id="content">

        @php
            $i = 1;
        @endphp

        <!-- Each sheet element should have the class "sheet" -->
        <!-- "padding-**mm" is optional: you can set 10, 15, 20 or 25 -->
        <div class="page-break" data-page="{{ $i }}">
            <div class="sheet" style=" margin:0 auto; padding: 9mm; ">

                <!-- Write HTML just like a web page -->
                <div style="border: 0px solid #666;  padding: 1mm 1mm; height: 100%;">
                    <div class="topolife row">

                        <table class="" style="width: 100%;" border="0">
                            <tbody>
                            <tr style="border-bottom: 0px solid #c1c1c1;">
                                <th class="a40" style="text-align:left;"><img src="{{ asset('_app_cadastro_cliente/proposta/img/logo.jpg') }}" width="250"></th>
                                <th class="a20"></th>
                                <th class="a40"
                                    style="text-align: left;padding: 5px 15px !important; background-color:#009bf5;">
                                    <span style="color:#fff; text-transform: uppercase; width:55%; float:left; display:table; ">Contrato nº: </span><span
                                            class=""
                                            style="background-color:#fff; width:45%; display:table; text-align:center; padding:5px;">{{ $idCliente }}</span>
                                </th>

                            </tr>

                            </tbody>
                        </table>

                        <table class="" style="width: 100%; MARGIN-TOP:10PX;" border="0">
                            <tbody>
                            <tr style="border-bottom: 0px solid #c1c1c1;">

                                <th class="a100"
                                    style="text-align: center; font-size: 23px; padding: 10px 15px !important; background-color:#009bf5;">
                                    <span style="color:#fff; text-align: center;  text-transform: uppercase; ">PROPOSTA DE ADESÃO </span>
                                </th>

                            </tr>

                            </tbody>
                        </table>

                        <table class="" style="width: 100%; MARGIN-TOP:10PX;" border="0">
                            <tbody>
                            <tr style="border-bottom: 0px solid #c1c1c1;">

                                <th class="a100"
                                    style="text-align: left; font-size: 18px; padding: 10px 0px  10px 0px !important; border-bottom:1px solid #197db8;">
                                    <span style="color:#197db8; text-align: left;  text-transform: uppercase; ">DADOS DO CONTRATANTE </span>
                                </th>

                            </tr>

                            </tbody>
                        </table>

                        <table style="width: 100%;" class="dadosgerais">
                            <tbody>
                            <tr>
                                <td style="width: 10%; text-align: right;" class="titulo">Contratante:</td>
                                <td style="width: 87%;" colspan="3" class="texto" >{{ $dados_proposta->cliente->dados->nome_cliente }}</td>
                            </tr>
                            <tr>
                                <td style="width: 10%; text-align: right;" class="titulo">E-mail:</td>
                                <td style="width: 87%;" colspan="3" class="texto" >{{ $dados_proposta->cliente->dados->email }}</td>
                            </tr>
                            <tr>
                                <td style="width:  10%; text-align: right;" class="titulo">CPF:</td>
                                <td style="width: 30%;" class="texto" >{{ $dados_proposta->cliente->dados->cpf }}</td>
                                <td style="width: 20%; text-align: right;" class="titulo">RG:</td>
                                <td style="width: 30%;" class="texto" >{{ $dados_proposta->cliente->dados->rg }}</td>
                            </tr>
                            <tr>
                                <td style="width:  10%; text-align: right;" class="titulo">Sexo:</td>
                                <td style="width: 30%;" class="texto" >{{ $dados_proposta->cliente->dados->sexo == 'M' ? 'Masculino' : 'Feminino' }}</td>
                                <td style="width: 20%; text-align: right;" class="titulo">Data de Nasc.:</td>
                                <td style="width: 30%;" class="texto" >{{ \Carbon\Carbon::createFromFormat('d/m/Y', $dados_proposta->cliente->dados->data_nascimento)->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <td style="width:  10%; text-align: right;" class="titulo">Celular:</td>
                                <td style="width: 30%;" class="texto" >{{ $dados_proposta->cliente->dados->celular }}</td>
                                <td style="width: 20%; text-align: right;" class="titulo">Telefone Fixo:</td>
                                <td style="width: 30%;" class="texto" >{{ $dados_proposta->cliente->dados->telefone_fixo }}</td>
                            </tr>
                            </tbody>
                        </table>

                        <table style="width: 100%;" class="dadosgerais">
                            <tbody>
                            <tr>
                                <td style="width: 13.5%; text-align: right;" class="titulo">Endereço:</td>
                                <td style="width: 87%;" colspan="3" class="texto" >{{ $dados_proposta->cliente->endereco->rua }}</td>
                            </tr>

                            <tr>
                                <td style="width:  10%; text-align: right;" class="titulo">Número:</td>
                                <td style="width: 30%;" class="texto" >{{ $dados_proposta->cliente->endereco->numero_endereco }}</td>
                                <td style="width: 20%; text-align: right;" class="titulo">Complemento:</td>
                                <td style="width: 30%;" class="texto" >{{ $dados_proposta->cliente->endereco->complemento_endereco }}</td>
                            </tr>
                            <tr>
                                <td style="width:  10%; text-align: right;" class="titulo">Cidade/UF:</td>
                                <td style="width: 30%;" class="texto" >{{ $dados_proposta->cliente->endereco->cidade }}/{{ $dados_proposta->cliente->endereco->estado }}</td>
                                <td style="width: 20%; text-align: right;" class="titulo">CEP:</td>
                                <td style="width: 30%;" class="texto" >{{ $dados_proposta->cliente->endereco->cep }}</td>
                            </tr>
                            </tbody>
                        </table>

                        <table style="width: 100%;" class="dadosgerais">
                            <tbody>
                            <tr>
                                <td style="width:  13.5%; text-align: right;" class="titulo">Obs.:</td>
                                <td style="width: 80%;" class="texto">{{ $dados_proposta->cliente->dados->observacoes }}</td>
                            </tr>
                            </tbody>
                        </table>

                        <table class="" style="width: 100%; MARGIN-TOP:15PX;" border="0">
                            <tbody>
                            <tr style="border-bottom: 0px solid #c1c1c1;">

                                <th class="a100"
                                    style="text-align: left; font-size: 18px; padding: 10px 0px  10px 0px !important; border-bottom:1px solid #197db8;">
                                    <span style="color:#197db8; text-align: left;  text-transform: uppercase; ">Dados de pagamento </span>
                                </th>

                            </tr>

                            </tbody>
                        </table>

                        <table style="width: 100%;" class="dadosgerais">
                            <tbody>
                            @php
                                $formaPagamento = isset($dados_proposta->cliente->forma_pagamento) ? $dados_proposta->cliente->forma_pagamento : '';

                                if(isset($dados_proposta->cliente->dados->forma_pagamento)) {
                                    $formaPagamento = $dados_proposta->cliente->dados->forma_pagamento != 'boleto' ? 'Cartão' : 'Boleto';
                                }

                            @endphp

                            <tr>
                                <td style="width:  12%; text-align: right;" class="titulo">Data venc.:</td>
                                <td style="width: 25%;" class="texto" > {{ $dados_proposta->cliente->dados->vencimento }}</td>
                                <td style="width: 22%; text-align: right;" class="titulo">Forma de pagamento:</td>
                                <td style="width: 25%;" class="texto" > {{ $formaPagamento }}</td>
                            </tr>
                            </tbody>
                        </table>

                        <ul>
                            <li>
                                A contratação só é válida após a entrega legível dos seguintes documentos: i) Documento oficial
                                com foto (RG e
                                CPF); ii) Comprovante de residência (últimos 60 dias); iii) Carteira de vacinação atualizada.
                            </li>
                            <li>
                                Reajuste Contratual: de acordo com o contrato.
                            </li>
                            <li>
                                Área de Abrangência: Estadual ou Nacional (conforme modalidade do plano).
                            </li>
                        </ul>
                        <ul class="check">
                            <li>
                                <img src="{{ $iconCheckbox }}" width="18">
                                Reconheço que sou responsável por todas as informações declaradas.
                            </li>
                            <li>
                                <img src="{{ $iconCheckbox }}" width="18">
                                Declaro que estou ciente e de acordo com a Tabela de Cobertura e Carências e com o Contrato do Cliente.
                            </li>
                        </ul>
                        <ul>
                            <li>
                                Data de Vigência: conforme assinatura da proposta de adesão.
                            </li>
                            <li>
                                Ao assinar o presente contrato confirmo estar de acordo com o
                                "Contrato do Cliente" e "Guia do Cliente", apresentados no momento da
                                assinatura, disponível no site www.lifepet.com.br/contrato e que
                            </li>
                        </ul>

                        <table style="width: 100%;" class="dadosgerais">
                            <tbody>


                            </tbody>
                        </table>
                        <table style="width: 100%;" class="dadosgerais">
                            <tbody>

                            <tr>
                                <td style="width: 100%; text-align:left !important;padding-left: 40px;" colspan="2" class="titulo">
                                    Vila Velha,
                                    {{ \Carbon\Carbon::parse($dados_proposta->data_proposta)->day }} de
                                    {{ \App\Helpers\Utils::getMonthName(\Carbon\Carbon::parse($dados_proposta->data_proposta)->month) }} de
                                    {{ \Carbon\Carbon::parse($dados_proposta->data_proposta)->year }}
                                </td>
                            </tr>

                            @if($aceite)
                                <tr>
                                    <td width="50%" rowspan="3" height="100" class="assinatura">
                                        <b class="nome-assinatura" style="text-transform: capitalize;">{{ strtolower($dados_proposta->cliente->dados->nome_cliente) }}</b>
                                        Contratante
                                    </td>
                                    <td width="50%" rowspan="3" height="100" class="assinatura">
                                        <b class="nome-assinatura" style="text-transform: capitalize;">Lifepet Brasil - Plano de Saúde S/A</b>
                                        Contratada
                                    </td>
                                </tr>
                            @endif

                            </tbody>
                        </table>

                        <table class="" style="width: 100%; MARGIN-TOP:15PX;" border="0">
                            <tbody>
                            <tr style="border-bottom: 0px solid #c1c1c1;">

                                <th class="a100"
                                    style="text-align: center; font-size: 10px; padding: 10px 15px !important; background-color: #009bf5; position: absolute;  bottom: 20px; left: 0px;">
                                    <span style="color:#fff; text-align: center;  text-transform: uppercase; ">Lifepet: (27) 4007-2441 | atendimento@lifepet.com.br | www.lifepet.com.br  </span>
                                </th>

                            </tr>

                            </tbody>
                        </table>

                    </div>
                </div>

            </div>
        </div>

        @foreach($dados_proposta->pets as $key => $pet)
        <div class="page-break" data-page="{{ $i+1 }}">
            <div class="sheet" style=" margin:0 auto; padding: 9mm; ">

                <!-- Write HTML just like a web page -->
                <div style="border: 0px solid #666;  padding: 1mm 1mm; height: 100%;">
                    <div class="topolife row">

                        <table class="" style="width: 100%;" border="0">
                            <tbody>
                            <tr style="border-bottom: 0px solid #c1c1c1;">
                                <th class="a40" style="text-align:left;"><img src="{{ asset('_app_cadastro_cliente/proposta/img/logo.jpg') }}" width="250"></th>
                                <th class="a20"></th>
                                <th class="a40"
                                    style="text-align: left;padding: 5px 15px !important; background-color:#009bf5;">
                                    <span style="color:#fff; text-transform: uppercase; width:55%; float:left; display:table; ">Contrato nº: </span>
                                    <span class="" style="background-color:#fff; width:45%; display:table; text-align:center; padding:5px;">{{ $idCliente }}</span>
                                </th>

                            </tr>

                            </tbody>
                        </table>


                        <table class="" style="width: 100%; MARGIN-TOP:10PX;" border="0">
                            <tbody>
                            <tr style="border-bottom: 0px solid #c1c1c1;">

                                <th class="a100"
                                    style="text-align: left; font-size: 18px; padding: 10px 0px  10px 0px !important; border-bottom:1px solid #197db8;">
                                    <span style="color:#197db8; text-align: left;  text-transform: uppercase; ">DADOS DO PET {{ $i }} </span>
                                </th>

                            </tr>

                            </tbody>
                        </table>

                        <table style="width: 100%;" class="dadosgerais">
                            <tbody>

                            <tr>
                                <td style="width: 20%; text-align: right;" class="titulo">Nome:</td>
                                <td style="width: 60%;" class="texto" >{{ $pet->pet->nome_pet }}</td>
                            </tr>
                            <tr>
                                <td style="width: 20%; text-align: right;" class="titulo">Tipo:</td>
                                <td style="width: 60%;" class="texto" >{{ $pet->pet->tipo }}</td>
                            </tr>
                            <tr>
                                <td style="width: 20%; text-align: right;" class="titulo">Raça:</td>
                                <td style="width: 60%;" class="texto" >{{ (new \App\Models\Raca)->find($pet->pet->id_raca)->nome }}</td>
                            </tr>

                            <tr>
                                <td style="width: 20%; text-align: right;" class="titulo">Sexo:</td>
                                <td style="width: 60%;" class="texto" >{{ $pet->pet->sexo == 'M' ? 'Masculino' : 'Feminino' }}</td>
                            </tr>

                            <tr>
                                <td style="width: 20%; text-align: right;" class="titulo">Data de Nasc.:</td>
                                <td style="width: 60%;" class="texto" >{{ \Carbon\Carbon::createFromFormat('d/m/Y', $pet->pet->data_nascimento)->format('d/m/Y') }}</td>
                            </tr>

                            </tbody>
                        </table>

                        <table class="" style="width: 100%; MARGIN-TOP:15PX;" border="0">
                            <tbody>
                            <tr style="border-bottom: 0px solid #c1c1c1;">

                                <th class="a100"
                                    style="text-align: left; font-size: 18px; padding: 10px 0px  10px 0px !important; border-bottom:1px solid #197db8;">
                                    <span style="color:#197db8; text-align: left;  text-transform: uppercase; ">Dados do plano - Pet {{ $i++ }}</span>
                                </th>

                            </tr>

                            </tbody>
                        </table>

                        <table style="width: 100%;" class="dadosgerais">
                            <tbody>
                            <tr>
                                <td style="width: 10%; text-align: right;" class="titulo">Plano:</td>
                                <td style="width: 87%;" colspan="3" class="texto" >{{ (new \App\Models\Planos)->find($pet->plano->id_plano)->nome_plano }}</td>
                            </tr>

                            <tr>
                                <td style="width:  25%; text-align: right;" class="titulo">Taxa de Adesão:</td>
                                <td style="width: 25%;" class="texto" >{{ \App\Helpers\Utils::money(\App\Helpers\Utils::moneyReverse($pet->plano->valor_adesao)) }}</td>
                                <td style="width: 25%; text-align: right;" class="titulo">Valor:</td>
                                <td style="width: 25%;" class="texto" > {{ \App\Helpers\Utils::money(\App\Helpers\Utils::moneyReverse($pet->plano->valor_plano)) }}</td>
                            </tr>
                            <tr>
                                <td style="width:  25%; text-align: right;" class="titulo">Data venc.:</td>
                                <td style="width: 25%;" class="texto" > {{ $dados_proposta->cliente->dados->vencimento }}</td>
                                <td style="width: 25%; text-align: right;" class="titulo">Forma de pagamento:</td>

                                @php
                                    $formaPagamento = isset($dados_proposta->cliente->forma_pagamento) ? $dados_proposta->cliente->forma_pagamento : '';

                                    if(isset($dados_proposta->cliente->dados->forma_pagamento)) {
                                        $formaPagamento = $dados_proposta->cliente->dados->forma_pagamento != 'boleto' ? 'Cartão' : 'Boleto';
                                    }

                                @endphp
                                
                                <td style="width: 25%;" class="texto" > {{ $formaPagamento }}</td>
                            </tr>
                            <tr>
                                <td style="width:  25%; text-align: right;" class="titulo">Frequência:</td>
                                <td style="width: 25%;" class="texto" > {{ $pet->plano->regime }}</td>
                                <td style="width: 25%; text-align: right;" class="titulo">Modalidade:</td>
                                <td style="width: 25%;" class="texto" > {{ $pet->plano->participativo == 1 ? 'Participativo' : 'Integral' }}</td>
                            </tr>
                            </tbody>
                        </table>

                        <table style="width: 100%;" class="dadosgerais">
                            <tbody>
                            <tr>
                                <td style="width:  10%; text-align: right;" class="titulo">Observações/descontos:</td>
                                <td style="width: 80%;" class="texto" >{{ $pet->pet->observacoes }}</td>
                            </tr>
                            </tbody>
                        </table>

                        <ul class="doencaspet">
                            @foreach($dados_proposta->doencas_pre_existentes as $doenca)
                                @php
                                if(isset($doenca->pets)) {
                                    $doenca->pets = json_decode(json_encode($doenca->pets, true), true);
                                }
                                @endphp
                                <li>
                                    {{ $doenca->doenca }} <b>{{ isset($doenca->pets) && isset($doenca->pets[$key]) && isset($doenca->pets[$key]['possui']) ? 'Sim' : 'Não' }} {{ isset($doenca->pets[$key]['descricao']) ? ' - ' . $doenca->pets[$key]['descricao'] : '' }}</b>
                                </li>
                            @endforeach
                        </ul>


                        <ul class="check doencaspet" style="margin-left:-10px;">
                            <li>
                                <img src="{{ $iconCheckbox }}" width="18">
                                Declaro para os devidos fins que na hipotese de
                                doença preexistente, conhecida ou não, a cobertura do procedimento será após 12 meses de
                                permanencia initerrupta no plano. No ato da microchipagem, o(s) pet(s) passará(ão) por uma
                                avaliação de pré-existência.Considera-se doença preexistente a que o Pet tinha antes da
                                contratação do plano.
                            </li>
                        </ul>

                            <table style="width: 100%;" class="dadosgerais">
                                <tbody>

                                <tr>
                                    <td style="width: 100%; text-align:left !important;padding-left: 30px;" colspan="2" class="titulo">
                                        Vila Velha,
                                        {{ \Carbon\Carbon::parse($dados_proposta->data_proposta)->day }} de
                                        {{ \App\Helpers\Utils::getMonthName(\Carbon\Carbon::parse($dados_proposta->data_proposta)->month) }} de
                                        {{ \Carbon\Carbon::parse($dados_proposta->data_proposta)->year }}
                                    </td>
                                </tr>

                                @if($aceite)
                                    <tr>
                                        <td width="50%" rowspan="3" height="100" class="assinatura">
                                            <b class="nome-assinatura" style="text-transform: capitalize;">{{ strtolower($dados_proposta->cliente->dados->nome_cliente) }}</b>
                                            Contratante
                                        </td>
                                        <td width="50%" rowspan="3" height="100" class="assinatura">
                                            <b class="nome-assinatura" style="text-transform: capitalize;">Lifepet Brasil - Plano de Saúde S/A</b>
                                            Contratada
                                        </td>
                                    </tr>
                                @endif

                                </tbody>
                            </table>


                            <table class="" style="width: 100%; MARGIN-TOP:15PX;" border="0">
                                <tbody>
                                <tr style="border-bottom: 0px solid #c1c1c1;">

                                    <th class="a100"
                                        style="text-align: center; font-size: 10px; padding: 10px 15px !important; background-color: #009bf5; position: absolute;  bottom: 20px; left: 0px;">
                                        <span style="color:#fff; text-align: center;  text-transform: uppercase; ">Lifepet: (27) 4007-2441 | atendimento@lifepet.com.br | www.lifepet.com.br  </span>
                                    </th>

                                </tr>

                                </tbody>
                            </table>


                    </div>
                </div>

            </div>
        </div>
        @endforeach

        <div class="page-break" data-page="{{ $i+1 }}">
            <div class="sheet" style=" margin:0 auto; padding: 9mm; ">

                <!-- Write HTML just like a web page -->
                <div style="border: 0px solid #666;  padding: 1mm 1mm; height: 100%;">
                    <div class="topolife row">

                        <table class="" style="width: 100%;" border="0">
                            <tbody>
                            <tr style="border-bottom: 0px solid #c1c1c1;">
                                <th class="a40" style="text-align:left;"><img src="{{ asset('_app_cadastro_cliente/proposta/img/logo.jpg') }}" width="250"></th>
                                <th class="a20"></th>
                                <th class="a40"
                                    style="text-align: left;padding: 5px 15px !important; background-color:#009bf5;">
                                    <span style="color:#fff; text-transform: uppercase; width:55%; float:left; display:table; ">Contrato nº: </span>
                                    <span class="" style="background-color:#fff; width:45%; display:table; text-align:center; padding:5px;">{{ $idCliente }}</span>
                                </th>

                            </tr>

                            </tbody>
                        </table>


                        <table class="" style="width: 100%; MARGIN-TOP:10PX;" border="0">
                            <tbody>
                            <tr style="border-bottom: 0px solid #c1c1c1;">

                                <th class="a100"
                                    style="text-align: center; font-size: 23px; padding: 10px 15px !important; background-color:#009bf5;">
                                    <span style="color:#fff; text-align: center;  text-transform: uppercase; ">CHECKLIST </span>
                                </th>

                            </tr>

                            </tbody>
                        </table>

                        <br/>
                        <br/>

                        <ul class="check2 check">
                            @foreach($dados_proposta->checklist as $check)
                                <li>
                                    <img src="{{ $iconCheckbox }}" width="18">{!! $check->item !!}
                                </li>
                            @endforeach
                        </ul>

                            <table style="width: 100%;" class="dadosgerais">
                                <tbody>

                                <tr>
                                    <td style="width: 100%; text-align:left !important;padding-left: 40px;" colspan="3" class="titulo">
                                        Vila Velha,
                                        {{ \Carbon\Carbon::parse($dados_proposta->data_proposta)->day }} de
                                        {{ \App\Helpers\Utils::getMonthName(\Carbon\Carbon::parse($dados_proposta->data_proposta)->month) }} de
                                        {{ \Carbon\Carbon::parse($dados_proposta->data_proposta)->year }}
                                    </td>
                                </tr>

                                @if($aceite)
                                    <tr>
                                        <td width="50%" rowspan="3" height="100" class="assinatura">
                                            <b class="nome-assinatura" style="text-transform: capitalize;">{{ strtolower($dados_proposta->cliente->dados->nome_cliente) }}</b>
                                            Contratante
                                        </td>
                                        <td width="50%" rowspan="3" height="100" class="assinatura">
                                            <b class="nome-assinatura" style="text-transform: capitalize;">Lifepet Brasil - Plano de Saúde S/A</b>
                                            Contratada
                                        </td>
                                    </tr>
                                @endif

                                </tbody>
                            </table>


                            <table class="" style="width: 100%; MARGIN-TOP:15PX;" border="0">
                                <tbody>
                                <tr style="border-bottom: 0px solid #c1c1c1;">

                                    <th class="a100"
                                        style="text-align: center; font-size: 10px; padding: 10px 15px !important; background-color: #009bf5; position: absolute;  bottom: 20px; left: 0px;">
                                        <span style="color:#fff; text-align: center;  text-transform: uppercase; ">Lifepet: (27) 4007-2441 | atendimento@lifepet.com.br | www.lifepet.com.br  </span>
                                    </th>

                                </tr>

                                </tbody>
                            </table>

                    </div>
                </div>

            </div>
        </div>

    </div>

    @if(!$aceite)
        <div class="separador-aceite"></div>
        <div class="modal fade" id="modal-aceite" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 style="text-align: center;">Aceite da Proposta</h3>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('clientes.aceiteProposta', ['id' => $idCliente, 'numProposta' => $numProposta]) }}" method="POST" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            {{--<input autocomplete="off" type="hidden" name="assinatura" class="assinatura">--}}
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" required>
                                    Reconheço que sou responsável por todas as informações declaradas.
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" required>
                                    Declaro que estou ciente e de acordo com a Tabela de Cobertura e Carências e com o Contrato do Cliente.
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" required>
                                    Declaro para os devidos fins que na hipotese de doença preexistente, conhecida ou não,
                                    a cobertura do procedimento será após 12 meses de permanencia initerrupta no plano.
                                    No ato da microchipagem, o(s) pet(s) passará(ão) por uma avaliação de pré-existência.
                                    Considera-se doença preexistente a que o Pet tinha antes da contratação do plano.
                                </label>
                            </div>

                            <div class="box_anexos">
                                <label for="anexos">
                                    Adicione aqui os documentos solicitados:
                                    <br>
                                    <input type="file" multiple id="anexos" name="anexos[]" required>
                                </label>
                            </div>

                            <button class="btn btn-success btn-submit form-control">
                                <i class="fa fa-check-square-o"></i>
                                Declaro que li e concordo com as informações deste documento
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="aceite">
            <button class="btn btn-success form-control" data-toggle="modal" data-target="#modal-aceite">
                <p><i class="fa fa-check-square-o"></i> Declaro que li e concordo com as informações deste documento</p>
            </button>
        </div>

        <script type="text/javascript">
            /*!
 * Generated using the Bootstrap Customizer (https://getbootstrap.com/docs/3.4/customize/)
 */

            /*!
             * Bootstrap v3.4.1 (https://getbootstrap.com/)
             * Copyright 2011-2019 Twitter, Inc.
             * Licensed under the MIT license
             */

            if (typeof jQuery === 'undefined') {
                throw new Error('Bootstrap\'s JavaScript requires jQuery')
            }
            +function ($) {
                'use strict';
                var version = $.fn.jquery.split(' ')[0].split('.')
                if ((version[0] < 2 && version[1] < 9) || (version[0] == 1 && version[1] == 9 && version[2] < 1) || (version[0] > 3)) {
                    throw new Error('Bootstrap\'s JavaScript requires jQuery version 1.9.1 or higher, but lower than version 4')
                }
            }(jQuery);

            /* ========================================================================
             * Bootstrap: modal.js v3.4.1
             * https://getbootstrap.com/docs/3.4/javascript/#modals
             * ========================================================================
             * Copyright 2011-2019 Twitter, Inc.
             * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
             * ======================================================================== */


            +function ($) {
                'use strict';

                // MODAL CLASS DEFINITION
                // ======================

                var Modal = function (element, options) {
                    this.options = options
                    this.$body = $(document.body)
                    this.$element = $(element)
                    this.$dialog = this.$element.find('.modal-dialog')
                    this.$backdrop = null
                    this.isShown = null
                    this.originalBodyPad = null
                    this.scrollbarWidth = 0
                    this.ignoreBackdropClick = false
                    this.fixedContent = '.navbar-fixed-top, .navbar-fixed-bottom'

                    if (this.options.remote) {
                        this.$element
                            .find('.modal-content')
                            .load(this.options.remote, $.proxy(function () {
                                this.$element.trigger('loaded.bs.modal')
                            }, this))
                    }
                }

                Modal.VERSION = '3.4.1'

                Modal.TRANSITION_DURATION = 300
                Modal.BACKDROP_TRANSITION_DURATION = 150

                Modal.DEFAULTS = {
                    backdrop: true,
                    keyboard: true,
                    show: true
                }

                Modal.prototype.toggle = function (_relatedTarget) {
                    return this.isShown ? this.hide() : this.show(_relatedTarget)
                }

                Modal.prototype.show = function (_relatedTarget) {
                    var that = this
                    var e = $.Event('show.bs.modal', { relatedTarget: _relatedTarget })

                    this.$element.trigger(e)

                    if (this.isShown || e.isDefaultPrevented()) return

                    this.isShown = true

                    this.checkScrollbar()
                    this.setScrollbar()
                    this.$body.addClass('modal-open')

                    this.escape()
                    this.resize()

                    this.$element.on('click.dismiss.bs.modal', '[data-dismiss="modal"]', $.proxy(this.hide, this))

                    this.$dialog.on('mousedown.dismiss.bs.modal', function () {
                        that.$element.one('mouseup.dismiss.bs.modal', function (e) {
                            if ($(e.target).is(that.$element)) that.ignoreBackdropClick = true
                        })
                    })

                    this.backdrop(function () {
                        var transition = $.support.transition && that.$element.hasClass('fade')

                        if (!that.$element.parent().length) {
                            that.$element.appendTo(that.$body) // don't move modals dom position
                        }

                        that.$element
                            .show()
                            .scrollTop(0)

                        that.adjustDialog()

                        if (transition) {
                            that.$element[0].offsetWidth // force reflow
                        }

                        that.$element.addClass('in')

                        that.enforceFocus()

                        var e = $.Event('shown.bs.modal', { relatedTarget: _relatedTarget })

                        transition ?
                            that.$dialog // wait for modal to slide in
                                .one('bsTransitionEnd', function () {
                                    that.$element.trigger('focus').trigger(e)
                                })
                                .emulateTransitionEnd(Modal.TRANSITION_DURATION) :
                            that.$element.trigger('focus').trigger(e)
                    })
                }

                Modal.prototype.hide = function (e) {
                    if (e) e.preventDefault()

                    e = $.Event('hide.bs.modal')

                    this.$element.trigger(e)

                    if (!this.isShown || e.isDefaultPrevented()) return

                    this.isShown = false

                    this.escape()
                    this.resize()

                    $(document).off('focusin.bs.modal')

                    this.$element
                        .removeClass('in')
                        .off('click.dismiss.bs.modal')
                        .off('mouseup.dismiss.bs.modal')

                    this.$dialog.off('mousedown.dismiss.bs.modal')

                    $.support.transition && this.$element.hasClass('fade') ?
                        this.$element
                            .one('bsTransitionEnd', $.proxy(this.hideModal, this))
                            .emulateTransitionEnd(Modal.TRANSITION_DURATION) :
                        this.hideModal()
                }

                Modal.prototype.enforceFocus = function () {
                    $(document)
                        .off('focusin.bs.modal') // guard against infinite focus loop
                        .on('focusin.bs.modal', $.proxy(function (e) {
                            if (document !== e.target &&
                                this.$element[0] !== e.target &&
                                !this.$element.has(e.target).length) {
                                this.$element.trigger('focus')
                            }
                        }, this))
                }

                Modal.prototype.escape = function () {
                    if (this.isShown && this.options.keyboard) {
                        this.$element.on('keydown.dismiss.bs.modal', $.proxy(function (e) {
                            e.which == 27 && this.hide()
                        }, this))
                    } else if (!this.isShown) {
                        this.$element.off('keydown.dismiss.bs.modal')
                    }
                }

                Modal.prototype.resize = function () {
                    if (this.isShown) {
                        $(window).on('resize.bs.modal', $.proxy(this.handleUpdate, this))
                    } else {
                        $(window).off('resize.bs.modal')
                    }
                }

                Modal.prototype.hideModal = function () {
                    var that = this
                    this.$element.hide()
                    this.backdrop(function () {
                        that.$body.removeClass('modal-open')
                        that.resetAdjustments()
                        that.resetScrollbar()
                        that.$element.trigger('hidden.bs.modal')
                    })
                }

                Modal.prototype.removeBackdrop = function () {
                    this.$backdrop && this.$backdrop.remove()
                    this.$backdrop = null
                }

                Modal.prototype.backdrop = function (callback) {
                    var that = this
                    var animate = this.$element.hasClass('fade') ? 'fade' : ''

                    if (this.isShown && this.options.backdrop) {
                        var doAnimate = $.support.transition && animate

                        this.$backdrop = $(document.createElement('div'))
                            .addClass('modal-backdrop ' + animate)
                            .appendTo(this.$body)

                        this.$element.on('click.dismiss.bs.modal', $.proxy(function (e) {
                            if (this.ignoreBackdropClick) {
                                this.ignoreBackdropClick = false
                                return
                            }
                            if (e.target !== e.currentTarget) return
                            this.options.backdrop == 'static'
                                ? this.$element[0].focus()
                                : this.hide()
                        }, this))

                        if (doAnimate) this.$backdrop[0].offsetWidth // force reflow

                        this.$backdrop.addClass('in')

                        if (!callback) return

                        doAnimate ?
                            this.$backdrop
                                .one('bsTransitionEnd', callback)
                                .emulateTransitionEnd(Modal.BACKDROP_TRANSITION_DURATION) :
                            callback()

                    } else if (!this.isShown && this.$backdrop) {
                        this.$backdrop.removeClass('in')

                        var callbackRemove = function () {
                            that.removeBackdrop()
                            callback && callback()
                        }
                        $.support.transition && this.$element.hasClass('fade') ?
                            this.$backdrop
                                .one('bsTransitionEnd', callbackRemove)
                                .emulateTransitionEnd(Modal.BACKDROP_TRANSITION_DURATION) :
                            callbackRemove()

                    } else if (callback) {
                        callback()
                    }
                }

                // these following methods are used to handle overflowing modals

                Modal.prototype.handleUpdate = function () {
                    this.adjustDialog()
                }

                Modal.prototype.adjustDialog = function () {
                    var modalIsOverflowing = this.$element[0].scrollHeight > document.documentElement.clientHeight

                    this.$element.css({
                        paddingLeft: !this.bodyIsOverflowing && modalIsOverflowing ? this.scrollbarWidth : '',
                        paddingRight: this.bodyIsOverflowing && !modalIsOverflowing ? this.scrollbarWidth : ''
                    })
                }

                Modal.prototype.resetAdjustments = function () {
                    this.$element.css({
                        paddingLeft: '',
                        paddingRight: ''
                    })
                }

                Modal.prototype.checkScrollbar = function () {
                    var fullWindowWidth = window.innerWidth
                    if (!fullWindowWidth) { // workaround for missing window.innerWidth in IE8
                        var documentElementRect = document.documentElement.getBoundingClientRect()
                        fullWindowWidth = documentElementRect.right - Math.abs(documentElementRect.left)
                    }
                    this.bodyIsOverflowing = document.body.clientWidth < fullWindowWidth
                    this.scrollbarWidth = this.measureScrollbar()
                }

                Modal.prototype.setScrollbar = function () {
                    var bodyPad = parseInt((this.$body.css('padding-right') || 0), 10)
                    this.originalBodyPad = document.body.style.paddingRight || ''
                    var scrollbarWidth = this.scrollbarWidth
                    if (this.bodyIsOverflowing) {
                        this.$body.css('padding-right', bodyPad + scrollbarWidth)
                        $(this.fixedContent).each(function (index, element) {
                            var actualPadding = element.style.paddingRight
                            var calculatedPadding = $(element).css('padding-right')
                            $(element)
                                .data('padding-right', actualPadding)
                                .css('padding-right', parseFloat(calculatedPadding) + scrollbarWidth + 'px')
                        })
                    }
                }

                Modal.prototype.resetScrollbar = function () {
                    this.$body.css('padding-right', this.originalBodyPad)
                    $(this.fixedContent).each(function (index, element) {
                        var padding = $(element).data('padding-right')
                        $(element).removeData('padding-right')
                        element.style.paddingRight = padding ? padding : ''
                    })
                }

                Modal.prototype.measureScrollbar = function () { // thx walsh
                    var scrollDiv = document.createElement('div')
                    scrollDiv.className = 'modal-scrollbar-measure'
                    this.$body.append(scrollDiv)
                    var scrollbarWidth = scrollDiv.offsetWidth - scrollDiv.clientWidth
                    this.$body[0].removeChild(scrollDiv)
                    return scrollbarWidth
                }


                // MODAL PLUGIN DEFINITION
                // =======================

                function Plugin(option, _relatedTarget) {
                    return this.each(function () {
                        var $this = $(this)
                        var data = $this.data('bs.modal')
                        var options = $.extend({}, Modal.DEFAULTS, $this.data(), typeof option == 'object' && option)

                        if (!data) $this.data('bs.modal', (data = new Modal(this, options)))
                        if (typeof option == 'string') data[option](_relatedTarget)
                        else if (options.show) data.show(_relatedTarget)
                    })
                }

                var old = $.fn.modal

                $.fn.modal = Plugin
                $.fn.modal.Constructor = Modal


                // MODAL NO CONFLICT
                // =================

                $.fn.modal.noConflict = function () {
                    $.fn.modal = old
                    return this
                }


                // MODAL DATA-API
                // ==============

                $(document).on('click.bs.modal.data-api', '[data-toggle="modal"]', function (e) {
                    var $this = $(this)
                    var href = $this.attr('href')
                    var target = $this.attr('data-target') ||
                        (href && href.replace(/.*(?=#[^\s]+$)/, '')) // strip for ie7

                    var $target = $(document).find(target)
                    var option = $target.data('bs.modal') ? 'toggle' : $.extend({ remote: !/#/.test(href) && href }, $target.data(), $this.data())

                    if ($this.is('a')) e.preventDefault()

                    $target.one('show.bs.modal', function (showEvent) {
                        if (showEvent.isDefaultPrevented()) return // only register focus restorer if modal will actually get shown
                        $target.one('hidden.bs.modal', function () {
                            $this.is(':visible') && $this.trigger('focus')
                        })
                    })
                    Plugin.call($target, option, this)
                })

            }(jQuery);
        </script>
    @endif

    @if($successo_aceite)
    <script>
        $(document).ready(function () {
            swal('Sucesso!', '{{ $successo_aceite }}', 'success');
        });
    </script>
        @php
            Session::forget('successo_aceite');
        @endphp
    @endif

</body>
</html>
