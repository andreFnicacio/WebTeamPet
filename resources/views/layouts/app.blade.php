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
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"
          type="text/css">

    <link href="{{ url('/') }}/assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="{{ url('/') }}/assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="{{ url('/') }}/assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="{{ url('/') }}/assets/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet"
          type="text/css"/>
    <link href="{{ url('/') }}/assets/global/plugins/jquery-multi-select/css/multi-select.css" rel="stylesheet"
          type="text/css"/>
    <link href="{{ url('/') }}/assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css"/>
    <link href="{{ url('/') }}/assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="{{ url('/') }}/assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css"
          rel="stylesheet" type="text/css"/>
    <link href="{{ url('/') }}/assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css"
          rel="stylesheet" type="text/css"/>
    <link href="{{ url('/') }}/assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css"
          rel="stylesheet" type="text/css"/>
    <link href="{{ url('/') }}/assets/global/plugins/clockface/css/clockface.css" rel="stylesheet" type="text/css"/>
    <link href="{{ url('/') }}/assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="{{ url('/') }}/assets/global/plugins/morris/morris.css" rel="stylesheet" type="text/css"/>
    <link href="{{ url('/') }}/assets/global/plugins/fullcalendar/fullcalendar.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="{{ url('/') }}/assets/global/plugins/jqvmap/jqvmap/jqvmap.css" rel="stylesheet" type="text/css"/>
    <link href="{{ url('/') }}/assets/global/plugins/dropzone/dropzone.min.css" rel="stylesheet" type="text/css"/>
    <!-- END PAGE LEVEL PLUGINS -->
    <!-- BEGIN THEME GLOBAL STYLES -->
    <link href="{{ url('/') }}/assets/global/css/components-md.min.css" rel="stylesheet" id="style_components"
          type="text/css"/>
    <link href="{{ url('/') }}/assets/global/css/plugins-md.min.css" rel="stylesheet" type="text/css"/>
    <!-- END THEME GLOBAL STYLES -->
    <!-- BEGIN THEME LAYOUT STYLES -->
    <link href="{{ url('/') }}/assets/layouts/layout2/css/layout.css" rel="stylesheet" type="text/css"/>
    <link href="{{ url('/') }}/assets/layouts/layout2/css/themes/blue.min.css" rel="stylesheet" type="text/css"
          id="style_color"/>
    <link href="{{ url('/') }}/assets/layouts/layout2/css/custom.min.css" rel="stylesheet" type="text/css"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css"/>
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:100,400,500|Roboto:300,400,700,900|Open+Sans:400,300,600,700,900&subset=all"
          rel="stylesheet">
    <link rel="stylesheet" href="{{ url('/') }}/assets/global/plugins/nouislider/nouislider.min.css">
    <link rel="stylesheet" href="{{ url('/') }}/assets/global/plugins/ion.rangeslider/css/ion.rangeSlider.css">
    <link rel="stylesheet" href="{{ url('/') }}/assets/global/plugins/ion.rangeslider/css/ion.rangeSlider.Metronic.css">
    <style>
        .amcharts-chart-div a {
            display: none !important;
        }

        .page-header.navbar .top-menu .navbar-nav > li.dropdown .dropdown-toggle:hover {
            background: none;
        }

        [id$="error"] {
            color: #E26A6A !important;
        }

        input[required].error, textarea[required].error, select[required].error {
            border-color: #E26A6A !important;
        }

        @media (max-width: 600px) {
            .box.box-primary {
                overflow-y: scroll;
            }

        }

        /** Personal Styles **/
        html {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            /*text-shadow: #fff 0px 1px 1px;*/
        }

        .page-content-wrapper .page-content {
            padding: 1px 15px 10px;
        }

        .page-content-wrapper .page-content > .row > .col-sm-12,
        .page-content-wrapper .page-content > .row > .col-md-12 {
            padding: 0;
        }

        .page-content-wrapper .page-content .portlet .portlet-body.portlet-table {
            background: white;
        }

        .page-content .table th {
            text-transform: uppercase;
            font-family: 'Montserrat', sans-serif;
            font-weight: 500;
            letter-spacing: 0.63px;
            font-size: 10.5px;
            color: rgba(44, 44, 44, 0.55);
        }

        .page-content .table td {
            font-family: 'Roboto', sans-serif;
            font-weight: 500;
            color: rgba(88, 88, 88, 0.91);
        }

        span.uppercase {
            text-transform: uppercase !important;
        }

        h4.filter-label {
            text-transform: uppercase;
            font-family: 'Montserrat', sans-serif;
            font-weight: 500;
            letter-spacing: 0.63px;
            font-size: 10.5px;
            color: rgba(44, 44, 44, 0.55);
        }

        .page-content .table td {
            vertical-align: middle;
            padding: 13px 20px;
        }

        .page-content .table td span.number {
            font-weight: 600;
            color: #737373;
            letter-spacing: 1.25px;
        }

        .page-content .table th {
            border-bottom: 1px solid rgba(142, 142, 142, 0.38);
            padding: 15px 20px;
        }

        .page-sidebar-menu .nav-item i {
            color: hsla(217, 12%, 57%, 1) !important;
        }

        .page-content .content-header {
            display: table;
            width: 100%;
            padding: 35px 30px;
        }

        .page-content .content-header h1 {
            margin-top: 10px;
            font-family: 'Montserrat', sans-serif;
            letter-spacing: 0.6px;
            color: hsla(218, 12%, 43%, 0.78);
            text-transform: uppercase;
            font-weight: 100 !important;
            text-shadow: #e0e0e0 0px 1px 1px;
        }

        .portlet .table-wrapper {
            padding: 10px 15px;
            background: white;
        }

        .page-sidebar-menu .nav-item .title {
            text-transform: uppercase;
            font-family: 'Montserrat', sans-serif;
            font-weight: 400;
            font-size: 11px;
            letter-spacing: 0.6px;
            color: rgba(255, 255, 255, 0.6);
        }

        button.btn.flat, button.btn.flat:hover {
            transition: none;
            box-shadow: 0 0 0 0 !important;
        }

        .portlet.light > .portlet-title {
            margin-top: 20px;
        }

        .search-pagination {
            text-align: center;
        }

        .search-pagination ul.pagination {
            margin-bottom: 3px;
            font-family: 'Montserrat', sans-serif;
        }

        .search-pagination ul.pagination li:first-child a {
            border-left: none;
        }

        .search-pagination ul.pagination li a {
            border-top: none;
            border-bottom: none;
            padding: 6px 20px;
            border-radius: 0;
        }

        .search-pagination ul.pagination li:last-child a {
            border-right: none;
        }

        .btn-circle.edit {
            padding: 10px !important;
            border-radius: 100% !important;
        }

        .notificable {
            position: relative;
        }

        .notificable .notification-group {
            position: absolute;
            top: 8px;
            width: 100%;
            display: table;
            text-align: right;
            padding-right: 10px;
            z-index: 10;
        }

        .notificable .notification-group span.notification {
            background: white;
            padding: 1px 6px;
            font-family: 'Montserrat', sans-serif;
            font-size: 9px;
            margin-right: 3px;
            display: inline-block;
            min-width: 10px;
            float: right;
            border-radius: 20px;
            text-shadow: 0 0 0 transparent;
        }

        .notificable .notification-group span.notification.notification-unread {
            background: #F7CA18 !important;
        }

        .notificable .notification-group span.notification.notification-undone {
            background: #5C9BD1 !important;
        }

        .Timesheet--status::after {
            content: '';
            display: inline;
            position: absolute;
            right: 10px;
            top: 22%;
            padding: 4px;
            border-radius: 10px;
        }

        .Timesheet--status.Timesheet--status-active::after {
            background: #26C281;
        }

        .Timesheet--status.Timesheet--status-inactive::after {
            background: #ACB5C3;
        }

        .page-header.navbar .top-menu .navbar-nav > li.dropdown-user:hover a.dropdown-toggle * {
            color: #1f7fd5;
        }

        .page-header.navbar .top-menu .navbar-nav > li.dropdown-user a.dropdown-toggle .badge {
            width: 10px;
            height: 10px;
            display: block;
            padding: 0;
        }

    </style>
    {{--Toasts--}}
    <style>
        .z-depth-1, .toast {
            box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.14), 0 3px 1px -2px rgba(0, 0, 0, 0.12), 0 1px 5px 0 rgba(0, 0, 0, 0.2);
        }

        .z-depth-1-half {
            box-shadow: 0 3px 3px 0 rgba(0, 0, 0, 0.14), 0 1px 7px 0 rgba(0, 0, 0, 0.12), 0 3px 1px -1px rgba(0, 0, 0, 0.2);
        }

        /* 6dp elevation modified*/
        .z-depth-2 {
            box-shadow: 0 4px 5px 0 rgba(0, 0, 0, 0.14), 0 1px 10px 0 rgba(0, 0, 0, 0.12), 0 2px 4px -1px rgba(0, 0, 0, 0.3);
        }

        /* 12dp elevation modified*/
        .z-depth-3 {
            box-shadow: 0 8px 17px 2px rgba(0, 0, 0, 0.14), 0 3px 14px 2px rgba(0, 0, 0, 0.12), 0 5px 5px -3px rgba(0, 0, 0, 0.2);
        }

        /* 16dp elevation */
        .z-depth-4 {
            box-shadow: 0 16px 24px 2px rgba(0, 0, 0, 0.14), 0 6px 30px 5px rgba(0, 0, 0, 0.12), 0 8px 10px -7px rgba(0, 0, 0, 0.2);
        }

        /* 24dp elevation */
        .z-depth-5 {
            box-shadow: 0 24px 38px 3px rgba(0, 0, 0, 0.14), 0 9px 46px 8px rgba(0, 0, 0, 0.12), 0 11px 15px -7px rgba(0, 0, 0, 0.2);
        }

        #toast-container {
            display: block;
            position: fixed;
            z-index: 10000;
        }

        @media only screen and (max-width: 600px) {
            #toast-container {
                min-width: 100%;
                bottom: 0%;
            }
        }

        @media only screen and (min-width: 601px) and (max-width: 992px) {
            #toast-container {
                left: 5%;
                bottom: 7%;
                max-width: 90%;
            }
        }

        @media only screen and (min-width: 993px) {
            #toast-container {
                bottom: 10%;
                right: 7%;
                max-width: 86%;
            }
        }

        .toast {
            border-radius: 2px;
            top: 35px;
            width: auto;
            margin-top: 10px;
            position: relative;
            max-width: 100%;
            height: auto;
            min-height: 48px;
            line-height: 16px;
            word-break: break-all;
            background-color: #323232;
            padding: 10px 25px;
            font-size: 16px;
            font-weight: 300;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: default;
            font-family: "Roboto", sans-serif;
        }

        .toast .toast-action {
            color: #eeff41;
            font-weight: 500;
            margin-right: -25px;
            margin-left: 3rem;
        }

        .toast.rounded {
            border-radius: 24px;
        }

        @media only screen and (max-width: 600px) {
            .toast {
                width: 100%;
                border-radius: 0;
            }
        }

        @media only screen and (max-width: 992px) {
            .page-sidebar.navbar-collapse {
                position: absolute !important;
                z-index: 99999 !important;
                top: -68px !important;
                right: 0px !important;
                width: 100% !important;
                margin: 0px !important;
            }
        }

        .z-depth-1, nav, .card-panel, .card, .toast, .btn, .btn-large, .btn-floating, .dropdown-content, .collapsible, .side-nav {
            -webkit-box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.14), 0 1px 5px 0 rgba(0, 0, 0, 0.12), 0 3px 1px -2px rgba(0, 0, 0, 0.2) !important;
            box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.14), 0 1px 5px 0 rgba(0, 0, 0, 0.12), 0 3px 1px -2px rgba(0, 0, 0, 0.2) !important;
        }
    </style>
    <link href="{{ url('/') }}/assets/global/plugins/introjs/introjs.min.css" rel="stylesheet" type="text/css"/>
    @yield('css')
    {{--Purple Theme--}}
    <style>
        .purple-background {
            background-color: #6e64ec;
        }

        .page-header.navbar .page-logo {
            background-color: #6e64ec;
        }

        .btn.btn-outline.blue {
            border-color: #6e64ec;
            color: #6e64ec;
            background: 0 0;
        }

        .btn.btn-outline.blue.active, .btn.btn-outline.blue:active, .btn.btn-outline.blue:active:focus, .btn.btn-outline.blue:active:hover, .btn.btn-outline.blue:focus, .btn.btn-outline.blue:hover {
            border-color: #6e64ec;
            color: #FFF;
            background-color: #6e64ec;
        }

        .btn.blue:not(.btn-outline) {
            color: #FFF;
            background-color: #6e64ec;
            border-color: #6e64ec;
        }

        .btn.blue:not(.btn-outline).active, .btn.blue:not(.btn-outline):active, .btn.blue:not(.btn-outline):hover, .open > .btn.blue:not(.btn-outline).dropdown-toggle {
            color: #FFF;
            background-color: #5550b1;
            border-color: #5550b1;
        }
    </style>
    <style>
        strong.info-only, span.info-only, p.info-only {
            padding: 7px 7px !important;
            display: block;
            border: 1px solid #ccc;
            border-radius: 4px;
            background: #f1f1f1;
        }
    </style>
