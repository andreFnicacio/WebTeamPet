@extends('relatorios.base')

@section('title')
    @parent
    - Clientes
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
                    <form role="form" action="{{ route('relatorios.clientesSemFaturaCompetencia') }}" method="GET" >
                        <div class="form-body">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <h4>Competência</h4>
                                    </div>
                                    <div class="col-sm-3">
                                        <h4>Ano</h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="input-group input-large">
                                            <select name="mes" class="form-control">
                                                @for($i=1; $i<=12; $i++)
                                                    @php
                                                        $selected = '';
                                                        if (isset($_GET['mes'])) {
                                                            if ($_GET['mes'] == $i) {
                                                                $selected = 'selected';
                                                            }
                                                        } elseif ($i == (\Carbon\Carbon::today()->month)) {
                                                            $selected = 'selected';
                                                        }
                                                    @endphp
                                                    <option value="{{ $i }}" {{ $selected }}>{{ \App\Helpers\Utils::getMonthName($i) }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="input-group input-large">
                                            <select name="ano" class="form-control">
                                                @for($i=\Carbon\Carbon::today()->subYear(1)->year; $i<=\Carbon\Carbon::today()->year+1; $i++)
                                                    <option value="{{ $i }}" {{ isset($_GET['ano']) && $_GET['ano'] == $i ? 'selected' : '' }}>{{ $i }}</option>
                                                @endfor
                                            </select>
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
    </div>
    <div class="row">


        <div class="col-sm-12">
            <div class="portlet light bordered">
                <div class="portlet-title" style="border-bottom: 0">
                    <div class="caption font-red-sunglo">

                    </div>

                    <div class="row">
                        <div class="col-sm-12 only-print" style="padding: 0">
                            <div class="col-sm-6">
                                <h2>Relatório de indicações</h2>
                                <small>{{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</small>
                            </div>
                            <div class="col-sm-6">
                                <img src="{{ url('/') }}/assets/layouts/layout2/img/logo-blue.png" alt="logo" class="logo-default pull-right text-right" />
                            </div>
                        </div>
                    </div>

                </div>
                <div class="portlet-body">
                    @include('relatorios.parts.clientes-sem-fatura.table', ['results' => $results])
                </div>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
    @parent
@endsection