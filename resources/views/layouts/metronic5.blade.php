@php
    if(Entrust::hasRole(['CLIENTE'])) {
        $cliente = \App\Models\Clientes::where('id_usuario', auth()->user()->id)->first();
        if(!$cliente) {
            abort(404, "Não foi encontrado um cliente vinculado a esse usuário. Entre em contato com a Lifepet.");
        }
    }
    $pets = \App\Models\Pets::petsFromUser();
    $cliente = \App\Models\Clientes::where('id_usuario', auth()->user()->id)->first();
    $emAtraso = $cliente->status_pagamento === "EM ATRASO";
    $encaminhamentos = \Modules\Guides\Entities\HistoricoUso::whereIn('id_pet', $pets->pluck('id'))
                                                ->where('tipo_atendimento', \Modules\Guides\Entities\HistoricoUso::TIPO_ENCAMINHAMENTO)
                                                ->whereNull('realizado_em')
                                                ->where('status', \Modules\Guides\Entities\HistoricoUso::STATUS_LIBERADO)
                                                ->count('id');
    $historicos = \Modules\Guides\Entities\HistoricoUso::whereIn('id_pet', $pets->pluck('id'))
                                         ->orderBy('created_at', 'DESC')
                                         ->get();
    $badges = \App\Helpers\Utils::petBadges($pets->all());
    $links = (object) [
        'pets'       => route('cliente.pets'),
        'financeiro' => route('cliente.financeiro'),
        'dados'      => route('cliente.dados'),
        'documentos' => route('cliente.documentos')
    ];
@endphp

        <!DOCTYPE html>
