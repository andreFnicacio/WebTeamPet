@extends('layouts.app')

@section('title')
    @parent
    Home
@endsection

@section('css')
    @parent
    <link href="../assets/pages/css/profile.min.css" rel="stylesheet" type="text/css">
    <style>
        .mt-element-ribbon .ribbon.ribbon-color-instagram,
        .mt-element-ribbon .ribbon.ribbon-color-instagram>.ribbon-sub {
            background-color: #F1C40F;
            color: #010100;
        }
        .mt-element-ribbon .ribbon.ribbon-color-instagram>.ribbon-sub:after {
            border-color: #122b40 #F1C40F;
        }
        .mt-element-ribbon .ribbon.ribbon-color-facebook,
        .mt-element-ribbon .ribbon.ribbon-color-facebook>.ribbon-sub {
            background-color: #337ab7;
            color: #FFF;
        }
        .mt-element-ribbon .ribbon.ribbon-color-facebook>.ribbon-sub {
            color: #000;
        }
        .mt-element-ribbon .ribbon.ribbon-color-facebook>.ribbon-sub:after {
            border-color: #122b40 #337ab7;
        }
        .mt-element-ribbon .ribbon.ribbon-color-facebook:after {
            border-color: #286090;
        }
    </style>
@endsection

@section('content')

    {{--<div class="row">--}}
        <div>

            <div class="col-xs-12">
                <!-- BEGIN PAGE HEAD-->
                <div class="page-head">
                    <!-- BEGIN PAGE TITLE -->
                    <div class="page-title">
                        {{--<h1>--}}
                            {{--Bem-vindo, {{ $clinica->nome_clinica }}--}}
                        {{--</h1>--}}
                    </div>
                    <!-- END PAGE TITLE -->
                </div>
                <!-- END PAGE HEAD-->
            </div>

            <div class="col-xs-12">
                <!-- BEGIN PAGE BASE CONTENT -->

                <!-- BEGIN PROFILE SIDEBAR -->
                <div class="profile-sidebar">
                    <!-- PORTLET MAIN -->
                    <div class="portlet light profile-sidebar-portlet bordered">
                        <!-- SIDEBAR USERPIC -->
                        <div class="profile-userpic">
                            <img src="{{ url('/') }}\assets\pages\img\lifepet-logotipo.png" class="img-responsive" alt=""> </div>
                        <!-- END SIDEBAR USERPIC -->
                        <!-- SIDEBAR USER TITLE -->
                        <div class="profile-usertitle">
                            <div class="profile-usertitle-name">{{ $clinica->nome_clinica }}</div>
                            <div class="profile-usertitle-job">{{ $clinica->cidade }}</div>
                        </div>
                        <!-- END SIDEBAR USER TITLE -->
                        <!-- SIDEBAR BUTTONS -->
                        <div class="profile-userbuttons">
                            <a href="{!! route('usuarios.mudarsenha') !!}" style="text-decoration: none !important;">
                                <button type="button" class="btn btn-circle green btn-sm" data-toggle="tooltip">Mudar Senha</button>
                            </a>
                            <button type="button" class="btn btn-circle red-sunglo btn-sm" data-toggle="modal" href="#modal-sugestoes">Reportar Erro</button>
                        </div>
                        <!-- END SIDEBAR BUTTONS -->
                        <!-- SIDEBAR MENU -->
                        <div class="profile-usermenu">
                            <ul class="nav">
                                <li class="active">
                                    {{--<a href="{{ route('clinicas.extrato') }}">--}}
                                    <a href="{{ url('/home') }}">
                                        <i class="icon-home"></i>Início
                                    </a>
                                </li>
                                <li>
                                    <a href="#" onclick="document.getElementById('formLogout').submit();">
                                        <i class="icon-logout"></i>Sair
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <!-- END MENU -->
                    </div>
                    <!-- END PORTLET MAIN -->

                    <div class="portlet light bordered">
                        <div class="portlet-title">
                            <span class="caption-subject font-blue-madison bold uppercase">Avaliações</span>
                            <br>
                            <small>As avaliações dos clientes demoram até 10 dias para serem contabilizadas e não refletem uma avaliação imediata</small>
                        </div>
                        <div class="portlet-body">
                            <div class="scroller" style="height:200px" data-handle-color="#a1b2bd">
                                <div class="task-content tasks-widget">
                                    <ul class="task-list">
                                        @php
                                            $rankingCount = 1;
                                        @endphp
                                        @foreach($prestadoresRating as $prestador)
                                            <li>
                                                <div class="row task-title">
                                                    <div class="col-xs-2">
                                                        <strong>{{ $prestador['rating'] ? $rankingCount++ . '&ordm;' : '#' }}</strong>
                                                    </div>
                                                    <div class="col-xs-8">
                                                        <span class="task-title-sp">
                                                            {{ mb_strtoupper($prestador['nome']) }}
                                                        </span>
                                                    </div>
                                                    <div class="col-xs-2">
                                                        {!! $prestador['badge'] !!}
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- PORTLET MAIN -->
                    <div class="portlet light bordered">
                        <!-- STAT -->
                        <div class="row list-separated profile-stat">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="uppercase profile-stat-text">{{ \App\Helpers\Utils::getMonthName(\Carbon\Carbon::today()->month) }} / {{ \Carbon\Carbon::today()->year }}</div>
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-6">
                                <div class="uppercase profile-stat-title"> {{ $contadores['guiasEmitidas'] }} </div>
                                <div class="uppercase profile-stat-text">Guias Emitidas</div>
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-6">
                                <div class="uppercase profile-stat-title"> {{ $contadores['guiasGlosadas'] }} </div>
                                <div class="uppercase profile-stat-text">Guias Glosadas</div>
                            </div>

                        </div>
                        <!-- END STAT -->
                        <div>
                            <h4 class="profile-desc-title">Precisa de Ajuda?</h4>
                            <span class="profile-desc-text"> Acesse um dos nossos canais abaixo. </span>
                            <div class="margin-top-20 profile-desc-link">
                                <i class="fa fa-globe"></i>
                                <a href="https://www.lifepet.com.br">www.lifepet.com.br</a>
                            </div>
                            <div class="margin-top-20 profile-desc-link">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    style="margin: 0 5px -6px 0; fill:#abb6c4;"
                                    x="0px" y="0px"
                                    width="22" height="22"
                                    viewBox="0 0 24 24">    
                                    <path d="M 12.011719 2 C 6.5057187 2 2.0234844 6.478375 2.0214844 11.984375 C 2.0204844 13.744375 2.4814687 15.462563 3.3554688 16.976562 L 2 22 L 7.2324219 20.763672 C 8.6914219 21.559672 10.333859 21.977516 12.005859 21.978516 L 12.009766 21.978516 C 17.514766 21.978516 21.995047 17.499141 21.998047 11.994141 C 22.000047 9.3251406 20.962172 6.8157344 19.076172 4.9277344 C 17.190172 3.0407344 14.683719 2.001 12.011719 2 z M 12.009766 4 C 14.145766 4.001 16.153109 4.8337969 17.662109 6.3417969 C 19.171109 7.8517969 20.000047 9.8581875 19.998047 11.992188 C 19.996047 16.396187 16.413812 19.978516 12.007812 19.978516 C 10.674812 19.977516 9.3544062 19.642812 8.1914062 19.007812 L 7.5175781 18.640625 L 6.7734375 18.816406 L 4.8046875 19.28125 L 5.2851562 17.496094 L 5.5019531 16.695312 L 5.0878906 15.976562 C 4.3898906 14.768562 4.0204844 13.387375 4.0214844 11.984375 C 4.0234844 7.582375 7.6067656 4 12.009766 4 z M 8.4765625 7.375 C 8.3095625 7.375 8.0395469 7.4375 7.8105469 7.6875 C 7.5815469 7.9365 6.9355469 8.5395781 6.9355469 9.7675781 C 6.9355469 10.995578 7.8300781 12.182609 7.9550781 12.349609 C 8.0790781 12.515609 9.68175 15.115234 12.21875 16.115234 C 14.32675 16.946234 14.754891 16.782234 15.212891 16.740234 C 15.670891 16.699234 16.690438 16.137687 16.898438 15.554688 C 17.106437 14.971687 17.106922 14.470187 17.044922 14.367188 C 16.982922 14.263188 16.816406 14.201172 16.566406 14.076172 C 16.317406 13.951172 15.090328 13.348625 14.861328 13.265625 C 14.632328 13.182625 14.464828 13.140625 14.298828 13.390625 C 14.132828 13.640625 13.655766 14.201187 13.509766 14.367188 C 13.363766 14.534188 13.21875 14.556641 12.96875 14.431641 C 12.71875 14.305641 11.914938 14.041406 10.960938 13.191406 C 10.218937 12.530406 9.7182656 11.714844 9.5722656 11.464844 C 9.4272656 11.215844 9.5585938 11.079078 9.6835938 10.955078 C 9.7955938 10.843078 9.9316406 10.663578 10.056641 10.517578 C 10.180641 10.371578 10.223641 10.267562 10.306641 10.101562 C 10.389641 9.9355625 10.347156 9.7890625 10.285156 9.6640625 C 10.223156 9.5390625 9.737625 8.3065 9.515625 7.8125 C 9.328625 7.3975 9.131125 7.3878594 8.953125 7.3808594 C 8.808125 7.3748594 8.6425625 7.375 8.4765625 7.375 z"></path>
                                </svg>
                                <a href="https://api.whatsapp.com/send/?phone=5511944985420&text&app_absent=0" target="_blank">(11) 94498-5420</a>
                            </div>
                            <div class="margin-top-20 profile-desc-link">
                                <i class="fa fa-envelope"></i>
                                <a href="mailto:thaiane.luz@lifepet.com.br">thaiane.luz@lifepet.com.br </a>
                            </div>
                        </div>
                    </div>
                    <!-- END PORTLET MAIN -->
                </div>
                <!-- END BEGIN PROFILE SIDEBAR -->

                <!-- BEGIN PROFILE CONTENT -->
                <div class="profile-content">

                    {{--<div class="row">--}}
                        {{--<div class="col-md-12">--}}
                            {{--<!-- BEGIN PORTLET - EXTRATO -->--}}
                            {{--<div class="portlet light bordered" >--}}
                                {{--<div class="portlet-title">--}}
                                    {{--<div class="caption caption-md">--}}
                                        {{--<i class="icon-bar-chart theme-font hide"></i>--}}
                                        {{--<span class="caption-subject font-blue-madison bold uppercase">Extrato do mês</span>--}}
                                        {{--<br>--}}
                                        {{--<span class="caption-helper">Últimos lançamentos em ordem decrescente</span>--}}
                                    {{--</div>--}}
                                    {{--<div class="actions">--}}
                                        {{-- <div class="btn-group btn-group-devided" data-toggle="buttons">--}}
                                            {{--<label class="btn btn-transparent grey-salsa btn-circle btn-sm">--}}
                                                {{--<input type="radio" name="options" class="toggle" id="option2">--}}
                                                {{--{{ \App\Helpers\Utils::getMonthName(\Carbon\Carbon::today()->month) }}--}}
                                                {{--<i class="fa fa-caret-down"></i>--}}
                                            {{--</label>--}}
                                        {{--</div> --}}
                                        {{--<div class="btn-group btn-group-devided">--}}
                                            {{--<div class="btn-group btn-group-devided">--}}
                                                {{--<a href="{{ route('clinicas.extrato') }}">--}}
                                                    {{--<button class="btn btn-transparent green btn-circle btn-sm">--}}
                                                        {{--Ver Relatório Completo--}}
                                                    {{--</button>--}}
                                                {{--</a>--}}
                                            {{--</div>--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                                {{--<div class="portlet-body">--}}
                                    {{--<div class="row number-stats margin-bottom-30">--}}

                                        {{--<div class="col-md-12 col-sm-12 col-xs-12">--}}
                                            {{--<div class="stat-right">--}}
                                                {{--<div class="stat-chart">--}}
                                                    {{--<!-- do not line break "sparkline_bar" div. sparkline chart has an issue when the container div has line break -->--}}
                                                    {{--<div id="graficoMovimentacaoAcumulada" data-info="[{{ implode(',', $graficoMovimentacaoAcumulada) }}]">--}}
                                                        {{--<canvas width="90" height="45" style="display: inline-block; width: 90px; height: 45px; vertical-align: top;"></canvas>--}}
                                                    {{--</div>--}}
                                                {{--</div>--}}
                                                {{--<div class="stat-number">--}}
                                                    {{--<div class="title">URH Acumulada</div>--}}
                                                    {{--<div class="number">{{ $valorUrhAcumulada }} </div>--}}
                                                {{--</div>--}}
                                            {{--</div>--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                    {{--<div class="table-scrollable table-scrollable-borderless">--}}
                                        {{--<table class="table table-hover table-light">--}}
                                            {{--<thead>--}}
                                                {{--<tr class="uppercase">--}}
                                                    {{--<th colspan="1">GUIA</th>--}}
                                                    {{--<th width="40%">DESCRIÇÃO</th>--}}
                                                    {{--<th>URH</th>--}}
                                                    {{--<th>DATA</th>--}}
                                                    {{--<th>PRESTADOR</th>--}}
                                                {{--</tr>--}}
                                            {{--</thead>--}}
                                            {{--<tbody>--}}
                                                {{--@if($extrato->count())--}}
                                                    {{--@foreach($extrato as $ex)--}}
                                                        {{--<tr>--}}
                                                            {{--<td>--}}
                                                                {{--<a href="{{ route('autorizador.verGuia', $ex->numero_guia) }}" target="_blank" class="primary-link">#{{ $ex->numero_guia }}</a>--}}
                                                            {{--</td>--}}
                                                            {{--<td>--}}
                                                                {{--{{ $ex->descricao }}--}}
                                                            {{--</td>--}}
                                                            {{--<td>--}}
                                                                {{--<span class="badge {{ $ex->urh > 0 ? 'badge-success' : 'badge-danger' }} badge-success btn-sm btn-circle" style="margin-right:4px; margin-top:-2px;">--}}
                                                                    {{--<i class="fa fa-database"></i>--}}
                                                                {{--</span>--}}
                                                                {{--{{ $ex->urh }}--}}
                                                            {{--</td>--}}
                                                            {{--<td>{{ $ex->data }}</td>--}}
                                                            {{--<td>--}}
                                                                {{--<span class="bold theme-font uppercase">{{ $ex->prestador }}</span>--}}
                                                            {{--</td>--}}
                                                        {{--</tr>--}}
                                                    {{--@endforeach--}}
                                                {{--@else--}}
                                                    {{--<tr>--}}
                                                        {{--<td colspan="4">--}}
                                                            {{--<h5 class="text-center">Nenhuma guia emitida neste mês até o momento</h5>--}}
                                                        {{--</td>--}}
                                                    {{--</tr>--}}
                                                {{--@endif--}}
                                            {{--</tbody>--}}
                                        {{--</table>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                            {{--<!-- END PORTLET - EXTRATP -->--}}
                        {{--</div>--}}

                    {{--</div>--}}

                    <div class="row">

                        <div class="col-md-12">
                            <!-- BEGIN PORTLET -->
                            <div class="portlet light bordered tasks-widget">
                                <div class="portlet-title">
                                    <div class="caption caption-md">
                                        <i class="icon-bar-chart theme-font hide"></i>
                                        <span class="caption-subject font-blue-madison bold uppercase">GUIAS GLOSADAS</span>
                                        {{--<span class="caption-helper">16 pendentes</span>--}}
                                    </div>
                                    <div class="inputs">
                                        <a href="{{ route('autorizador.verGuiasGlosadas') }}">
                                            <label class="btn btn-transparent yellow-crusta btn-circle btn-sm">Acessar Guias Glosadas</label>
                                        </a>
                                    </div>
                                </div>
                                <div class="portlet-body">
                                    <div class="task-content">
                                        <div class="scroller" style="height: 282px;" data-always-visible="1" data-rail-visible1="0" data-handle-color="#D7DCE2">
                                            <table class="table table-hover table-light">
                                                <thead>
                                                    <tr class="uppercase">
                                                        <th colspan="1">GUIA</th>
                                                        <th>DESCRIÇÃO</th>
                                                        <th>STATUS</th>
                                                        <th>DATA</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if($guiasGlosadas->count())
                                                        @foreach($guiasGlosadas as $gg)
                                                            <tr>
                                                                <td>
                                                                    <a href="{{ route('autorizador.verGuia', $gg->numero_guia) }}" target="_blank" class="primary-link">#{{ $gg->numero_guia }}</a>
                                                                </td>
                                                                <td>
                                                                    {{ $gg->procedimento->nome_procedimento }}
                                                                </td>
                                                                <td>
                                                                    {!! $gg->getGlosaLabel() !!}
                                                                </td>
                                                                <td>{{ \Carbon\Carbon::parse($gg->dataGuia())->format('d/m/Y') }}</td>
                                                            </tr>
                                                        @endforeach
                                                    @else
                                                        <tr>
                                                            <td colspan="4">
                                                                <h5 class="text-center">Nenhuma guia glosada neste mês até o momento</h5>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- END PORTLET -->
                        </div>

                        {{-- <div class="col-md-6">
                            <div class="portlet light bordered">
                                <div class="portlet-title">
                                    <div class="caption caption-md">
                                        <i class="icon-bar-chart theme-font hide"></i>
                                        <span class="caption-subject font-blue-madison bold uppercase">
                                            ASSINATURAS PENDENTES
                                        </span>
                                    </div>
                                </div>
                                <div class="portlet-body">
                                    <table class="table table-hover table-light">
                                        <thead>
                                            <tr>
                                                <th>Guia</th>
                                                <th>Prestador</th>
                                                <th>Cliente</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>#123</td>
                                                <td>Lorem ipsum...</td>
                                                <td>Lorem ipsum...</td>
                                            </tr>
                                            <tr>
                                                <td>#456</td>
                                                <td>Lorem ipsum...</td>
                                                <td>Lorem ipsum...</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div> --}}
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="portlet light portlet-fit bordered mt-element-ribbon">
                                <a href="https://www.instagram.com/lifepetsaude/" target="_blank">
                                    <div class="ribbon ribbon-right ribbon-vertical-right ribbon-border-dash-vert ribbon-color-instagram uppercase" data-toggle="tooltip" data-title="Instagram">
                                        <div class="ribbon-sub ribbon-bookmark"></div>
                                        <i class="fa fa-instagram"></i>
                                    </div>
                                </a>
                                <a href="https://www.facebook.com/lifepetsaude/" target="_blank">
                                    <div class="ribbon ribbon-right ribbon-vertical-right ribbon-border-dash-vert ribbon-color-facebook uppercase" data-toggle="tooltip" data-title="Facebook">
                                        <div class="ribbon-sub ribbon-bookmark"></div>
                                        <i class="fa fa-facebook"></i>
                                    </div>
                                </a>
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class="icon-microphone font-green"></i>
                                        <span class="caption-subject bold font-green uppercase">COMUNICADOS E NOVIDADES</span>
                                    </div>
                                    <div class="actions">
                                        <div class="btn-group btn-group-devided" data-toggle="buttons"></div>
                                    </div>
                                </div>
                                <div class="portlet-body">
                                    <div class="timeline">
                                        @foreach($comunicadosCredenciados as $comunicado)
                                            <!-- TIMELINE ITEM -->
                                            <div class="timeline-item">
                                                <div class="timeline-badge">
                                                    <img class="timeline-badge-userpic" src="{{ url('/') }}\assets\pages\img\lifepet-logotipo.png">
                                                </div>
                                                <div class="timeline-body">
                                                    <div class="timeline-body-arrow"> </div>
                                                    <div class="timeline-body-head">
                                                        <div class="timeline-body-head-caption">
                                                            <a href="javascript:;" class="timeline-body-title font-blue-madison">{{ $comunicado->titulo }}</a>
                                                            <span class="timeline-body-time font-grey-cascade">{{ \Carbon\Carbon::parse($comunicado->published_at)->format('d/m/Y H:i') }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="timeline-body-content">
                                                        <span class="font-grey-cascade">
                                                            {!! htmlspecialchars_decode(stripslashes(nl2br(str_replace('\n', '<br>', $comunicado->corpo)))) !!}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- END TIMELINE ITEM -->
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END PROFILE CONTENT -->

                <!-- END PAGE BASE CONTENT -->
            </div>

        </div>
    {{--</div>--}}
    <div class="clearfix"></div>
@endsection

@section('scripts')
    <script src="{{ url('/') }}/assets/global/plugins/jquery.sparkline.min.js" type="text/javascript"></script>
    <script type="text/javascript">
        var Profile = function ()
        {
            return {
                init: function ()
                {
                    Profile.initMiniCharts()
                },
                initMiniCharts: function ()
                {
                    App.isIE8() && !Function.prototype.bind && (Function.prototype.bind = function (t)
                    {
                        if ("function" != typeof this) throw new TypeError("Function.prototype.bind - what is trying to be bound is not callable");
                        var i = Array.prototype.slice.call(arguments, 1),
                            r = this,
                            n = function () {},
                            o = function ()
                            {
                                return r.apply(this instanceof n && t ? this : t, i.concat(Array.prototype.slice.call(arguments)))
                            };
                        return n.prototype = this.prototype, o.prototype = new n, o
                    }), $("#sparkline_bar").sparkline([8, 9, 10, 11, 10, 10, 12, 10, 10, 11, 9, 12, 11],
                        {
                            type: "bar",
                            width: "100",
                            barWidth: 6,
                            height: "45",
                            barColor: "#F36A5B",
                            negBarColor: "#e02222"
                        }), $("#graficoMovimentacaoAcumulada").sparkline($("#graficoMovimentacaoAcumulada").data('info'),
                        {
                            type: "bar",
                            width: "100",
                            barWidth: 8,
                            height: "45",
                            barColor: "#5C9BD1",
                            negBarColor: "#e02222"
                        })
                }
            }
        }();
        App.isAngularJsApp() === !1 && jQuery(document).ready(function ()
        {
            Profile.init()
        });
    </script>
@endsection