</head>

<body class="page-header-fixed page-sidebar-closed-hide-logo page-container-bg-solid page-content-white">
@include('common.swal')
@if (!Auth::guest())

    <div class="wrapper">
        <!-- BEGIN HEADER -->
        <div class="page-header navbar navbar-fixed-top" id="page-top">
            <!-- BEGIN HEADER INNER -->
            <div class="page-header-inner ">
                <!-- BEGIN LOGO -->
                <div class="page-logo">
                    <a href="{{ url('/') }}">
                        <img src="{{ url('/') }}/assets/pages/img/lifepet-logotipo-branco-300px.png" alt="logo"
                             class="logo-default"/>
                    </a>
                    <div class="menu-toggler sidebar-toggler">
                        <!-- DOC: Remove the above "hide" to enable the sidebar toggler button on header -->
                    </div>
                </div>
                <!-- END LOGO -->
                <!-- BEGIN RESPONSIVE MENU TOGGLER -->
                <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse"
                   data-target=".navbar-collapse"> </a>
                <!-- END RESPONSIVE MENU TOGGLER -->
                <!-- BEGIN PAGE ACTIONS -->
                <!-- DOC: Remove "hide" class to enable the page header actions -->
                @php
                    $showSearch = !(Entrust::hasRole(['CLINICAS','CLIENTE']) && !Entrust::hasRole(['ADMINISTRADOR', 'AUTORIZADOR', 'ATENDIMENTO', 'MEDICO_LIFEPET']));
                @endphp
                @if($showSearch)
                    <div class="page-actions">
                        <div class="btn-group">
                            <button type="button" class="btn btn-circle btn-outline red dropdown-toggle"
                                    data-toggle="dropdown">
                                <i class="fa fa-search"></i>&nbsp;
                                <span class="hidden-sm hidden-xs" id="current-searchable">@{{ current.title }}</span>&nbsp;
                                <i class="fa fa-angle-down"></i>
                            </button>
                            <ul class="dropdown-menu searchable-toggle" role="menu">
                                <li v-for="searchable in searchables">
                                    <a @click="setCurrent(searchable)">
                                        <i :class="searchable.icon"></i>
                                        <span class="title">@{{ searchable.title }}</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    {{-- @elseif(Entrust::hasRole(['MEDICO_LIFEPET']))
                        <div class="page-actions">
                            <div class="btn-group">
                                <button type="button" class="btn btn-circle btn-outline red dropdown-toggle" data-toggle="dropdown">
                                    <i class="fa fa-search"></i>&nbsp;
                                    <span class="hidden-sm hidden-xs" id="current-searchable">Pets</span>&nbsp;
                                    <i class="fa fa-angle-down"></i>
                                </button>
                                <ul class="dropdown-menu searchable-toggle" role="menu">
                                    <li>
                                        <a>
                                            <i class="fa fa-paw"></i>
                                            <span class="title">Pets</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div> --}}
                @endif
                <!-- END PAGE ACTIONS -->
                <!-- BEGIN PAGE TOP -->
                <div class="page-top">
                    @if($showSearch)
                        <!-- BEGIN HEADER SEARCH BOX -->
                        <!-- DOC: Apply "search-form-expanded" right after the "search-form" class to have half expanded search box -->
                        <form id="search-form" class="search-form search-form-expanded" :action="current.url"
                              method="GET">
                            <div class="input-group">
                                <input type="text" id="query" class="form-control" placeholder="Buscar..."
                                       name="search">
                                <span class="input-group-btn">
                                    <a href="javascript:;" class="btn submit">
                                        <i class="icon-magnifier"></i>
                                    </a>
                                </span>
                            </div>
                        </form>
                    @elseif(Entrust::hasRole(['MEDICO_LIFEPET']))
                        <form id="search-form" class="search-form search-form-expanded"
                              action="{{ route('pets.index') }}" method="GET">
                            <div class="input-group">
                                <input type="text" id="query" class="form-control" placeholder="Buscar..."
                                       name="search">
                                <span class="input-group-btn">
                            <a href="javascript:;" class="btn submit">
                                <i class="icon-magnifier"></i>
                            </a>
                        </span>
                            </div>
                        </form>
                    @endif
                    <!-- END HEADER SEARCH BOX -->
                    <!-- BEGIN TOP NAVIGATION MENU -->
                    <div class="top-menu">
                        <ul class="nav navbar-nav pull-right">
                            <!-- BEGIN NOTIFICATION DROPDOWN -->
                            <!-- DOC: Apply "dropdown-dark" class below "dropdown-extended" to change the dropdown styte -->
                            <!-- DOC: Apply "dropdown-hoverable" class after below "dropdown" and remove data-toggle="dropdown" data-hover="dropdown" data-close-others="true" attributes to enable hover dropdown mode -->
                            <!-- DOC: Remove "dropdown-hoverable" and add data-toggle="dropdown" data-hover="dropdown" data-close-others="true" attributes to the below A element with dropdown-toggle class -->

                            <!-- END NOTIFICATION DROPDOWN -->

                            {{--<li class="dropdown dropdown-user" style="cursor: pointer !important;">--}}
                            {{--<a href="javascript:;" style="cursor: default;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true" style="cursor: pointer !important;">--}}
                            {{--<img alt="" class="img-circle" src="{{ url('/') }}/assets/layouts/layout2/img/avatar3_small.jpg??" />--}}
                            {{--<i class="fa fa-chevron-right" aria-hidden="true"></i>--}}
                            {{--<span class="username username-hide-on-mobile"> {{ Auth::user() ->name}} </span>--}}
                            {{--</a>--}}
                            {{--<ul class="dropdown-menu dropdown-menu-default" >--}}
                            {{--@role('TIMESHEET')--}}
                            {{--<li>--}}
                            {{--<a href="{{ route('timesheet.index') }}" class="Timesheet--status Timesheet--status-{{ (new \App\Http\Controllers\TimesheetController)->status(false) ? 'active' : 'inactive' }}">--}}
                            {{--<i class="icon-clock"></i> Timesheet--}}
                            {{--</a>--}}
                            {{--</li>--}}
                            {{--@endrole--}}
                            {{--</ul>--}}
                            {{--</li>--}}

                            <li class="dropdown dropdown-extended quick-sidebar-toggler"
                                style="padding: 19px 24px 19px 6px;">

                                @if(\Entrust::hasRole(['CLINICAS']))
                                    @php
                                        $clinica = (new \Modules\Clinics\Entities\Clinicas)->where('id_usuario', Auth::user()->id)->first();
                                    @endphp
                                    @if ($clinica && $clinica->guiasPendentesAssinatura()->count())
                                        <link rel="stylesheet"
                                              href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.2/animate.min.css">
                                        <a href="{{ route('autorizador.assinaturasPendentes') }}"
                                           class="label label-warning yellow-saffron animated tada flow">
                                            <span class="badge bg-red font-white margin-right-10">{{ $clinica->guiasPendentesAssinatura()->count() }}</span>
                                            Assinaturas Pendentes
                                        </a>
                                    @endif
                                @endif
                            </li>

                            <li class="dropdown dropdown-user">
                                <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown"
                                   data-hover="dropdown" data-close-others="true">
                                    <span class="username username-hide-on-mobile"> {{ Auth::user()->name }} </span>
                                    <i class="fa fa-angle-down"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-default">
                                    @role('TIMESHEET')
                                    <li>
                                        <a href="{{ route('timesheet.index') }}" class="Timesheet--status">
                                            <i class="icon-clock"></i> Timesheet
                                            @if((new \App\Http\Controllers\TimesheetController)->status(false))
                                                <span style="float:right;">
                                                    <i class="fa fa-play text-success" style="margin: -2px;"></i>
                                                </span>
                                            @else
                                                <span style="float:right;">
                                                    <i class="fa fa-stop text-danger" style="margin: -2px;"></i>
                                                </span>
                                            @endif
                                        </a>
                                    </li>
                                    @endrole
                                    <li>
                                        <a href="{!! route('usuarios.mudarsenha') !!}">
                                            <i class="fa fa-lock"></i> Mudar Senha
                                        </a>
                                    </li>
                                    <li>
                                        <a data-toggle="modal" href="#modal-sugestoes">
                                            <i class="fa fa-envelope"></i> Envie sugestões
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            <!-- END USER LOGIN DROPDOWN -->
                            <!-- BEGIN QUICK SIDEBAR TOGGLER -->
                            <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
                            <form action="{{ url('/logout') }}" method="POST" id="formLogout"
                                  style="display: none; visibility: hidden;">
                                {{ csrf_field() }}
                            </form>

                            <li class="dropdown dropdown-extended quick-sidebar-toggler"
                                style="padding: 19px 24px 19px 6px;">
                                <button type="button" class="btn blue mt-ladda-btn ladda-button btn-outline"
                                        data-style="slide-up" data-spinner-color="#333"
                                        onclick="document.getElementById('formLogout').submit();">
                                    <span class="ladda-label">
                                        <i class="icon-logout"></i> Sair</span>
                                    <span class="ladda-spinner"></span>
                                </button>

                            </li>
                            <!-- END QUICK SIDEBAR TOGGLER -->
                        </ul>

                    </div>
                    <!-- END TOP NAVIGATION MENU -->
                </div>
                <!-- END PAGE TOP -->
            </div>
            <!-- END HEADER INNER -->
        </div>
        <!-- END HEADER -->
        <div class="clearfix"></div>
        <!-- END HEADER & CONTENT DIVIDER -->
        <!-- BEGIN CONTAINER -->
        <div class="page-container">
            <!-- Left side column. contains the logo and sidebar -->
            @if(Entrust::hasRole(['CLIENTE']))
                @include('layouts.sidebar-clientes')
            @else
                @include('layouts.sidebar')
            @endif

            <!-- BEGIN CONTENT -->
            <div class="page-content-wrapper">
                <!-- BEGIN CONTENT BODY -->
                <div class="page-content">
                    <!-- BEGIN PAGE HEADER-->
                    <!-- BEGIN THEME PANEL -->
                    @yield('content')
                    <!-- END THEME PANEL -->
                </div>
                <!-- END CONTENT BODY -->
            </div>
            <!-- END CONTENT -->
        </div>
    </div>
