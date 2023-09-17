<!DOCTYPE html>
<html>
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

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css">

        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
        <style>
            .elementor-13880 .elementor-element.elementor-element-ffe2750 > .elementor-container{min-height:297px;}.elementor-13880 .elementor-element.elementor-element-ffe2750 > .elementor-container:after{content:"";min-height:inherit;}.elementor-13880 .elementor-element.elementor-element-ffe2750:not(.elementor-motion-effects-element-type-background), .elementor-13880 .elementor-element.elementor-element-ffe2750 > .elementor-motion-effects-container > .elementor-motion-effects-layer{background-color:#009CF5;}.elementor-13880 .elementor-element.elementor-element-ffe2750{transition:background 0.3s, border 0.3s, border-radius 0.3s, box-shadow 0.3s;padding:40px 00px 20px 0px;}.elementor-13880 .elementor-element.elementor-element-ffe2750 > .elementor-background-overlay{transition:background 0.3s, border-radius 0.3s, opacity 0.3s;}.elementor-13880 .elementor-element.elementor-element-ebe0760 > .elementor-column-wrap > .elementor-widget-wrap > .elementor-widget:not(.elementor-widget__width-auto):not(.elementor-widget__width-initial):not(:last-child):not(.elementor-absolute){margin-bottom:10px;}.elementor-13880 .elementor-element.elementor-element-ebe0760 > .elementor-element-populated{margin:0px 20px 0px 0px;padding:20px 30px 40px 30px;}.elementor-13880 .elementor-element.elementor-element-6cb54ae .elementor-spacer-inner{height:20px;}.elementor-13880 .elementor-element.elementor-element-7112231 .elementor-repeater-item-7d22e7e.elementor-social-icon{background-color:#00d7ff;}.elementor-13880 .elementor-element.elementor-element-7112231 .elementor-repeater-item-7d22e7e.elementor-social-icon i{color:#ffffff;}.elementor-13880 .elementor-element.elementor-element-7112231 .elementor-repeater-item-7d22e7e.elementor-social-icon svg{fill:#ffffff;}.elementor-13880 .elementor-element.elementor-element-7112231 .elementor-repeater-item-d3b8a13.elementor-social-icon{background-color:#00d7ff;}.elementor-13880 .elementor-element.elementor-element-7112231 .elementor-repeater-item-d3b8a13.elementor-social-icon i{color:#ffffff;}.elementor-13880 .elementor-element.elementor-element-7112231 .elementor-repeater-item-d3b8a13.elementor-social-icon svg{fill:#ffffff;}.elementor-13880 .elementor-element.elementor-element-7112231 .elementor-repeater-item-4e52577.elementor-social-icon{background-color:#00d7ff;}.elementor-13880 .elementor-element.elementor-element-7112231 .elementor-repeater-item-f7fbaf3.elementor-social-icon{background-color:#00d7ff;}.elementor-13880 .elementor-element.elementor-element-7112231{--grid-template-columns:repeat(0, auto);--grid-column-gap:5px;--grid-side-margin:5px;--grid-row-gap:0px;--grid-bottom-margin:0px;}.elementor-13880 .elementor-element.elementor-element-7112231 .elementor-widget-container{justify-content:flex-start;}.elementor-13880 .elementor-element.elementor-element-7112231 .elementor-social-icon{font-size:18px;}.elementor-13880 .elementor-element.elementor-element-3968dd0{color:#ffffff;font-size:15px;}.elementor-13880 .elementor-element.elementor-element-87a97fa > .elementor-element-populated{padding:20px 20px 20px 20px;}.elementor-13880 .elementor-element.elementor-element-c1255e5 .elementor-heading-title{color:#ffffff;font-size:16px;}.elementor-13880 .elementor-element.elementor-element-c1255e5 > .elementor-widget-container{margin:0px 0px 30px 0px;}.elementor-13880 .elementor-element.elementor-element-36c486b{font-size:12px;}.elementor-13880 .elementor-element.elementor-element-def9486 > .elementor-element-populated{padding:20px 20px 20px 20px;}.elementor-13880 .elementor-element.elementor-element-7994b8f .elementor-heading-title{color:#ffffff;font-size:16px;}.elementor-13880 .elementor-element.elementor-element-7994b8f > .elementor-widget-container{margin:0px 0px 30px 0px;}.elementor-13880 .elementor-element.elementor-element-4e81f34{font-size:12px;}.elementor-13880 .elementor-element.elementor-element-c1fcbb8 > .elementor-element-populated{padding:20px 20px 20px 20px;}.elementor-13880 .elementor-element.elementor-element-6597c9b .elementor-heading-title{color:#ffffff;font-size:16px;}.elementor-13880 .elementor-element.elementor-element-6597c9b > .elementor-widget-container{margin:0px 0px 30px 0px;}.elementor-13880 .elementor-element.elementor-element-1e38157{font-size:12px;}.elementor-13880 .elementor-element.elementor-element-d7aa6a6 > .elementor-element-populated{padding:20px 20px 20px 20px;}.elementor-13880 .elementor-element.elementor-element-34d332f .elementor-heading-title{color:#ffffff;font-size:16px;}.elementor-13880 .elementor-element.elementor-element-34d332f > .elementor-widget-container{margin:0px 0px 30px 0px;}.elementor-13880 .elementor-element.elementor-element-9a29bb8{font-size:12px;}.elementor-13880 .elementor-element.elementor-element-8ca7f22{font-size:12px;}@media(min-width:768px){.elementor-13880 .elementor-element.elementor-element-ebe0760{width:34%;}.elementor-13880 .elementor-element.elementor-element-87a97fa{width:16%;}.elementor-13880 .elementor-element.elementor-element-def9486{width:19%;}.elementor-13880 .elementor-element.elementor-element-c1fcbb8{width:15%;}.elementor-13880 .elementor-element.elementor-element-d7aa6a6{width:24.937%;}}/* Start custom CSS for section, class: .elementor-element-ffe2750 */.footergeral a {color:#fff !important;}/* End custom CSS */
        </style>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500&display=swap" rel="stylesheet">
        <style>
            body {
                font: 300 15px/28px "Nunito",sans-serif;
                letter-spacing: 0;
            }s
        </style>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css" />
        @yield('css')
    </head>
    <body>
    <!-- END FOOTER -->
    <!-- BEGIN QUICK NAV -->
    @include('common.swal')

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
    <!-- END CORE PLUGINS -->
    <!-- BEGIN PAGE LEVEL PLUGINS -->


    <!-- END PAGE LEVEL PLUGINS -->
    <!-- BEGIN THEME GLOBAL SCRIPTS -->
    <!-- END THEME GLOBAL SCRIPTS -->
    <!-- BEGIN PAGE LEVEL SCRIPTS -->
    <script src="{{ url('/') }}/assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
    <script src="{{ url('/') }}/assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js" type="text/javascript"></script>
    <script src="{{ url('/') }}/assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
    <script src="{{ url('/') }}/assets/pages/scripts/components-select2.min.js" type="text/javascript"></script>
    <script src="{{ url('/') }}/assets/pages/scripts/components-multi-select.min.js" type="text/javascript"></script>

    <script src="{{ url('/') }}/assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
    <script src="{{ url('/') }}/assets/global/plugins/jquery-validation/js/localization/messages_pt_BR.min.js" type="text/javascript"></script>
    <!-- END PAGE LEVEL PLUGINS -->

    <!-- END THEME GLOBAL SCRIPTS -->
    <!-- BEGIN PAGE LEVEL SCRIPTS -->

    <!-- END PAGE LEVEL SCRIPTS -->

    <script src="{{ url('/') }}/assets/global/plugins/dropzone/dropzone.min.js" type="text/javascript"></script>

    <script src="{{ url('/') }}/assets/global/plugins/jquery-mask/jquery.mask.min.js" type="text/javascript"></script>


    <!-- END THEME LAYOUT SCRIPTS -->

    @yield('scripts')
    </body>
</html>

