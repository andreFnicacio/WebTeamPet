<div class="portlet box blue">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-list-alt"></i>Plano
        </div>
    </div>
    <div class="portlet-body">

        @php
            $petsPlanos = $pets->petsPlanosAtual()->first();
            $planoAtual = new \App\Models\Planos();
            $primeiroPlano = $pets->primeiroContrato();

            if($petsPlanos) {
                $planoAtual = $petsPlanos->plano()->first();
            } else {
                $petsPlanos = new \App\Models\PetsPlanos();
            }
        @endphp
        @if(!\Entrust::hasRole(['CLIENTE']))

            <table class="table">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Plano</th>
                    <th>Valor</th>
                    <th>Início</th>
                    <th>Encerramento</th>
                    <th>Vendedor</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
                </thead>
                <tbody>

                @foreach(\App\Models\PetsPlanos::where('id_pet', $pets->id)->orderBy('created_at', 'ASC')->get() as $petPlano)
                    <tr>
                        <td>{{ $petPlano->id }}</td>
                        <td><a target="_blank"
                               ref="{{ route('planos.edit', ['id' => $petPlano->id_plano]) }}">{{ $petPlano->plano()->first()->nome_plano }}</a>
                        </td>
                        <td>{{ \App\Helpers\Utils::money($petPlano->valor_momento) }}</td>
                        <td>{{ ($petPlano->data_inicio_contrato)->format('d/m/Y') }}</td>
                        <td>{{ $petPlano->data_encerramento_contrato ? ($petPlano->data_encerramento_contrato)->format('d/m/Y') : " - " }}</td>
                        <td style="padding: 2px;">
                            @if(isset($petPlano->id_vendedor))
                                <img src="{{  route('vendedores.avatar', \App\Models\Vendedores::find($petPlano->id_vendedor)) }}"
                                     title="{{ \App\Models\Vendedores::find($petPlano->id_vendedor)->nome }}"
                                     class="img-circle" width="40">
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if(isset($petPlano->status))
                                <span class="label label-sm {{ \App\Models\PetsPlanos::STATUS_CORES[$petPlano->status] }}"
                                      data-toggle="tooltip"
                                      data-original-title="{{ \App\Models\PetsPlanos::STATUS[$petPlano->status] }}">
                                    {{ $petPlano->status }}
                                </span>
                            @else
                                -
                            @endif
                        </td>
                        <td>

                            @if(\Entrust::hasRole(['CADASTRO','ADMINISTRADOR', 'PLAN_MANAGER']))
                                @if($petPlano->id !== $petsPlanos->id)
                                    <form action="{{ route('pets_planos.delete') }}" method="POST">
                                        {{ csrf_field() }}
                                        <input type="hidden" name="id_pets_planos" value="{{ $petPlano->id }}">
                                        <button class="btn btn-circle bg-red-sunglo bg-font-red-sunglo btn-deletePetsPlanos"
                                                type="submit" data-toggle="tooltip" data-original-title="Excluir">
                                            <span class="fa fa-trash"></span>
                                        </button>
                                    </form>
                                @endif
                            @endif

                            {{--@if($petPlano->id === $petsPlanos->id && $petPlano->data_encerramento_contrato)--}}
                            {{--<form action="{{ route('petsPlanos.reverter') }}" method="POST">--}}
                            {{--{{ csrf_field() }}--}}
                            {{--<input type="hidden" name="id_pets_planos" value="{{ $petPlano->id }}">--}}
                            {{--<button class="btn btn-circle bg-green-meadow bg-font-green-meadow" type="submit" data-toggle="tooltip" data-original-title="Reverter cancelamento"><span class="fa fa-undo"></span></button>--}}
                            {{--</form>--}}
                            {{--@endif--}}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>


    </div>
</div>