@else
    {{ \App\Http\Controllers\AppBaseController::setMessage('Você ficou muito tempo inativo. Entre novamente.', 'info', 'Sessão expirada') }}
    @include('auth.login')
@endif

<div class="page-footer">
    <div class="scroll-to-top">
        <i class="icon-arrow-up"></i>
    </div>
</div>
<!-- END FOOTER -->
<!-- BEGIN QUICK NAV -->
<div id="modal-sugestoes" class="modal fade" tabindex="-1" data-replace="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Enviar sugestão</h4>
            </div>
            <div class="modal-body col-sm-10 col-sm-offset-1">
                <form role="form" action="{{ route('ajuda.sugestoes.store') }}" method="POST">
                    {{ csrf_field() }}
                    <div class="form-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <h6>Título</h6>
                                    <div class="input-group col-sm-12">
                                                        <span class="input-group-addon input-left">
                                                            <i class="fa fa-pencil"></i>
                                                        </span>
                                        <input type="text" class="form-control text-uppercase" required name="titulo"
                                               placeholder="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <h6>Sugestão</h6>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <textarea name="corpo" id="corpo" class="form-control" required rows="4"
                                                      style="resize: none;"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <h6>Solicitante</h6>
                                    <div class="input-group col-sm-7">
                                                        <span class="input-group-addon input-left">
                                                            <i class="fa fa-user"></i>
                                                        </span>
                                        <input type="text" class="form-control text-uppercase" readonly=""
                                               value="{{ Auth::user()->name }}">
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-actions">
                        <div class="col-sm-3 pull-left" style="margin-top: 16px;
                                                                               display: inline-block;
                                                                               padding-left: 0;
                                                                               font-family: 'Roboto', sans-serif;
                                                                               font-size: 12px;
                                                                               color: #848484;">
                            <small>{{ (new \Carbon\Carbon())->format('d/m/Y') }}</small>
                        </div>
                        <button type="submit" class="btn blue pull-right">
                            <span>Enviar</span> <span class="fa fa-send"></span>
                        </button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                {{--<button type="button" data-dismiss="modal" class="btn dark btn-outline">Fechar</button>--}}
            </div>
        </div>
    </div>
