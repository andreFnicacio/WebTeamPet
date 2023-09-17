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
    <link href="{{ url('/') }}/assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ url('/') }}/assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
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
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,400;1,100;1,200;1,400;1,600&display=swap" rel="stylesheet">
    <style>


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

        span.page-title.separator {
            border-left: 2px solid white;
            height: 44px;
            display: inline-block;
            padding-left: 20px;
            vertical-align: middle;
            font-size: 26px;
            color: white;
            font-family: 'Poppins';
            font-style: italic;
            line-height: 50px;
        }

    </style>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css" />
    @yield('css')
</head>
<body>
    <!-- END FOOTER -->
    <!-- BEGIN QUICK NAV -->
    @include('common.swal')

    <div class="topbar">
        <div class="container text-left" style="padding: 0">
            <a href="https://app.lifepet.com.br" style="display: inline-block;">
                <img style="width:150px; margin:10px 20px 10px 10px;" src="https://lifepet.com.br/wp-content/themes/lifepet2020/assets/img/lifepet-logo.svg" class="img-fluid">
            </a>
            
            <span class="page-title separator">
                @yield('page-title')
            </span>
        </div>
        
    </div>
    @yield('content')

    

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
  
    <script src="{{ url('/') }}/assets/global/plugins/nouislider/nouislider.min.js" type="text/javascript"></script>
    <script src="{{ url('/') }}/assets/global/plugins/ion.rangeslider/js/ion.rangeSlider.min.js" type="text/javascript"></script>
    <script src="{{ url('/') }}/assets/global/plugins/jquery-mask/jquery.mask.min.js" type="text/javascript"></script>


    <!-- END THEME LAYOUT SCRIPTS -->

    @yield('scripts')
</body>
