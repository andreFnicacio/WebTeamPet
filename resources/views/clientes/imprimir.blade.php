<!DOCTYPE html>
<html lang="en">
@php
    //Os dados serão acessíveis pelo nome presente no banco de dados

    //Acessando ID do Cliente
    //$cliente->id;
    //Acessando o plano do pet. Fazer foreach
    //$pets[0]->plano()->nome_plano;
    //Acessando os dados de pet
    //$pets[0]->nome_pet;
@endphp
<head>
    <meta charset="utf-8">
    <title>Dados do cliente - {{ $cliente->nome_cliente }}</title>

    <!-- Normalize or reset CSS with your favorite library -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/3.0.3/normalize.css">

    <!-- Load paper.css for happy printing -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paper-css/0.2.3/paper.css">

    <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
    <link href="{{ url('/') }}/assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ url('/') }}/assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ url('/') }}/assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ url('/') }}/assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />
    <!-- END GLOBAL MANDATORY STYLES -->
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <link href="{{ url('/') }}/assets/global/css/components-md.min.css" rel="stylesheet" id="style_components" type="text/css" />
    <link href="{{ url('/') }}/assets/global/css/plugins-md.min.css" rel="stylesheet" type="text/css" />

    <!-- END PAGE LEVEL PLUGINS -->
    <!-- BEGIN THEME GLOBAL STYLES -->
    <link href="{{ url('/') }}/assets/global/css/components-md.min.css" rel="stylesheet" id="style_components" type="text/css" />
    <link href="{{ url('/') }}/assets/global/css/plugins-md.min.css" rel="stylesheet" type="text/css" />
    <!-- END THEME GLOBAL STYLES -->
    <!-- BEGIN THEME LAYOUT STYLES -->
    <link href="{{ url('/') }}/assets/layouts/layout2/css/layout.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ url('/') }}/assets/layouts/layout2/css/themes/light.min.css" rel="stylesheet" type="text/css" id="style_color" />
    <link href="{{ url('/') }}/assets/layouts/layout2/css/custom.min.css" rel="stylesheet" type="text/css" />

    <!-- Set page size here: A5, A4 or A3 -->
    <!-- Set also "landscape" if you need -->
    <style>
        @media print
        {
            * {-webkit-print-color-adjust:exact !important;}
            body { -webkit-print-color-adjust: exact !important; }
        }

       

        @page { size: A4 ; height: auto !important;   -webkit-print-color-adjust: exact; }
        .a25 {
            width:25%;
        }
        p{padding-bottom: 0px; font-size: 12px; margin-bottom: 0px; padding-top: 2px; margin-top: 2px;}

        .a33 {
            width:33%;
        }

        .a40 {
            width:40%;
        }

        .a65 {
            width:65%;
        }

        .a60 {
            width:60%;
        }
        .a50{
            width:50%;
        }
        .a20 {
            width:20%;
        }
        .a100 {
            width:100%;
        }
    </style>
</head>

<!-- Set "A5", "A4" or "A3" for class name -->
<!-- Set also "landscape" if you need -->
<body class="A4 login">