</div>
<div class="quick-nav-overlay"></div>
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
<script src="{{ url('/') }}/assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js"
        type="text/javascript"></script>
<script src="{{ url('/') }}/assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="{{ url('/') }}/assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js"
        type="text/javascript"></script>
<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL PLUGINS -->


<script src="{{ url('/') }}/assets/global/plugins/moment.min.js" type="text/javascript"></script>
<script src="{{ url('/') }}/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"
        type="text/javascript"></script>
<script src="{{ url('/') }}/assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js"
        type="text/javascript"></script>
<script src="{{ url('/') }}/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"
        type="text/javascript"></script>
<script src="{{ url('/') }}/assets/global/plugins/clockface/js/clockface.js" type="text/javascript"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN THEME GLOBAL SCRIPTS -->
<script src="{{ url('/') }}/assets/global/scripts/app.min.js" type="text/javascript"></script>
<!-- END THEME GLOBAL SCRIPTS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="{{ url('/') }}/assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js"
        type="text/javascript"></script>
<script src="{{ url('/') }}/assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js"
        type="text/javascript"></script>
<script src="{{ url('/') }}/assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
<script src="{{ url('/') }}/assets/pages/scripts/components-select2.min.js" type="text/javascript"></script>
<script src="{{ url('/') }}/assets/pages/scripts/components-multi-select.min.js" type="text/javascript"></script>
<script src="{{ url('/') }}/assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script>
{{--<script src="{{ url('/') }}/assets/global/plugins/morris/morris.min.js" type="text/javascript"></script>--}}
{{--<script src="{{ url('/') }}/assets/global/plugins/morris/raphael-min.js" type="text/javascript"></script>--}}
{{--<script src="{{ url('/') }}/assets/global/plugins/counterup/jquery.waypoints.min.js" type="text/javascript"></script>--}}
<script src="{{ url('/') }}/assets/global/plugins/counterup/jquery.counterup.min.js" type="text/javascript"></script>
{{--<script src="{{ url('/') }}/assets/global/plugins/amcharts/amcharts/amcharts.js" type="text/javascript"></script>--}}
{{--<script src="{{ url('/') }}/assets/global/plugins/amcharts/amcharts/serial.js" type="text/javascript"></script>--}}
{{--<script src="{{ url('/') }}/assets/global/plugins/amcharts/amcharts/pie.js" type="text/javascript"></script>--}}
{{--<script src="{{ url('/') }}/assets/global/plugins/amcharts/amcharts/radar.js" type="text/javascript"></script>--}}
{{--<script src="{{ url('/') }}/assets/global/plugins/amcharts/amcharts/themes/light.js" type="text/javascript"></script>--}}
{{--<script src="{{ url('/') }}/assets/global/plugins/amcharts/amcharts/themes/patterns.js" type="text/javascript"></script>--}}
{{--<script src="{{ url('/') }}/assets/global/plugins/amcharts/amcharts/themes/chalk.js" type="text/javascript"></script>--}}
{{--<script src="{{ url('/') }}/assets/global/plugins/amcharts/ammap/ammap.js" type="text/javascript"></script>--}}
{{--<script src="{{ url('/') }}/assets/global/plugins/amcharts/ammap/maps/js/worldLow.js" type="text/javascript"></script>--}}
{{--<script src="{{ url('/') }}/assets/global/plugins/amcharts/amstockcharts/amstock.js" type="text/javascript"></script>--}}
<script src="{{ url('/') }}/assets/global/plugins/fullcalendar/fullcalendar.min.js" type="text/javascript"></script>
{{--<script src="{{ url('/') }}/assets/global/plugins/horizontal-timeline/horizontal-timeline.js" type="text/javascript"></script>--}}
{{--<script src="{{ url('/') }}/assets/global/plugins/flot/jquery.flot.min.js" type="text/javascript"></script>--}}
{{--<script src="{{ url('/') }}/assets/global/plugins/flot/jquery.flot.resize.min.js" type="text/javascript"></script>--}}
{{--<script src="{{ url('/') }}/assets/global/plugins/flot/jquery.flot.categories.min.js" type="text/javascript"></script>--}}
{{--<script src="{{ url('/') }}/assets/global/plugins/jquery-easypiechart/jquery.easypiechart.min.js" type="text/javascript"></script>--}}
<script src="{{ url('/') }}/assets/global/plugins/jquery.sparkline.min.js" type="text/javascript"></script>
{{--<script src="{{ url('/') }}/assets/global/plugins/jqvmap/jqvmap/jquery.vmap.js" type="text/javascript"></script>--}}
{{--<script src="{{ url('/') }}/assets/global/plugins/jqvmap/jqvmap/maps/jquery.vmap.russia.js" type="text/javascript"></script>--}}
{{--<script src="{{ url('/') }}/assets/global/plugins/jqvmap/jqvmap/maps/jquery.vmap.world.js" type="text/javascript"></script>--}}
{{--<script src="{{ url('/') }}/assets/global/plugins/jqvmap/jqvmap/maps/jquery.vmap.europe.js" type="text/javascript"></script>--}}
{{--<script src="{{ url('/') }}/assets/global/plugins/jqvmap/jqvmap/maps/jquery.vmap.germany.js" type="text/javascript"></script>--}}
{{--<script src="{{ url('/') }}/assets/global/plugins/jqvmap/jqvmap/maps/jquery.vmap.usa.js" type="text/javascript"></script>--}}
{{--<script src="{{ url('/') }}/assets/global/plugins/jqvmap/jqvmap/data/jquery.vmap.sampledata.js" type="text/javascript"></script>--}}
<script src="{{ url('/') }}/assets/global/plugins/jquery-validation/js/jquery.validate.min.js"
        type="text/javascript"></script>
