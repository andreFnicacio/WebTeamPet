@extends('layouts.metronic5')

@section('title')
    @parent
    Pets - Editar - {{ $pets->nome_pet }}
@endsection
@section('content')
    <!-- BEGIN VALIDATION STATES-->


    <div class="portlet-body">
        <!-- BEGIN FORM-->
        {!! Form::model($pets, [
                            'route' => [
                                'pets.update',
                                $pets->id
                            ],
                            'method' => 'patch',
                            'class' => 'form-horizontal',
                            'id' => 'pets'
                        ]);
        !!}
        <div class="form-body">


            <div class="form-group">
                <div class="col-sm-12">

                    <div class="form-group">
                        <label class="control-label col-md-3">Número interno do Pet
                        </label>
                        <div class="col-md-4">
                            <input type="text" value="{{ $pets->id }}" placeholder="Gerado Automaticamente" disabled
                                   class="form-control"/>
                        </div>
                    </div>
                    @if(\Request::route()->getName() == 'pets.edit')
                        <div class="form-group">
                            <label class="control-label col-md-3">Ativo
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-4">
                                {{ Form::hidden('ativo',0) }}
                                <input type="checkbox" {{ $pets->ativo ? "checked" : "" }} name="ativo"
                                       class="make-switch" data-on-color="success" data-off-color="danger"
                                       data-on-text="Sim" data-off-text="Não" value="1">
                            </div>
                        </div>
                    @endif
                    <div class="form-group">
                        <label class="control-label col-md-3">Nome do Pet
                            <span class="required"> * </span>
                        </label>
                        <div class="col-md-4">
                            <input type="text" required value="{{ $pets->nome_pet }}" name="nome_pet" data-required="1"
                                   class="form-control"/>
                        </div>
                    </div>
                    @if(!\Entrust::hasRole(['CLIENTE']))
                        <div class="form-group">
                            <label class="control-label col-md-3">Tutor
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-4">
                                <select id="id_cliente" name="id_cliente" required placeholder="Selecione um cadastro"
                                        class="form-control select2">
                                    <option></option>
                                    @foreach(\App\Models\Clientes::orderBy('nome_cliente', 'asc')->get() as $c)
                                        <option
                                                value="{{ $c->id }}"
                                                {{ $c->id == $pets->id_cliente ? "selected" : "" }}
                                        >{{ $c->id . " - " . $c->nome_cliente }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @endif
                    <div class="form-group">
                        <label class="control-label col-md-3">Microchip
                            <span class="required"> * </span>
                        </label>
                        <div class="col-md-4">
                            <input type="text" value="{{ $pets->numero_microchip }}" name="numero_microchip"
                                   required="required" data-required="1" class="form-control"/>
                            <small>
                                Verifique corretamente o número digitado.
                            </small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3" for="tipo">Tipo
                            <span class="required"> * </span>
                        </label>
                        <div class="col-md-4">
                            <select required name="tipo" id="tipo" class="form-control">
                                @foreach([
                                    'cachorro' => 'Cachorro',
                                    'gato'     => 'Gato =^.^='
                                ] as $value => $option)
                                    <option value="{{ $value }}" {{ $value === $pets->tipo ? "selected" : '' }}>
                                        {{ $option }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Raça
                            <span class="required"> * </span>
                        </label>
                        <div class="col-md-4">
                            <input type="text" value="{{ $pets->raca->nome }}" required name="raca" data-required="1"
                                   class="form-control"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Data de Nasc.
                            <span class="required"> * </span>
                        </label>
                        <div class="col-md-4">
                            <div required class="input-group input-medium date date-picker"
                                 data-date-format="dd/mm/yyyy">
                                <input required type="text"
                                       value="{{ $pets->data_nascimento ? $pets->data_nascimento->format('d/m/Y') : ""}}"
                                       name="data_nascimento" class="form-control" readonly>
                                <span class="input-group-btn">
                                         <button class="btn default" type="button">
                                            <i class="fa fa-calendar"></i>
                                         </button>
                                    </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Doenças Pré-existentes
                            <span class="required"> * </span>
                        </label>
                        <div class="col-md-4">
                            <input type="checkbox"
                                   {{ $pets->contem_doencas_pre_existentes ? "checked" : "" }} name="contem_doencas_pre_existente"
                                   class="make-switch" data-on-color="success" data-off-color="danger"
                                   data-on-text="Sim" data-off-text="Não">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Cite as doenças
                            <span class="required"> </span>
                        </label>
                        <div class="col-md-4">
                            <textarea name="doencas_pre_existentes" type="text" class="form-control"></textarea>
                        </div>
                    </div>
                    @if(!\Entrust::hasRole(['CLIENTE']))
                        <div class="form-group">
                            <label class="control-label col-md-3">Observações
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-4">
                                <textarea name="observacoes" type="text"
                                          class="form-control">{{ $pets->observacoes }}</textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Id Externo
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-4">
                                <input type="text" value="{{ $pets->id_externo }}" name="id_externo" data-required="1"
                                       class="form-control"/>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            @if(!\Entrust::hasRole(['CLIENTE']))
                <div class="form-group">
                    <div class="col-md-12">
                        <div class="col-md-12" style="margin-bottom: 20px;">
                            <h3 class="block" style="margin-top: 30px;">Dados de Cobrança</h3>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Participativo?
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-4">
                                {{Form::hidden('participativo',0)}}
                                <input type="checkbox" name="ativo"
                                       {{ $pets->participativo ? "checked" : "" }} class="make-switch"
                                       data-on-color="success" data-off-color="danger" data-on-text="Sim"
                                       data-off-text="Não" value="1">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Conveniado

                            </label>
                            <div class="col-md-2">
                                <select name="id_conveniado" id="id_conveniado" class="form-control">
                                    <option value=""></option>
                                    @foreach(\App\Models\Conveniados::all() as $conveniado)
                                        <option value="{{ $conveniado->id }}" {{ $pets->id_conveniado === $conveniado->id ? "selected" : "" }}>{{ $conveniado->nome_conveniado }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Vencimento
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-2">
                                <select name="vencimento" id="vencimento" class="form-control" required>
                                    @for($i = 1; $i < 32; $i++)
                                        <option value="{{ $i }}" {{ $pets->vencimento === $i ? "selected" : "" }}>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Regime

                            </label>
                            <div class="col-md-2">
                                <select name="regime" id="regime" class="form-control">
                                    <option value="MENSAL" {{ $pets->regime === "MENSAL" ? "selected=selected" : "" }}>
                                        Mensal
                                    </option>
                                    <option value="ANUAL" {{ $pets->regime === "ANUAL" ? "selected=selected" : "" }}>
                                        Anual
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Valor
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-3">
                                <div class="input-group">
                                    <span class="input-group-addon">R$</span>
                                    <input name="valor" value="{{ $pets->valor }}" type="number" class="form-control"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif


            <!-- Nome Pet Field -->


            <!-- Submit Field -->
            <!--
            <div class="form-group col-sm-12">
                {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
            <a href="{!! route('pets.index') !!}" class="btn btn-default">Cancel</a>
            </div>
            -->


        </div>
        {!! Form::close() !!}
    </div>

    <!-- END FORM-->

    @php
        $petsPlanos = $pets->petsPlanos()->orderBy('id', 'DESC')->first();
        $planoAtual = new \App\Models\Planos();
        $primeiroPlano = \App\Models\PetsPlanos::where('id_pet', $pets->id)->where('status', 'P')->get()->first();
        if($petsPlanos) {
            $planoAtual = $petsPlanos->plano()->first();
        } else {
            $petsPlanos = new \App\Models\PetsPlanos();
        }
    @endphp
    @if(!\Entrust::hasRole(['CLIENTE']))
        <div class="portlet light portlet-fit portlet-form ">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-settings font-green-jungle"></i>
                    <span class="caption-subject font-green-jungle sbold uppercase">
                  Planos
                </span>
                </div>
                <div class="actions" data-target="#pets_planos">
                    <div class="btn-group btn-group-devided" data-toggle="buttons">
                        <button type="submit" id="save" class="btn green-jungle">Salvar</button>
                        <button type="submit" id="cancel" class="btn red-sunglo">Cancelar</button>
                    </div>
                </div>
            </div>
            <div class="portlet-body">
                <form action="{{ route('pets_planos.create') }}" class="form-horizontal" method="POST" id="pets_planos">
                    {{ csrf_field() }}
                    <input type="hidden" name="id_pet" value="{{ $pets->id }}">
                    <div class="form-body">

                        @include('pets.planos')
                    </div>
                </form>
            </div>
        </div>
    @endif
    <!-- END VALIDATION STATES-->

    <!-- BEGIN VALIDATION STATES -->
    @php
        $today = new \Carbon\Carbon();
        /*
        $dataContrato = $petsPlanos->data_inicio_contrato ?: $today;
        $diffInDays = $today->diffInDays($dataContrato);
        if($diffInDays > 365) {
            $dataUltimoContrato = \Carbon\Carbon::createFromFormat('d/m/Y', $dataContrato->format('d/m') . '/' . $today->format('Y'));
            if($dataUltimoContrato->gt($today)) {
                $dataUltimoContrato = \Carbon\Carbon::createFromFormat('d/m/Y', $dataContrato->format('d/m') . '/' . $today->subYear()->format('Y'));
            }
            $start = $dataUltimoContrato;
        } else {
            $start = $dataContrato;
        }
        $end = \Carbon\Carbon::createFromFormat('d/m/Y', $start->format('d/m/') . $start->copy()->addYear()->format('Y'));
        */

        $vigencias = $pets->vigencias();
        $start = $vigencias[0];
        $end = $vigencias[1];

        $startPrimeiroPlano = null;
        if ($primeiroPlano) {
            $startPrimeiroPlano = \Carbon\Carbon::createFromFormat('Y-m-d', $primeiroPlano->data_inicio_contrato->format('Y-m-d'));
        }

        /**
         * @var \App\Models\Planos
         */
        $plano  = $pets->plano();
        $planosGrupos = [];
        if($plano) {
            $planosGrupos = $plano->planosGrupos()->get();
        }

    @endphp

    @if(!empty($planosGrupos))
        @if($pets->isBichos())
            <div class="m-portlet portlet  light  portlet-form ">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <span class="m-portlet__head-icon">
                                <i class="fa fa-calendar"></i>
                            </span>
                            <h3 class="m-portlet__head-text">
                                Placar das Carências
                                @if($startPrimeiroPlano)
                                    <p class="font-blue font-sm help-block m-portlet__head-desc">Cliente desde:
                                        <strong>{{ $startPrimeiroPlano->format('d/m/Y') }}
                                            ({{ $startPrimeiroPlano->diffInDays($today) }} dias)</strong></p>
                                    <p class="font-blue font-sm help-block m-portlet__head-desc">Vigência:
                                        <strong>({{ $start->format('d/m/Y') }} - {{ $end->format('d/m/Y') }})</strong>
                                    </p>
                                @endif
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <div class="table-responsive">
                        <table class="table col-md-12">
                            <thead>
                            <tr>
                                <th> Procedimento</th>
                                <th> Carência</th>
                                <th> Qtde Permitida / ano</th>
                                <th> Qtde utilizada / 365 dias</th>
                                <th> Qtde Restante</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($pets->getPlacarCarenciasPorProcedimento() as $item)
                                <tr>
                                    <td>{!! $item['nome'] !!}</td>
                                    <td>{!! $item['carencia'] !!}</td>
                                    <td>{!! $item['qtd_permitida'] !!}</td>
                                    <td>{!! $item['qtd_utilizada'] !!}</td>
                                    <td>{!! $item['qtd_restante'] !!}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- END VALIDATION STATES -->
        @else
            <div class="m-portlet portlet  light  portlet-form ">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <span class="m-portlet__head-icon">
                                <i class="fa fa-calendar"></i>
                            </span>
                            <h3 class="m-portlet__head-text">
                                Placar das Carências
                                @if($startPrimeiroPlano)
                                    <p class="font-blue font-sm help-block m-portlet__head-desc">Cliente desde:
                                        <strong>{{ $startPrimeiroPlano->format('d/m/Y') }}
                                            ({{ $startPrimeiroPlano->diffInDays($today) }} dias)</strong></p>
                                    <p class="font-blue font-sm help-block m-portlet__head-desc">Vigência:
                                        <strong>({{ $start->format('d/m/Y') }} - {{ $end->format('d/m/Y') }})</strong>
                                    </p>
                                @endif
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <div class="table-responsive">
                        <table class="table col-md-12">
                            <thead>
                            <tr>
                                <th> Grupo</th>
                                <th> Carência</th>
                                <th> Qtde Permitida / ano</th>
                                <th> Qtde utilizada / 365 dias</th>
                                <th> Qtde Restante</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($pets->getPlacarCarenciasPorGrupo() as $item)
                                <tr>
                                    <td>{!! $item['grupo']->nome_grupo !!}</td>
                                    <td>
                                        <span class="badge {!! $item['carencia_helper']['cor'] !!}"
                                              data-toggle="tooltip"
                                              data-original-title="{!! $item['carencia_helper']['tooltip'] !!}">
                                            {!! $item['carencia_helper']['conteudo'] !!}
                                        </span>
                                    </td>
                                    <td>{!! $item['historicoUso']['qtd_permitida_label'] !!}</td>
                                    <td>{!! $item['historicoUso']['qtd_utilizada_label'] !!}</td>
                                    <td>{!! $item['historicoUso']['qtd_restante_label'] !!}</td>
                                    <td class="text-center">
                                        <a href="#modal-procedimentos-{{ $item['grupo']->id }}" data-toggle="modal"
                                           class="text-center btn btn-xs bg-blue font-white">VER DETALHES</a>
                                        <div id="modal-procedimentos-{{ $item['grupo']->id }}" class="modal fade"
                                             tabindex="-1" data-replace="true" style="display: none;">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal"
                                                                aria-hidden="true"></button>
                                                        <h4 class="modal-title">Procedimentos do
                                                            Grupo {{ $item['grupo']->nome_grupo }}</h4>
                                                    </div>
                                                    <div class="modal-body">
                                                        <table class="table table-bordered table-responsive table-stripped">
                                                            <thead>
                                                            <tr>
                                                                <th class="text-center">Código</th>
                                                                <th class="text-center">Nome</th>
                                                                <th class="text-center">Carência</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            @foreach($item['procedimentos'] as $proc)
                                                                <tr>
                                                                    <td>{{ $proc['id_procedimento'] }}</td>
                                                                    <td>{{ $proc['procedimento'] }}</td>
                                                                    <td>
                                                                        @if($proc['carencia'] >= 0)
                                                                            FALTAM <span
                                                                                    class="badge badge-warning bg-yellow-gold"><strong>{{ $proc['carencia'] }}</strong></span>
                                                                            DIAS
                                                                        @else
                                                                            <span class="badge badge-success"><i
                                                                                        class="fa fa-check"></i></span>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" data-dismiss="modal"
                                                                class="btn dark btn-outline">Fechar
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- END VALIDATION STATES -->

        @endif
        <!-- BEGIN VALIDATION STATES-->
        <div class="m-portlet portlet  light  portlet-form ">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <div class="m-portlet__head-title">
                            <span class="m-portlet__head-icon">
                                <i class="fa fa-check-square-o"></i>
                            </span>
                        <h3 class="m-portlet__head-text">
                            Histórico de Uso
                        </h3>
                    </div>
                </div>
            </div>
            <div class="m-portlet__body">
                <div class="table-responsive">
                    <table class="table col-md-12">
                        <thead>
                        <tr>
                            <th> Data</th>
                            <th> Clínica</th>
                            <th> Procedimento</th>
                            @if(!\Entrust::hasRole(['CLIENTE']))
                                <th> Valor para o plano</th>
                            @endif
                            <th> Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $queryHistorico = \Modules\Guides\Entities\HistoricoUso::where('id_pet', $pets->id)
                                    ->where('tipo_atendimento', '!=', \Modules\Guides\Entities\HistoricoUso::TIPO_ENCAMINHAMENTO)
                                    ->orderBy('created_at', 'desc');
                            if(\Entrust::hasRole(['CLIENTE'])) {
                                $queryHistorico->where('status', '=', 'LIBERADO');
                            }
                            $historicosComuns = $queryHistorico->get();
                            $historicosEncaminhamentos = \Modules\Guides\Entities\HistoricoUso::where('id_pet', $pets->id)
                                                         ->where('tipo_atendimento', \Modules\Guides\Entities\HistoricoUso::TIPO_ENCAMINHAMENTO)
                                                         ->orderBy('created_at', 'desc')->get();
                            $historicos = $historicosComuns->merge($historicosEncaminhamentos)->sortByDesc(function($guia) {
                                return $guia->realizado_em ? $guia->realizado_em->format('YmdHis') : $guia->created_at->format('YmdHis');
                            });
                        @endphp
                        @foreach($queryHistorico->get() as $historico)
                            <tr>
                                <td>{{ $historico->created_at->format('d/m/Y') }}</td>
                                <td>{{ $historico->clinica()->first()->nome_clinica }}</td>
                                <td>{{ $historico->procedimento()->first()->nome_procedimento }}</td>
                                @if(!\Entrust::hasRole(['CLIENTE']))
                                    <td>R$ {{ number_format($historico->valor_momento, 2, ",", ".") }}</td>
                                @endif
                                <td>
                                    <span class="badge badge-{{ $historico->status === "LIBERADO" ? "success" : "danger" }}"> {{ $historico->status }} </span>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

@endsection

@section('scripts')
    @parent

    <script>
        $(document).ready(function () {
            var $actions = $('.actions[data-target]');


            $.each($actions, function (k, v) {
                var $v = $(v);
                $v.find('#save').click(function () {
                    var target = $v.attr('data-target');
                    if (target != '') {
                        $(target).submit();
                    }
                });
                $v.find('#cancel').click(function () {
                    var target = $v.attr('data-target');
                    location.href = "{!! route('pets.index') !!}";
                    return;
                });
            });

        });
    </script>
@endsection
