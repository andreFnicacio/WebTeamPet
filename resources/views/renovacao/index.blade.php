@extends('layouts.app')

@section('title')
    @parent
    Validação de renovações
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
        tr.renovacao-NAO_OPTANTE {
            background: rgb(243 106 90 / 50%) !important;
        }

        tr.renovacao-PAGO {
            background: rgb(54 215 183 / 0.5) !important;
        }

        tr.renovacao-EM_NEGOCIACAO {
            background: rgba(92, 155, 209, 0.5) !important;
        }

    </style>
@endsection

@section('content')
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
                    <form role="form" action="{{ route('renovacao.index') }}" method="GET" >
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
    <div class="content">
        <div class="clearfix"></div>

        @include('flash::message')

        <div class="clearfix"></div>
        <div class="box box-primary">
            <div class="box-body">
                @include('renovacao.table-preparacao', ['dados' => $dados])
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @parent
            <script>
                window.$competencia_mes = '{{ isset($_GET['mes']) ? sprintf("%02d", $_GET['mes']) : \Carbon\Carbon::now()->format('m') }}';
                window.$competencia_ano = '{{ isset($_GET['ano']) ? sprintf("%02d", $_GET['ano']) : \Carbon\Carbon::now()->format('Y') }}';

                function getFormData($index) {
                    var $regime = $('#regime-'+$index).val();
                    var $valorBase = parseFloat($('#valor_base-'+$index).val().replace(',', '.'));
                    var $reajuste = parseFloat($('#reajuste-'+$index).val().replace(',', '.'));
                    var $desconto = parseFloat($('#desconto-'+$index).val().replace(',', '.'));
                    var $idPet = $('#id_pet-' + $index).val();
                    var $parcelas = $('#parcelas-' + $index).val();

                    return {
                        "id_pet": $idPet,
                        "regime" : $regime,
                        "valor_original" : $valorBase,
                        "valorBase" : $valorBase,
                        "reajuste" : $reajuste,
                        "desconto" : $desconto,
                        "parcelas" : $parcelas,
                        "anual" : (($valorBase * 12) * (1 + ($reajuste/100))) * (1 - ($desconto/100)),
                        "mensal" : (($valorBase) * (1 + ($reajuste/100))) * (1 - ($desconto/100))
                    }
                }


                function atualizarLinha($index, renovacao) {
                    {{--if(renovacao.regime === '{{ \App\Models\Pets::REGIME_MENSAL }}') {--}}
                        {{--location.reload();--}}
                    {{--}--}}
                    location.reload();
                }

                $(document).ready(function() {
                    $('.calculo-renovacao').change(function(e) {
                        var $index = $(this).data('index');
                        var $data = getFormData($index);


                        $('#valor_anual-'+$index).val($data.anual.toFixed(2));
                        $('#valor_mensal-'+$index).val($data.mensal.toFixed(2));
                    });

                    $('.confirmar-renovacao').click(function($e) {
                        $e.preventDefault();
                        var $index = $(this).data('index');
                        var $data = getFormData($index);
                        $data["_token"] = '{{ csrf_token() }}';
                        $data['status'] = '{{ \App\Models\Renovacao::STATUS_NOVO }}';
                        $data['competencia_ano'] = window.$competencia_ano;
                        $data['competencia_mes'] = window.$competencia_mes;

                        $.ajax('{{ route('renovacao.api.criar') }}', {
                            'method' : 'POST',
                            'data' : $data,
                            'success': function (data) {
                                alert('Uma nova renovação foi gerada. Confira no ambiente de controle.');

                                atualizarLinha($index, data);
                            }
                        })
                    });

                    $('.excluir-renovacao').click(function($e) {
                        $e.preventDefault();
                        var $index = $(this).data('index');
                        var $data = getFormData($index);
                        $data["_token"] = '{{ csrf_token() }}';
                        $data['status'] = '{{ \App\Models\Renovacao::STATUS_NAO_OPTANTE }}';
                        $data['competencia_ano'] = window.$competencia_ano;
                        $data['competencia_mes'] = window.$competencia_mes;

                        $.ajax('{{ route('renovacao.api.criar') }}', {
                            'method' : 'POST',
                            'data' : $data,
                            'success': function (data) {
                                alert('Registramos que o pet não optou pelo reajuste.');
                            }
                        })
                    });
                });

            </script>
@endsection