<script src="{{ url('/') }}/assets/global/plugins/jquery-validation/js/localization/messages_pt_BR.min.js"
        type="text/javascript"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN THEME GLOBAL SCRIPTS -->
<script src="{{ url('/') }}/assets/global/scripts/app.min.js" type="text/javascript"></script>
<!-- END THEME GLOBAL SCRIPTS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
{{--<script src="{{ url('/') }}/assets/pages/scripts/dashboard.min.js" type="text/javascript"></script>--}}
<!-- END PAGE LEVEL SCRIPTS -->
<!-- BEGIN THEME LAYOUT SCRIPTS -->
<script src="{{ url('/') }}/assets/layouts/layout/scripts/layout.min.js" type="text/javascript"></script>
{{--<script src="{{ url('/') }}/assets/layouts/layout2/scripts/demo.min.js" type="text/javascript"></script>--}}
<script src="{{ url('/') }}/assets/layouts/global/scripts/quick-sidebar.min.js" type="text/javascript"></script>
<script src="{{ url('/') }}/assets/layouts/global/scripts/quick-nav.min.js" type="text/javascript"></script>
<script src="{{ url('/') }}/assets/global/plugins/dropzone/dropzone.min.js" type="text/javascript"></script>
<script src="{{ url('/') }}/assets/global/plugins/introjs/intro.min.js" type="text/javascript"></script>
<script src="{{ url('/') }}/assets/global/plugins/materialize/materialize.js?{{ time() }}"
        type="text/javascript"></script>
