@extends('relatorios.base')

@section('title')
    @parent
    - Cancelamentos
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
                    <form role="form" action="{{ route('relatorios.cancelamento') }}" method="GET" >
                        <div class="form-body">

                                <div class="row">
                                    <div class="col-md-4">
                                        <h4>Data de cancelamento</h4>
                                        <div class="input-group input-large date-picker input-daterange" data-date="{{ $params['start'] }}" data-date-format="dd/mm/yyyy">
                                            <input type="text" class="form-control" name="start" value="{{ $params['start'] }}">
                                            <span class="input-group-addon"> até </span>
                                            <input type="text" class="form-control" name="end"
                                                   value="{{ $params['end'] }}"> 
                                        </div>
                                    </div>    
                                    <div class="col-md-4">
                                        <h4>Nome do cliente</h4>
                                        <div>
                                            <input type="text" class="form-control" name="cliente_nome" value="{{ ($params['cliente_nome']) ?? null }}">
                                        </div>
                                    </div>  
                                    <div class="col-md-4">
                                        <h4>Nome do pet</h4>
                                        <div>
                                            <input type="text" class="form-control" name="pet_nome" value="{{($params['pet_nome']) ?? null }}">
                                        </div>
                                    </div>  
                                </div>
                           
                            <div class="row">
                         
                                <div class="form-group col-md-4">
                                    <h4>Planos</h4>
                                    <div class="input-group">
                                    <span class="input-group-addon input-left">
                                        <i class="fa fa-book"></i>
                                    </span>
                                        <select name="planos[]" id="planos" class="form-control select2" multiple="multiple">
                                            @foreach(\App\Models\Planos::all() as $plano)
                                                <option value="{{ $plano->id }}"
                                                        {{ \App\Http\Controllers\RelatoriosController::setSelected($plano->id, $params, 'planos') }}>
                                                        {{ $plano->id }} - {{ $plano->nome_plano }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <h4>Motivo</h4>
                                    <select name="motivo" id="select_motivo" class="form-control select2-modal">
                                        <option value=""></option>
                                        @foreach(\App\Models\Cancelamento::MOTIVOS as $key => $value)
                                            <option value="{{ $key }}" 
                                            {{ \App\Http\Controllers\RelatoriosController::setSelected($key, $params, 'motivo') }}
                                            >
                                                {{ $value }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <h4>Status financeiro</h4>
                                    <select name="status_financeiro" id="select_status_financeiro" class="form-control select2-modal">
                                        <option value=""></option>
                                        <option value="{{\App\Models\Clientes::PAGAMENTO_EM_DIA}}"
                                            {{isset($params['status_financeiro']) && $params['status_financeiro'] == \App\Models\Clientes::PAGAMENTO_EM_DIA ? 'selected' : ''}} 
                                        >{{\App\Models\Clientes::PAGAMENTO_EM_DIA}}</option>
                                        <option value="{{\App\Models\Clientes::INADIMPLENTE_60_DIAS}}"
                                            {{isset($params['status_financeiro']) && $params['status_financeiro'] == \App\Models\Clientes::INADIMPLENTE_60_DIAS ? 'selected' : ''}} 
                                        >{{\App\Models\Clientes::INADIMPLENTE_60_DIAS}}</option>
                                        <option value="{{\App\Models\Clientes::EM_ATRASO}}"
                                            {{isset($params['status_financeiro']) && $params['status_financeiro'] == \App\Models\Clientes::EM_ATRASO ? 'selected' : ''}} 
                                        >{{\App\Models\Clientes::EM_ATRASO}}</option>
                                    </select>
                                </div>

                                
                        </div>
                        <div class='row'>
                            <div class="col-md-4">
                                <h4>ID do cliente no Sistema Financeiro</h4>
                                <div>
                                    <input type="text" class="form-control" name="cliente_id_externo" value="{{($params['cliente_id_externo']) ?? null }}">
                                </div>
                            </div>  
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn blue" onclick="$('#pesquisar_label').text('Pesquisando, aguarde...'); $(this).attr('disabled');">
                                <span id="pesquisar_label">Pesquisar</span> <span class="fa fa-search"></span>
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
                                    <a href="{{ route('relatorios.cancelamento.download') }}?{{ http_build_query(array_merge($_GET, ['format' => 'xlsx'])) }}" download>
                                        <i class="fa fa-file-excel-o"></i> Excel
                                    </a>
                                </li>
                                {{-- <li>
                                    <a href="javascript:window.print();">
                                        <i class="fa fa-file-pdf-o disabled"></i> PDF
                                    </a>
                                </li> --}}
                            </ul>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 only-print" style="padding: 0">
                            <div class="col-sm-6">
                                <h2>Relatório de cancelamentos</h2>
                                <small>{{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</small>
                            </div>
                            <div class="col-sm-6">
                                <img src="{{ url('/') }}/assets/layouts/layout2/img/logo-blue.png" alt="logo" class="logo-default pull-right text-right" />
                            </div>
                        </div>
                    </div>

                </div>
                <div class="portlet-body">
                    @include('relatorios.parts.cancelamento.table', ['cancelamentos' => $cancelamentos])
                </div>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
    @parent
@endsection