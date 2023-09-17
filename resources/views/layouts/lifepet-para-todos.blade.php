<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta name="robots" content="noindex">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $routeName = \Request::route()->getName();
        $fullQualifiedRoute = $routeName;
        if($routeName != "") {
            $routeName = explode('.', $routeName);
            if(is_array($routeName)) {
                $routeName = $routeName[0];
            }
        }
        $isAdmin = Entrust::hasRole(['ADMINISTRADOR']);
    @endphp
    <meta name="route-group" content="{{ $routeName }}">
    <meta name="isadmin" content="{{ $isAdmin }}">
    <meta name="isauditor" content="{{ Entrust::hasRole(['AUDITORIA']) }}">
    <title>Lifepet Saúde - @yield('title')</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

    <!-- <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css"> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css">
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/2.3.11/css/AdminLTE.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/2.3.11/css/skins/_all-skins.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/iCheck/1.0.2/skins/square/_all.css"> -->


    {{--<link href="{{ url('/') }}/assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />--}}


    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/brands.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css">
    <link rel="stylesheet" href="https://lifepet.com.br/wp-content/plugins/elementor/assets/css/frontend.min.css?ver=3.0.5">
    <style>
        .elementor-13880 .elementor-element.elementor-element-ffe2750 > .elementor-container{min-height:297px;}.elementor-13880 .elementor-element.elementor-element-ffe2750 > .elementor-container:after{content:"";min-height:inherit;}.elementor-13880 .elementor-element.elementor-element-ffe2750:not(.elementor-motion-effects-element-type-background), .elementor-13880 .elementor-element.elementor-element-ffe2750 > .elementor-motion-effects-container > .elementor-motion-effects-layer{background-color:#009CF5;}.elementor-13880 .elementor-element.elementor-element-ffe2750{transition:background 0.3s, border 0.3s, border-radius 0.3s, box-shadow 0.3s;padding:40px 00px 20px 0px;}.elementor-13880 .elementor-element.elementor-element-ffe2750 > .elementor-background-overlay{transition:background 0.3s, border-radius 0.3s, opacity 0.3s;}.elementor-13880 .elementor-element.elementor-element-ebe0760 > .elementor-column-wrap > .elementor-widget-wrap > .elementor-widget:not(.elementor-widget__width-auto):not(.elementor-widget__width-initial):not(:last-child):not(.elementor-absolute){margin-bottom:10px;}.elementor-13880 .elementor-element.elementor-element-ebe0760 > .elementor-element-populated{margin:0px 20px 0px 0px;padding:20px 30px 40px 30px;}.elementor-13880 .elementor-element.elementor-element-6cb54ae .elementor-spacer-inner{height:20px;}.elementor-13880 .elementor-element.elementor-element-7112231 .elementor-repeater-item-7d22e7e.elementor-social-icon{background-color:#00d7ff;}.elementor-13880 .elementor-element.elementor-element-7112231 .elementor-repeater-item-7d22e7e.elementor-social-icon i{color:#ffffff;}.elementor-13880 .elementor-element.elementor-element-7112231 .elementor-repeater-item-7d22e7e.elementor-social-icon svg{fill:#ffffff;}.elementor-13880 .elementor-element.elementor-element-7112231 .elementor-repeater-item-d3b8a13.elementor-social-icon{background-color:#00d7ff;}.elementor-13880 .elementor-element.elementor-element-7112231 .elementor-repeater-item-d3b8a13.elementor-social-icon i{color:#ffffff;}.elementor-13880 .elementor-element.elementor-element-7112231 .elementor-repeater-item-d3b8a13.elementor-social-icon svg{fill:#ffffff;}.elementor-13880 .elementor-element.elementor-element-7112231 .elementor-repeater-item-4e52577.elementor-social-icon{background-color:#00d7ff;}.elementor-13880 .elementor-element.elementor-element-7112231 .elementor-repeater-item-f7fbaf3.elementor-social-icon{background-color:#00d7ff;}.elementor-13880 .elementor-element.elementor-element-7112231{--grid-template-columns:repeat(0, auto);--grid-column-gap:5px;--grid-side-margin:5px;--grid-row-gap:0px;--grid-bottom-margin:0px;}.elementor-13880 .elementor-element.elementor-element-7112231 .elementor-widget-container{justify-content:flex-start;}.elementor-13880 .elementor-element.elementor-element-7112231 .elementor-social-icon{font-size:18px;}.elementor-13880 .elementor-element.elementor-element-3968dd0{color:#ffffff;font-size:15px;}.elementor-13880 .elementor-element.elementor-element-87a97fa > .elementor-element-populated{padding:20px 20px 20px 20px;}.elementor-13880 .elementor-element.elementor-element-c1255e5 .elementor-heading-title{color:#ffffff;font-size:16px;}.elementor-13880 .elementor-element.elementor-element-c1255e5 > .elementor-widget-container{margin:0px 0px 30px 0px;}.elementor-13880 .elementor-element.elementor-element-36c486b{font-size:12px;}.elementor-13880 .elementor-element.elementor-element-def9486 > .elementor-element-populated{padding:20px 20px 20px 20px;}.elementor-13880 .elementor-element.elementor-element-7994b8f .elementor-heading-title{color:#ffffff;font-size:16px;}.elementor-13880 .elementor-element.elementor-element-7994b8f > .elementor-widget-container{margin:0px 0px 30px 0px;}.elementor-13880 .elementor-element.elementor-element-4e81f34{font-size:12px;}.elementor-13880 .elementor-element.elementor-element-c1fcbb8 > .elementor-element-populated{padding:20px 20px 20px 20px;}.elementor-13880 .elementor-element.elementor-element-6597c9b .elementor-heading-title{color:#ffffff;font-size:16px;}.elementor-13880 .elementor-element.elementor-element-6597c9b > .elementor-widget-container{margin:0px 0px 30px 0px;}.elementor-13880 .elementor-element.elementor-element-1e38157{font-size:12px;}.elementor-13880 .elementor-element.elementor-element-d7aa6a6 > .elementor-element-populated{padding:20px 20px 20px 20px;}.elementor-13880 .elementor-element.elementor-element-34d332f .elementor-heading-title{color:#ffffff;font-size:16px;}.elementor-13880 .elementor-element.elementor-element-34d332f > .elementor-widget-container{margin:0px 0px 30px 0px;}.elementor-13880 .elementor-element.elementor-element-9a29bb8{font-size:12px;}.elementor-13880 .elementor-element.elementor-element-8ca7f22{font-size:12px;}@media(min-width:768px){.elementor-13880 .elementor-element.elementor-element-ebe0760{width:34%;}.elementor-13880 .elementor-element.elementor-element-87a97fa{width:16%;}.elementor-13880 .elementor-element.elementor-element-def9486{width:19%;}.elementor-13880 .elementor-element.elementor-element-c1fcbb8{width:15%;}.elementor-13880 .elementor-element.elementor-element-d7aa6a6{width:24.937%;}}/* Start custom CSS for section, class: .elementor-element-ffe2750 */.footergeral a {color:#fff !important;}/* End custom CSS */
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500&display=swap" rel="stylesheet">
    <style>

        .disabled {
            pointer-events: none;
        }

        body {
            font: 300 15px/28px "Poppins",sans-serif;
            background-color: #F5F6F8;
            letter-spacing: 0;
            color: #677294;
        }

        .box-cadastro-para-todos {
            margin-bottom: 2rem;
        }
        .box-cadastro-para-todos h4 {
            margin-bottom: 1rem;
        }

        .topbar {
            width: 100%;
            background: #00B0FF;
            text-align: center;
            padding: 1rem;
            margin-bottom: 3rem;
        }

    </style>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css" />


    {{--Purple theme--}}
    <style>
        .topbar {
            background: #6e64ec;
        }
        .heading h2 {
            color: #6e64ec;
        }
        .btn-primary {
            background-color: #6e64ec;
        }
        .btn-primary:hover {
            background-color: #5550b1;
        }
        .elementor-13880 .elementor-element.elementor-element-ffe2750:not(.elementor-motion-effects-element-type-background), .elementor-13880 .elementor-element.elementor-element-ffe2750 > .elementor-motion-effects-container > .elementor-motion-effects-layer {
            background-color: #5550b1;
        }
        .elementor-social-icon {
            background-color: #ff0087 !important;
        }
    </style>

    @yield('css')
</head>
<body>
    <!-- END FOOTER -->
    <!-- BEGIN QUICK NAV -->
    @include('common.swal')

    <div class="topbar">
        <a href="https://site.lifepet.com.br" target="_blank">
            <img style="width:150px; margin:10px 20px 10px 10px;" src="{{ url('/') }}/assets/pages/img/lifepet-logotipo-branco-800px.png" class="img-fluid">
        </a>
    </div>
    @yield('content')

    <div data-elementor-type="wp-post" data-elementor-id="13880" class="elementor elementor-13880" data-elementor-settings="[]">
        <div class="elementor-inner">
            <div class="elementor-section-wrap">
                <section class="elementor-section elementor-top-section elementor-element elementor-element-ffe2750 elementor-section-height-min-height footergeral elementor-section-boxed elementor-section-height-default elementor-section-items-middle" data-id="ffe2750" data-element_type="section" data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
                    <div class="elementor-container elementor-column-gap-default">
                        <div class="elementor-row">
                            <div class="elementor-column elementor-col-20 elementor-top-column elementor-element elementor-element-ebe0760" data-id="ebe0760" data-element_type="column">
                                <div class="elementor-column-wrap elementor-element-populated">
                                    <div class="elementor-widget-wrap">
                                        <div class="elementor-element elementor-element-83fd067 elementor-widget elementor-widget-html" data-id="83fd067" data-element_type="widget" data-widget_type="html.default">
                                            <div class="elementor-widget-container">
                                                <a href="https://site.lifepet.com.br"><img style="width:150px; margin:10px 20px 10px 0px;" src="{{ url('/') }}/assets/pages/img/lifepet-logotipo-branco-800px.png" class="img-fluid"></a>
                                            </div>
                                        </div>
                                        <div class="elementor-element elementor-element-6cb54ae elementor-widget elementor-widget-spacer" data-id="6cb54ae" data-element_type="widget" data-widget_type="spacer.default">
                                            <div class="elementor-widget-container">
                                                <div class="elementor-spacer">
                                                    <div class="elementor-spacer-inner"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="elementor-element elementor-element-7112231 elementor-shape-circle elementor-grid-0 elementor-widget elementor-widget-social-icons" data-id="7112231" data-element_type="widget" data-widget_type="social-icons.default">
                                            <div class="elementor-widget-container">
                                                <div class="elementor-social-icons-wrapper elementor-grid">
                                                    <div class="elementor-grid-item">
                                                        <a class="elementor-icon elementor-social-icon elementor-social-icon-facebook-f elementor-repeater-item-7d22e7e" href="https://pt-br.facebook.com/lifepetsaude/" target="_blank">
                                                            <span class="elementor-screen-only">Facebook-f</span>
                                                            <i class="fab fa-facebook-f"></i>					</a>
                                                    </div>
                                                    <div class="elementor-grid-item">
                                                        <a class="elementor-icon elementor-social-icon elementor-social-icon-instagram elementor-repeater-item-d3b8a13" href="https://www.instagram.com/lifepetsaude/?hl=pt-br" target="_blank">
                                                            <span class="elementor-screen-only">Instagram</span>
                                                            <i class="fab fa-instagram"></i>					</a>
                                                    </div>
                                                    <div class="elementor-grid-item">
                                                        <a class="elementor-icon elementor-social-icon elementor-social-icon-youtube elementor-repeater-item-4e52577" href="https://www.youtube.com/channel/UCWNzVg87fNLxlEvLB26Dg4w" target="_blank">
                                                            <span class="elementor-screen-only">Youtube</span>
                                                            <i class="fab fa-youtube"></i>					</a>
                                                    </div>
                                                    <div class="elementor-grid-item">
                                                        <a class="elementor-icon elementor-social-icon elementor-social-icon-linkedin elementor-repeater-item-f7fbaf3" href="https://www.linkedin.com/company/lifepetsaude/" target="_blank">
                                                            <span class="elementor-screen-only">Linkedin</span>
                                                            <i class="fab fa-linkedin"></i>					</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="elementor-element elementor-element-3968dd0 elementor-widget elementor-widget-text-editor" data-id="3968dd0" data-element_type="widget" data-widget_type="text-editor.default">
                                            <div class="elementor-widget-container">
                                                <div class="elementor-text-editor elementor-clearfix"><p>@ LIFEPET BRASIL PLANO DE SAÚDE S.A.<br>CNPJ sob o nº 32.618.650/0001-91</p></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="elementor-column elementor-col-20 elementor-top-column elementor-element elementor-element-87a97fa elementor-hidden-phone" data-id="87a97fa" data-element_type="column">
                                <div class="elementor-column-wrap elementor-element-populated">
                                    <div class="elementor-widget-wrap">
                                        <div class="elementor-element elementor-element-c1255e5 elementor-widget elementor-widget-heading" data-id="c1255e5" data-element_type="widget" data-widget_type="heading.default">
                                            <div class="elementor-widget-container">
                                                <h2 class="elementor-heading-title elementor-size-default">Home</h2>		</div>
                                        </div>
                                        <div class="elementor-element elementor-element-36c486b elementor-widget elementor-widget-text-editor" data-id="36c486b" data-element_type="widget" data-widget_type="text-editor.default">
                                            <div class="elementor-widget-container">
                                                <div class="elementor-text-editor elementor-clearfix"><p><span style="color: #ffffff;"><a href="https://site.lifepet.com.br/orcamento/?step=1">Orçamento</a><br><a href="https://lifepet.com.br/#destaques">Destaques</a><br><a href="https://lifepet.com.br/#baixenossoapp">Baixe nosso App</a></span></p></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="elementor-column elementor-col-20 elementor-top-column elementor-element elementor-element-def9486 elementor-hidden-phone" data-id="def9486" data-element_type="column">
                                <div class="elementor-column-wrap elementor-element-populated">
                                    <div class="elementor-widget-wrap">
                                        <div class="elementor-element elementor-element-7994b8f elementor-widget elementor-widget-heading" data-id="7994b8f" data-element_type="widget" data-widget_type="heading.default">
                                            <div class="elementor-widget-container">
                                                <h2 class="elementor-heading-title elementor-size-default">Sobre a Lifepet</h2>		</div>
                                        </div>
                                        <div class="elementor-element elementor-element-4e81f34 elementor-widget elementor-widget-text-editor" data-id="4e81f34" data-element_type="widget" data-widget_type="text-editor.default">
                                            <div class="elementor-widget-container">
                                                <div class="elementor-text-editor elementor-clearfix"><p><a href="https://lifepet.com.br/quem-somos/"><span style="color: #ffffff;">Nossa História</span></a><span style="color: #ffffff;"><br><a href="https://lifepet.com.br/nossos-diferenciais-2/">Propósito, missão e valores</a></span><br><a href="https://lifepet.com.br/faq/"><span style="color: #ffffff;">FAQ</span></a></p></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="elementor-column elementor-col-20 elementor-top-column elementor-element elementor-element-d7aa6a6 elementor-hidden-phone" data-id="d7aa6a6" data-element_type="column">
                                <div class="elementor-column-wrap elementor-element-populated">
                                    <div class="elementor-widget-wrap">
                                        <div class="elementor-element elementor-element-34d332f elementor-widget elementor-widget-heading" data-id="34d332f" data-element_type="widget" data-widget_type="heading.default">
                                            <div class="elementor-widget-container">
                                                <h2 class="elementor-heading-title elementor-size-default">Mais</h2>		</div>
                                        </div>
                                        <div class="elementor-element elementor-element-9a29bb8 elementor-widget elementor-widget-text-editor" data-id="9a29bb8" data-element_type="widget" data-widget_type="text-editor.default">
                                            <div class="elementor-widget-container">
                                                <div class="elementor-text-editor elementor-clearfix"><p><a href="https://lifepet.com.br/blog/"><span style="color: #ffffff;">Blog<br></span></a><a href="https://lifepet.com.br/depoimentos/"><span style="color: #ffffff;">Depoimentos</span></a></p></div>
                                            </div>
                                        </div>
                                        <div class="elementor-element elementor-element-8ca7f22 elementor-widget elementor-widget-text-editor" data-id="8ca7f22" data-element_type="widget" data-widget_type="text-editor.default">
                                            <div class="elementor-widget-container">
                                                <div class="elementor-text-editor elementor-clearfix"><p><img class="alignnone wp-image-14637" style="letter-spacing: 0px;" src="https://lifepet.com.br/wp-content/uploads/2020/03/fundossl-300x72.png" alt="" width="175" height="42" srcset="https://lifepet.com.br/wp-content/uploads/2020/03/fundossl-300x72.png 300w, https://lifepet.com.br/wp-content/uploads/2020/03/fundossl.png 345w" sizes="(max-width: 175px) 100vw, 175px"></p></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <!-- END QUICK NAV -->
    <!--[if lt IE 9]>
    <script src="{{ url('/') }}/assets/global/plugins/respond.min.js"></script>
    <script src="{{ url('/') }}/assets/global/plugins/excanvas.min.js"></script>
    <script src="{{ url('/') }}/assets/global/plugins/ie8.fix.min.js"></script>
    <![endif]-->
    <!-- BEGIN CORE PLUGINS -->
    <script src="{{ url('/') }}/assets/global/plugins/jquery.min.js" type="text/javascript"></script>
    <script src="{{ url('/') }}/assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="{{ url('/') }}/assets/global/plugins/js.cookie.min.js" type="text/javascript"></script>
    <script src="{{ url('/') }}/assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
    <script src="{{ url('/') }}/assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
    <script src="{{ url('/') }}/assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
    <!-- END CORE PLUGINS -->
    <!-- BEGIN PAGE LEVEL PLUGINS -->


    <script src="{{ url('/') }}/assets/global/plugins/moment.min.js" type="text/javascript"></script>
    <script src="{{ url('/') }}/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
    <script src="{{ url('/') }}/assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js" type="text/javascript"></script>
    <script src="{{ url('/') }}/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
    <script src="{{ url('/') }}/assets/global/plugins/clockface/js/clockface.js" type="text/javascript"></script>
    <!-- END PAGE LEVEL PLUGINS -->
    <!-- BEGIN THEME GLOBAL SCRIPTS -->
    <script src="{{ url('/') }}/assets/global/scripts/app.min.js" type="text/javascript"></script>
    <!-- END THEME GLOBAL SCRIPTS -->
    <!-- BEGIN PAGE LEVEL SCRIPTS -->
    <script src="{{ url('/') }}/assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
    <script src="{{ url('/') }}/assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js" type="text/javascript"></script>
    <script src="{{ url('/') }}/assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
    <script src="{{ url('/') }}/assets/pages/scripts/components-select2.min.js" type="text/javascript"></script>
    <script src="{{ url('/') }}/assets/pages/scripts/components-multi-select.min.js" type="text/javascript"></script>
    <script src="{{ url('/') }}/assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script>

    <script src="{{ url('/') }}/assets/global/plugins/counterup/jquery.counterup.min.js" type="text/javascript"></script>

    <script src="{{ url('/') }}/assets/global/plugins/fullcalendar/fullcalendar.min.js" type="text/javascript"></script>

    <script src="{{ url('/') }}/assets/global/plugins/jquery.sparkline.min.js" type="text/javascript"></script>

    <script src="{{ url('/') }}/assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
    <script src="{{ url('/') }}/assets/global/plugins/jquery-validation/js/localization/messages_pt_BR.min.js" type="text/javascript"></script>
    <!-- END PAGE LEVEL PLUGINS -->
    <!-- BEGIN THEME GLOBAL SCRIPTS -->
    <script src="{{ url('/') }}/assets/global/scripts/app.min.js" type="text/javascript"></script>
    <!-- END THEME GLOBAL SCRIPTS -->
    <!-- BEGIN PAGE LEVEL SCRIPTS -->

    <!-- END PAGE LEVEL SCRIPTS -->
    <!-- BEGIN THEME LAYOUT SCRIPTS -->
    <script src="{{ url('/') }}/assets/layouts/layout/scripts/layout.min.js" type="text/javascript"></script>

    <script src="{{ url('/') }}/assets/layouts/global/scripts/quick-sidebar.min.js" type="text/javascript"></script>
    <script src="{{ url('/') }}/assets/layouts/global/scripts/quick-nav.min.js" type="text/javascript"></script>
    <script src="{{ url('/') }}/assets/global/plugins/dropzone/dropzone.min.js" type="text/javascript"></script>
    <script src="{{ url('/') }}/assets/global/plugins/introjs/intro.min.js" type="text/javascript"></script>
    <script src="{{ url('/') }}/assets/global/plugins/materialize/materialize.js?{{ time() }}" type="text/javascript"></script>
    <script src="{{ url('/') }}/assets/global/plugins/nouislider/nouislider.min.js" type="text/javascript"></script>
    <script src="{{ url('/') }}/assets/global/plugins/ion.rangeslider/js/ion.rangeSlider.min.js" type="text/javascript"></script>
    <script src="{{ url('/') }}/assets/global/plugins/jquery-mask/jquery.mask.min.js" type="text/javascript"></script>


    <!-- END THEME LAYOUT SCRIPTS -->
    <script>
        $(document).ready(function() {
            $('.one-click-only').click(function() {
                let $item = $(this);
                let $form = $item.closest('form');
                if($form && $form.valid()) {
                    $item.addClass('disabled');
                    $item.attr('disabled', 'disabled');
                    $item.prop('disabled', 'disabled');
                    $form.submit();
                    swal('Enviando dados...', 'Aguarde até o recarregamento da página', 'info');
                    return true;
                }
            });
        });
    </script>
@yield('scripts')
</body>
