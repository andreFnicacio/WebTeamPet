@extends('layouts.logs')

@section('title')
    @parent
    Gerenciador de Renovações
@endsection
@section('page-title')
    Gerenciador de Renovações 2.0
@endsection
@section('css')
    @parent
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pretty-checkbox@3.0/dist/pretty-checkbox.min.css">
    <style type="text/css">
        @media (min-width: 1200px) {
            .container, .container-lg, .container-md, .container-sm, .container-xl {
                max-width: 960px;
            }
        }


        #vuer {
            width: 100%;
        }

        .logger-background {
            padding: 0 0 0px 0;
            box-shadow: 0px 2px 4px -3px #887a7a;
            color: black;
        }

        body {
            font-family: inherit !important;
        }


        .search-input {
            width: 90%;
            display: inline-block;
            position: relative;
        }

        .advanced-filter {
            width: 10%;
            display: inline-block;
            text-align: center;
        }

        .advanced-filter svg {
            height: 30px;
            fill: #00b0ff;
            opacity: .5;
        }
        .advanced-filter svg {
            height: 40px;
            fill: #00b0ff;
            opacity: .5;
            padding: 5px;
        }

        .advanced-filter:hover svg {
            opacity: 1;
            cursor: pointer;
            background: white;
            border-radius: 100%;
            padding: 5px;
        }

        .search-container.container {
            padding: 15px 0;
        }
        .search-input svg {
            position: absolute;
            top: 25px;
            left: 15px;
            width: 25px;
            margin-top: -16.5px;
            fill: #ccc;
        }

        .search-input input[type="search"] {
            width: 100%;
            outline: none;
            border: 1px solid rgb(235, 238, 240);
            border-radius: 30px;
            padding: 6px 15px;
            background: rgb(235, 238, 240);
            padding-left: 50px;
        }

        .search-input input[type="search"]:focus {
            background: rgb(255 255 255);
            color: #073642;
            border: 1px solid rgb(0 176 255);
        }

        .log-container {
            margin-bottom: 100px;
        }

        #vuer .log-item {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #e0e0e0;
            background: #fff;
            border-bottom: none !important;
        }
        #vuer article.log-item.loading {
            background: #f1f2f3  !important;
            border: none !important;
            box-shadow: none;
            border-radius: 30px 30px 0px 0;
            padding: 0 !important;
            overflow: hidden;
        }

        .log-item.loading svg {
            height: 40px;
        }

        #vuer .log-item .log-item--header {
            padding-bottom: 5px;
        }

        .log-item--area {
            text-transform: lowercase;
            color: rgb(91, 112, 131);
            font-style: italic;
            margin-right: 10px;
        }

        .log-item--event {
            background-color: #ccc;
        }

        .log-item--priority {
            text-align: right;
            float: right;
        }

        .log-item--body {
            margin-bottom: 15px;
            word-break: break-word;
        }

        .log-item--json {
            position: relative;
        }
        span.log-item--json-toggler {
            position: absolute;
            bottom: 15px;
            right: 15px;
            text-transform: lowercase;
            font-style: italic;
            color: #2e2e2e;
            background: #fdf6e3;
            padding: 0px 10px;
            font-size: 10px;
            cursor: pointer;
            line-height: 20px;
            font-family: 'Fira Code';
            font-weight: 600;
            border: 1px solid #fdf6e3;
        }
        span.log-item--json-toggler:hover {
            color: #fdf6e3;
            background: #2e2e2e;
            border: 1px solid #fdf6e3;
        }
        .log-item--json pre {
            background: #2e2e2e;
            color: #d6d6d6;
            line-height: 15px;
            padding: 15px;
            font-family: 'Fira Code', monospace;
            height: 100px;
            overflow: hidden;
        }
        .log-item--json.expanded pre {
            overflow-y: hidden;
            height: auto;
            white-space: pre-wrap;
        }
        .log-item--json .key {
            color: #b4d273;
        }
        .log-item--json .string {
            color: #e1ca72;
        }
        .log-item--json .null {
            color: #f92468;
        }

        .log-item--footer {
            text-align: right;
            color: rgb(91, 112, 131);
            font-size: 12px;
            font-style: italic;
        }

        .log-item--footer strong {
            font-weight: 800 !important;
        }

        #vuer .bg-info {
            background: #17a2b8!important;
            color: #fdf6e3;
        }
        #vuer .bg-warning-2 {
            background: #e4b00c!important;
            color: #fdf6e3;
        }
        #vuer .bg-error {
            background: #dc322f!important;
            color: #fdf6e3;
        }
        #vuer .bg-warning {
            background: #cb4b16!important;
            color: #fdf6e3;
        }
        #vuer .bg-change {
            background: #eee8d5!important;
            color: #073642;
        }
        #vuer .bg-notify {
            background: #073642!important;
            color: #eee8d5;
        }
        #vuer .bg-success {
            background: #859900!important;
            color: #eee8d5;
        }
        #vuer .bg-default {
            background: #00b0ff!important;
            color: #fff;
        }
        #vuer .color-success {
            color: #859900!important;
        }
        #vuer .color-warning {
            color: #cb4b16!important;
        }
        #vuer .color-danger {
            color: #dc322f!important;
        }
        #vuer .color-notify {
            color: #073642!important;
        }
        #vuer .color-notify {
            color: #00b0ff!important;
        }

        .modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1040;
            width: 100vw;
            height: 100vh;
            background-color: #000;
            opacity: .2;
        }
        .modal-dialog {
            margin-top: 4rem;
            max-width: 560px;
        }

        span.select2-selection.select2-selection--multiple {
            box-shadow: none !important;
            border: 1px solid #ccc !important;
            padding: 5px 0px 8px 0;
        }
        .select2-container--bootstrap .select2-selection--multiple .select2-selection__rendered {
            line-height: 24px;
        }
        li.select2-selection__choice {
            padding: 1px 8px !important;
            margin-top: 4px !important;
            color: #008dcc !important;
            border-color: #00b0ff !important;
        }
        .select2-container--bootstrap .select2-selection--multiple .select2-selection__choice__remove {
            color: #00b0ff;
            cursor: pointer;
            display: inline-block;
            font-weight: bold;
            margin-right: 3px;
            font-family: 'Font Awesome 5 Free';
        }

        /**
         * TRANSITIONS
         */
        .slide-enter-active {
            transition: all 0.3s ease-out;
        }

        .slide-leave-active {
            transition: all 0.3s cubic-bezier(1, 0.5, 0.8, 1);
        }

        .slide-enter-from {
            height: 0;
        }
        .slide-enter-to {
            /**transform: translateX(20px);
            opacity: 0;**/
            height: 40px;
        }
        .slide-leave-from {
            height: 40px;
        }
        .slide-leave-to {
            height: 0;
        }

    </style>
    <style type="text/css">
        .log-item.renewal {
            padding-bottom: 30px !important;
        }
        .log-item--header {
            margin-bottom: 10px;
        }
        .renewal-item--actions {
            display: inline-block;
            float: right;
        }
        .renewal-item--action {
            padding: 0px 10px;
            font-size: 10px;
            cursor: pointer;
            line-height: 20px;
            font-family: 'Fira Code';
            font-weight: 600;
            margin-left: 3px;
            border-radius: 0;
        }

        .renewal-item--action:active, .renewal-item--action:focus {
            box-shadow: none;
        }
        .renewal-item--action.btn-open-renewal {
            font-style: italic;
            color: #17a2b8;
            background: #fdf6e3;
            border: 1px solid #17a2b8;
        }
        .renewal-item--action.btn-open-renewal:hover {
            font-weight: bold;
            color: #fdf6e3;
            background: #17a2b8;
            border: 1px solid #fdf6e3;
        }
        .renewal-item--action.btn-passthu-renewal {
            font-style: italic;
            color: #96750b;
            background: #fdf6e3;
            border: 1px solid #e4b00c;
        }
        .renewal-item--action.btn-passthu-renewal:hover {
            font-weight: bold;
            color: #fdf6e3;
            background: #e4b00c;
            border: 1px solid #fdf6e3;
        }

        .renewal-item--action.btn-cancel {
            font-style: italic;
            color: #a72624;
            background: #fdf6e3;
            border: 1px solid #dc322f;
        }
        .renewal-item--action.btn-cancel:hover {
            font-weight: bold;
            color: #fdf6e3;
            background: #dc322f;
            border: 1px solid #fdf6e3;
        }

        .renewal-item--action.btn-confirm {
            font-style: italic;
            color: #859900;
            background: #fdf6e3;
            border: 1px solid #859900;
        }
        .renewal-item--action.btn-confirm:hover {
            font-weight: bold;
            color: #fdf6e3;
            background: #859900;
            border: 1px solid #fdf6e3;
        }

        .renewal--table th {
            font-weight: 400;
            background: #ebeef0;
            color: #656464;
            font-style: italic;
            font-family: 'Poppins';
            font-size: 14px;
        }

        .renewal--table .numeric {

            font-style: italic;
            font-family: 'Fira Code';
        }
        .renewal--table-client-info.loaded {
            margin-bottom: 0;
        }
        .renewal--table.renewal-data {
            width: 100%
        }
        .renewal--table.renewal-data td.nopadding {
            padding: 0;
        }
        .renewal--table.renewal-data td.nopadding .ghost {
            border-radius: 0;
            border-color: transparent;
        }
        .renewal--table.renewal-data td.nopadding .ghost:focus {
            box-shadow: none;
            border-color: #17a2b8;
        }
        .renewal--table.renewal-data .main-option {
            font-weight: bold;
        }
        .renewal h5 {
            color: rgb(91, 112, 131);
            font-family: 'Poppins';
            font-style: italic;
            margin-top: 25px;
            font-weight: 200;
        }
        .renewal .inputtable.brl {
            position: relative;
        }
        .renewal .inputtable.brl .brl-before {
            position: absolute;
            left: 5px;
            font-family: "Fira Code";
            top: 0;
            line-height: 39px;
        }
        .renewal input.brl {
            padding-left: 30px;
        }
        .numeric {
            font-style: italic;
            font-family: 'Fira Code', monospace;
        }
        .numeric strong {
            font-weight: bold;
        }
        .tooltip.fade {
            opacity: 1;
        }
        .text-bold {
            font-weight: bold;
        }
        [v-cloak] {
            display: none !important;
        }
        .cs-loader {
            position: relative;
            height: 20px;
            width: 100%;
        }

        .cs-loader-inner {
            transform: translateY(-50%);
            top: 50%;
            position: absolute;
            width: 100%;
            color: #FFF;
            padding: 0 100px;
            text-align: center;
        }

        .cs-loader-inner label {
            font-size: 20px;
            opacity: 0;
            display:inline-block;
            color: #737e84;
            font-family: 'Fira Code';
        }

        @keyframes lol {
            0% {
                opacity: 0;
                transform: translateX(-300px);
            }
            33% {
                opacity: 1;
                transform: translateX(0px);
            }
            66% {
                opacity: 1;
                transform: translateX(0px);
            }
            100% {
                opacity: 0;
                transform: translateX(300px);
            }
        }

        @-webkit-keyframes lol {
            0% {
                opacity: 0;
                -webkit-transform: translateX(-300px);
            }
            33% {
                opacity: 1;
                -webkit-transform: translateX(0px);
            }
            66% {
                opacity: 1;
                -webkit-transform: translateX(0px);
            }
            100% {
                opacity: 0;
                -webkit-transform: translateX(300px);
            }
        }

        .cs-loader-inner label:nth-child(6) {
            -webkit-animation: lol 3s infinite ease-in-out;
            animation: lol 3s infinite ease-in-out;
        }

        .cs-loader-inner label:nth-child(5) {
            -webkit-animation: lol 3s 100ms infinite ease-in-out;
            animation: lol 3s 100ms infinite ease-in-out;
        }

        .cs-loader-inner label:nth-child(4) {
            -webkit-animation: lol 3s 200ms infinite ease-in-out;
            animation: lol 3s 200ms infinite ease-in-out;
        }

        .cs-loader-inner label:nth-child(3) {
            -webkit-animation: lol 3s 300ms infinite ease-in-out;
            animation: lol 3s 300ms infinite ease-in-out;
        }

        .cs-loader-inner label:nth-child(2) {
            -webkit-animation: lol 3s 400ms infinite ease-in-out;
            animation: lol 3s 400ms infinite ease-in-out;
        }

        .cs-loader-inner label:nth-child(1) {
            -webkit-animation: lol 3s 500ms infinite ease-in-out;
            animation: lol 3s 500ms infinite ease-in-out;
        }
    </style>
    <style>
        /**
          SWAL CUSTOMIZE
         */

        .swal2-buttonswrapper button {
            padding: 5px 20px;
            font-size: 12px;
            cursor: pointer;
            line-height: 20px;
            font-family: 'Fira Code';
            font-weight: 600;
            margin-left: 3px;
            border-radius: 0;
            cursor: pointer;
            font-style: italic;
        }
        .swal2-buttonswrapper button:focus {
            outline: none;
        }

        .swal2-buttonswrapper button.swal2-confirm {
            color: #17a2b8;
            background: #fdf6e3;
            border: 1px solid #17a2b8;
        }
        .swal2-buttonswrapper button.swal2-cancel {
            color: #a72624;
            background: #fdf6e3;
            border: 1px solid #dc322f;
        }
        .swal2-modal {
            font-family: 'Poppins';
        }
        .swal2-modal .swal2-title {
            font-size: 24px;
        }
        .swal2-modal .swal2-content {
            font-weight: 400;
        }
        .swal2-modal .order-1 { order: 1; }
        .swal2-modal .order-2 { order: 2; }

    </style>