<div class="portlet box blue">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-cog"></i>Dados do plano integral atual
        </div>
    </div>
    <div class="portlet-body">
        <div id="modal-pets-planos" class="modal fade" tabindex="-1" data-replace="true" style="display: none">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">Manter o mês do reajuste?</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-body form-horizontal">
                            <div class="form-group">
                                <label class="control-label col-md-4">Mês do Reajuste
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-6">
                                    <div class="input-group input-medium">
                                        <select class="form-control"
                                                onchange="$('#pets_planos .pets_planos_mes_reajuste').val($(this).val())"
                                                required>
                                            <option value="">Selecione um mês</option>
                                            @for($i = 1; $i <= 12; $i++)
                                                <option value="{{ $i }}" {{ $pets->mes_reajuste === $i ? "selected" : "" }}>{{ $i }}
                                                    - {{ \App\Helpers\Utils::getMonthName($i) }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" data-dismiss="modal" class="btn dark btn-outline">Fechar</button>
                        <button type="submit" form="pets_planos" class="btn green-meadow btn-outline"
                                onclick="this.disabled='disabled';this.form.submit();">Enviar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        @if($pets->cancelamentoAgendado())
            <div class="font-lg font-yellow-gold text-center padding-tb-20">
                <p>Existe um cancelamento agendado
                    para {{ $pets->cancelamentoAgendado()->data_cancelamento->format('d/m/Y') }}</p>
                <form action="{{ route('pets.revogarCancelamento', $pets->id) }}" method="post">
                    {{ csrf_field() }}
                    <input type="hidden" name="id_cancelamento" value="{{ $pets->cancelamentoAgendado()->id }}">
                    <button type="submit" class="btn btn-default mx-auto yellow-gold margin-bottom-30">
                        <i class="fa fa-refresh"></i> Revogar Cancelamento Agendado
                    </button>
                </form>
            </div>

        @elseif($pets->ativo)

            <form action="{{ route('pets_planos.create') }}" method="POST" id="pets_planos">
                {{ csrf_field() }}
                <input type="hidden" name="id_pet" value="{{ $pets->id }}">
                <input type="hidden" name="mes_reajuste" value="{{  $pets->mes_reajuste }}"
                       class="pets_planos_mes_reajuste">
                <div class="form-body">

                    @if(\Request::route()->getName() == 'pets.edit')

                        @if(!empty($petsPlanos)  && empty($petsPlanos->data_encerramento_contrato))
                            <input type="hidden" name="id_pets_planos" value="{{ $petsPlanos->id }}">
                        @endif
                        <div class="form-group col-md-6">
                            <label class="control-label">Plano
                                <span class="required"> * </span>
                            </label>
                            <div>
                                <select name="id_plano" data-previous="{{$planoAtual->id}}" id="single"
                                        placeholder="Selecione um cadastro" class="form-control select2">
                                    <option></option>
                                    @foreach(\App\Models\Planos::orderBy('ativo', 'desc')->get() as $plano)
                                        <option value="{{ $plano->id }}"
                                                {{ (!empty($planoAtual) && $plano->id == $planoAtual->id) ? "selected" : (!$plano->ativo ? "disabled" : "") }}
                                        >
                                            {{ $plano->id }}-{{ $plano->nome_plano }} - Ind:
                                            R${{ number_format($plano->preco_plano_individual) }} / Fam:
                                            R${{ number_format($plano->preco_plano_familiar) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group col-md-6">
                            <label class="control-label">Vendedor</label>
                            <div>
                                <select name="id_vendedor" style="width: 100%" id="vendedor"
                                        placeholder="Selecione um cadastro" class="form-control" required>
                                    <option></option>
                                    @foreach(\App\Models\Vendedores::all() as $vendedor)
                                        <option value="{{ $vendedor->id }}"
                                                data-image="{{ route('vendedores.avatar', $vendedor->id) }}" {{ $petsPlanos->id_vendedor == $vendedor->id ? "selected" : "" }}>
                                            {{ $vendedor->id }} - {{ $vendedor->nome }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group col-md-12">
                            <label class="control-label">Promoções vinculadas (opcional)</label>
                            <div>
                                <select multiple="multiple" name="promocao[]" style="width: 100%" id="promocao"
                                        placeholder="Selecione" class="select2 select2-hidden-accessible form-control">
                                    <option></option>
                                    @foreach(\App\Models\Promocao::all() as $promocao)
                                        <option value="{{ $promocao->id }}">
                                            {{ $promocao->id }} - {{ $promocao->nome }}
                                            (desconto: {{ $promocao->tipo_desconto == 'F' ? "R$" : "" }} {{ $promocao->desconto }}{{ $promocao->tipo_desconto == 'P' ? "% " : "" }}
                                            )
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group col-md-6">
                            <label class="control-label">Conveniada</label>
                            <div>
                                <select name="id_conveniada" style="width: 100%" id="conveniada"
                                        placeholder="Selecione uma conveniada" class="form-control">
                                    <option></option>
                                    @foreach(\App\Models\Conveniada::all() as $conveniada)
                                        <option value="{{ $conveniada->id }}" {{ $petsPlanos->id_conveniada == $conveniada->id ? "selected" : "" }}>
                                            {{ $conveniada->id }} - {{ $conveniada->nome }}
                                            (desconto: {{ $conveniada->tipo_desconto == 'F' ? "R$" : "" }} {{ $conveniada->desconto }}{{ $conveniada->tipo_desconto == 'P' ? "% " : "" }}
                                            )
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group col-md-4">
                            <label class="control-label">Desconto em folha?
                                <span class="required"> * </span>
                            </label>
                            <div>
                                {{Form::hidden('desconto_folha',0)}}
                                <input type="checkbox"
                                       {{ $petsPlanos->desconto_folha ? "checked" : "" }} name="desconto_folha"
                                       class="make-switch" data-on-color="success" data-off-color="danger"
                                       data-on-text="Sim" data-off-text="Não" value="1">
                            </div>
                        </div>

                        <div class="form-group col-md-2">
                            <label class="control-label">Participativo?
                                <span class="required"> * </span>
                            </label>
                            <br>
                            <span class="label label-info {{ $pets->participativo ? 'label-warning' : '' }}">{{ $pets->participativo ? 'SIM' : 'NÃO' }}</span>
                        </div>

                        {{--@if(!empty($planoAtual->id))--}}
                        <div class="form-group form-status col-md-6">
                            <label class="control-label" for="sexo">Status
                                <span class="required"> * </span>
                            </label>
                            <div>
                                <select required name="status" id="status" class="form-control">
                                    @if(!empty($primeiroPlano))
                                        @foreach(\App\Models\PetsPlanos::STATUS as $key => $status)
                                            <option value="{{ $key }}">{{ $status }}</option>
                                        @endforeach
                                    @else
                                        <option value="{{ \App\Models\PetsPlanos::STATUS_PRIMEIRO_PLANO }}">{{ \App\Models\PetsPlanos::STATUS[\App\Models\PetsPlanos::STATUS_PRIMEIRO_PLANO] }}</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        {{--@endif--}}

                        <div class="form-group col-md-4">
                            <label class="control-label">Data Inicial do Contrato
                                <span class="required"> * </span>
                            </label>
                            <div>
                                <div class="input-group input-medium date date-picker" data-date-format="dd/mm/yyyy">
                                    <input value="{{ $petsPlanos->data_inicio_contrato ? $petsPlanos->data_inicio_contrato->format('d/m/Y') : (new \Carbon\Carbon())->format('d/m/Y') }}"
                                           type="text" class="form-control" name="data_inicio_contrato"
                                           {{ $petsPlanos->data_inicio_contrato ? "readonly" : "" }} required>
                                    <span class="input-group-btn">
                                        <button class="btn default" type="button">
                                            <i class="fa fa-calendar"></i>
                                        </button>
                                        </span>
                                </div>
                            </div>
                        </div>


                        <div class="form-group col-md-2">
                            <label class="control-label">Familiar?
                                <span class="required"> * </span>
                            </label>
                            <div>
                                <input type="checkbox" {{ $pets->familiar ? "checked" : "" }} name="familiar"
                                       class="make-switch" data-on-color="success" data-off-color="danger"
                                       data-on-text="Sim" data-off-text="Não" value="1">
                            </div>
                        </div>

                        <div class="form-group col-md-12">
                            <label class="control-label">Justificativa de transição
                                <span class="required">  * </span>
                            </label>
                            <div>
                                <select required name="transicao" id="transicao" class="form-control">
                                    @foreach(\App\Models\PetsPlanos::TRANSICOES as $t)
                                        <option value="{{ $t }}">{{ $t }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group col-md-6">
                            <label class="control-label">Adesão
                                <span class="required"> </span>
                            </label>
                            <div>
                                <div class="input-group">
                                    <span class="input-group-addon">R$</span>
                                    <input name="adesao" value="{{ $petsPlanos->adesao }}" type="number"
                                           class="form-control"/>
                                </div>
                            </div>
                        </div>

                        <div class="form-group col-md-6">
                            <label class="control-label">Valor do Plano (sem desconto)
                                <span class="required"> </span>
                            </label>
                            <div>
                                <div class="input-group">
                                    <span class="input-group-addon">R$</span>
                                    <input name="valor_plano" value="{{ $petsPlanos->valor_momento }}" type="number"
                                           class="form-control"/>
                                </div>
                            </div>
                        </div>
                        <!--
                            <div class="form-group">
                                <label class="control-label col-md-3">Encerramento
                                    <span class="required"> </span>
                                </label>
                                <div class="col-md-4">
                                    <div class="input-group input-medium date date-picker"  data-date-format="dd/mm/yyyy">
                                        <input value="{{ $petsPlanos->data_encerramento_contrato ?: "" }}" name="data_encerramento_contrato" type="text" class="form-control" {{ $petsPlanos->data_encerramento_contrato ? "readonly" : "" }}>
                                        <span class="input-group-btn">
                                    <button class="btn default" type="button">
                                        <i class="fa fa-calendar"></i>
                                    </button>
                                    </span>
                                    </div>
                                </div>
                            </div>
                            -->

                    @endif

                    <div class="actions" data-target="#pets_planos">
                        <div class="btn-group btn-group-devided" data-toggle="buttons">
                            <a class="btn green-jungle" href="#modal-pets-planos" data-toggle="modal">
                                Salvar
                            </a>
                            <button type="submit" id="cancel" class="btn red-sunglo">Cancelar</button>
                        </div>
                    </div>

                </div>
            </form>

        @endif

        @endif
    </div>
</div>

@php
    $today = new \Carbon\Carbon();
    /*
    $dataContrato = $petsPlanos->data_inicio_contrato ?: $today;
    $diffInDays = $today->diffInDays($dataContrato);
    if($diffInDays > 365) {
        $dataUltimoContrato = \Carbon\Carbon::createFromFormat('d/m/Y', $dataContrato->format('d/m') . '/' . $today->format('Y'));
        if($dataUltimoContrato->gt($today)) {
            $dataUltimoContrato = \Carbon\Carbon::createFromFormat('d/m/Y', $dataContrato->format('d/m') . '/' . $today->copy()->subYear()->format('Y'));
        }
        $start = $dataUltimoContrato;
    } else {
        $start = $dataContrato;
    }
    $end = \Carbon\Carbon::createFromFormat('d/m/Y', $start->format('d/m/') . $start->copy()->addYear()->format('Y'));
    */

    $startPrimeiroPlano = null;
    if ($primeiroPlano) {
        $vigencias = $pets->vigencias();
        $start = $vigencias[0];
        $end = $vigencias[1];
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

    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-list-alt"></i>Placar de carências

            </div>

            <div class="actions">
                @if($startPrimeiroPlano)
                    <span class="font-white font-sm help-block">Cliente desde: <strong>{{ $startPrimeiroPlano->format('d/m/Y') }} ({{ $startPrimeiroPlano->diffInDays($today) }} dias)</strong></span>
                    <span class="font-white font-sm help-block">Vigência: <strong>({{ $start->format('d/m/Y') }} - {{ $end->format('d/m/Y') }})</strong></span>
                @endif
            </div>
        </div>
        <div class="portlet-body">
            <div class="table-responsive">
                @if($primeiroPlano)
                    @if($pets->isBichos())
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
                    @else
                        <table class="table col-md-12">
                            <thead>
                            <tr>
                                <th> Grupo</th>
                                <th> Carência</th>
                                <th> Qtde Permitida / ano</th>
                                <th> Qtde utilizada / 365 dias</th>
                                <th> Qtde Restante</th>
                                <th> Ver procedimentos</th>
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
                                            <div class="modal-dialog">
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
                    @endif
                @endif
            </div>
        </div>
    </div>

    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-list-alt"></i>Exceções de Grupos
            </div>

            <div class="actions">
                @permission('create_excecao_grupo')
                <a class="btn  green-jungle sbold" href="#modal-criar-excecao" data-toggle="modal"><i
                            class="icon-plus red-sunglo"></i> ADICIONAR</a>
                @endpermission
            </div>
        </div>
        <div class="portlet-body">
            <div class="table-responsive">

                <table class="table col-md-12">
                    <thead>
                    <tr>
                        <th> Grupo</th>
                        <th> Carência</th>
                        <th> Qtde Permitida / ano</th>
                        <th width="20%"> Ações</th>
                        {{--<th class="text-center"> Liberação automática</th>--}}
                    </tr>
                    </thead>
                    <tbody>
                    @foreach(\App\Models\PetsGrupos::where('id_pet', $pets->id)->get() as $excecao)
                        <tr>
                            <td>
                                {{ $excecao->grupo->nome_grupo }}
                            </td>
                            <td><span class="badge badge-success"> {{ $excecao->dias_carencia }} </span></td>
                            <td><span class="badge badge-success"> {{ $excecao->quantidade_usos }} </span></td>
                            <td>
                                @if(\Entrust::hasRole(['CADASTRO','ADMINISTRADOR', 'PLAN_MANAGER']))
                                    <form action="{{ route('petsGrupos.delete', ['id'=> $excecao->id]) }}"
                                          method="POST">
                                        @method('DELETE')
                                        {{ csrf_field() }}
                                        <button class="btn btn-circle bg-red-sunglo bg-font-red-sunglo btn-deletePetsGrupos"
                                                type="submit" data-toggle="tooltip" data-original-title="Excluir">
                                            <span class="fa fa-trash"></span>
                                        </button>
                                    </form>
                                @endif
                            </td>
                            {{--<td class="text-center">--}}
                            {{--@if($excecao->liberacao_automatica)--}}
                            {{--<span class="badge badge-success">SIM</span>--}}
                            {{--@else--}}
                            {{--<span class="badge badge-danger">NÃO</span>--}}
                            {{--@endif--}}
                            {{--</td>--}}
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                @permission('create_excecao_grupo')
                <div id="modal-criar-excecao" class="modal fade contains-select2" tabindex="-1" data-replace="true"
                     style="display: none">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                <h4 class="modal-title">Nova exceção de grupo</h4>
                            </div>

                            <form action="{{ route('pets.criarExcecaoGrupo') }}" method="POST" class="form-horizontal"
                                  id="form-criar-excecao">
                                {{ csrf_field() }}
                                <input type="hidden" name="id_pet" value="{{ $pets->id }}">
                                <div class="modal-body">

                                    <div class="form-body">

                                        <div class="form-group">
                                            <label class="control-label col-md-5">Grupo
                                                <span class="required"> * </span>
                                            </label>
                                            <div class="col-md-6">
                                                <select name="excecao_grupo" id="select_excecao_grupo"
                                                        class="form-control select2-modal"
                                                        data-parent="form-criar-excecao">
                                                    <option value=""></option>
                                                    @foreach($planosGrupos as $pg)
                                                        <option value="{{ $pg->grupo_id }}"
                                                                data-carencia="{{ $pg->dias_carencia }}"
                                                                {{--data-liberacao-automatica="{{ $pg->liberacao_automatica ? "1" : "0" }}"--}}
                                                                data-quantidade-usos="{{ $pg->quantidade_usos }}">
                                                            {{ $pg->grupo()->first()->nome_grupo }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-5">Dias de carência
                                                <span class="required"> * </span>
                                            </label>
                                            <div class="col-md-6">
                                                <input type="number" id="excecao_dias_carencia" required
                                                       name="excecao_dias_carencia" data-required="1"
                                                       class="form-control"/>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-5">Quantidade de usos
                                                <span class="required"> * </span>
                                            </label>
                                            <div class="col-md-6">
                                                <input type="number" id="excecao_quantidade_usos" required
                                                       name="excecao_quantidade_usos" data-required="1"
                                                       class="form-control"/>
                                            </div>
                                        </div>
                                        {{--<div class="form-group">--}}
                                        {{--<label class="control-label col-md-5">Liberação automática?--}}
                                        {{--<span class="required"> * </span>--}}
                                        {{--</label>--}}
                                        {{--<div class="col-md-6">--}}
                                        {{--<input type="hidden" name="excecao_liberacao_automatica" value="0">--}}
                                        {{--<input type="checkbox" id="excecao_liberacao_automatica" name="ativo" class="make-switch" data-on-color="success" data-off-color="danger" data-on-text="Sim" data-off-text="Não" value="1">--}}
                                        {{--</div>--}}
                                        {{--</div>--}}
                                    </div>

                                </div>
                                <div class="modal-footer">
                                    <button type="button" data-dismiss="modal" class="btn dark btn-outline">Fechar
                                    </button>
                                    <button type="submit" class="btn green-meadow btn-outline">Enviar</button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
                @endpermission
            </div>
        </div>
    </div>

    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-list-alt"></i>Exceções de Cobertura
            </div>

            <div class="actions">
                @permission('create_excecao_grupo')
                <a class="btn  green-jungle sbold" href="#modal-criar-excecao-cobertura" data-toggle="modal"><i
                            class="icon-plus red-sunglo"></i> ADICIONAR</a>
                @endpermission
            </div>
        </div>
        <div class="portlet-body">
            <div class="table-responsive">

                <table class="table col-md-12">
                    <thead>
                    <tr>
                        <th> Procedimento</th>
                        <th width="20%"> Ações</th>
                        {{--<th class="text-center"> Liberação automática</th>--}}
                    </tr>
                    </thead>
                    <tbody>
                    @foreach(\App\Models\ExtensaoProcedimento::where('id_pet', $pets->id)->get() as $extensao)
                        <tr>
                            <td>
                                {{ $extensao->procedimento->nome_procedimento }}
                            </td>
                            <td>
                                @if(\Entrust::hasRole(['CADASTRO','ADMINISTRADOR', 'PLAN_MANAGER']))
                                    <form action="{{ route('extensaoProcedimento.delete', ['id'=> $extensao->id]) }}"
                                          method="POST">
                                        @method('DELETE')
                                        {{ csrf_field() }}
                                        <button class="btn btn-circle bg-red-sunglo bg-font-red-sunglo btn-deleteExtensaoProcedimentos"
                                                type="submit" data-toggle="tooltip" data-original-title="Excluir">
                                            <span class="fa fa-trash"></span>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                @permission('create_excecao_grupo')
                <div id="modal-criar-excecao-cobertura" class="modal fade contains-select2" tabindex="-1"
                     data-replace="true" style="display: none">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                <h4 class="modal-title">Nova exceção de cobertura</h4>
                            </div>

                            <form action="{{ route('pets.criarExtensaoProcedimento') }}" method="POST"
                                  class="form-horizontal" id="form-criar-excecao-cobertura">
                                {{ csrf_field() }}
                                <input type="hidden" name="id_pet" value="{{ $pets->id }}">
                                <div class="modal-body">

                                    <div class="form-body">

                                        <div class="form-group">
                                            <label class="control-label col-md-5">Procedimento
                                                <span class="required"> * </span>
                                            </label>
                                            <div class="col-md-6">
                                                <select name="id_procedimento" id="select_excecao_grupo"
                                                        class="form-control select2-modal"
                                                        data-parent="modal-criar-excecao-cobertura">
                                                    <option value=""></option>
                                                    @foreach(\App\Models\Procedimentos::where('ativo', 1)->get() as $p)
                                                        <option value="{{ $p->id }}">{{ $p->nome_procedimento }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="modal-footer">
                                    <button type="button" data-dismiss="modal" class="btn dark btn-outline">Fechar
                                    </button>
                                    <button type="submit" class="btn green-meadow btn-outline">Enviar</button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
                @endpermission
            </div>
        </div>
    </div>

    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-list-alt"></i>Encaminhamentos a realizar

            </div>
        </div>
        <div class="portlet-body">
            <div class="table-responsive">
                <table class="table col-md-12">
                    <thead>
                    <tr>
                        <th> Data</th>
                        <th> Clínica</th>
                        <th> Procedimento</th>
                        <th> Liberado em</th>
                        <th> Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php
                        $queryHistorico = \Modules\Guides\Entities\HistoricoUso::where('id_pet', $pets->id)
                        ->orderBy('created_at', 'desc')
                        ->where('tipo_atendimento', \Modules\Guides\Entities\HistoricoUso::TIPO_ENCAMINHAMENTO)
                        ->whereNull('realizado_em')
                        ->where('status', '=', 'LIBERADO');
                    @endphp
                    @foreach($queryHistorico->get() as $historico)
                        <tr>
                            <td>{{ $historico->created_at->format('d/m/Y') }}</td>
                            <td>{{ $historico->clinica()->first()->nome_clinica }}</td>
                            <td>{{ $historico->procedimento()->first()->nome_procedimento }}</td>
                            <td>{{ $historico->data_liberacao ? $historico->data_liberacao->format('d/m/Y') : 'Não liberado.' }}</td>
                            <td>
                                <span class="label label-sm label-{{ $historico->status === "LIBERADO" ? "success" : "danger" }}"> {{ $historico->status }} </span>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-list-alt"></i>Histórico de Uso

            </div>
        </div>
        <div class="portlet-body">
            <div class="table-responsive">
                <table class="table table-historico col-md-12">
                    <thead>
                    <tr>
                        <td> #</td>
                        <th> Data</th>
                        <th> Clínica</th>
                        <th> Procedimento</th>
                        @if(!\Entrust::hasRole(['CLIENTE']))
                            <th> Valor para o plano</th>
                        @endif
                        <th> Status</th>
                        <th> Ações</th>
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
                    @foreach($historicos as $historico)
                        <tr>
                            <td> {{ $historico->numero_guia  }} </td>
                            <td>{{ $historico->realizado_em ? $historico->realizado_em->format('d/m/Y') : $historico->created_at->format('d/m/Y') }}</td>
                            <td>{{ $historico->clinica()->first()->nome_clinica }}</td>
                            <td>{{ $historico->procedimento()->first()->nome_procedimento }}</td>
                            @if(!\Entrust::hasRole(['CLIENTE']))
                                <td>R$ {{ number_format($historico->valor_momento, 2, ",", ".") }}</td>
                            @endif
                            <td>
                                <span class="label label-sm label-{{ $historico->status === "LIBERADO" ? "success" : "danger" }}"> {{ $historico->status }} </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button class="btn btn-xs green dropdown-toggle" type="button"
                                            data-toggle="dropdown" aria-expanded="false"> Ações
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-acoes-guia" role="menu">
                                        @php
                                            $dataLiberada = $historico->dataLiberada();
                                        @endphp
                                        <li class="{{ $dataLiberada ? "" : 'disabled' }}">
                                            @if($historico->tipo === "ENCAMINHAMENTO")
                                                @if($historico->status === 'REALIZADO' || Entrust::hasRole(['AUTORIZADOR', 'ADMINISTRADOR', 'AUDITORIA']))
                                                    <a target="_blank"
                                                       href="{{ $dataLiberada ? route('autorizador.verGuia', $historico->numero_guia) : "" }}"
                                                    >
                                                        <i class="icon-tag"></i> Ver
                                                        guia {{ $dataLiberada ? "" : "({$historico->data_liberacao->format('d/m/Y H:i')})"}}
                                                    </a>
                                                @endif
                                            @else
                                                @if($historico->status === 'LIBERADO' || Entrust::hasRole(['AUTORIZADOR', 'ADMINISTRADOR', 'AUDITORIA']))
                                                    <a target="_blank"
                                                       href="{{ $dataLiberada ? route('autorizador.verGuia', $historico->numero_guia) : "" }}"
                                                    >
                                                        <i class="icon-tag"></i> Ver guia
                                                    </a>
                                                @endif
                                            @endif
                                        </li>
                                        <li>
                                            @if(!empty($historico->laudo) || !empty($historico->justificativa))
                                                <a href="#laudo_guia_{{ $historico->numero_guia }}" data-toggle="modal">
                                                    <i class="icon-user"></i> Ver laudo
                                                </a>
                                            @endif
                                        </li>

                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if($pets->participativo)
        <div class="portlet box blue">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-list-alt"></i>Participações
                </div>
                <div class="pull-right text-right">
                    Teto participativo: (ANUAL: {{ \App\Helpers\Utils::money($pets->plano()->teto_participativo) }} |
                    MENSAL: {{ \App\Helpers\Utils::money($pets->plano()->teto_participativo/10) }})<br>
                    Participado
                    total: {{ \App\Helpers\Utils::money(\App\Models\Participacao::participadoTotal($pets->id)) }}
                </div>
            </div>
            <div class="portlet-body">
                <div class="table-responsive">
                    <table class="table table-historico col-md-12">
                        <thead>
                        <tr>
                            <th> Número da guia</th>
                            <th> Status da guia</th>
                            <th> Cliente</th>
                            <th> Pet</th>
                            <th> Participação</th>
                            <th> Competência</th>
                            <th> Agendado</th>
                            <th> Lançado</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($participacoes as $participacao)
                            <tr>
                                <td>{{ $participacao->historicoUso->numero_guia }}</td>
                                <td>
                                    <span class="label label-sm label-{{ $participacao->historicoUso->status === "LIBERADO" ? "success" : "danger" }}"> {{ $participacao->historicoUso->status }} </span>
                                </td>
                                <td>{{ $participacao->cliente->nome_cliente }}</td>
                                <td>{{ $participacao->pet->nome_pet }}</td>
                                <td>{{ \App\Helpers\Utils::money($participacao->valor_participacao) }}</td>
                                <td>{{ $participacao->competencia }}</td>
                                <td>{{ $participacao->agendado ? $participacao->agendado->format(\App\Helpers\Utils::BRAZILIAN_DATE) : '-' }}</td>
                                <td>{{ $participacao->executado ? $participacao->executado->format(\App\Helpers\Utils::BRAZILIAN_DATE) : '-' }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-list-alt"></i>Tabela do Plano
            </div>
        </div>
        <div class="portlet-body">
            <div class="table-responsive">
                <table class="table table-historico col-md-12">
                    <thead>
                    <tr>
                        <th> Tipo</th>
                        <th> Visualizar</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>TABELA</td>
                        @if($planoAtual->tabela)
                            <td>
                                <a class="btn btn-xs blue font-white tooltips" data-placement="top"
                                   data-original-title="Baixar"
                                   href="{{ url('/') }}/{{ $planoAtual->tabela->file->path }}" type="download"
                                   download="{{ $planoAtual->tabela->file->original_name }}"><span
                                            class="fa fa-download tooltips"></span></a>
                            </td>
                        @else
                            <td> -</td>
                        @endif
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @foreach($historicos as $guia)
        <div id="laudo_guia_{{ $guia->numero_guia }}" class="modal fade" tabindex="-1" data-replace="true"
             style="display: none;">
            <div class="modal-dialog">

                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">Laudo ({{ $guia->numero_guia }})</h4>
                    </div>
                    <div class="modal-body">
                        <p>
                            {!! nl2br($guia->laudo) !!}
                        </p>
                        @if(!empty($guia->justificativa) && \Entrust::hasRole(['ADMINISTRADOR', 'AUDITORIA', 'ATENDIMENTO']))
                            <br>
                            <hr>Justificativa: </hr>
                            <p>
                                {{ $guia->justificativa }}
                            </p>
                        @endif
                    </div>
                    <div class="modal-footer">

                        <button type="button" data-dismiss="modal" class="btn dark btn-outline">Fechar</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endif


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
                        var campos = $(target).find('select[required], input[required], textarea[required]');
                        var valid = true;
                        for (var i = 0; i < campos.length; i++) {
                            valid &= campos[i].checkValidity();
                        }

                        if (valid) {
                            $(target).submit();
                        } else {
                            swal({
                                title: 'Oops!',
                                html: "Para finalizar, você precisa preencher todos os campos.",
                                type: 'error',
                                confirmButtonColor: '#ff8400',
                                confirmButtonText: 'Ok!'
                            })
                        }
                    }
                });
                $v.find('#cancel').click(function () {
                    var target = $v.attr('data-target');
                    location.href = "{!! route('pets.index') !!}";
                    return;
                });
            });

            $('#single').change(function () {
                var previous = $(this).data('previous');
                if ($(this).val() !== previous) {
                    [
                        'input[name="data_inicio_contrato"]',
                        'input[name="data_encerramento_contrato"]'
                    ].forEach(function (e) {
                        var input = $(e);
                        input.val('');
                        input.removeAttr('readonly');
                    });
                }

            });

            function imageCircle(option, size) {
                if (!size) {
                    size = 15;
                }
                if (!option.id) {
                    return option.text;
                }
                return "<img src='" + $(option.element).data('image') + "' class='img-circle' width='" + size + "'>" + option.text;
            };

            $("#vendedor").select2({
                placeholder: "Selecione um vendedor",
                templateResult: function (option) {
                    return imageCircle(option, 50)
                },
                templateSelection: function (option) {
                    return imageCircle(option, 20)
                },
                escapeMarkup: function (m) {
                    return m;
                }
            });

            $('.contains-select2').on('shown.bs.modal', function (e) {
                console.log('loaded');
                $(this).find('.select2-modal').each(function (k, v) {
                    $(v).select2({
                        tags: true,
                        dropdownParent: $("#" + $(v).data('parent'))
                    });
                });
            });

            $('.select2-modal').each(function (k, v) {
                $(v).select2({
                    tags: true,
                    dropdownParent: $("#" + $(v).data('parent'))
                });
            });

            $('#select_excecao_grupo').change(function (e) {
                var liberacaoAutomatica = $(this).find('option:selected').data('liberacao-automatica');
                var diasCarencia = $(this).find('option:selected').data('carencia');
                var quantidadeUsos = $(this).find('option:selected').data('quantidade-usos');

                $('#excecao_liberacao_automatica').bootstrapSwitch('state', liberacaoAutomatica);
                $('#excecao_dias_carencia').val(diasCarencia);
                $('#excecao_quantidade_usos').val(quantidadeUsos);
            });

            $('.btn-deletePetsPlanos').click(function (e) {
                e.preventDefault();

                swal({
                    type: "warning",
                    title: "Tem certeza que deseja deletar este plano?",
                    showConfirmButton: true,
                    showCancelButton: true,
                    confirmButtonText: "Sim",
                    cancelButtonText: "Não",
                    reverseButtons: true
                }).then(() => {
                    console.log('true');
                    $(this).closest('form').submit();
                    $(this).prop('disabled', true);
                }).catch(() => {
                    console.log('false');
                    return false;
                });

                return false;
            });

            $('.btn-deletePetsGrupos').click(function (e) {
                e.preventDefault();

                swal({
                    type: "warning",
                    title: "Tem certeza que deseja deletar esta exceção?",
                    showConfirmButton: true,
                    showCancelButton: true,
                    confirmButtonText: "Sim",
                    cancelButtonText: "Não",
                    reverseButtons: true
                }).then(() => {
                    console.log('true');
                    $(this).closest('form').submit();
                    $(this).prop('disabled', true);
                }).catch(() => {
                    console.log('false');
                    return false;
                });

                return false;
            });

            $('.btn-deleteExtensaoProcedimentos').click(function (e) {
                e.preventDefault();

                swal({
                    type: "warning",
                    title: "Tem certeza que deseja deletar esta exceção?",
                    showConfirmButton: true,
                    showCancelButton: true,
                    confirmButtonText: "Sim",
                    cancelButtonText: "Não",
                    reverseButtons: true
                }).then(() => {
                    console.log('true');
                    $(this).closest('form').submit();
                    $(this).prop('disabled', true);
                }).catch(() => {
                    console.log('false');
                    return false;
                });

                return false;
            });
        });
    </script>
@endsection