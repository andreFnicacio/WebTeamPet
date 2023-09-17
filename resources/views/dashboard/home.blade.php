@extends('layouts.app')

@section('title')
    @parent
    Dashboard
@endsection

@section('css')
    @parent
    <link rel="stylesheet" href="https://unpkg.com/placeholder-loading/dist/css/placeholder-loading.min.css">
    <style>
        table th {
            vertical-align: middle !important;
        }
        table tr.total {
            border-bottom: 1px solid #e7ecf1;
        }
        table {
            margin-bottom: 40px !important;
        }
        div.box-result {
            background: white;
            min-height: 135px;
            padding: 20px 14px;
            margin-bottom: 34px;
            margin-right: 33px;
            display: inline-block;
            float: left;
            width: 100%;
        }
        .box-result span.ca-title {
            text-transform: uppercase;
            color: #999999;
            font-size: 15px;
        }
        .box-result div.ca-icon-box {
            display: inline-block;
            width: 60px;
            height: 60px;
            text-align: center;
            margin-right: 10px;
            margin-top: 10px;
        }
        .box-result span.ca-icon {
            line-height: 60px !important;
            font-size: 22px;
        }
        .box-result .details {
            display: table;
        }
        .box-result span.number {
            vertical-align: bottom;
            font-size: 20px;
            font-weight: bold;
        }
        .actions-container {
            display: table;
            width: 100%;
            position: relative
        }
        .actions-container div.data-filter {
            position: absolute;
            right: 0;
            top: 0;
            display: inline-block;
            z-index: 9994;
        }
        .actions-container .data-filter ul.options {
            list-style: none;
            padding: 0;
            padding-top: 0px;
            padding-right: 0px;
            padding-bottom: 0px;
            padding-left: 0px;
        }

        .actions-container .button-wrapper{
            display: table;
            width: 100%;
        }

        .toggle-content {
            display: block;
            background: white;
            padding: 12px;
            box-shadow: 1px 2px 2px 1px #c3c3c3;
        }
        .actions-container .options li {
            padding: 3px 5px;
            background: #f5f5f5;
            margin: 9px;
            cursor: pointer;
        }

        .actions-container .options li input {
            border: none;
            outline: none;
            background: transparent;
            width: 100%;
        }
        .actions-container .options li:hover {
            border: 2px solid #3598dc;
            padding: 1px 3px;
        }

        .actions-container .options li.selected {
            color: white;
            background-color: #3598dc;
        }

        .dashboard-chart {
            background: white;
            margin-bottom: 30px;
        }
        .dashboard-chart .title {
            padding: 20px;
            color: #58bbb3;
            text-transform: uppercase;
            font-weight: bold;
        }
        .dashboard-chart .content {
            height: 390px;
            width: 100%;
            padding: 0 20px;
        }
        .dashboard-chart p.content {
            text-align: center;
            vertical-align: middle;
            line-height: 390px;
            font-size: 20px;
            color: #909090;
            margin-bottom: 0;
        }

        .scrollable {
            overflow: scroll;
            text-transform: uppercase;
        }

        .copy-table {
            text-align: right;float: right;margin-top: -10px;
        }
        span.data-range {
            font-size: 14px;
            vertical-align: middle;
            margin-left: 10px;
            color: #13ab2e;
        }
        span.ca-extra {
            float: right;
            color: #13ab2e;
        }
        .dashboard-chart .content {
            position: relative;
            overflow: hidden;
        }
        .dashboard-chart .content::after {
            content: 'CLIQUE PARA DESBLOQUEAR';
            display: block;
            position: absolute;
            background-color: #fbfbfb;
            height: 100%;
            width: 100%;
            left: 0;
            top: 0;
            opacity: 0.8;
            text-align: center;
            line-height: 390px;
            font-family: Roboto;
            color: #585151;
            font-size: 22px;
            font-weight: 100;
        }
        .dashboard-chart .content.scrollable {
            overflow: scroll;
        }
        .dashboard-chart .content.scrollable::after {
            display: none;
        }
        .dashboard-stat {
            margin-bottom: 34px !important;
        }
        .dashboard-stat .badge-extra, .dashboard-stat-custom .badge-extra {
            margin: 5px 0 0 5px;
            padding: 2px 10px;
            height: 15px;
            float: right;
        }
        .hidden-badges .badge-extra {
            display: none;
        }

        .daterangepicker.dropdown-menu {
            z-index: 9999;
        }
        .daterangepicker.dropdown-menu.single.opensleft .ranges {
            display: block !important;
        }
        .daterangepicker.dropdown-menu .ranges {
            min-width: 190px;
        }

        .dashboard-stat-custom {
            margin-bottom: 34px !important;
            display: block;
            overflow: hidden;
            border-radius: 2px;
            box-shadow: 0 2px 3px 2px rgba(0,0,0,.1);
        }
        .dashboard-stat-custom .details {
            padding: 15px;
            float: left;
            width: 70%;
        }
        .dashboard-stat-custom .details .desc {
            float: left;
            font-size: 15px;
        }
        .dashboard-stat-custom .details .number {
            float: left;
            font-size: 20px;
            font-weight: bold;
            margin-top: 8px;
        }
        .dashboard-stat-custom .details .badge-extra {
            float: right;
        }
        .dashboard-stat-custom .visual {
            width: 30%;
            margin: 0;
            padding: 0;
            display: block;
            float: left;
            font-size: 35px;
            line-height: 35px;
        }
        .dashboard-stat-custom .visual i.fa {
            font-size: 60px;
            margin: 0;
            padding: 0;
            display: block;
            text-align: center;
            line-height: 100px;
        }
        .dashboard-stat-custom .dashboard-stat-divider {
            height: 1px;
            display: block;
            width: 100%;
            background-color: #eef1f5;
            float: left;
            margin: 5px 0;
        }
    </style>
