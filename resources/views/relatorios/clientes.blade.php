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
                    <form role="form" action="{{ route('relatorios.clientes') }}" method="GET" >
                        <div class="form-body">
                                
                            <div class="row">
                                <div class='col-md-12'>
                                    <h3>Cliente</h3>
                                    <hr>
                                </div>
                                <div class="col-md-5 col-lg-4">
                                    <h4>Data de cadastro</h4>
                                    <div class="input-group input-large date-picker input-daterange" data-date="{{ $params['start'] }}" data-date-format="dd/mm/yyyy">
                                        <input type="text" class="form-control" name="start" value="{{ $params['start'] }}" required>
                                        <span class="input-group-addon"> até </span>
                                        <input type="text" class="form-control" name="end"
                                                value="{{ $params['end'] }}" required> 
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <h4>Ativo?</h4>
                                    <div>
                                        <select name="ativo" id="select_ativo" class="form-control select2-modal">
                                            <option value=""></option>
                                            <option value="1"
                                                {{isset($params['ativo']) && $params['ativo'] == 1 ? 'selected' : ''}} 
                                            >Sim</option>
                                            <option value="0"
                                                {{isset($params['ativo']) && $params['ativo'] == 0 ? 'selected' : ''}} 
                                            >Não</option>
                                            
                                        </select>
                                    </div>
                                </div>    
                                <div class="col-md-2">
                                    <h4>Sexo:</h4>
                                    <div>
                                        <select name="sexo" id="select_sexo" class="form-control select2-modal">
                                            <option value=""></option>
                                            <option value="M"
                                                {{isset($params['sexo']) && $params['sexo'] == 'M' ? 'selected' : ''}} 
                                            >Masculino</option>
                                            <option value="F"
                                                {{isset($params['sexo']) && $params['sexo'] == 'F' ? 'selected' : ''}} 
                                            >Feminino</option>
                                            
                                        </select>
                                    </div>
                                </div>  

                            
                                
                            </div>
                           <br>
                            <div class="row">
                                <div class='col-md-12'>
                                    <h3>Pet(s) do cliente</h3>
                                    <hr>
                                </div>
                                <div class="col-md-5 col-lg-4">
                                    <h4>Aniversário de contratação</h4>
                                    <div class="input-group input-large date-picker input-daterange" data-date="{{ $params['pet_plano_aniversario_inicio']  ?? null }}" data-date-format="dd/mm/yyyy">
                                        <input type="text" class="form-control" name="pet_plano_aniversario_inicio" value="{{ $params['pet_plano_aniversario_inicio'] ?? null }}">
                                        <span class="input-group-addon"> até </span>
                                        <input type="text" class="form-control" name="pet_plano_aniversario_fim"
                                                value="{{ $params['pet_plano_aniversario_fim'] ?? null }}"> 
                                    </div>
                                </div>

                                <div class="form-group col-md-4">
                                    <h4>Plano(s)</h4>
                                    <div class="input-group">
                                    <span class="input-group-addon input-left">
                                        <i class="fa fa-book"></i>
                                    </span>
                                        <select name="pet_planos[]" id="pet_planos" class="form-control select2" multiple="multiple">
                                            <option value="0">Todos</option>
                                            @foreach(\App\Models\Planos::all() as $plano)
                                                <option value="{{ $plano->id }}"
                                                        {{ \App\Http\Controllers\RelatoriosController::setSelected($plano->id, $params, 'pet_planos') }}>
                                                        {{ $plano->id }} - {{ $plano->nome_plano }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <h4>Idade do pet:</h4>
                                <div class="input-group input-large">
                                    <input type="number" class="form-control" name="pet_idade_de" value="{{ $params['pet_idade_de'] ?? null }}">
                                    <span class="input-group-addon"> até </span>
                                    <input type="number" class="form-control" name="pet_idade_ate"
                                            value="{{ $params['pet_idade_ate'] ?? null }}"> 
                                </div>
                            </div>  

                            <div class="col-md-4">
                                <h4>Sexo do pet:</h4>
                                <div>
                                    <select name="pet_sexo" id="select_pet_sexo" class="form-control select2-modal">
                                        <option value=""></option>
                                        <option value="ND">Não declarado</option>
                                        <option value="M"
                                            {{isset($params['pet_sexo']) && $params['pet_sexo'] == 'M' ? 'selected' : ''}} 
                                        >Macho</option>
                                        <option value="F"
                                            {{isset($params['pet_sexo']) && $params['pet_sexo'] == 'F' ? 'selected' : ''}} 
                                        >Fêmea</option>
                                        
                                    </select>
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
                                    <a href="{{ route('relatorios.clientes.download') }}?{{ http_build_query(array_merge($_GET, ['format' => 'xlsx'])) }}" download>
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
                                <h2>Relatório de clientes</h2>
                                <small>{{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</small>
                            </div>
                            <div class="col-sm-6">
                                <img src="{{ url('/') }}/assets/layouts/layout2/img/logo-blue.png" alt="logo" class="logo-default pull-right text-right" />
                            </div>
                        </div>
                    </div>

                </div>
                <div class="portlet-body">
                    @include('relatorios.parts.clientes.table', ['dados' => $dados])
                </div>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
    @parent
@endsection