<!-- Each sheet element should have the class "sheet" -->
<!-- "padding-**mm" is optional: you can set 10, 15, 20 or 25 -->

    <section class="sheet"  style=" margin:0 auto; padding: 10mm;">

    <!-- Write HTML just like a web page -->
    <div style="border: 1px solid #666;  padding: 2mm 10mm; height: 100%;">
        <div class="topolife row">

            <style type="text/css">
                -webkit-print-color-adjust: exact;
                .tg  {border-collapse:collapse;border-spacing:0;}
                .tg .tg-yw4l{vertical-align:top}
                p{ font-size: 12px !important; font-weight: 200; }
                .backcinza{background-color: #ededed; font-size: 20px; border: 1px solid #777; padding: 10px;}
                i{padding: 10px; background-color: #ededed; border-radius:50px; display: block; margin-right: 5px; }

            </style>
            <table class="" style="width: 100%;" border="0" >
                <tr style="border-bottom: 1px solid #c1c1c1;">
                    <th class="a40" ><img class="login-logo login-6" src="{{ url('/') }}/assets/pages/img/logo-big-white.png" style="width: 120px;" /></th>
                    <th class="a60"><h4 style="font-weight: 700; text-align: right;"><br/><small>RECADASTRAMENTO LIFEPET 2017</small> <br/>DOCUMENTO DE CONFIRMAÇÃO DE DADOS</h4>
<br/>
                    </th>
                   
                </tr>

            </table>



            <h3 style="margin-bottom: 0px;">Dados do Cliente:</h3>
            <table class="" style="width: 100%; margin-top :10px;" border="0" >
                <tr style="">
<th class="a60" >

                            <p><b>ID: </b> {{$cliente->id}} </p>
                            <p><b>Nome: </b> {{$cliente->nome_cliente }}</p>
                            <p><b>CPF: </b> {{$cliente->cpf }}</p>
                            <p><b>RG: </b> {{$cliente->rg }}</p>
                            <p><b>Telefone: </b> {{$cliente->telefone_fixo}} </p>
                            <p><b>Celular: </b> {{$cliente->celular}} </p>  <p><b>E-mail: </b> {{$cliente->email}} </p>


                    </th>
                </tr>


            </table>

                <br/>

            <h3 style="margin-bottom: 0px;">Dados do Endereço:</h3>
            <table class="" style="width: 100%; margin-top :10px;" border="0" >
                <tr style="">
                        <th class="a60" >

                                <p><b>CEP: </b> {{$cliente->cep}} </p>
                                <p><b>Rua: </b> {{$cliente->rua }}, {{$cliente->numero_endereco }} </p>
                                <p><b>Complemento: </b> {{$cliente->complemento_endereco }}</p>
                                <p><b>Bairro: </b> {{$cliente->bairro }}</p>
                                <p><b>Cidade/UF: </b> {{$cliente->cidade}}/{{$cliente->estado}} </p>


                        </th>
                </tr>


            </table>


<div class="" style="width: 100%; clear:both; display: table;" >
                @foreach($pets as $pet)

<div class="" style="width: 33%; margin-top :10px; float:left;" border="0" >
                       

                        <h3 style="margin-bottom: 0px;">Pet:</h3>
                    <table class="" style="width: 100%; margin-top :10px;" border="0" >
                        <tr style="">
                                <th class="a60" >

                                        <p><b>Nome Pet: </b> {{$pet->nome_pet}} </p>
                                        <p><b>Microchip: </b> {{$pet->numero_microchip}} </p>
                                        <p><b>Plano: </b> {{ $pet->plano()->nome_plano }} </p>
                                        <p><b>Raça: </b> {{$pet->raca}}</p>
                                        <p><b>Data de nascimento: </b>{{$pet->data_nascimento->format('d/m/Y')}}</p>
                                        <p><b>Doenças Pré-existentes: </b> {{$pet->doencas_pre_existentes}}</p>
                                        <p><b>Observações: </b> {{$pet->observacoes}}</p>
                                              @php
                                $status = $pet->ativo ? "Ativo" : "Inativo";
                            @endphp
                                        <p><b>Status: </b> {{$status}}</p>


                                </th>
                        </tr>


                    </table>
                                
                           </div>    

                          
                                
                                
                                
                            @endforeach   </div>  

            

            <table class="" style="width: 100%; clear: both; display: block;  margin-top: 40px;  border-radius:10px !important;" border="0" >


<br/><br/>

                <tr>
                    <th class="a100" style="clear: both;"><p> Ao assinar o presente termo, declaro verídicas as informações acima. <br/><br/></th>
                   
                </tr>
            </table>

<br/><br/>
        <table class="" style="width: 100%;  margin-top: 40px;  border-radius:10px !important;" border="0" >



                <tr>

                    <th class="a33" style="border-top: 1px solid #000; margin-right: 25px !important; display: block; width: 45%; float: left; ">Assinatura e carimbo do médico veterinário</th>
                    <th class="a33" style="border-top: 1px solid #000; margin-right: 25px !important; display: block; width: 45%;  float: left; ">Assinatura do tutor/responsável</th>
                </tr>
            </table>



        </div>
    </div>

</section>

<!-- BEGIN CORE PLUGINS -->
<script src="{{ url('/') }}/assets/global/plugins/jquery.min.js" type="text/javascript"></script>
<script src="{{ url('/') }}/assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="{{ url('/') }}/assets/global/plugins/js.cookie.min.js" type="text/javascript"></script>
<script src="{{ url('/') }}/assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<script src="{{ url('/') }}/assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="{{ url('/') }}/assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
<!-- END CORE PLUGINS -->

<!-- BEGIN THEME GLOBAL SCRIPTS -->
<script src="{{ url('/') }}/assets/global/scripts/app.min.js" type="text/javascript"></script>
<!-- END THEME GLOBAL SCRIPTS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="{{ url('/') }}/assets/pages/scripts/dashboard.min.js" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->
<!-- BEGIN THEME LAYOUT SCRIPTS -->
<script src="{{ url('/') }}/assets/layouts/layout2/scripts/layout.min.js" type="text/javascript"></script>
<script src="{{ url('/') }}/assets/layouts/layout2/scripts/demo.min.js" type="text/javascript"></script>
<script src="{{ url('/') }}/assets/layouts/global/scripts/quick-sidebar.min.js" type="text/javascript"></script>
<script src="{{ url('/') }}/assets/layouts/global/scripts/quick-nav.min.js" type="text/javascript"></script>

</body>

</html>