@extends('layouts.app')

@section('title')
    Controle de renovações
@endsection

@section('css')
    @parent
    <style>
        input.calculo-renovacao, input.valor-renovacao {
            min-width: 100px;
        }
        .page-content-wrapper {
            width: unset !important;
        }

        .tableFixHead          { overflow-y: auto; height: 500px; min-height: 500px; }
        .tableFixHead thead th { position: sticky; top: 0; z-index: 9999; background-color: white; }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12 no-print">
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption font-red-sunglo">
                        <i class="fa fa-refresh font-red-sunglo"></i>
                        <span class="caption-subject bold uppercase">Controle de renovações</span>
                    </div>
                </div>
                <div class="portlet-body form">

                </div>
            </div>
        </div>
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption font-red-sunglo">
                    <i class="fa fa-filter font-red-sunglo"></i>
                    <span class="caption-subject bold uppercase">Filtros</span>
                </div>
            </div>
            <div class="portlet-body form">
                <form role="form" action="{{ route('renovacao.controle') }}" method="GET" >
                    <div class="form-body">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-3">
                                    <h4>Mês</h4>
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
                                                    } elseif ($i == (\Carbon\Carbon::today()->month + 1)) {
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
                                            @for($i=\Carbon\Carbon::today()->year; $i<=\Carbon\Carbon::today()->year+1; $i++)
                                                <option value="{{ $i }}" {{ isset($_GET['ano']) && $_GET['ano'] == $i ? 'selected' : '' }}>{{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-3">
                                    <h4>Regime</h4>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="input-group input-large">
                                        <select name="regime" id="regime" required class="form-control">
                                            <option value="{{ \App\Models\Pets::REGIME_MENSAL }}" {{ isset($_GET['regime']) && $_GET['regime'] ==  \App\Models\Pets::REGIME_MENSAL ? 'selected' : '' }}>{{ \App\Models\Pets::REGIME_MENSAL }}</option>
                                            <option value="{{ \App\Models\Pets::REGIME_ANUAL }}" {{ isset($_GET['regime']) && $_GET['regime'] ==  \App\Models\Pets::REGIME_ANUAL ? 'selected' : '' }}>{{ \App\Models\Pets::REGIME_ANUAL }}</option>
                                            <option value="TODOS" {{ isset($_GET['regime']) && $_GET['regime'] ==  'TODOS' ? 'selected' : '' }}>TODOS</option>
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
        <div class="content">

            @include('flash::message')
            <div class="portlet light bordered">

                @include('renovacao.table')
            </div>


        </div>
@endsection