<script src="{{ url('/') }}/assets/global/plugins/nouislider/nouislider.min.js" type="text/javascript"></script>
<script src="{{ url('/') }}/assets/global/plugins/ion.rangeslider/js/ion.rangeSlider.min.js"
        type="text/javascript"></script>
<script src="{{ url('/') }}/assets/global/plugins/jquery-mask/jquery.mask.min.js" type="text/javascript"></script>
{{-- <script src="{{ url('/') }}/assets/global/plugins/jquery.pulsate.min.js" type="text/javascript"></script> --}}

<!-- END THEME LAYOUT SCRIPTS -->
<script>
    $(document).ready(function () {
        // Masks
        $('.money').mask('000.000.000.000.000,00', {reverse: true});
        $('.cpf').mask('000.000.000-00', {reverse: true});

        // $('.cel').mask('(00) 90000-0000');
        $('.tel').blur(function () {
            if ($(this).val().length < 14) {
                swal('Atenção!', 'O número do telefone deve conter todos os caracteres!', 'warning');
                $(this).val('');
            }
        });

        $('.tel').focusout(function () {
            var phone, element;
            element = $(this);
            element.unmask();
            phone = element.val().replace(/\D/g, '');
            if (phone.length > 10) {
                element.mask("(00) 00000-0000");
            } else {
                element.mask("(00) 0000-00000");
            }
        }).trigger('focusout');

        var optionsCpfCnpj = {
            onKeyPress: function (cpfcnpj, e, field, options) {
                var masks = ['000.000.000-000', '00.000.000/0000-00'];
                var mask = (cpfcnpj.length > 14) ? masks[1] : masks[0];
                $('.cpf_cnpj').mask(mask, options);
            }
        };
        $('.cpf_cnpj').mask('000.000.000-000', optionsCpfCnpj);

        $(":checkbox").attr("autocomplete", "off");
        $('#clickmewow').click(function () {
            $('#radio1003').attr('checked', 'checked');
        });
        $('[data-toggle="tooltip"]').tooltip();
        $('input[nouirange]').each(function (k, v) {
            noUiSlider.create(v, {
                start: 1,
                range: {
                    'min': 0,
                    'max': 100
                }
            });
        });
        $('input[ionrange]').ionRangeSlider({
            min: 1,
            max: 100
        });
    });
