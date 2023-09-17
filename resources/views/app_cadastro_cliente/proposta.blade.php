<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>A4 landscape</title>

    <!-- Normalize or reset CSS with your favorite library -->
    <link rel="stylesheet" href="{{ asset('_app_cadastro_cliente/proposta/inc/normalize.css') }}">

    <!-- Load paper.css for happy printing -->
    <link rel="stylesheet" href="{{ asset('_app_cadastro_cliente/proposta/inc/paper.css') }}">

    <link href="{{ asset('_app_cadastro_cliente/proposta/inc/css.css') }}" rel="stylesheet" type="text/css">

    {{--<script src="{{ asset('_app_cadastro_cliente/proposta/inc/jquery-1.js') }}"></script>--}}
    {{--<script src="{{ asset('_app_cadastro_cliente/proposta/inc/jspdf.js') }}"></script>--}}

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.5/jspdf.debug.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>

    <link rel="icon" href="{{ asset('_app_cadastro_cliente/proposta/img/logo-azul.jpg') }}" />
    <link rel="shortcut icon" href="{{ asset('_app_cadastro_cliente/proposta/img/logo-azul.jpg') }}" />

    <!-- Set page size here: A5, A4 or A3 -->
    <!-- Set also "landscape" if you need -->
    <style>
        @media print {
            * {
                /*-webkit-print-color-adjust: exact !important;*/
            }

            body {
                /*-webkit-print-color-adjust: exact !important;*/
                color: #4f74ab;
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
            color: #4f74ab;
            font-size: 14px;
            margin-bottom: 1px;
        }

        .titulo {
            font-size: 15px;
            color: #4f74ab;
            padding-right: 3px;
            text-align: right;
        }

        .assinatura {
            font-size: 15px;
            color: #4f74ab;
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

        i {
            padding: 10px;
            background-color: #ededed;
            border-radius: 50px;
            display: block;
            margin-right: 5px;
        }

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

        <div class="row">
            <div class="col-xs-12">
                <button type="button" class="btn btn-default form-control btn-generatePdf" onclick="generateCanvasPerPage(1);">
                    <strong> GERAR PROPOSTA </strong>
                </button>
            </div>
        </div>

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
                                    style="text-align: left;padding: 5px 15px !important; background-color:#4f74ab;">
                                    <span style="color:#fff; text-transform: uppercase; width:55%; float:left; display:table; ">Contrato nº: </span><span
                                            class=""
                                            style="background-color:#fff; width:45%; display:table; text-align:center; padding:5px;">{{ $cliente->id }}</span>
                                </th>

                            </tr>

                            </tbody>
                        </table>

                        <table class="" style="width: 100%; MARGIN-TOP:10PX;" border="0">
                            <tbody>
                            <tr style="border-bottom: 0px solid #c1c1c1;">

                                <th class="a100"
                                    style="text-align: center; font-size: 23px; padding: 10px 15px !important; background-color:#4f74ab;">
                                    <span style="color:#fff; text-align: center;  text-transform: uppercase; ">PROPOSTA DE ADESÃO </span>
                                </th>

                            </tr>

                            </tbody>
                        </table>


                        <table class="" style="width: 100%; MARGIN-TOP:10PX;" border="0">
                            <tbody>
                            <tr style="border-bottom: 0px solid #c1c1c1;">

                                <th class="a100"
                                    style="text-align: left; font-size: 18px; padding: 10px 0px  10px 0px !important; border-bottom:1px solid #4f74ab;">
                                    <span style="color:#4f74ab; text-align: left;  text-transform: uppercase; ">DADOS DO CONTRATANTE </span>
                                </th>

                            </tr>

                            </tbody>
                        </table>

                        <table style="width: 100%;" class="dadosgerais">
                            <tbody>
                            <tr>
                                <td style="width: 10%; text-align: right;" class="titulo">Contratante:</td>
                                <td style="width: 87%;" colspan="3" class="texto">{{ $cliente->nome_cliente }}</td>
                            </tr>
                            <tr>
                                <td style="width: 10%; text-align: right;" class="titulo">E-mail:</td>
                                <td style="width: 87%;" colspan="3" class="texto">{{ $cliente->email }}</td>
                            </tr>
                            <tr>
                                <td style="width:  10%; text-align: right;" class="titulo">CPF:</td>
                                <td style="width: 30%;" class="texto">{{ $cliente->cpf }}</td>
                                <td style="width: 20%; text-align: right;" class="titulo">RG:</td>
                                <td style="width: 30%;" class="texto">{{ $cliente->rg }}</td>
                            </tr>
                            <tr>
                                <td style="width:  10%; text-align: right;" class="titulo">Sexo:</td>
                                <td style="width: 30%;" class="texto">{{ $cliente->sexo == 'M' ? 'Masculino' : 'Feminino' }}</td>
                                <td style="width: 20%; text-align: right;" class="titulo">Data de Nasc.:</td>
                                <td style="width: 30%;" class="texto">{{ $cliente->data_nascimento->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <td style="width:  10%; text-align: right;" class="titulo">Celular:</td>
                                <td style="width: 30%;" class="texto">{{ $cliente->celular }}</td>
                                <td style="width: 20%; text-align: right;" class="titulo">Telefone Fixo:</td>
                                <td style="width: 30%;" class="texto">{{ $cliente->telefone_fixo }}</td>
                            </tr>
                            </tbody>
                        </table>

                        <table style="width: 100%;" class="dadosgerais">
                            <tbody>
                            <tr>
                                <td style="width: 13.5%; text-align: right;" class="titulo">Endereço:</td>
                                <td style="width: 87%;" colspan="3" class="texto">{{ $cliente->rua }}</td>
                            </tr>

                            <tr>
                                <td style="width:  10%; text-align: right;" class="titulo">Número:</td>
                                <td style="width: 30%;" class="texto">{{ $cliente->numero_endereco }}</td>
                                <td style="width: 20%; text-align: right;" class="titulo">Complemento:</td>
                                <td style="width: 30%;" class="texto">{{ $cliente->complemento_endereco }}</td>
                            </tr>
                            <tr>
                                <td style="width:  10%; text-align: right;" class="titulo">Cidade/UF:</td>
                                <td style="width: 30%;" class="texto">{{ $cliente->cidade }}/{{ $cliente->estado }}</td>
                                <td style="width: 20%; text-align: right;" class="titulo">CEP:</td>
                                <td style="width: 30%;" class="texto">{{ $cliente->cep }}</td>
                            </tr>
                            </tbody>
                        </table>

                        <table style="width: 100%;" class="dadosgerais">
                            <tbody>
                            <tr>
                                <td style="width:  13.5%; text-align: right;" class="titulo">Obs.:</td>
                                <td style="width: 80%;" class="texto observacao_vendedor" contenteditable="true"></td>
                            </tr>
                            </tbody>
                        </table>

                        <table class="" style="width: 100%; MARGIN-TOP:15PX;" border="0">
                            <tbody>
                            <tr style="border-bottom: 0px solid #c1c1c1;">

                                <th class="a100"
                                    style="text-align: left; font-size: 18px; padding: 10px 0px  10px 0px !important; border-bottom:1px solid #4f74ab;">
                                    <span style="color:#4f74ab; text-align: left;  text-transform: uppercase; ">Dados de pagamento </span>
                                </th>

                            </tr>

                            </tbody>
                        </table>


                        <table style="width: 100%;" class="dadosgerais">
                            <tbody>

                            <tr>
                                <td style="width:  12%; text-align: right;" class="titulo">Data venc.:</td>
                                <td style="width: 25%;" class="texto"> {{ $cliente->vencimento }}</td>
                                <td style="width: 22%; text-align: right;" class="titulo">Forma de pagamento:</td>
                                <td style="width: 25%;" class="texto"> {{ $request['forma-pagamento'] }}</td>
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
                                    {{ \Carbon\Carbon::now()->format('d') }} de
                                    {{ \App\Helpers\Utils::getMonthName(\Carbon\Carbon::now()->format('m')) }} de
                                    {{ \Carbon\Carbon::now()->format('Y') }}
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 50%;" rowspan="3" height="100" class="assinatura">
                                    <img src="{{ route('vendedores.assinatura', $vendedor->id) }}" alt="" width="60%">
                                    _________________________________
                                    <br>
                                    <b class="nome-assinatura" style="text-transform: capitalize;">{{ $vendedor->nome }}</b>
                                    Consultor
                                </td>
                                <td style="width: 50%;" rowspan="3" height="100" class="assinatura">
                                    <img src="{{ asset($cliente->assinatura) }}" alt="" width="60%">
                                    _________________________________
                                    <br>
                                    <b class="nome-assinatura" style="text-transform: capitalize;">{{ strtolower($cliente->nome_cliente) }}</b>
                                    Contratante
                                </td>
                            </tr>

                            </tbody>
                        </table>


                        <table class="" style="width: 100%; MARGIN-TOP:15PX;" border="0">
                            <tbody>
                            <tr style="border-bottom: 0px solid #c1c1c1;">

                                <th class="a100"
                                    style="text-align: center; font-size: 10px; padding: 10px 15px !important; background-color: #4f74ab; position: absolute;  bottom: 20px; left: 0px;">
                                    <span style="color:#fff; text-align: center;  text-transform: uppercase; ">Lifepet: (27) 4007-2441 | atendimento@lifepet.com.br | www.lifepet.com.br  </span>
                                </th>

                            </tr>

                            </tbody>
                        </table>


                    </div>
                </div>

            </div>
        </div>

        @foreach($pets as $pet)
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
                                    style="text-align: left;padding: 5px 15px !important; background-color:#4f74ab;">
                                    <span style="color:#fff; text-transform: uppercase; width:55%; float:left; display:table; ">Contrato nº: </span><span
                                            class=""
                                            style="background-color:#fff; width:45%; display:table; text-align:center; padding:5px;">{{ $cliente->id }}</span>
                                </th>

                            </tr>

                            </tbody>
                        </table>


                        <table class="" style="width: 100%; MARGIN-TOP:10PX;" border="0">
                            <tbody>
                            <tr style="border-bottom: 0px solid #c1c1c1;">

                                <th class="a100"
                                    style="text-align: left; font-size: 18px; padding: 10px 0px  10px 0px !important; border-bottom:1px solid #4f74ab;">
                                    <span style="color:#4f74ab; text-align: left;  text-transform: uppercase; ">DADOS DO PET {{ $i }} </span>
                                </th>

                            </tr>

                            </tbody>
                        </table>

                        <table style="width: 100%;" class="dadosgerais">
                            <tbody>

                            <tr>
                                @if($pet->foto)
                                <td style="width: 20%; height: 147px;padding: 0;border: 0;" rowspan="5">
                                    <img src="{{ asset($pet->foto) }}" alt="" width="100%">
                                </td>
                                @endif
                                <td style="width: 20%; text-align: right;" class="titulo">Nome:</td>
                                <td style="width: 60%;" class="texto">{{ $pet->nome_pet }}</td>
                            </tr>
                            <tr>
                                <td style="width: 20%; text-align: right;" class="titulo">Tipo:</td>
                                <td style="width: 60%;" class="texto">{{ $pet->tipo }}</td>
                            </tr>
                            <tr>
                                <td style="width: 20%; text-align: right;" class="titulo">Raça:</td>
                                <td style="width: 60%;" class="texto">{{ $pet->raca->nome }}</td>
                            </tr>

                            <tr>
                                <td style="width: 20%; text-align: right;" class="titulo">Sexo:</td>
                                <td style="width: 60%;" class="texto">{{ $pet->sexo == 'M' ? 'Masculino' : 'Feminino' }}</td>
                            </tr>

                            <tr>
                                <td style="width: 20%; text-align: right;" class="titulo">Data de Nasc.:</td>
                                <td style="width: 60%;" class="texto">{{ $pet->data_nascimento->format('d/m/Y') }}</td>
                            </tr>

                            </tbody>
                        </table>

                        <table class="" style="width: 100%; MARGIN-TOP:15PX;" border="0">
                            <tbody>
                            <tr style="border-bottom: 0px solid #c1c1c1;">

                                <th class="a100"
                                    style="text-align: left; font-size: 18px; padding: 10px 0px  10px 0px !important; border-bottom:1px solid #4f74ab;">
                                    <span style="color:#4f74ab; text-align: left;  text-transform: uppercase; ">Dados do plano - Pet {{ $i++ }}</span>
                                </th>

                            </tr>

                            </tbody>
                        </table>

                        <table style="width: 100%;" class="dadosgerais">
                            <tbody>
                            <tr>
                                <td style="width: 10%; text-align: right;" class="titulo">Plano:</td>
                                <td style="width: 87%;" colspan="3" class="texto">{{ $pet->plano()->nome_plano }}</td>
                            </tr>

                            <tr>
                                <td style="width:  25%; text-align: right;" class="titulo">Taxa de Adesão:</td>
                                <td style="width: 25%;" class="texto">{{ \App\Helpers\Utils::money($pet->petsPlanos()->first()->adesao) }}</td>
                                <td style="width: 25%; text-align: right;" class="titulo">Valor:</td>
                                <td style="width: 25%;" class="texto"> {{ \App\Helpers\Utils::money($pet->valor) }}</td>
                            </tr>
                            <tr>
                                <td style="width:  25%; text-align: right;" class="titulo">Data venc.:</td>
                                <td style="width: 25%;" class="texto"> {{ $cliente->vencimento }}</td>
                                <td style="width: 25%; text-align: right;" class="titulo">Forma de pagamento:</td>
                                <td style="width: 25%;" class="texto"> {{ $request['forma-pagamento'] }}</td>
                            </tr>
                            <tr>
                                <td style="width:  25%; text-align: right;" class="titulo">Frequência:</td>
                                <td style="width: 25%;" class="texto"> {{ $pet->regime }}</td>
                                <td style="width: 25%; text-align: right;" class="titulo">Modalidade:</td>
                                <td style="width: 25%;" class="texto"> {{ $pet->participativo == 1 ? 'Participativo' : 'Integral' }}</td>
                            </tr>
                            </tbody>
                        </table>

                        <table style="width: 100%;" class="dadosgerais">
                            <tbody>
                            <tr>
                                <td style="width:  10%; text-align: right;" class="titulo">Observações/descontos:</td>
                                <td style="width: 80%;" class="texto">{{ $pet->observacoes }}</td>
                            </tr>
                            </tbody>
                        </table>

                        <ul class="doencaspet">
                            <li>
                                Sofre(u) de alguma doença infecciosa ou parasitária: erlichia ou anaplasma (doença do carrapato), hepatite, meningite, infecções virais, entre outros?
                                <b>{{ isset($request['questao-1-'.$pet->id]) ? 'Sim'.(isset($request['especificacao-1-'.$pet->id]) ? ' - ' . $request['especificacao-1-'.$pet->id] : '') : 'Não' }}</b>
                            </li>
                            <li>
                                Sofre(u) de alguma tipo de neoplasia (câncer)?
                                <b>{{ isset($request['questao-2-'.$pet->id]) ? 'Sim'.(isset($request['especificacao-2-'.$pet->id]) ? ' - ' . $request['especificacao-2-'.$pet->id] : '') : 'Não' }}</b>
                            </li>
                            <li>
                                Sofre(u) de alguma doença no sangue (anemias)?
                                <b>{{ isset($request['questao-3-'.$pet->id]) ? 'Sim'.(isset($request['especificacao-3-'.$pet->id]) ? ' - ' . $request['especificacao-3-'.$pet->id] : '') : 'Não' }}</b>
                            </li>
                            <li>
                                É portador(a) de alguma doença endócrina (diabetes, hiperadrenocorticismo, hipotireoidismo, entre outras)?
                                <b>{{ isset($request['questao-4-'.$pet->id]) ? 'Sim'.(isset($request['especificacao-4-'.$pet->id]) ? ' - ' . $request['especificacao-4-'.$pet->id] : '') : 'Não' }}</b>
                            </li>
                            <li>
                                Sofre(u) de alguma doença do sistema nervoso (convulsões, ataxias, entre outras)?
                                <b>{{ isset($request['questao-5-'.$pet->id]) ? 'Sim'.(isset($request['especificacao-5-'.$pet->id]) ? ' - ' . $request['especificacao-5-'.$pet->id] : '') : 'Não' }}</b>
                            </li>
                            <li>
                                Alguma afecção dermatológica? (atopia, DAPE, Sarna)?
                                <b>{{ isset($request['questao-6-'.$pet->id]) ? 'Sim'.(isset($request['especificacao-6-'.$pet->id]) ? ' - ' . $request['especificacao-6-'.$pet->id] : '') : 'Não' }}</b>
                            </li>
                            <li>
                                É portador de alguma enfermidade circulatória(sopro, arritmia, hipertensão)?
                                <b>{{ isset($request['questao-7-'.$pet->id]) ? 'Sim'.(isset($request['especificacao-7-'.$pet->id]) ? ' - ' . $request['especificacao-7-'.$pet->id] : '') : 'Não' }}</b>
                            </li>
                            <li>
                                Sofre(u) algum problema em ouvido?
                                <b>{{ isset($request['questao-8-'.$pet->id]) ? 'Sim'.(isset($request['especificacao-8-'.$pet->id]) ? ' - ' . $request['especificacao-8-'.$pet->id] : '') : 'Não' }}</b>
                            </li>
                            <li>
                                Sofre(u) alguma afecção do aparelho respiratório (colapso de traqueia, bronquite, pneumonia, estenose de narinas, (palato alongado)?
                                <b>{{ isset($request['questao-9-'.$pet->id]) ? 'Sim'.(isset($request['especificacao-9-'.$pet->id]) ? ' - ' . $request['especificacao-9-'.$pet->id] : '') : 'Não' }}</b>
                            </li>
                            <li>
                                Sofre(u) de doenças do aparelho digestivo (gastrite, úlceras, diarreias, corpo estranho)?
                                <b>{{ isset($request['questao-10-'.$pet->id]) ? 'Sim'.(isset($request['especificacao-10-'.$pet->id]) ? ' - ' . $request['especificacao-10-'.$pet->id] : '') : 'Não' }}</b>
                            </li>
                            <li>
                                Sofre(u) de doença do aparelho genito-urinário (piometras, hiperplasia prostática, mastites, hematúria, obstruções, cistite, cálculo, fimose, insuficiência renal)?
                                <b>{{ isset($request['questao-11-'.$pet->id]) ? 'Sim'.(isset($request['especificacao-11-'.$pet->id]) ? ' - ' . $request['especificacao-11-'.$pet->id] : '') : 'Não' }}</b>
                            </li>
                            <li>
                                Sofre(u) algum tipo de fratura ou traumatismo?
                                <b>{{ isset($request['questao-12-'.$pet->id]) ? 'Sim'.(isset($request['especificacao-12-'.$pet->id]) ? ' - ' . $request['especificacao-12-'.$pet->id] : '') : 'Não' }}</b>
                            </li>
                            <li>
                                Realizou algum procedimento cirúrgico para correção ortopédica (fratura ou traumatismo)?
                                <b>{{ isset($request['questao-13-'.$pet->id]) ? 'Sim'.(isset($request['especificacao-13-'.$pet->id]) ? ' - ' . $request['especificacao-13-'.$pet->id] : '') : 'Não' }}</b>
                            </li>
                            <li>
                                Realizou algum tipo de procedimento cirúrgico?
                                <b>{{ isset($request['questao-14-'.$pet->id]) ? 'Sim'.(isset($request['especificacao-14-'.$pet->id]) ? ' - ' . $request['especificacao-14-'.$pet->id] : '') : 'Não' }}</b>
                            </li>
                            <li>
                                Sofre de alguma má formação congênita?
                                <b>{{ isset($request['questao-15-'.$pet->id]) ? 'Sim'.(isset($request['especificacao-15-'.$pet->id]) ? ' - ' . $request['especificacao-15-'.$pet->id] : '') : 'Não' }}</b>
                            </li>
                            <li>
                                Sofre(u) algum tipo de doença não relacionada acima?
                                <b>{{ isset($request['questao-16-'.$pet->id]) ? 'Sim'.(isset($request['especificacao-16-'.$pet->id]) ? ' - ' . $request['especificacao-16-'.$pet->id] : '') : 'Não' }}</b>
                            </li>
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
                                        {{ \Carbon\Carbon::now()->format('d') }} de
                                        {{ \App\Helpers\Utils::getMonthName(\Carbon\Carbon::now()->format('m')) }} de
                                        {{ \Carbon\Carbon::now()->format('Y') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 50%;" rowspan="3" height="100" class="assinatura">
                                        <img src="{{ route('vendedores.assinatura', $vendedor->id) }}" alt="" width="60%">
                                        _________________________________
                                        <br>
                                        <b class="nome-assinatura" style="text-transform: capitalize;">{{ $vendedor->nome }}</b>
                                        Consultor
                                    </td>
                                    <td style="width: 50%;" rowspan="3" height="100" class="assinatura">
                                        <img src="{{ asset($cliente->assinatura) }}" alt="" width="60%">
                                        _________________________________
                                        <br>
                                        <b class="nome-assinatura" style="text-transform: capitalize;">{{ strtolower($cliente->nome_cliente) }}</b>
                                        Contratante
                                    </td>
                                </tr>

                                </tbody>
                            </table>


                            <table class="" style="width: 100%; MARGIN-TOP:15PX;" border="0">
                                <tbody>
                                <tr style="border-bottom: 0px solid #c1c1c1;">

                                    <th class="a100"
                                        style="text-align: center; font-size: 10px; padding: 10px 15px !important; background-color: #4f74ab; position: absolute;  bottom: 20px; left: 0px;">
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
                                    style="text-align: left;padding: 5px 15px !important; background-color:#4f74ab;">
                                    <span style="color:#fff; text-transform: uppercase; width:55%; float:left; display:table; ">Contrato nº: </span><span
                                            class=""
                                            style="background-color:#fff; width:45%; display:table; text-align:center; padding:5px;">{{ $cliente->id }}</span>
                                </th>

                            </tr>

                            </tbody>
                        </table>


                        <table class="" style="width: 100%; MARGIN-TOP:10PX;" border="0">
                            <tbody>
                            <tr style="border-bottom: 0px solid #c1c1c1;">

                                <th class="a100"
                                    style="text-align: center; font-size: 23px; padding: 10px 15px !important; background-color:#4f74ab;">
                                    <span style="color:#fff; text-align: center;  text-transform: uppercase; ">CHECKLIST </span>
                                </th>

                            </tr>

                            </tbody>
                        </table>

                        <br/>
                        <br/>

                        <ul class="check2 check">
                            <li>
                                <img src="{{ $iconCheckbox }}" width="18">Em hipótese alguma o Pet será atendido sem o microchip (ainda que seja caso de emergência);
                            </li>
                            <li>
                                <img src="{{ $iconCheckbox }}" width="18">Para a realização da michochipagem é necessário que o Pet esteja com a carteira de vacinação em dia. Sendo filhote, deve estar sadio e ter mais de 60 dias;
                            </li>
                            <li>
                                <img src="{{ $iconCheckbox }}" width="18">Os atendimentos em caso de urgência e emergência só poderão ser realizados 72h após a microchipagem do Pet;
                            </li>
                            <li>
                                <img src="{{ $iconCheckbox }}" width="18">Coberturas e Carências constam na Área do Cliente que está disponível no site: www.lifepet.com.br;
                            </li>
                            <li>
                                <img src="{{ $iconCheckbox }}" width="18">Doenças e males preexistentes terão cobertura após 12 meses de contrato ininterrupto (Cobertura Parcial Temporária - CPT);
                            </li>
                            <li>
                                <img src="{{ $iconCheckbox }}" width="18">No caso de atraso de pagamento, o serviço é automaticamente SUSPENSO (inclusive para urgências e emergências);
                            </li>
                            <li>
                                <img src="{{ $iconCheckbox }}" width="18">Os pagamentos realizados em boleto ou cartão podem demorar até 72 horas para serem reconhecidos pelo banco;
                            </li>
                            <li>
                                <img src="{{ $iconCheckbox }}" width="18">No caso de atraso superior a 60 dias, o plano é automaticamente CANCELADO, permanecendo as cobranças de mensalidades vencidas;
                            </li>
                            <li>
                                <img src="{{ $iconCheckbox }}" width="18">Após 60 dias de inadimplência o débito em aberto poderá ser inscrito no SPC e SERASA (desde que previamente comunicado);
                            </li>
                            <li>
                                <img src="{{ $iconCheckbox }}" width="18">REDE CREDENCIADA: www.lifepet.com.br/rede;
                            </li>
                            <li>
                                <img src="{{ $iconCheckbox }}" width="18">No prazo de até 7 dias após a assinatura dessa proposta será enviado ao e-mail de cadastro, os seguintes documentos: i) Carta de boas vindas; ii) Login e senha da Área do Cliente;
                            </li>
                            <li>
                                <img src="{{ $iconCheckbox }}" width="18">A vigência do contrato se inicia conforme data da assiantura dessa proposta.
                            </li>
                            <li>
                                <img src="{{ $iconCheckbox }}" width="18">Você será contactado ou poderá solicitar a microchipagem 72h após assinatura do contrato.
                            </li>
                            <li>
                                <img src="{{ $iconCheckbox }}" width="18">Caso o animal venha a óbito ou haja desistência do plano não haverá restituição da mensalidade paga(em caso de planos com recorrência mensal) e nem dos valores já pagos para planos anuais (caso seja beneficiado com algum desconto);
                            </li>
                            <li>
                                <img src="{{ $iconCheckbox }}" width="18">Fidelidade: o contrato possui fidelidade de 12 meses, podendo ser cancelado sem multa rescisória caso não tenha utilizado qualquer procedimento;
                            </li>
                            <li>
                                <img src="{{ $iconCheckbox }}" width="18">O contrato será automaticamente renovado por tempo indeterminado, caso nenhuma das partes faça a comunicação de cancelamento formal nos 30 (trinta) dias que antecedem seu término inicial.
                            </li>
                            <li>
                                <img src="{{ $iconCheckbox }}" width="18">Caso ocorra rescisão contratual antes do decurso da fidelidade, já tendo utilizado o/a CONTRATANTE qualquer procedimento em benefício de seu animal, arcará com multa no valor das mensalidades vincendas acrescidas de 50% (cinquenta por cento).
                            </li>

                        </ul>

                            <table style="width: 100%;" class="dadosgerais">
                                <tbody>

                                <tr>
                                    <td style="width: 100%; text-align:left !important;padding-left: 40px;" colspan="2" class="titulo">
                                        Vila Velha,
                                        {{ \Carbon\Carbon::now()->format('d') }} de
                                        {{ \App\Helpers\Utils::getMonthName(\Carbon\Carbon::now()->format('m')) }} de
                                        {{ \Carbon\Carbon::now()->format('Y') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 50%;" rowspan="3" height="100" class="assinatura">
                                        <img src="{{ route('vendedores.assinatura', $vendedor->id) }}" alt="" width="60%">
                                        _________________________________
                                        <br>
                                        <b class="nome-assinatura" style="text-transform: capitalize;">{{ $vendedor->nome }}</b>
                                        Consultor
                                    </td>
                                    <td style="width: 50%;" rowspan="3" height="100" class="assinatura">
                                        <img src="{{ asset($cliente->assinatura) }}" alt="" width="60%">
                                        _________________________________
                                        <br>
                                        <b class="nome-assinatura" style="text-transform: capitalize;">{{ strtolower($cliente->nome_cliente) }}</b>
                                        Contratante
                                    </td>
                                </tr>

                                </tbody>
                            </table>


                            <table class="" style="width: 100%; MARGIN-TOP:15PX;" border="0">
                                <tbody>
                                <tr style="border-bottom: 0px solid #c1c1c1;">

                                    <th class="a100"
                                        style="text-align: center; font-size: 10px; padding: 10px 15px !important; background-color: #4f74ab; position: absolute;  bottom: 20px; left: 0px;">
                                        <span style="color:#fff; text-align: center;  text-transform: uppercase; ">Lifepet: (27) 4007-2441 | atendimento@lifepet.com.br | www.lifepet.com.br  </span>
                                    </th>

                                </tr>

                                </tbody>
                            </table>


                    </div>
                </div>

            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">
                <button type="button" class="btn btn-default form-control btn-generatePdf" onclick="generateCanvasPerPage(1);">
                    <strong> GERAR PROPOSTA </strong>
                </button>
            </div>
        </div>

    </div>

    <script src="https://unpkg.com/sweetalert2@7.18.0/dist/sweetalert2.all.js"></script>
    <script src="/_app_cadastro_cliente/inc/html2canvas.min.js?"></script>
    <script>

        function generateCanvasPerPage(index){
            $('.loading-overlay').show();
            // $.each($('.page-break'), function(i,j) {
            html2canvas($('.page-break[data-page="'+index+'"] .sheet')[0]).then(canvas => {
                $(canvas).hide();
                document.body.appendChild(canvas);
                if($('.page-break[data-page="'+(index+1)+'"] .sheet')[0]) {
                    generateCanvasPerPage(index+1);
                } else {
                    savePdf();
                }
            });
            // });
        }
        // generateCanvasPerPage(1);

        function savePdf() {
            var pdf = new jsPDF("p", "pt", "a4", true);
            var canvasEl = document.querySelectorAll("canvas");
            canvasEl.forEach(function(canvas,index){
                // canvas.getContext('2d').fillRect(0,0,100*(index+1),100*(index+1));
                pdf.addImage(canvas.toDataURL("image/png", 1), 'JPEG', 0, 0, (canvas.width-260), 850,undefined,'FAST');
                if(index == canvasEl.length-1){

                    // pdf.save("file.pdf");

                    var blob = pdf.output('blob');
                    var formData = new FormData();
                    formData.append('pdf', blob);
                    formData.append('_token', '{{ csrf_token() }}');
                    // console.log(formData);
                    $.ajax({
                        url: "{{ route('app_cadastro_cliente.propostaPdf', $cliente->id) }}",
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(data){
                            $('.loading-overlay').hide();
                            $('canvas').remove();
                            $.ajax({
                                url: "/notas/{{ $cliente->id }}",
                                method: 'POST',
                                data: {
                                    'corpo': $('.observacao_vendedor').text()
                                },
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                success: function(response){
                                    swal('Sucesso!', 'Bem vindo à Lifepet!', 'success').then(function () {
                                        window.location.replace('{{ route('app_cadastro_cliente.index') }}');
                                    });
                                    console.log('Success:', response);
                                },
                                error: function(data){console.log('Error:', data.responseText)}
                            });
                            {{--swal('Sucesso!', 'Bem vindo à Lifepet!', 'success').then(function () {--}}
                                {{--window.location.replace('{{ route('app_cadastro_cliente.index') }}');--}}
                            {{--});--}}
                            {{--console.log('Success:', data);--}}
                        },
                        error: function(data){console.log('Error:', data.responseText)}
                    });
                }
                else {
                    pdf.addPage();
                }
            })
        }

        function download(dataURL, filename) {
            if (navigator.userAgent.indexOf("Safari") > -1 && navigator.userAgent.indexOf("Chrome") === -1) {
                window.open(dataURL);
            } else {
                var blob = dataURLToBlob(dataURL);
                var url = window.URL.createObjectURL(blob);

                var a = document.createElement("a");
                a.style = "display: none";
                a.href = url;
                a.download = filename;

                document.body.appendChild(a);
                a.click();

                window.URL.revokeObjectURL(url);
            }
        }

        function dataURLToBlob(dataURL) {
            // Code taken from https://github.com/ebidel/filer.js
            var parts = dataURL.split(';base64,');
            var contentType = parts[0].split(":")[1];
            var raw = window.atob(parts[1]);
            var rawLength = raw.length;
            var uInt8Array = new Uint8Array(rawLength);

            for (var i = 0; i < rawLength; ++i) {
                uInt8Array[i] = raw.charCodeAt(i);
            }

            return new Blob([uInt8Array], { type: contentType });
        }

        // html2canvas(document.querySelector("body")).then(canvas => {
        //     // document.body.appendChild(canvas);
        //     // doc.fromHTML(canvas, 15, 15, {
        //     //     'width': '100%',
        //     //     'elementHandlers': specialElementHandlers
        //     // });
        //     // doc.save('sample-file.pdf');
        //     console.log(canvas);
        // });



        var doc = new jsPDF('p', 'pt', 'a4');
        var specialElementHandlers = {
            '#editor': function (element, renderer) {
                return true;
            }
        };

        // $(document).ready(function() {
        //     doc.fromHTML($('#content').html(), 15, 15, {
        //         'width': '100%',
        //         'elementHandlers': specialElementHandlers
        //     });
        //     doc.save('sample-file.pdf');
        // });

        $('#cmd').click(function () {
            doc.fromHTML($('#content').html(), 15, 15, {
                'width': '100%',
                'elementHandlers': specialElementHandlers
            });
            doc.save('sample-file.pdf');
        });

        // This code is collected but useful, click below to jsfiddle link.
    </script>

</body>
</html>