@endsection

@section('content')
    <div id="vuer" class="root" v-cloak>
        <div class="search-container container">
            <div class="search-input">
                <svg viewBox="0 0 24 24" class="r-m0bqgq r-4qtqp9 r-yyyyoo r-1xvli5t r-dnmrzs r-4wgw6l r-f727ji r-bnwqim r-1plcrui r-lrvibr"><g><path d="M21.53 20.47l-3.66-3.66C19.195 15.24 20 13.214 20 11c0-4.97-4.03-9-9-9s-9 4.03-9 9 4.03 9 9 9c2.215 0 4.24-.804 5.808-2.13l3.66 3.66c.147.146.34.22.53.22s.385-.073.53-.22c.295-.293.295-.767.002-1.06zM3.5 11c0-4.135 3.365-7.5 7.5-7.5s7.5 3.365 7.5 7.5-3.365 7.5-7.5 7.5-7.5-3.365-7.5-7.5z"></path></g></svg>
                <input type="search" v-model="text" placeholder="Buscar renovações"
                       v-on:keyup.enter="getPreviews()">
            </div>
            <div class="advanced-filter" v-on:click="openAdvancedFiltersModal">
                <svg viewBox="0 0 24 24" class="r-13gxpu9 r-4qtqp9 r-yyyyoo r-1q142lx r-50lct3 r-dnmrzs r-bnwqim r-1plcrui r-lrvibr r-1srniue"><g><path d="M12 8.21c-2.09 0-3.79 1.7-3.79 3.79s1.7 3.79 3.79 3.79 3.79-1.7 3.79-3.79-1.7-3.79-3.79-3.79zm0 6.08c-1.262 0-2.29-1.026-2.29-2.29S10.74 9.71 12 9.71s2.29 1.026 2.29 2.29-1.028 2.29-2.29 2.29z"></path><path d="M12.36 22.375h-.722c-1.183 0-2.154-.888-2.262-2.064l-.014-.147c-.025-.287-.207-.533-.472-.644-.286-.12-.582-.065-.798.115l-.116.097c-.868.725-2.253.663-3.06-.14l-.51-.51c-.836-.84-.896-2.154-.14-3.06l.098-.118c.186-.222.23-.523.122-.787-.11-.272-.358-.454-.646-.48l-.15-.014c-1.18-.107-2.067-1.08-2.067-2.262v-.722c0-1.183.888-2.154 2.064-2.262l.156-.014c.285-.025.53-.207.642-.473.11-.27.065-.573-.12-.795l-.094-.116c-.757-.908-.698-2.223.137-3.06l.512-.512c.804-.804 2.188-.865 3.06-.14l.116.098c.218.184.528.23.79.122.27-.112.452-.358.477-.643l.014-.153c.107-1.18 1.08-2.066 2.262-2.066h.722c1.183 0 2.154.888 2.262 2.064l.014.156c.025.285.206.53.472.64.277.117.58.062.794-.117l.12-.102c.867-.723 2.254-.662 3.06.14l.51.512c.836.838.896 2.153.14 3.06l-.1.118c-.188.22-.234.522-.123.788.112.27.36.45.646.478l.152.014c1.18.107 2.067 1.08 2.067 2.262v.723c0 1.183-.888 2.154-2.064 2.262l-.155.014c-.284.024-.53.205-.64.47-.113.272-.067.574.117.795l.1.12c.756.905.696 2.22-.14 3.06l-.51.51c-.807.804-2.19.864-3.06.14l-.115-.096c-.217-.183-.53-.23-.79-.122-.273.114-.455.36-.48.646l-.014.15c-.107 1.173-1.08 2.06-2.262 2.06zm-3.773-4.42c.3 0 .593.06.87.175.79.328 1.324 1.054 1.4 1.896l.014.147c.037.4.367.7.77.7h.722c.4 0 .73-.3.768-.7l.014-.148c.076-.842.61-1.567 1.392-1.892.793-.33 1.696-.182 2.333.35l.113.094c.178.148.366.18.493.18.206 0 .4-.08.546-.227l.51-.51c.284-.284.305-.73.048-1.038l-.1-.12c-.542-.65-.677-1.54-.352-2.323.326-.79 1.052-1.32 1.894-1.397l.155-.014c.397-.037.7-.367.7-.77v-.722c0-.4-.303-.73-.702-.768l-.152-.014c-.846-.078-1.57-.61-1.895-1.393-.326-.788-.19-1.678.353-2.327l.1-.118c.257-.31.236-.756-.048-1.04l-.51-.51c-.146-.147-.34-.227-.546-.227-.127 0-.315.032-.492.18l-.12.1c-.634.528-1.55.67-2.322.354-.788-.327-1.32-1.052-1.397-1.896l-.014-.155c-.035-.397-.365-.7-.767-.7h-.723c-.4 0-.73.303-.768.702l-.014.152c-.076.843-.608 1.568-1.39 1.893-.787.326-1.693.183-2.33-.35l-.118-.096c-.18-.15-.368-.18-.495-.18-.206 0-.4.08-.546.226l-.512.51c-.282.284-.303.73-.046 1.038l.1.118c.54.653.677 1.544.352 2.325-.327.788-1.052 1.32-1.895 1.397l-.156.014c-.397.037-.7.367-.7.77v.722c0 .4.303.73.702.768l.15.014c.848.078 1.573.612 1.897 1.396.325.786.19 1.675-.353 2.325l-.096.115c-.26.31-.238.756.046 1.04l.51.51c.146.147.34.227.546.227.127 0 .315-.03.492-.18l.116-.096c.406-.336.923-.524 1.453-.524z"></path></g></svg>
            </div>
        </div>
        <div class="container search-container">
            <div class="active-filter-container">
                <div class="row">
                    <div class="col-sm-4 text-left numeric">
                        <strong data-toggle="tooltip" data-original-title="Total de renovações filtradas.">@{{ filteredRenewals.length }}</strong> / <span data-toggle="tooltip" data-original-title="Total de renovações encontradas.">@{{ renewals.length }}</span>
                    </div>
                    <div class="col-sm-8 text-right">
                        <div class="pretty p-switch p-fill p-info">
                            <input type="checkbox" v-model="selectedFilters.anual"/>
                            <div class="state p-primary">
                                <label>Anuais</label>
                            </div>
                        </div>
                        <div class="pretty p-switch p-fill p-info">
                            <input type="checkbox" v-model="selectedFilters.monthly"/>
                            <div class="state p-primary">
                                <label>Mensais</label>
                            </div>
                        </div>
                        <div class="pretty p-switch p-fill p-info">
                            <input type="checkbox" v-model="selectedFilters.recent"/>
                            <div class="state p-primary">
                                <label>Recentes</label>
                            </div>
                        </div>
                        <div class="pretty p-switch p-fill p-info">
                            <input type="checkbox" v-model="selectedFilters.renewed"/>
                            <div class="state p-primary">
                                <label>Renovados</label>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="logger-background container">
            <div class="logger">
                <div class="log-container">
                    <transition name="slide">
                        <article class="log-item loading" v-if="loading">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="100px" height="100px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" style="margin: auto;background: rgb(241, 242, 243);display: block;"><g transform="translate(20 50)"><circle cx="10" cy="0" r="10" fill="#81a3bd"><animateTransform attributeName="transform" type="scale" begin="-0.375s" calcMode="spline" keySplines="0.3 0 0.7 1;0.3 0 0.7 1" values="0;1;0" keyTimes="0;0.5;1" dur="1s" repeatCount="indefinite"></animateTransform></circle></g><g transform="translate(40 50)"><circle cx="10" cy="0" r="10" fill="#00b0ff"><animateTransform attributeName="transform" type="scale" begin="-0.25s" calcMode="spline" keySplines="0.3 0 0.7 1;0.3 0 0.7 1" values="0;1;0" keyTimes="0;0.5;1" dur="1s" repeatCount="indefinite"></animateTransform></circle></g><g transform="translate(60 50)"><circle cx="10" cy="0" r="10" fill="#17a2b8"><animateTransform attributeName="transform" type="scale" begin="-0.125s" calcMode="spline" keySplines="0.3 0 0.7 1;0.3 0 0.7 1" values="0;1;0" keyTimes="0;0.5;1" dur="1s" repeatCount="indefinite"></animateTransform></circle></g><g transform="translate(80 50)"><circle cx="10" cy="0" r="10" fill="#073642"><animateTransform attributeName="transform" type="scale" begin="0s" calcMode="spline" keySplines="0.3 0 0.7 1;0.3 0 0.7 1" values="0;1;0" keyTimes="0;0.5;1" dur="1s" repeatCount="indefinite"></animateTransform></circle></g></svg>
                        </article>
                    </transition>
                    <article class="log-item" v-if="filteredRenewals.length == 0">
                        <div class="log-item--header">
                        <span class="log-item--area">
                            Geral
                        </span>
                            <span class="log-item--event badge bg-info">
                            NOTÍCIA
                        </span>
                        </div>
                        <div class="log-item--body">
                            <div class="log-item--body-string">Não há renovações disponíveis. Utilize os filtros para carregar as renovações.</div>
                        </div>
                    </article>
                    <div v-if="filteredRenewals.length">
                        <article class="log-item renewal" v-for="renewal in filteredRenewals">
                            <div class="log-item--header">
                                <span class="log-item--area" v-bind:data-original-title="contractAgeMessage(renewal)" v-bind:class="{ 'color-danger text-bold' : !closeToExpireContract(renewal) }" data-toggle="tooltip">
                                    @{{ renewal.data_inicio_contrato }}
                                </span>
                                <span class="log-item--event badge bg-info">
                                    @{{ renewal.regime }} / @{{ renewal.modalidade }}
                                </span>
                                <div class="renewal-item--actions text-right">
                                    <div v-if="renewal.status === RenewalStatuses.initial">
                                        <button class="btn btn-circle renewal-item--action btn-passthu-renewal" @click="skipRenewal(renewal)" :disabled="renewal.processing">
                                            pular
                                        </button>
                                        <button class="btn btn-round renewal-item--action btn-open-renewal" @click="loadRenewalDetails(renewal)" :disabled="renewal.processing">
                                            abrir
                                        </button>
                                    </div>
                                    <div v-if="renewal.status === RenewalStatuses.open">
                                        <button class="btn btn-circle renewal-item--action btn-cancel" @click="cancelRenewal(renewal)" :disabled="renewal.processing">
                                            cancelar
                                        </button>
                                        <button class="btn btn-circle renewal-item--action btn-confirm" @click="confirmRenewal(renewal)" :disabled="renewal.processing">
                                            confirmar
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="log-item--body">
                                <div class="log-item--body-string">
                                    <table class="table table-sm table-bordered renewal--table renewal--table-client-info" v-bind:class="renewal.detailed ? 'loaded' : '' ">
                                        <thead>
                                        <tr>
                                            <th width="20%">Tutor</th>
                                            <th width="20%">Pet</th>
                                            <th width="40%">Plano</th>
                                            <th width="20%">Mês de reajuste</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td><a v-bind:href="renewal.tutor.link" target="_blank">@{{ renewal.tutor.nome }}</a></td>
                                            <td><a v-bind:href="renewal.pet.link" target="_blank">@{{ renewal.pet.nome }}</a></td>
                                            <td>@{{ renewal.plano.nome }}<span class="numeric"> | @{{ formatNumberToMoney(renewal.plano.valor) }}</span></td>
                                            <td>@{{ renewal.mes_reajuste_numerico }} - @{{ renewal.mes_reajuste }}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <div v-if="renewal.detailed">
                                        <table class="table table-sm table-bordered renewal--table renewal--table-usage-info">
                                            <thead>
                                            <tr>
                                                <th width="20%">Faturado</th>
                                                <th width="20%">Utilizado</th>
                                                <th width="40%">Relação de uso</th>
                                                <th width="20%">Reajuste</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td class="numeric">@{{ formatNumberToMoney(getValorFaturado(renewal)) }}</td>
                                                <td class="numeric">@{{ formatNumberToMoney(renewal.detailed.utilizado) }}</td>
                                                <td class="numeric">@{{ formatNumber(renewal.detailed.relacao_uso) }}%</td>
                                                <td class="numeric">@{{ formatNumber(renewal.detailed.reajuste) }}%</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                        <h5>Dados de renovação: </h5>
                                        <table class="table table-sm table-bordered renewal--table renewal-data">
                                            <thead>
                                            <tr>
                                                <th width="20%">Valor mensal original</th>
                                                <th width="20%">Desconto (%)</th>
                                                <th width="10%">Parcelas</th>
                                                <th class="text-center">Plano reajustado (Mensal | Anual)</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td class="nopadding inputtable brl">
                                                    <span class="brl-before">R$ </span>
                                                    <input title="Valor mensal original" type="number" min="0" required class="form-control ghost numeric brl" v-model="renewal.calculed.valor_mensal_original" v-on:dblclick="autoFillValorMensal(renewal)" v-on:blur="calculateTotalRenewal(renewal)">
                                                </td>
                                                <td class="nopadding inputtable">
                                                    <input title="Valor de desconto" type="number" min="1" step="10" max="100" required class="form-control ghost numeric" v-model="renewal.calculed.desconto" v-on:blur="calculateTotalRenewal(renewal)">
                                                </td>
                                                <td class="nopadding inputtable">
                                                    <input title="Parcelas para o anual" type="number" max="12" min="1" required class="form-control ghost numeric" v-model="renewal.calculed.parcelas">
                                                </td>
                                                <td class="text-center">
                                                    <span class="numeric" v-bind:class="{ 'main-option': renewal.mensal }">@{{ formatNumberToMoney(renewal.calculed.total_mensal) }}</span>
                                                    <span class="separator" style="font-family: 'Fira Code'"> | </span>
                                                    <span class="numeric" v-bind:class="{ 'main-option': renewal.anual }">@{{ formatNumberToMoney(renewal.calculed.total_anual) }}</span>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                            </div>
                            <div class="log-item--footer">
                                <div v-if="renewal.renovacao.renovado">
                                    <span class="log-item--timestamp" v-if="renewal.status === RenewalStatuses.processing">
                                        Processo de renovação <strong>#@{{ renewal.renovacao.object.id }}</strong> em andamento sob status de <strong>@{{ renewal.renovacao.object.status }}.</strong>
                                    </span>
                                    <span class="log-item--timestamp" v-if="renewal.status === RenewalStatuses.complete">
                                        Processo de renovação <strong>#@{{ renewal.renovacao.object.id }}</strong> concluído em sob status de <strong>@{{ renewal.renovacao.object.status }}.</strong>
                                    </span>
                                </div>
                                <div class="log-item--progress" v-if="renewal.processing">
                                    <div class="progress-status text-center">
                                        <span class="progress-message">@{{ renewal.processingMessage }}</span>
                                    </div>
                                    <div class="progress-bar-container">
                                        <div class="cs-loader">
                                            <div class="cs-loader-inner">
                                                <label>.</label>
                                                <label>.</label>
                                                <label>.</label>
                                                <label>.</label>
                                                <label>.</label>
                                                <label>.</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </article>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal" id="advanced-filter-modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Filtros avançados</h5>
                    </div>
                    <div class="modal-body">
                        <p>Filtre as renovações por data.</p>
                        <form>
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="recipient-name" class="col-form-label">Ano: </label>
                                        <select class="form-control"  v-model="selectedFilters.ano" ref="anoSelect">
                                            @for($i = (\Carbon\Carbon::now()->year - 1); $i < \Carbon\Carbon::now()->addYears(5)->year; $i++)
                                                <option>{{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="recipient-name" class="col-form-label">Mês: </label>
                                        <select class="form-control" v-model="selectedFilters.mes" ref="mesSelect">
                                            @for($i = 1; $i <= 12; $i++)
                                                <option value="{{ $i }}">{{ sprintf('%02d', $i) . ' - ' . \Carbon\Carbon::createFromFormat('m/d', $i . '/01')->formatLocalized('%B') }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                            </div>



                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn bg-default" data-dismiss="modal" v-on:click="applyFilters()">Aplicar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    @parent
    <script type="text/javascript" src="{!! asset('js/json-view/bundle.js') !!}"></script>
    <script src="{!! asset('js/moment/moment.js') !!}"></script>
    <script src="https://unpkg.com/vue@3.0.11"></script>
    <script src="https://unpkg.com/axios@0.21.1/dist/axios.min.js"></script>

    <script>
        window.RenewalSettings = {
            token: '{{ csrf_token() }}',
            base: '{{ url('/renewals/v2/api') }}',
            soundManager: {
                url: '{{ asset('/assets/sounds/logger-notification-sound.ogg') }}',
                play: false,
                audio: null,
                enabled: true,
            }
        };
    </script>
    <script src="{!! asset('js/renewals/vuer.js') !!}"></script>
    <script>
        const renewalVuer = Vue.createApp(RenewalVuer).mount('#vuer');

        renewalVuer.selectedFilters.ano = {{ \Carbon\Carbon::now()->year }};
        renewalVuer.selectedFilters.mes = {{ \Carbon\Carbon::now()->month + 1 }};
    </script>
@endsection