</script>
<script type="text/javascript" src="{{ mix('js/app.js') }}?{{ time() }}"></script>
@if(\Entrust::hasRole(['CLIENTE']))
    <script type="text/javascript">
        jQuery(document).ready(function ($) {
            var $inputs = $("input:not(.for-client):not([type=hidden]), select:not(.for-client), textarea:not(.for-client)");
            $inputs.each(function (index, el) {
                var $el = $(el);
                $el.attr('readonly', 'readonly');
                $el.attr('disabled', 'disabled');
            });
        });
    </script>
    <script type="text/javascript">
        var Intros = function () {
            var introOptions = {
                exitOnEsc: false,
                /* Close introduction when clicking on overlay layer? */
                exitOnOverlayClick: false,
                nextLabel: 'Próximo &rarr;',
                /* Previous button label in tooltip box */
                prevLabel: '&larr; Anterior',
                /* Skip button label in tooltip box */
                skipLabel: 'Pular',
                /* Done button label in tooltip box */
                doneLabel: 'Entendido',

            };
            this.mudarSenha = function () {
                var i = introJs();
                i.setOptions($.extend(introOptions, {
                    steps: [
                        {
                            intro: "Bem-vindo a nova área do cliente Lifepet."
                        },
                        {
                            element: document.querySelectorAll('#menu-dados-cliente')[0],
                            intro: 'Aqui você terá acesso aos seus dados cadastrais e poderá solicitar modificações em caso de divergências.'
                        },
                        {
                            element: document.querySelectorAll('#menu-pets-cliente')[0],
                            intro: 'Aqui você poderá conferir os dados de cada pet, visualizar carências e o históricos de utilizações de cada plano.'
                        },
                        {
                            element: document.querySelectorAll('#menu-financeiro-cliente')[0],
                            intro: 'Esse menu lhe permite acessar o seu histórico dos seus pagamentos, ver a situação das suas cobranças e obter a 2ª via do boleto.'
                        },
                        {
                            element: document.querySelectorAll('#menu-documentos-cliente')[0],
                            intro: 'Na área de documentos você poderá obter documentos assinados (atualização em andamento) e baixar uma cópia da minuta de contrato.'
                        },
                        {
                            element: document.querySelectorAll('#form-mudar-senha')[0],
                            intro: 'No seu primeiro acesso, pedimos para que você modifique sua senha padrão para uma de sua escolha.'
                        }
                    ]
                }));
                i.start();
            }
        }
        var AppIntros = new Intros();
    </script>
