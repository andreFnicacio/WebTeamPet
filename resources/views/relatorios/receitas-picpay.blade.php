@extends('relatorios.base')

@section('title')
    @parent
    - Receitas Picpay
@endsection

@section('css')
    @parent
    <style>
        form input, form select {
            border-radius: 0;
        }
        .select2-selection.select2-selection--multiple {
            border-radius: 0 !important;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            .only-print {
                display: block !important;
            }
        }
        @page {
            size: landscape;
        }

        .portlet.light.bordered {
            border: none !important;
        }
        .only-print {
            display: none;
        }
        .datepicker.dropdown-menu {
            z-index: 10000;
        }
    </style>
@endsection

@section('content')
    @parent
    <div class="row">
        <div class="col-sm-12 no-print">
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption font-red-sunglo">
                        <i class="fa fa-filter font-red-sunglo"></i>
                        <span class="caption-subject bold uppercase">Filtros</span>
                    </div>
                </div>
                <div class="portlet-body form">
                    <form role="form" action="{{ route('relatorios.receitas-picpay') }}" method="GET" >
                        <div class="form-body">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-4">
                                        <h4>Período</h4>
                                        <div class="input-group input-large date-picker input-daterange" data-date="{{ $params['inicio'] }}" data-date-format="dd/mm/yyyy">
                                            <input type="text" class="form-control" name="inicio" value="{{ $params['inicio'] }}">
                                            <span class="input-group-addon"> até </span>
                                            <input type="text" class="form-control" name="fim"
                                                   value="{{ $params['fim'] }}">
                                        </div>
                                    </div>
                                </div> 
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn blue">
                                <span>Pesquisar</span> <span class="fa fa-search"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-sm-12">
            <div class="portlet light bordered">
                <div class="portlet-title" style="border-bottom: 0">
                    <div class="caption font-red-sunglo">

                    </div>
                    <div class="actions">
                        <div class="btn-group">
                            <a class="btn btn-sm green dropdown-toggle no-print" href="javascript:;" data-toggle="dropdown"> EXPORTAR
                                <i class="fa fa-angle-down"></i>
                            </a>
                            <ul class="dropdown-menu pull-right">
                                <li>
                                    <a href="{{ route('relatorios.receitas-picpay.download') }}?{{ http_build_query(array_merge($_GET, ['format' => 'xlsx'])) }}" download>
                                        <i class="fa fa-file-excel-o"></i> Excel
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:window.print();">
                                        <i class="fa fa-file-pdf-o disabled"></i> PDF
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 only-print" style="padding: 0">
                            <div class="col-sm-6">
                                <h2>Relatório de Receitas - Picpay</h2>
                                <small>{{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</small>
                            </div>
                            <div class="col-sm-6">
                                <img src="{{ url('/') }}/assets/layouts/layout2/img/logo-blue.png" alt="logo" class="logo-default pull-right text-right" />
                            </div>
                        </div>
                    </div>

                </div>
                <div class="portlet-body">
                    @include('relatorios.parts.receitas-picpay.table', ['receitas' => $receitas])
                </div>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
    @parent
@endsection