<!--
Template Name: Metronic - Responsive Admin Dashboard Template build with Twitter Bootstrap 4
Version: 5.0.5
Author: KeenThemes
Website: http://www.keenthemes.com/
Contact: support@keenthemes.com
Follow: www.twitter.com/keenthemes
Dribbble: www.dribbble.com/keenthemes
Like: www.facebook.com/keenthemes
Purchase: http://themeforest.net/item/metronic-responsive-admin-dashboard-template/4021469?ref=keenthemes
Renew Support: http://themeforest.net/item/metronic-responsive-admin-dashboard-template/4021469?ref=keenthemes
License: You must have a valid license purchased only from themeforest(the above link) in order to legally use the theme for your project.
-->
<html lang="en">
<!-- begin::Head -->
<head>
    <meta charset="UTF-8">
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
    <title>Lifepet Saúde - @yield('title')</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- begin::Web font -->
    <script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.16/webfont.js"></script>
    <script>
        WebFont.load({
            google: {"families": ["Poppins:300,400,500,600,700", "Roboto:300,400,500,600,700"]},
            active: function () {
                sessionStorage.fonts = true;
            }
        });
    </script>

    <link href="{{ url('/') }}/assets/global/css/components-md.min.css" rel="stylesheet" id="style_components"
          type="text/css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css">
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/2.3.11/css/AdminLTE.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/2.3.11/css/skins/_all-skins.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/iCheck/1.0.2/skins/square/_all.css"> -->


    {{--<link href="{{ url('/') }}/assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />--}}
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"
          type="text/css">

    <link href="{{ url('/') }}/assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet"
          type="text/css"/>

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


    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css"/>
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:100,400,500|Roboto:300,400|Open+Sans:400,300,600,700&subset=all"
          rel="stylesheet">
    <link rel="stylesheet" href="{{ url('/') }}/assets/global/plugins/nouislider/nouislider.min.css">
    <link rel="stylesheet" href="{{ url('/') }}/assets/global/plugins/ion.rangeslider/css/ion.rangeSlider.css">
    <link rel="stylesheet" href="{{ url('/') }}/assets/global/plugins/ion.rangeslider/css/ion.rangeSlider.Metronic.css">

    <style>
        a.link-discreto {
            color: inherit;
        }

        a.link-discreto:hover {
            color: inherit;
            text-decoration: none;
            text-underline: none;
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
            font-weight: 300;
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

        .input-group-btn {
            font-size: 0;
            white-space: nowrap;
        }

        .btn.m-btn--pill {
            -webkit-border-radius: 60px !important;
            -moz-border-radius: 60px !important;
            border-radius: 60px !important;
            font-size: 12px !important;
            padding: 8px 14px 7px !important;
        }

        .m-widget17 .m-widget17__stats .m-widget17__items .m-widget17__item {
            -webkit-box-shadow: 0px 1px 15px 1px rgba(0, 0, 0, 0.12) !important;
            -moz-box-shadow: 0px 1px 15px 1px rgba(0, 0, 0, 0.12) !important;
            box-shadow: 0px 1px 15px 1px rgba(0, 0, 0, 0.12) !important;
        }

    </style>
    <link href="{{ url('/') }}/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css"
          rel="stylesheet" type="text/css"/>
    <link href="{{ url('/') }}/assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css"/>

    <!--end::Web font -->
    <!--begin::Base Styles -->
    <!--begin::Page Vendors -->
    <link href="{{ url('/') }}/assets/metronic5/dist/demo2/assets/vendors/custom/fullcalendar/fullcalendar.bundle.css"
          rel="stylesheet" type="text/css"/>
    <!--end::Page Vendors -->
    <link href="{{ url('/') }}/assets/metronic5/dist/demo2/assets/vendors/base/vendors.bundle.css" rel="stylesheet"
          type="text/css"/>
    <link href="{{ url('/') }}/assets/metronic5/dist/demo2/assets/demo/demo2/base/style.bundle.css" rel="stylesheet"
          type="text/css"/>
    <!--end::Base Styles -->
    <link rel="shortcut icon"
          href="{{ url('/') }}/assets/metronic5/dist/demo2/assets/demo/demo2/media/img/logo/favicon.ico"/>

    @section('css')
        <style>
            table.dataTable thead th, table.dataTable thead td {
                vertical-align: bottom;
                border-bottom: 2px solid #f4f5f8;
            }

            table.dataTable.no-footer {
                border-bottom: none;
            }

            table td span.badge {
                line-height: 16.5px;
            }

            .input-group-btn {
                font-size: 0;
                white-space: nowrap;
            }

            .input-group-addon, .input-group-btn {
                width: auto;
                white-space: nowrap;
                vertical-align: middle;
            }

            .input-group .form-control, .input-group-addon, .input-group-btn {
                display: table-cell;
            }

            .input-group, .input-group-btn, .input-group-btn > .btn {
                position: relative;
            }

            .datepicker > div {
                display: block;
            }


            .portlet .actions.floating {
                position: fixed;
                z-index: 99999;
                bottom: 40px;
                right: 20px;
            }

            .portlet .actions.floating .btn-group.btn-group-devided.btn.m-btn--pill.btn-outline-primary.btn-sm {
                overflow: hidden;
                position: relative;
                background: white;
            }

            .portlet .actions.floating .btn-group.btn-group-devided.btn.m-btn--pill.btn-outline-primary.btn-sm .reveal {
                width: 0;
                position: absolute;
                right: -10px;
                animation-timing-function: ease-out;
                transition: right 0.10s;
            }

            .portlet .actions.floating .btn-group.btn-group-devided.btn.m-btn--pill.btn-outline-primary.btn-sm:hover .reveal {
                right: 100%;
                animation-timing-function: ease-in;
                transition: right 0.15s;
            }

            .portlet .actions.floating .btn-group.btn-group-devided.btn.m-btn--pill.btn-outline-primary.btn-sm:hover .icon {
                opacity: 0;
            }

            p.aviso {
                padding-top: 30px;
                padding-left: 20px;
                padding-right: 55px;
                background: #f4516c;
                padding-bottom: 20px;
                margin-top: 20px;
                position: relative;
                color: white;
            }

            p.aviso-azul {
                background-color: #48baf9;
            }

            p.aviso .close {
                position: absolute;
                right: 20px;
                top: 50%;
                margin-top: -8px;
                text-shadow: none;
            }

            p.aviso .close:hover {
                color: white;
            }

            @media (max-width: 992px) {
                p.aviso {
                    margin-top: 130px;
                }

                .has-aviso.m-grid__item.m-grid__item--fluid.m-grid.m-grid--ver.m-container.m-container--responsive.m-container--xxl.m-page__container {
                    padding-top: 0;
                }
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

            .z-depth-1, nav, .card-panel, .card, .toast, .btn, .btn-large, .btn-floating, .dropdown-content, .collapsible, .side-nav {
                -webkit-box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.14), 0 1px 5px 0 rgba(0, 0, 0, 0.12), 0 3px 1px -2px rgba(0, 0, 0, 0.2) !important;
                box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.14), 0 1px 5px 0 rgba(0, 0, 0, 0.12), 0 3px 1px -2px rgba(0, 0, 0, 0.2) !important;
            }
        </style>
    @show

</head>
<!-- end::Head -->
<!-- end::Body -->
<body class="m-page--wide m-header--fixed m-header--fixed-mobile m-footer--push m-aside--offcanvas-default">
@include('common.swal')
<!-- begin:: Page -->
<div class="m-grid m-grid--hor m-grid--root m-page">
    <!-- begin::Header  Parte que deverá ser fixa para todas as páginas. -->
    <header class="m-grid__item		m-header " data-minimize="minimize" data-minimize-offset="200"
            data-minimize-mobile-offset="200">
        <div class="m-header__top">
            <div class="m-container m-container--responsive m-container--xxl m-container--full-height m-page__container">
                <div class="m-stack m-stack--ver m-stack--desktop">
                    <!-- begin::Brand -->
                    <div class="m-stack__item m-brand">
                        <div class="m-stack m-stack--ver m-stack--general m-stack--inline">
                            <div class="m-stack__item m-stack__item--middle m-brand__logo">
                                <a href="{{ url('/') }}" class="m-brand__logo-wrapper">
                                    <img alt=""
                                         src="{{ url('/') }}/assets/metronic5/dist/default/assets/app/media/img/logos/logo-2.png"
                                         height="65px"/>
                                </a>
                            </div>
                            <div class="m-stack__item m-stack__item--middle m-brand__tools">

                                <!-- begin::Responsive Header Menu Toggler-->
                                <a id="m_aside_header_menu_mobile_toggle" href="javascript:;"
                                   class="m-brand__icon m-brand__toggler m--visible-tablet-and-mobile-inline-block">
                                    <span></span>
                                </a>
                                <!-- end::Responsive Header Menu Toggler-->

                            </div>
                        </div>
                    </div>
                    <!-- end::Brand -->
                    <!-- begin::Topbar -->
                    <div class="m-stack__item m-stack__item--fluid m-header-head" id="m_header_nav">
                        <div id="m_header_topbar" class="m-topbar  m-stack m-stack--ver m-stack--general">
                            <div class="m-stack__item m-topbar__nav-wrapper">
                                <ul class="m-topbar__nav m-nav m-nav--inline">
                                    <li class="m-nav__item m-topbar__user-profile nomepessoa m-topbar__user-profile--img  m-dropdown m-dropdown--medium m-dropdown--arrow m-dropdown--header-bg-fill m-dropdown--align-right m-dropdown--mobile-full-width m-dropdown--skin-light"
                                        data-dropdown-toggle="click">
                                        <a href="#" class="m-nav__link m-dropdown__toggle active">
                                            <span class="m-topbar__userpic m--hide">
                                                <img src="{{ url('/') }}/assets/metronic5/dist/demo2/assets/app/media/img/users/user4.jpg"
                                                     class="m--img-rounded m--marginless m--img-centered" alt=""/>
                                            </span>
                                            <span class="m-topbar__welcome">
                                                Bem-vindo,&nbsp;
                                            </span>
                                            <span class="m-topbar__username">
                                                {{ explode(' ', Auth::user() ->name)[0] }}
                                            </span>
                                        </a>
                                        <div class="m-dropdown__wrapper">
                                            <span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust"></span>
                                            <div class="m-dropdown__inner">
                                                <div class="m-dropdown__header m--align-center"
                                                     style="background: url({{ url('/') }}/assets/metronic5/dist/demo2/assets/app/media/img/misc/user_profile_bg.jpg); background-size: cover;">
                                                    <div class="m-card-user m-card-user--skin-dark">
                                                        <div class="m-card-user__details">
                                                            <span class="m-card-user__name m--font-weight-500">
                                                                {{ Auth::user() ->name }}
                                                            </span>
                                                            <a href="{{ route('cliente.dados') }}"
                                                               class="m-card-user__email m--font-weight-300 m-link">
                                                                {{ Auth::user() ->email }}
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="m-dropdown__body">
                                                    <div class="m-dropdown__content">
                                                        <ul class="m-nav m-nav--skin-light">
                                                            <li class="m-nav__section m--hide">
                                                                <span class="m-nav__section-text">
                                                                    {{--Section--}}
                                                                </span>
                                                            </li>
                                                            <li class="m-nav__item">
                                                                <a href="{{ $links->dados }}" class="m-nav__link">
                                                                    <i class="m-nav__link-icon flaticon-profile-1"></i>
                                                                    <span class="m-nav__link-title">
                                                                        <span class="m-nav__link-wrap">
                                                                            <span class="m-nav__link-text">
                                                                                Meus dados
                                                                            </span>
                                                                        </span>
                                                                    </span>
                                                                </a>
                                                            </li>
                                                            <li class="m-nav__item">
                                                                <a href="{{ route('cliente.resetarSenha') }}"
                                                                   class="m-nav__link">
                                                                    <i class="m-nav__link-icon flaticon-lock-1"></i>
                                                                    <span class="m-nav__link-text">
                                                                        Mudar senha
                                                                    </span>
                                                                </a>
                                                            </li>
                                                            <li class="m-nav__item">
                                                                <a href="#modal-sugestoes" data-toggle="modal"
                                                                   class="m-nav__link">
                                                                    <i class="m-nav__link-icon flaticon-chat-1"></i>
                                                                    <span class="m-nav__link-text">
                                                                        Relatar um problema
                                                                    </span>
                                                                </a>
                                                            </li>
                                                            <li class="m-nav__separator m-nav__separator--fit"></li>
                                                            {{--<li class="m-nav__item">--}}
                                                            {{--<a href="#" class="m-nav__link">--}}
                                                            {{--<i class="m-nav__link-icon flaticon-info"></i>--}}
                                                            {{--<span class="m-nav__link-text">--}}
                                                            {{--FAQ--}}
                                                            {{--<span style="margin-left: 15px;" class="m-badge m-badge--success">--}}
                                                            {{--Em breve--}}
                                                            {{--</span>--}}
                                                            {{--</span>--}}
                                                            {{--</a>--}}
                                                            {{--</li>--}}
                                                            <li class="m-nav__separator m-nav__separator--fit"></li>
                                                            <li class="m-nav__item">
                                                                <a href="{{ route('cliente.logout') }}"
                                                                   class="btn m-btn--pill btn-secondary m-btn m-btn--custom m-btn--label-brand m-btn--bolder">
                                                                    Sair
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="m-nav__item m-topbar__quick-actions m-topbar__quick-actions--img m-dropdown m-dropdown--large m-dropdown--header-bg-fill m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push m-dropdown--mobile-full-width m-dropdown--skin-light"
                                        data-dropdown-toggle="click">
                                        <a href="#" class="m-nav__link m-dropdown__toggle">
                                            <span class="m-nav__link-badge m-badge m-badge--dot m-badge--info m--hide"></span>
                                            <span class="m-nav__link-icon">
                                                <span class="m-nav__link-icon-wrapper">
                                                    <i class="flaticon-share"></i>
                                                </span>
                                            </span>
                                        </a>
                                        <div class="m-dropdown__wrapper">
                                            <span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust"></span>
                                            <div class="m-dropdown__inner">
                                                <div class="m-dropdown__header m--align-center"
                                                     style="background: url({{ url('/') }}/assets/metronic5/dist/demo2/assets/app/media/img/misc/quick_actions_bg.jpg); background-size: cover;">
                                                    <span class="m-dropdown__header-title">
                                                        Acesso rápido
                                                    </span>
                                                    <span class="m-dropdown__header-subtitle">
                                                        Resolva seu problema
                                                    </span>
                                                </div>
                                                <div class="m-dropdown__body m-dropdown__body--paddingless">
                                                    <div class="m-dropdown__content">
                                                        <div class="m-scrollable" data-scrollable="false"
                                                             data-max-height="380" data-mobile-max-height="200">
                                                            <div class="m-nav-grid m-nav-grid--skin-light">
                                                                <div class="m-nav-grid__row">
                                                                    <a href="{{ route('cliente.financeiro') }}"
                                                                       class="m-nav-grid__item">
                                                                        <i class="m-nav-grid__icon flaticon-coins"></i>
                                                                        <span class="m-nav-grid__text">
                                                                            2ª via de boletos
                                                                        </span>
                                                                    </a>
                                                                    <a href="javascript:$zopim.livechat.window.show();"
                                                                       class="m-nav-grid__item">
                                                                        <i class="m-nav-grid__icon flaticon-chat-1"></i>
                                                                        <span class="m-nav-grid__text">
                                                                            Chat
                                                                        </span>
                                                                    </a>
                                                                </div>
                                                                <div class="m-nav-grid__row">
                                                                    <a href="#" class="m-nav-grid__item">
                                                                        <i class="m-nav-grid__icon 	fa fa-phone"></i>
                                                                        <span class="m-nav-grid__text">
                                                                            4007-2441
                                                                        </span>
                                                                    </a>
                                                                    <a href="#modal-sugestoes" data-toggle="modal"
                                                                       class="m-nav-grid__item">
                                                                        <i class="m-nav-grid__icon fa fa-envelope-o"></i>
                                                                        <span class="m-nav-grid__text">
                                                                            Enviar sugestão
                                                                        </span>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>

                                    <li class="m-nav__item m-topbar__notifications m-topbar__notifications--img m-dropdown m-dropdown--large m-dropdown--header-bg-fill m-dropdown--arrow m-dropdown--align-center 	m-dropdown--mobile-full-width"
                                        data-dropdown-toggle="click" data-dropdown-persistent="true">
                                        <a href="#" class="m-nav__link m-dropdown__toggle"
                                           id="m_topbar_notification_icon">
                                            <span class="m-nav__link-badge m-badge m-badge--dot m-badge--dot-small m-badge--danger"></span>
                                            <span class="m-nav__link-icon">
                                                <span class="m-nav__link-icon-wrapper">
                                                    <i class="flaticon-music-2"></i>
                                                </span>
                                            </span>
                                        </a>
                                        <div class="m-dropdown__wrapper">
                                            <span class="m-dropdown__arrow m-dropdown__arrow--center"></span>
                                            <div class="m-dropdown__inner">
                                                <div class="m-dropdown__header m--align-center"
                                                     style="background: url({{ url('/') }}/assets/metronic5/dist/demo2/assets/app/media/img/misc/notification_bg.jpg); background-size: cover;">
                                                    <span class="m-dropdown__header-title">
                                                        Notificações
                                                    </span>
                                                    <span class="m-dropdown__header-subtitle">
                                                        Histórico de usos
                                                    </span>
                                                </div>
                                                <div class="m-dropdown__body">
                                                    <div class="tab-pane active" id="m_widget4_tab1_content">
                                                        <div class="m-scrollable" data-scrollable="true"
                                                             data-max-height="400"
                                                             style="height: 280px; overflow: hidden;">
                                                            <div class="m-list-timeline m-list-timeline--skin-light">
                                                                <div class="m-list-timeline__items"
                                                                     style="margin-bottom: 30px;">
                                                                    @foreach($historicos as $historico)
                                                                        <a href="#modal-detalhe-guia-{{ $historico->id }}"
                                                                           data-toggle="modal" class="link-discreto">
                                                                            <div class="m-list-timeline__item">
                                                                                <span class="m-list-timeline__badge m-list-timeline__badge--success"></span>
                                                                                <span class="m-list-timeline__text">
                                                                                {!! $badges[$historico->id_pet] !!}
                                                                                    <span class="liberacao text-capitalize">
                                                                                        {{ \App\Helpers\Utils::excerpt($historico->procedimento()->first()->nome_procedimento) }}
                                                                                    </span>
                                                                                </span>
                                                                                <span class="m-list-timeline__time">
                                                                                    {{ \App\Helpers\Utils::shortDate($historico->created_at) }}
                                                                                </span>
                                                                            </div>
                                                                        </a>

                                                                    @endforeach
                                                                </div>

                                                            </div>
                                                            {{--<button type="button" style="margin: 0 auto;" class="btn btn-info btn-sm m-btn--wide right" data-toggle="modal" data-target="#m_modal_1_2">Ver todas</button>--}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>

                                </ul>
                            </div>
                        </div>
                    </div>
                    <!-- end::Topbar -->
                </div>
            </div>
        </div>
        <div class="m-header__bottom">
            <div class="m-container m-container--responsive m-container--xxl m-container--full-height m-page__container">
                <div class="m-stack m-stack--ver m-stack--desktop">
                    <!-- begin::Horizontal Menu -->
                    <div class="m-stack__item m-stack__item--middle m-stack__item--fluid">
                        <button class="m-aside-header-menu-mobile-close  m-aside-header-menu-mobile-close--skin-light "
                                id="m_aside_header_menu_mobile_close_btn">
                            <i class="la la-close"></i>
                        </button>
                        <div id="m_header_menu"
                             class="m-header-menu m-aside-header-menu-mobile m-aside-header-menu-mobile--offcanvas  m-header-menu--skin-dark m-header-menu--submenu-skin-light m-aside-header-menu-mobile--skin-light m-aside-header-menu-mobile--submenu-skin-light ">
                            <ul class="m-menu__nav  m-menu__nav--submenu-arrow ">
                                <li class="m-menu__item  m-menu__item--active" aria-haspopup="true">
                                    <a href="" class="m-menu__link ">
                                        <span class="m-menu__item-here"></span>
                                        <span class="m-menu__link-text">
													Home
												</span>
                                    </a>
                                </li>
                                <li class="m-menu__item  m-menu__item--submenu m-menu__item--rel"
                                    data-menu-submenu-toggle="click" aria-haspopup="true">
                                    <a href="#" class="m-menu__link m-menu__toggle">
                                        <span class="m-menu__item-here"></span>
                                        <span class="m-menu__link-text">
													Meus pets
												</span>
                                        <i class="m-menu__hor-arrow la la-angle-down"></i>
                                        <i class="m-menu__ver-arrow la la-angle-right"></i>
                                    </a>
                                    <div class="m-menu__submenu m-menu__submenu--classic m-menu__submenu--left">
                                        <span class="m-menu__arrow m-menu__arrow--adjust"></span>
                                        <ul class="m-menu__subnav">
                                            @foreach(\App\Models\Pets::petsFromUser() as $pet)
                                                <li class="m-menu__item " aria-haspopup="true">
                                                    <a href="{{ route('cliente.pet', $pet->id) }}"
                                                       class="m-menu__link ">
                                                        <i class="m-menu__link-icon fa fa-paw"></i>
                                                        <span class="m-menu__link-title">
                                                        <span class="m-menu__link-wrap">
                                                            <span class="m-menu__link-text">
                                                                {{ $pet->primeiro_nome }}
                                                            </span>
                                                        </span>
                                                    </span>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </li>
                                <li class="m-menu__item  m-menu__item--submenu m-menu__item--rel"
                                    data-menu-submenu-toggle="click" aria-haspopup="false">
                                    <a href="{{ route('cliente.encaminhamentos') }}"
                                       class="m-menu__link m-menu__toggle">
                                        <span class="m-menu__item-here"></span>
                                        <span class="m-menu__link-text">
													Encaminhamentos
												</span>
                                        {{--<i class="m-menu__hor-arrow la la-angle-down"></i>--}}
                                        {{--<i class="m-menu__ver-arrow la la-angle-right"></i>--}}
                                    </a>
                                    {{--<div class="m-menu__submenu m-menu__submenu--classic m-menu__submenu--left">--}}
                                    {{--<span class="m-menu__arrow m-menu__arrow--adjust"></span>--}}
                                    {{--<ul class="m-menu__subnav">--}}
                                    {{--@foreach(\App\Models\Pets::petsFromUser() as $pet)--}}
                                    {{--<li class="m-menu__item "  aria-haspopup="true">--}}
                                    {{--<a  href="{{ route('cliente.pet', $pet->id) }}" class="m-menu__link ">--}}
                                    {{--<i class="m-menu__link-icon fa fa-paw"></i>--}}
                                    {{--<span class="m-menu__link-title">--}}
                                    {{--<span class="m-menu__link-wrap">--}}
                                    {{--<span class="m-menu__link-text">--}}
                                    {{--{{ $pet->primeiro_nome }}--}}
                                    {{--</span>--}}
                                    {{--</span>--}}
                                    {{--</span>--}}
                                    {{--</a>--}}
                                    {{--</li>--}}
                                    {{--@endforeach--}}
                                    {{--</ul>--}}
                                    {{--</div>--}}
                                </li>
                                <li class="m-menu__item" aria-haspopup="true">
                                    <a href="{{ route('cliente.financeiro') }}" class="m-menu__link ">
                                        <span class="m-menu__item-here"></span>
                                        <span class="m-menu__link-text">
                                            Histórico financeiro
                                        </span>
                                    </a>
                                </li>

                                <li class="m-menu__item" aria-haspopup="true">
                                    <a href="{{ route('cliente.documentos') }}" class="m-menu__link ">
                                        <span class="m-menu__item-here"></span>
                                        <span class="m-menu__link-text">
													Documentos
												</span>
                                    </a>
                                </li>

                                <li class="m-menu__item" aria-haspopup="true">
                                    <a href="#modal-sugestoes" data-toggle="modal" class="m-menu__link ">
                                        <span class="m-menu__item-here"></span>
                                        <span class="m-menu__link-text">
													Envie uma sugestão
												</span>
                                    </a>
                                </li>
                                <li class="m-menu__item  m-menu__item--submenu m-menu__item--rel"
                                    data-menu-submenu-toggle="click" aria-haspopup="true">
                                    <a href="#" class="m-menu__link m-menu__toggle">
                                        <span class="m-menu__item-here"></span>
                                        <span class="m-menu__link-text">
													Indicações
												</span>
                                        <i class="m-menu__hor-arrow la la-angle-down"></i>
                                        <i class="m-menu__ver-arrow la la-angle-right"></i>
                                    </a>
                                    <div class="m-menu__submenu m-menu__submenu--classic m-menu__submenu--left">
                                        <span class="m-menu__arrow m-menu__arrow--adjust"></span>
                                        <ul class="m-menu__subnav">
                                            <li class="m-menu__item " aria-haspopup="true">
                                                <a href="{{ route('indicacoes.indicar') }}" class="m-menu__link ">
                                                    <i class="m-menu__link-icon fa fa-envelope"></i>
                                                    <span class="m-menu__link-title">
                                                        <span class="m-menu__link-wrap">
                                                            <span class="m-menu__link-text">
                                                                Indicar
                                                            </span>
                                                        </span>
                                                    </span>
                                                </a>
                                            </li>
                                            <li class="m-menu__item " aria-haspopup="true">
                                                <a href="{{ route('indicacoes.listar') }}" class="m-menu__link ">
                                                    <i class="m-menu__link-icon fa fa-list"></i>
                                                    <span class="m-menu__link-title">
                                                        <span class="m-menu__link-wrap">
                                                            <span class="m-menu__link-text">
                                                                Listar
                                                            </span>
                                                        </span>
                                                    </span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>

                            </ul>
                        </div>
                    </div>
                    <!-- end::Horizontal Menu -->

                </div>
            </div>
        </div>

    </header>
    <!-- end::Header -->
    <!-- begin::Body -->
    <div class=" m-grid__item m-grid__item--fluid m-grid m-grid--hor-desktop m-grid--desktop m-body">
        @if($emAtraso)
            <p class="aviso panel panel-warning">
                Sua fatura ainda está em aberto. Por favor, entre na <a href="{{ route('cliente.financeiro') }}">área
                    financeira</a> e retire a 2ª via do seu boleto atualizado.
                <a class="close">&times;</a>
            </p>
        @endif
        {{--@if($encaminhamentos)--}}
        {{--<p class="aviso aviso-azul panel panel-info">--}}
        {{--Você possui {{$encaminhamentos}} guia(s) de encaminhamento que ainda não foram MARCADAS. Entre em contato com a CLÍNICA para agendar.--}}
        {{--<a class="close">&times;</a>--}}
        {{--</p>--}}
        {{--@endif--}}
        <div id="page-container"
             class="{{ $emAtraso ? "has-aviso" : "" }} m-grid__item m-grid__item--fluid  m-grid m-grid--ver	m-container m-container--responsive m-container--xxl m-page__container">
            <div class="m-grid__item m-grid__item--fluid m-wrapper">
                <div class="m-subheader ">
                    <div class="d-flex align-items-center">
                        <div class="mr-auto">
                            <h3 class="m-subheader__title ">
                                Área do cliente
                            </h3>
                        </div>
                        <div>
									<span class="m-subheader__daterange" id="">
										<span class="m-subheader__daterange-label">
											<span class="m-subheader__daterange-title">ID:</span>
											<span class="m-subheader__daterange-date m--font-brand">{{ auth()->user()->id }}</span>
										</span>

									</span>
                        </div>
                    </div>
                </div>
                <!-- END: Subheader -->
                <div class="m-content">
                    <!--begin:: Widgets/Stats-->


                    {{-- Exemplo de mensagem "dismissable" --}}

                    {{--<div class="m-alert m-alert--icon m-alert--icon-solid m-alert--outline alert alert-brand alert-dismissible fade show" role="alert">--}}
                    {{--<div class="m-alert__icon">--}}
                    {{--<i class="flaticon-exclamation-1"></i>--}}
                    {{--<span></span>--}}
                    {{--</div>--}}
                    {{--<div class="m-alert__text">--}}
                    {{--<strong>--}}
                    {{--Importante:--}}
                    {{--</strong>--}}
                    {{--O recadastramento só vai até o dia 10. Agende pelo telefone: 4007-2441.--}}

                    {{--<button type="button" class="btn btn-info btn-sm m-btn--wide right" data-toggle="modal" data-target="#m_modal_1_2">Saiba mais</button>--}}




                    {{--</div>--}}
                    {{--<div class="m-alert__close">--}}
                    {{--<button type="button" class="close" data-dismiss="alert" aria-label="Fechar"></button>--}}
                    {{--</div>--}}
                    {{--</div>--}}

                    <!--end:: Widgets/Stats-->
                    <!--Begin::Main Portlet-->
                    <div class="row">
                        <div class="col-xl-12">
                            @yield('content')
                        </div>

                    </div>
                    <!--End::Main Portlet-->

                </div>
            </div>
        </div>
    </div>
    <!-- end::Body -->
    <!-- begin::Footer -->
    <footer class="m-grid__item m-footer ">
        <div class="m-container m-container--responsive m-container--xxl m-container--full-height m-page__container">
            <div class="m-footer__wrapper">
                <div class="m-stack m-stack--flex-tablet-and-mobile m-stack--ver m-stack--desktop">
                    <div class="m-stack__item m-stack__item--left m-stack__item--middle m-stack__item--last"
                         style="text-align:center;">
								<span class="m-footer__copyright">
									2018 &copy; <b style="color: #009cf3 !important;">Lifepet</b>
								</span>
                    </div>

                </div>
            </div>
        </div>
    </footer>
    <!-- end::Footer -->
</div>
<!-- end:: Page -->
<!-- begin::Quick Sidebar -->

<!-- end::Quick Sidebar -->
<!-- begin::Scroll Top -->
<div class="m-scroll-top m-scroll-top--skin-top" data-toggle="m-scroll-top" data-scroll-offset="500"
     data-scroll-speed="300">
    <i class="la la-arrow-up"></i>
</div>

<div id="modal-sugestoes" class="modal fade" tabindex="-1" data-replace="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Enviar sugestão</h4>
            </div>
            <div class="modal-body col-sm-12">
                <form role="form" action="{{ route('ajuda.sugestoes.store') }}" method="POST">
                    {{ csrf_field() }}
                    <div class="form-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <h6>Título</h6>
                                    <div class="m-input-icon m-input-icon--left">
                                        <input type="text" class="form-control m-input for-client" name="titulo"
                                               required>
                                        <span class="m-input-icon__icon m-input-icon__icon--left"><span>
                                                    <i class="fa fa-pencil"></i></span>
                                            </span>
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
                                            <textarea name="corpo" id="corpo" class="form-control for-client" required
                                                      rows="4" style="resize: none;"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <h6>Solicitante</h6>
                                    <div class="m-input-icon m-input-icon--left">
                                        <input type="text" class="form-control m-input"
                                               value="{{ auth()->user()->name }}">
                                        <span class="m-input-icon__icon m-input-icon__icon--left"><span>
                                                    <i class="fa fa-user"></i></span>
                                            </span>
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
@foreach($historicos as $historico)
    <div id="modal-detalhe-guia-{{ $historico->id }}" class="modal fade" tabindex="-1" data-replace="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Guia #{{ $historico->numero_guia }}</h4>
                </div>
                <div class="modal-body col-sm-12">
                    <ul class="list lista-procedimento">
                        <li class="item"><strong>Pet: </strong>{{ $historico->pet()->first()->nome_pet }}</li>
                        <li class="item">
                            <strong>Procedimento: </strong>{{ $historico->procedimento()->first()->nome_procedimento }}
                        </li>
                        <li class="item">
                            <strong>Médico: </strong>{{ $historico->prestador()->first() ? $historico->prestador()->first()->nome : "-" }}
                        </li>
                        <li class="item"><strong>Liberação: </strong>{{ $historico->status }}</li>
                        <li class="item"><strong>Emissão: </strong>{{ $historico->created_at->format('d/m/Y H:i') }}
                        </li>
                        @if($historico->data_liberacao)
                            <li class="item"><strong>Liberado a partir
                                    de: </strong>{{ $historico->data_liberacao->format('d/m/Y H:i') }}</li>
                        @endif
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn dark btn-outline">Fechar</button>
                </div>
            </div>
        </div>
    </div>
@endforeach
<!--begin::Base Scripts -->
<script src="{{ url('/') }}/assets/metronic5/dist/demo2/assets/vendors/base/vendors.bundle.js"
        type="text/javascript"></script>
<script src="{{ url('/') }}/assets/metronic5/dist/demo2/assets/demo/demo2/base/scripts.bundle.js"
        type="text/javascript"></script>
<!--end::Base Scripts -->
<!--begin::Page Vendors -->
<script src="{{ url('/') }}/assets/metronic5/dist/demo2/assets/vendors/custom/fullcalendar/fullcalendar.bundle.js"
        type="text/javascript"></script>
<!--end::Page Vendors -->
<!--begin::Page Snippets -->
<script src="{{ url('/') }}/assets/metronic5/dist/demo2/assets/app/js/dashboard.js" type="text/javascript"></script>

<script src="{{ url('/') }}/assets/global/plugins/respond.min.js"></script>
<script src="{{ url('/') }}/assets/global/plugins/excanvas.min.js"></script>
<script src="{{ url('/') }}/assets/global/plugins/ie8.fix.min.js"></script>

<!-- BEGIN CORE PLUGINS -->
{{--<script src="{{ url('/') }}/assets/global/plugins/jquery.min.js" type="text/javascript"></script>--}}
{{--<script src="{{ url('/') }}/assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>--}}
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

<!-- END THEME GLOBAL SCRIPTS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="{{ url('/') }}/assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js"
        type="text/javascript"></script>
<script src="{{ url('/') }}/assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js"
        type="text/javascript"></script>
<script src="{{ url('/') }}/assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>

<script src="{{ url('/') }}/assets/global/plugins/counterup/jquery.counterup.min.js" type="text/javascript"></script>
<script src="{{ url('/') }}/assets/global/plugins/fullcalendar/fullcalendar.min.js" type="text/javascript"></script>
<script src="{{ url('/') }}/assets/global/plugins/jquery.sparkline.min.js" type="text/javascript"></script>
<script src="{{ url('/') }}/assets/global/plugins/jquery-validation/js/jquery.validate.min.js"
        type="text/javascript"></script>
<script src="{{ url('/') }}/assets/global/plugins/jquery-validation/js/localization/messages_pt_BR.min.js"
        type="text/javascript"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN THEME GLOBAL SCRIPTS -->
<script src="{{ url('/') }}/assets/global/scripts/app.min.js" type="text/javascript"></script>
<script src="{{ url('/') }}/assets/pages/scripts/components-select2.min.js" type="text/javascript"></script>
<script src="{{ url('/') }}/assets/pages/scripts/components-multi-select.min.js" type="text/javascript"></script>
<script src="{{ url('/') }}/assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script>
<!-- END THEME GLOBAL SCRIPTS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<!-- END PAGE LEVEL SCRIPTS -->
<!-- BEGIN THEME LAYOUT SCRIPTS -->
{{--<script src="{{ url('/') }}/assets/layouts/layout/scripts/layout.min.js" type="text/javascript"></script>--}}
<script src="{{ url('/') }}/assets/layouts/global/scripts/quick-sidebar.min.js" type="text/javascript"></script>
<script src="{{ url('/') }}/assets/layouts/global/scripts/quick-nav.min.js" type="text/javascript"></script>
<script src="{{ url('/') }}/assets/global/plugins/dropzone/dropzone.min.js" type="text/javascript"></script>
<script src="{{ url('/') }}/assets/global/plugins/introjs/intro.min.js" type="text/javascript"></script>
<script src="{{ url('/') }}/assets/global/plugins/materialize/materialize.js?{{ time() }}"
        type="text/javascript"></script>
<script src="{{ url('/') }}/assets/global/plugins/nouislider/nouislider.min.js" type="text/javascript"></script>
<script src="{{ url('/') }}/assets/global/plugins/ion.rangeslider/js/ion.rangeSlider.min.js"
        type="text/javascript"></script>

<!-- END THEME LAYOUT SCRIPTS -->
<script>
    $(document).ready(function () {
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
<script src="{{ url('/') }}/assets/global/scripts/datatable.js" type="text/javascript"></script>
<script src="{{ url('/') }}/assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
<script src="{{ url('/') }}/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js"
        type="text/javascript"></script>
<script>
    var TableDatatablesManaged = function () {

        var initTable = function () {

            var table = $('.datatables');

            // begin first table
            table.dataTable({

                // Internationalisation. For more info refer to http://datatables.net/manual/i18n
                "language": {
                    "aria": {
                        "sortAscending": ": ative para ordenar a coluna de forma ascendente",
                        "sortDescending": ": ative para ordenar a coluna de forma descendente"
                    },
                    "emptyTable": "Não há dados para essa tabela",
                    "info": "",
                    "infoEmpty": "Nenhum registro encontrado",
                    "infoFiltered": "(filtered1 de um total de _MAX_ registros)",
                    "lengthMenu": "Mostrar _MENU_",
                    "search": "Buscar:",
                    "zeroRecords": "Nenhum resultado encontrado",
                    "paginate": {
                        "previous": "Anterior",
                        "next": "Próximo",
                        "last": "Último",
                        "first": "Primeiro"
                    }
                },

                // Or you can use remote translation file
                //"language": {
                //   url: '//cdn.datatables.net/plug-ins/3cfcc339e89/i18n/Portuguese.json'
                //},

                // Uncomment below line("dom" parameter) to fix the dropdown overflow issue in the datatable cells. The default datatable layout
                // setup uses scrollable div(table-scrollable) with overflow:auto to enable vertical scroll(see: assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js).
                // So when dropdowns used the scrollable div should be removed.
                //"dom": "<'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r>t<'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",
                "sDom": "lrti",
                //"bStateSave": true, // save datatable state(pagination, sort, etc) in cookie.
                "bPaging": false,
                "paging": false,
                "lengthMenu": [
                    [20, 35, 50, -1],
                    [20, 35, 50, "Todos"] // change per page values here
                ],
                // set the initial value
                "pageLength": -1,
                "pagingType": "bootstrap_full_number"
            });

            table.find('.group-checkable').change(function () {
                var set = jQuery(this).attr("data-set");
                var checked = jQuery(this).is(":checked");
                jQuery(set).each(function () {
                    if (checked) {
                        $(this).prop("checked", true);
                        $(this).parents('tr').addClass("active");
                    } else {
                        $(this).prop("checked", false);
                        $(this).parents('tr').removeClass("active");
                    }
                });
            });

            table.on('change', 'tbody tr .checkboxes', function () {
                $(this).parents('tr').toggleClass("active");
            });
        };

        return {
            //main function to initiate the module
            init: function () {
                if (!jQuery().dataTable) {
                    return;
                }
                initTable();
            }
        };
    }();
    jQuery(document).ready(function () {
        TableDatatablesManaged.init();
    });
</script>
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
        var $forms = $('.portlet .portlet-body form[id][method=post]');
        $forms.validate();
    });
    $(document).ready(function () {
        $('p.aviso .close').click(function (e) {
            e.preventDefault();
            $(this).closest('p.aviso').fadeOut(400, function () {
                if ($('p.aviso:visible').length < 1) {
                    $('#page-container').removeClass('has-aviso');
                }
            });
        });
    });
</script>

<!-- INÍCIO DA CONCATENAÇÃO DE SCRIPTS -->
<!-- Start of lifepetsupport Zendesk Widget script -->
<script>/*<![CDATA[*/
    window.zEmbed || function (e, t) {
        var n, o, d, i, s, a = [], r = document.createElement("iframe");
        window.zEmbed = function () {
            a.push(arguments)
        }, window.zE = window.zE || window.zEmbed, r.src = "javascript:false", r.title = "", r.role = "presentation", (r.frameElement || r).style.cssText = "display: none", d = document.getElementsByTagName("script"), d = d[d.length - 1], d.parentNode.insertBefore(r, d), i = r.contentWindow, s = i.document;
        try {
            o = s
        } catch (e) {
            n = document.domain, r.src = 'javascript:var d=document.open();d.domain="' + n + '";void(0);', o = s
        }
        o.open()._l = function () {
            var e = this.createElement("script");
            n && (this.domain = n), e.id = "js-iframe-async", e.src = "https://assets.zendesk.com/embeddable_framework/main.js", this.t = +new Date, this.zendeskHost = "lifepetsupport.zendesk.com", this.zEQueue = a, this.body.appendChild(e)
        }, o.write('<body onload="document._l();">'), o.close()
    }();
    /*]]>*/</script>
<!-- End of lifepetsupport Zendesk Widget script -->

@yield('scripts')
<!-- FIM DA CONCATENAÇÃO DE SCRIPTS -->
<!--end::Page Snippets -->
</body>
<!-- end::Body -->
</html>