@endif

<script>
    $(document).ready(function () {
        var $forms = $('.portlet .portlet-body form[id][method=post]').not('.novalidate');
        $forms.validate({
            invalidHandler: function (form, validator) {
                var errors = validator.numberOfInvalids();
                if (errors) {
                    window.emitindoGuia = false;
                    validator.errorList[0].element.focus();
                }
            }
        });
    });
</script>
@yield('scripts')
{{--<script src="https://www.gstatic.com/firebasejs/4.6.2/firebase.js"></script>--}}
{{--<script src="https://www.gstatic.com/firebasejs/4.2.0/firebase-messaging.js"></script>--}}
{{--<script>--}}
{{--// Initialize Firebase--}}
{{--var config = {--}}
{{--apiKey: "AIzaSyBw2tmjuO430j8NBSLfspn7zc5Tv-ykVvU",--}}
{{--authDomain: "kickstarter-f1c88.firebaseapp.com",--}}
{{--databaseURL: "https://kickstarter-f1c88.firebaseio.com",--}}
{{--projectId: "kickstarter-f1c88",--}}
{{--storageBucket: "",--}}
{{--messagingSenderId: "107791906777"--}}
{{--};--}}
{{--firebase.initializeApp(config);--}}
{{--</script>--}}
</div>
</body>
</html>