@endsection

@section('content')
    @parent
    <div class="row" id="dashboardPanel">
        <div class="col-sm-12">
            <section class="content-header text-center">
                <h1 class="title">Dashboard</h1>
            </section>
            <div class="content">
                <div class="clearfix"></div>

                @include('flash::message')

                <div class="clearfix"></div>
                <div class="box box-primary">
                    <div class="box-body">
                        <div class="portlet" style="box-shadow: none;">
                            <div class="portlet-body">
                                <div class="table-wrapper" style="background: transparent; box-shadow: none">

                                    {{-- Dados diarios --}}
                                    <section id="dados-diarios">
                                        <div class="actions-container">
                                            <h3 class="section-title">DADOS DIÁRIOS </h3>
                                            <div class="data-filter">
                                                {{--<input type="text" id="dashboard-range-diario" class="form-control">--}}
                                                <div id="dashboard-range-diario" data-filtro="diario" class="dashboard-range pull-right tooltips btn btn-sm toggle-content" data-container="body" data-placement="bottom" data-original-title="Change dashboard date range">
                                                    <i class="icon-calendar"></i>&nbsp;
                                                    <span class="thin uppercase hidden-xs"></span>&nbsp;
                                                    <i class="fa fa-angle-down"></i>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-sm-3" v-for="ca in componentes.diarios" v-if="ca.permission">
                                                <div class="dashboard-stat dashboard-stat-v2"
                                                     v-bind:class="[ca.color]" href="#"
                                                     v-if="!ca.loading">
                                                    <div class="visual">
                                                        <i class="fa" v-bind:class="[ca.icon]" style="opacity: 0.2;"></i>
                                                    </div>
                                                    <div class="details" v-if="ca.data">
                                                        <div class="number">
                                                            <span data-counter="counterup" v-bind:data-value="ca.data.value">@{{ ca.data.value }}</span>
                                                        </div>
                                                        <div class="desc"> @{{ ca.title }} </div>
                                                        <span v-for="extra in ca.data.extra"
                                                              class="badge bg-font-white bg-white badge-extra"
                                                              data-toggle="tooltip"
                                                              v-html="extra.value + (extra.percent === false ? '' : '%')"
                                                              v-bind:title="extra.description ? extra.description : ''">
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="ph-item" v-if="ca.loading">
                                                    <div class="ph-col-12">
                                                        <div class="ph-picture"></div>
                                                        <div class="ph-col-8 empty"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </section>

                                    <div class="portlet light bordered">
                                        <div class="portlet-title" style="margin-top: 10px;">
                                            <div class="actions-container">
                                                <h3 class="section-title" style="margin: 5px 0 0;">DADOS POR PERÍODO </h3>
                                                <div class="data-filter">
                                                    <div id="dashboard-range-absoluto" data-filtro="absoluto" class="dashboard-range pull-right tooltips btn btn-sm toggle-content" data-container="body" data-placement="bottom" data-original-title="Change dashboard date range">
                                                        <i class="icon-calendar"></i>&nbsp;
                                                        <span class="thin uppercase hidden-xs"></span>&nbsp;
                                                        <i class="fa fa-angle-down"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="portlet-body">
                                            {{-- Dados absolutos --}}
                                            <section id="dados-absolutos">

                                                <div class="row">
                                                    <div class="col-sm-6 col-md-6 col-lg-4" v-for="ca in componentes.absolutos" v-if="ca.permission">
                                                        <div class="dashboard-stat-custom" href="#"
                                                             v-if="!ca.loading">
                                                            <div class="visual" v-bind:class="[ca.color]">
                                                                <i class="fa" v-bind:class="[ca.icon]" style="opacity: 0.2;"></i>
                                                            </div>
                                                            <div class="details" v-if="ca.data">
                                                                <div class="desc"> @{{ ca.title }} </div>
                                                                <span v-for="extra in ca.data.extra"
                                                                      class="badge bg-font-white bg-white badge-extra"
                                                                      v-bind:class="[ca.color]"
                                                                      data-toggle="tooltip"
                                                                      v-html="extra.value + (extra.percent === false ? '' : '%')"
                                                                      v-bind:title="extra.description ? extra.description : ''">
                                                                </span>
                                                                <div class="dashboard-stat-divider"></div>
                                                                <div class="number">
                                                                    <span data-counter="counterup" v-bind:data-value="ca.data.value">@{{ ca.data.value }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="ph-item" v-if="ca.loading">
                                                            <div class="ph-col-12">
                                                                <div class="ph-picture"></div>
                                                                <div class="ph-col-8 empty"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </section>
                                        </div>
                                    </div>

                                    {{-- Dados temporais --}}
                                    <section id="dados-temporais">
                                        <div class="actions-container">
                                            <h3 class="section-title">DADOS TEMPORAIS </h3>
                                            <div class="data-filter">
                                                <div id="dashboard-range-temporal" data-filtro="temporal" class="dashboard-range pull-right tooltips btn btn-sm toggle-content" data-container="body" data-placement="bottom" data-original-title="Change dashboard date range">
                                                    <i class="icon-calendar"></i>&nbsp;
                                                    <span class="thin uppercase hidden-xs"></span>&nbsp;
                                                    <i class="fa fa-angle-down"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-6" v-bind:class="componentSize(ct)" v-for="(ct, index) in componentes.temporais" v-if="ct.permission">
                                                <div class="dashboard-chart"
                                                     v-if="!ct.loading">
                                                    <div class="title">
                                                        <span class="fa fa-bar-chart"></span> @{{ ct.title }}
                                                        <button v-if="(ct.data.length || (typeof ct.data == 'object' && Object.keys(ct.data).length)) && ct.type == 'table'" v-bind:data-clipboard-target="'#' + ct.divId + ' table'" v-bind:id="ct.divId+'-copy'" @click="copyTable(ct)" class="btn btn-default copy-table">COPIAR</button>
                                                    </div>
                                                    <div v-bind:id="ct.divId"
                                                         v-bind:class="{ content: true, scrollable: isScrollEnabled(ct) }"
                                                         @click="enableScroll(ct)"
                                                         v-if="ct.data.length || (typeof ct.data == 'object' && Object.keys(ct.data).length)">

                                                        <table v-if="ct.type == 'table'" class="table table-condensed table-hover table-striped">
                                                            <thead>
                                                                <th v-for="h in ct.data.headers">@{{ h }}</th>
                                                            </thead>
                                                            <tbody>
                                                                <tr v-for="r in ct.data.rows">
                                                                    <td v-for="c in r">
                                                                        @{{ c }}
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <p v-if="!(ct.data.length || (typeof ct.data == 'object' && Object.keys(ct.data).length))" class="content">
                                                        Nenhum dado encontrado.
                                                    </p>
                                                </div>
                                                <div class="ph-item" v-if="ct.loading">
                                                    <div class="ph-col-12">
                                                        <div class="ph-picture"></div>
                                                        <div class="ph-col-8 empty"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </section>

                                </div>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @parent
    <script src="{!! asset('js/lodash.min.js') !!}"></script>
    <script src="{!! asset('js/clipboard.min.js') !!}"></script>
    <script src="{!! asset('js/vue-the-mask.js') !!}"></script>
    <script src="{!! asset('js/dashboard.vue.js') !!}?{{ time() }}"></script>
    <script src="{!! asset('js/amcharts/amcharts.js') !!}"></script>
    <script src="{!! asset('js/amcharts/serial.js') !!}"></script>
    <script src="{!! asset('js/amcharts/pie.js') !!}"></script>
    <script src="{!! asset('js/amcharts/plugins/export/export.min.js') !!}"></script>
    <script src="{!! asset('assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js') !!}" type="text/javascript"></script>
    <link rel="stylesheet" href="https://www.amcharts.com/lib/3/plugins/export/export.css" type="text/css" media="all" />
    <script src="https://www.amcharts.com/lib/3/themes/light.js"></script>
@endsection