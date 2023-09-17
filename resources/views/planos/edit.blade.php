@extends('layouts.app')

@section('title')
    @parent
    Planos - Editar - {{ $planos->nome_plano }}
@endsection

@section('css')
    @parent
    <script>
        window.idPlano = "{{ $planos->id }}";
    </script>
    <link href="{{ url('/') }}/assets/global/plugins/jstree/dist/themes/default/style.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ url('/') }}/assets/global/plugins/jquery-multi-select/css/multi-select.css" rel="stylesheet" type="text/css" />
    <style>
        .jstree-default .jstree-hovered {
            color: #1e1e1e;
        }
        .jstree-default .jstree-hovered, .jstree-default .jstree-clicked {
            background: none;
            border-radius: 0px;
            box-shadow: none;
        }
        .ms-optgroup-label {
            padding: 15px 0 !important;
            text-align: center;
            background: gray;
            color: white !important;
            margin-bottom: 20px !important;
        }
        .btn.collapse-button {
            margin-left: 15px;
        }
        #grupos-table .tree {
            margin-bottom: 15px;
        }
    </style>
@endsection

@section('content')
    <!-- BEGIN VALIDATION STATES-->
    <div class="portlet light portlet-fit portlet-form ">
        <div class="portlet-title">
            <div class="caption">
                <i class="icon-settings font-green-jungle"></i>
                <span class="caption-subject font-green-jungle sbold uppercase">
                  Planos
                </span>
            </div>
            @permission('edit_planos')
            <div class="actions" data-target="#planos">
                <div class="btn-group btn-group-devided" data-toggle="buttons">
                    <button type="submit" id="checar" class="btn blue-sharp" href="#modal-checar" data-toggle="modal">Checar</button>
                    <button type="submit" id="save" class="btn green-jungle">Salvar</button>
                    <button type="submit" id="cancel" class="btn red-sunglo">Cancelar</button>
                </div>
            </div>

            <div id="modal-checar" class="modal fade" tabindex="-1" data-replace="true" style="display: none">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                            <h4 class="modal-title">Checar Procedimentos</h4>
                        </div>

                        <form action="{{ route('planos.checarProcedimentosPlano') }}" method="POST" class="form-horizontal" id="form-checar" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <input type="hidden" name="plano_id" value="{{ $planos->id }}">
                            <div class="modal-body">

                                <div class="form-body">
                                    <div class="form-group">
                                        <label class="control-label col-md-4">Selecione o arquivo
                                            <span class="required"> * </span>
                                        </label>
                                        <div class="col-md-7">
                                            <input type="file" class="form-control" name="file" accept=".csv" required>
                                            <small>Selecione uma lista em formato .CSV contendo os procedimentos com seus respectivos ID, nome do procedimento, valores de coparticipação, limites e carência, obrigatoriamente nesta ordem.</small>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="modal-footer">
                                <button type="button" data-dismiss="modal" class="btn dark btn-outline">Fechar</button>
                                <button type="submit" class="btn green-meadow btn-outline">Enviar</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
            @endpermission
        </div>
        <div class="portlet-body">
            <!-- BEGIN FORM-->
            {!! Form::model($planos, [
                                'route' => [
                                    'planos.update',
                                    $planos->id
                                ],
                                'method' => 'patch',
                                'class' => 'form-horizontal',
                                'id' => 'planos'
                            ]);
            !!}
                <div class="form-body">

                    <div class="alert alert-danger display-hide">
                        <button class="close" data-close="alert"></button> Verifique se você preencheu todos os campos. 
                    </div>
                    <div class="alert alert-success display-hide">
                        <button class="close" data-close="alert"></button> Validado com sucesso. 
                    </div>
                    <div class="col-md-12" style="margin-bottom: 20px;"> 
                        <h3 class="block" style="margin-top: 0px;">Dados Gerais</h3>
                    </div>
                    @include('planos.fields')

                </div>
            {!! Form::close() !!}
        </div>
        <!-- END FORM-->
    </div>
    <!-- END VALIDATION STATES-->

    <div class="portlet light portlet-fit portlet-form ">
        <div class="portlet-title">
            <div class="caption">
                <i class="icon-settings font-green-jungle"></i>
                <span class="caption-subject font-green-jungle sbold uppercase">
                  Credenciados Habilitados
                </span>
            </div>
            <div class="tools">
                <button class="btn btn-info btn-circle collapse-button" data-target="#carencias-table">
                    <i class="fa fa-chevron-right"></i>
                </button>
            </div>
        </div>
        <div class="portlet-body">
            <div class="form-body" id="carencias-table" style="display: none">

                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>Nome</th>
                        <th class="text-center">Habilitado?</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($clinicas as $c)
                        <tr>
                            <td>
                                {{ $c->nome_clinica }}
                            </td>
                            <td class="text-center">
                                @if(Entrust::can('habilitar_credenciado_plano'))
                                    <input type="checkbox"
                                           class="check_plano_credenciado"
                                           data-credenciado="{{ $c->id }}"
                                           value="1"
                                            {{ $c->checkPlanoCredenciado($planos->id) ? "checked" : "" }}>
                                @else
                                    @if($c->checkPlanoCredenciado($planos->id))
                                        <span class="label label-success">Sim</span>
                                    @else
                                        <span class="label label-error">Não</span>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <!-- END FORM-->
    </div>

    <div class="portlet light portlet-fit portlet-form ">
        <div class="portlet-title">
            <div class="caption">
                <i class="icon-settings font-green-jungle"></i>
                <span class="caption-subject font-green-jungle sbold uppercase">
                  Grupos vs Procedimentos
                </span>
            </div>
            <div class="tools">
                <button class="btn green-jungle add-grupo">
                    <i class="fa fa-plus-circle"></i> Adicionar
                </button>
                <button class="btn btn-danger btn-circle collapse-button" data-target="#grupos-table">
                    <i class="fa fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="portlet-body">
            <table class="table table-responsive" id="grupos-table">
                <thead>
                    <tr>
                        <th width="25%">Nome</th>
                        <th width="15%">Quantidade</th>
                        <th width="15%">Carência</th>
                        <th width="15%">Uso único</th>
                        <th width="30%">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="bg-grey-steel" id="linha-novo-grupo" style="display: none">
                        <td>
                            <select class="form-control select2" name="id_grupo" required>
                                <option value=""></option>
                                @foreach($gruposNaoVinculados as $grupo)
                                    <option value="{{ $grupo->id }}">{{ $grupo->nome_grupo }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="number" class="col-sm-12 line-input" name="quantidade" value=""></td>
                        <td><input type="number" class="col-sm-12 line-input" name="carencia" value=""></td>
                        <td>
                            <div class="col-md-4">
                                <input type="hidden" name="uso_unico" value="0">
                                <input type="checkbox" name="uso_unico" class="make-switch" data-on-color="info" data-off-color="default" data-on-text="Sim" data-off-text="Não" value="1"><br>
                            </div>
                        </td>
                        <td>
                            <a class="btn btn-circle btn-success add-novo-grupo" data-on-complete="disable">
                                <span class="fa fa-save"></span>
                            </a>
                            <a class="btn btn-circle btn-danger disabled" disabled>
                                <span class="fa fa-trash"></span>
                            </a>
                            <a class="btn btn-circle btn-success disabled" disabled data-toggle="modal" data-target="#modalGrupo-novoGrupo">
                                Procedimentos
                            </a>
                            <div id="modalGrupo-novoGrupo" class="modal fade" role="dialog">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h4 class="modal-title"></h4>
                                        </div>
                                        <div class="modal-body">
                                            <p>Escolha os procedimentos:</p>
                                            <form method="post" action="{{ route('planos.addProcedimentosGrupo') }}" id="formProcedimentos-novoGrupo">
                                                {{ csrf_field() }}
                                                <input type="hidden" name="id_plano" value="{{ $planos->id }}">
                                                <input type="hidden" name="id_grupo" value="">
                                                <select multiple="multiple" class="" name="multi_procedimentos[]">
                                                    <optgroup label="TODOS">

                                                    </optgroup>
                                                </select>
                                            </form>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-success" form="formProcedimentos-novoGrupo">Salvar</button>
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </td>
                    </tr>
                    @foreach($planos->planosGrupos()->get() as $planoGrupo)
                        @php
                            $grupo = $planoGrupo->grupo()->first();
                        @endphp
                        <tr class="bg-grey-steel linha-grupo">
                            <td>{{ $grupo->nome_grupo }}</td>
                            <td><input type="number" class="col-sm-12 line-input" name="quantidade" value="{{ $planoGrupo->quantidade_usos }}"></td>
                            <td><input type="number" class="col-sm-12 line-input" name="carencia" value="{{ $planoGrupo->dias_carencia }}"></td>
                            <td>
                                <div>
                                    <input type="hidden" name="uso_unico" value="0">
                                    <input type="checkbox" {{ $planoGrupo->uso_unico ? "checked" : "" }} name="uso_unico" class="make-switch" data-on-color="info" data-off-color="default" data-on-text="Sim" data-off-text="Não" value="1"><br>
                                </div>
                            </td>
                            <td>
                                <a class="btn btn-circle btn-success edit-grupo" data-id="{{ $planoGrupo->id }}" data-on-complete="disable">
                                    <span class="fa fa-save"></span>
                                </a>
                                <a class="btn btn-circle btn-danger delete-grupo" data-id="{{ $planoGrupo->id }}">
                                    <span class="fa fa-trash"></span>
                                </a>
                                <a class="btn btn-circle btn-success" data-toggle="modal" data-target="#modalGrupo-{{ $planoGrupo->id }}">
                                    Procedimentos
                                </a>
                                <button class="btn btn-danger btn-circle collapse-button" data-target="#grupo-{{ $planoGrupo->id }}">
                                    <i class="fa fa-minus"></i>
                                </button>

                                <div id="modalGrupo-{{ $planoGrupo->id }}" class="modal fade" role="dialog">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                <h4 class="modal-title">{{ $grupo->nome_grupo }}</h4>
                                            </div>
                                            <div class="modal-body">
                                                <p>Escolha os procedimentos:</p>
                                                <form method="post" action="{{ route('planos.addProcedimentosGrupo') }}" id="formProcedimentos-{{ $planoGrupo->id }}">
                                                    {{ csrf_field() }}
                                                    <input type="hidden" name="id_plano" value="{{ $planoGrupo->plano_id }}">
                                                    <input type="hidden" name="id_grupo" value="{{ $grupo->id }}">
                                                    <select multiple="multiple" class="multi-select" name="multi_procedimentos[]">
                                                        <optgroup label="TODOS">
                                                            @foreach(\App\Models\Procedimentos::where('id_grupo', $grupo->id)->where('ativo', true)->orderBy('nome_procedimento')->get() as $proc)
                                                                <option value="{{ $proc->id }}" {{ isset($procedimentosPorGrupo[$grupo->id]) && in_array($proc->id, $procedimentosPorGrupo[$grupo->id]) ? 'selected' : '' }}>{{ $proc->nome_procedimento }}</option>
                                                            @endforeach
                                                        </optgroup>
                                                    </select>
                                                </form>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-success" form="formProcedimentos-{{ $planoGrupo->id }}">Salvar</button>
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr id="grupo-{{ $planoGrupo->id }}">
                            <td colspan="4" style="padding-top: 0;">
                                <div class="tree">
                                    <ul>
                                        @foreach($planos->procedimentosPorGrupo($grupo) as $planoProcedimento)
                                            @php
                                                $procedimento = \App\Models\Procedimentos::find($planoProcedimento->id_procedimento);
                                            @endphp
                                            <li data-jstree='{}'>
                                                <div class="proc-content">
                                                    <a class="id-procedimento" target="_blank" href="{{ route('procedimentos.edit', $procedimento->id) }}">
                                                        {{$procedimento->id}} - {{ $procedimento->nome_procedimento }}
                                                    </a>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <!-- END FORM-->
    </div>

    <!-- NOTAS -->
    <div class="portlet  light  portlet-form " id="notas-planos">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-file-text-o font-red-sunglo"></i>
                <span class="caption-subject font-red-sunglo sbold uppercase">Notas</span>
            </div>
            <div class="actions">
                <div class="row">
                    <div class="col-md-offset-0 col-md-12">

                        <a class="btn  green-jungle btn-outline sbold" href="#nova_nota" data-toggle="modal"><i class="icon-plus red-sunglo"></i> Novo</a>

                    </div>
                </div>
            </div>
        </div>
        <div class="portlet-body">
            <div class="table-responsive">
                <table class="table col-md-12 historico-financeiro">
                    <thead>
                    <tr>
                        <th> </th>
                        <th width="20%"> Data </th>
                        <th width="50%"> Corpo </th>
                        <th width="20%"> Autor </th>
                        <th width="10%"> Ações </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="n in notas">
                        <td></td>
                        <td width="20%">@{{ n.created_at }}</td>
                        <td width="50%">@{{ n.corpo }}</td>
                        <td width="20%">@{{ n.autor }}</td>
                        <td width="10%">
                            <a class="button btn-sm btn-circle bg-red tooltips" data-placement="top" data-original-title="Excluir a nota." @click="excluirNota(n)">
                                <i class="fa fa-trash font-white"></i>
                            </a>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div id="nova_nota" class="modal fade" tabindex="-1" data-replace="true" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">Nova nota</h4>
                    </div>
                    <div class="modal-body">
                        <form class="form">
                            <div class="form-body">
                                <div class="form-group col-sm-12">
                                    <label class="control-label col-md-3">Corpo
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-12">
                                        <textarea name="corpo" id="corpo-nota" rows="3" v-model="nota.corpo" class="form-control"></textarea>
                                    </div>
                                </div>
                                <div class="form-group col-sm-12">
                                    <label class="control-label col-md-3">Autor
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-12">
                                        <input type="text" readonly class="form-control" value="{{ Auth::user()->name }}">
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>
                    <div class="modal-footer">
                        <button type="button" data-dismiss="modal" class="btn dark btn-outline">Fechar</button>
                        <button type="button" id="salvar_nota" class="btn green-jungle btn-outline" @click="salvar()">Salvar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END NOTAS -->

    @permission('ver_documentos_internos')
    <br>
    <br>
    <div class="portlet  light  portlet-form " id="arquivos">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-folder-open-o font-red-sunglo"></i>
                <span class="caption-subject font-red-sunglo sbold uppercase">Documentos Internos</span>
            </div>
            @permission('criar_documentos_internos')
            <div class="actions">
                <div class="row">
                    <div class="col-md-offset-0 col-md-12">

                        <a class="btn  green-jungle btn-outline sbold tooltips" data-placement="top" data-original-title="Carregar" href="#novo_upload" data-toggle="modal"><i class="fa fa-upload red-sunglo"></i></a>

                    </div>
                </div>
            </div>
            @endpermission
        </div>
        <div class="portlet-body">
            <div class="table-responsive">
                <table class="table col-md-12 historico-financeiro">
                    <thead>
                    <tr>
                        <th> </th>
                        <th > Tipo </th>
                        <th > Descrição </th>
                        <th > Tamanho </th>
                        <th > Criação </th>
                        <th > Autor </th>
                        <th >  </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($planos->documentos() as $file)
                        <tr>
                            <td></td>
                            <td>{{ $file->documento->tipo ?: ' - ' }}</td>
                            <td>{{ nl2br($file->description) }}</td>
                            <td>{{ number_format($file->size/1024, 2, ",", ".") }}KB</td>
                            <td>{{ $file->created_at->format('d/m/Y H:i:s') }}</td>
                            <td>{{ $file->user()->first()->name }}</td>
                            <td ><a class="btn btn-xs blue font-white tooltips" data-placement="top" data-original-title="Baixar" href="{{ url('/') }}/{{ $file->path }}" type="download" download="{{ $file->original_name }}" ><span  class="fa fa-download tooltips"></span></a></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div id="novo_upload" class="modal fade" tabindex="-1" data-replace="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Novo upload</h4>
                </div>
                <div class="modal-body">
                    <form id="form_upload" class="form" action="{{ route('documentos_internos.upload') }}" method="POST" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="form-body">
                            <div class="form-group col-sm-12">
                                <label class="control-label col-md-5">Selecione o arquivo
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-12">
                                    <input type="file" class="form-control" name="file" accept="image/x-png,.tiff,image/bmp,image/jpeg,application/pdf,.csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel,application/msword,.doc, .docx,.ppt, .pptx,.txt,.pdf" required>
                                </div>
                            </div>
                            <div class="form-group col-sm-12">
                                <label class="control-label col-md-3">Descrição

                                </label>
                                <div class="col-md-12">
                                    <input type="text" name="description" class="form-control" value="" placeholder="Descrição do arquivo">
                                </div>
                            </div>
                            <div class="form-group col-sm-12">
                                <label class="control-label col-md-3">Visibilidade
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-12">
                                    {{ Form::hidden('publico',0) }}
                                    <input type="checkbox" name="publico" class="make-switch" data-on-color="success" data-off-color="danger" data-on-text="Público" data-off-text="Privado" value="1">
                                </div>
                            </div>
                            <div class="form-group col-sm-12">
                                <label class="control-label col-md-3">Tipo
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-12">
                                    <select class="select2 form-control" required name="tipo" id="tipo">
                                        @foreach(\App\Models\DocumentosInternos::TIPOS as $tipo)
                                            <option value="{{ $tipo }}">{{ $tipo }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-sm-12">
                                <label class="control-label col-md-3">Plano
                                </label>
                                <div class="col-sm-12">
                                    <input type="text" class="form-control" readonly value="{{ $planos->id . ' - ' . $planos->nome_plano }}">
                                    <input type="hidden" name="id_plano" value="{{ $planos->id }}">
                                </div>
                            </div>
                            <div class="form-group col-sm-12">
                                <label class="control-label col-md-3">Autor
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-12">
                                    <input type="text" readonly class="form-control" value="{{ Auth::user()->name }}">
                                </div>
                            </div>
                        </div>



                    </form>

                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn dark btn-outline">Fechar</button>
                    <button type="button" id="salvar_upload" data-target="#form_upload" class="btn green-jungle btn-outline">Salvar</button>
                </div>
            </div>
        </div>
    </div>
    @endpermission
    <!-- END VALIDATION STATES-->
@endsection

@section('scripts')
    @parent
    <script src="{{ url('/') }}/assets/global/plugins/jstree/dist/jstree.min.js" type="text/javascript"></script>
    <script src="{{ url('/') }}/assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js" type="text/javascript"></script>
    <script src="{{ url('/') }}/assets/global/plugins/quicksearch/quicksearch.js" type="text/javascript"></script>
    <script>
        $(document).ready(function() {
            var $actions = $('.actions[data-target="#planos"]');

            $actions.find('#save').click(function() {
                var target = $actions.attr('data-target');
                if(target != '') {
                    $(target).submit();
                }
            });
            $actions.find('#cancel').click(function() {
                var target = $actions.attr('data-target');
                location.href = "{!! route('planos.index') !!}";
                return;
            });
        });
    </script>
    <script>

        function aplicarMultiselect(elem) {
            elem.multiSelect({
                selectableHeader: "<input type='text' class='search-input form-control' autocomplete='off' placeholder='Buscar...'>",
                selectionHeader: "<input type='text' class='search-input form-control' autocomplete='off' placeholder='Buscar...'>",
                selectableOptgroup: true,
                afterInit: function(ms){
                    var that = this,
                        $selectableSearch = that.$selectableUl.prev(),
                        $selectionSearch = that.$selectionUl.prev(),
                        selectableSearchString = '#'+that.$container.attr('id')+' .ms-elem-selectable:not(.ms-selected)',
                        selectionSearchString = '#'+that.$container.attr('id')+' .ms-elem-selection.ms-selected';

                    that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
                    .on('keydown', function(e){
                    if (e.which === 40){
                        that.$selectableUl.focus();
                        return false;
                    }
                    });

                    that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
                    .on('keydown', function(e){
                    if (e.which == 40){
                        that.$selectionUl.focus();
                        return false;
                    }
                    });
                },
                afterSelect: function(){
                    this.qs1.cache();
                    this.qs2.cache();
                },
                afterDeselect: function(){
                    this.qs1.cache();
                    this.qs2.cache();
                }
            });
        }

        $(document).ready(function() {

            $('.add-novo-grupo').click(function () {
                var grupo_id = $('#linha-novo-grupo select[name="id_grupo"]').val();
                var quantidade_usos = $('#linha-novo-grupo input[name="quantidade"]').val();
                var dias_carencia = $('#linha-novo-grupo input[name="carencia"]').val();
                var uso_unico = $('#linha-novo-grupo input[name="uso_unico"][type="checkbox"]').is(':checked');
                if (grupo_id && quantidade_usos && dias_carencia) {
                    var data = {
                        _token: '{{ csrf_token() }}',
                        plano_id: '{{ $planos->id }}',
                        grupo_id: grupo_id,
                        quantidade_usos: quantidade_usos,
                        dias_carencia: dias_carencia,
                        uso_unico: uso_unico
                    }
                    $.ajax({
                        url: "{{ route('planos.addNovoGrupo') }}",
                        type: "POST",
                        data: data,
                        success: function (response) {
                            var optionSelected = $('#linha-novo-grupo select.select2 option:selected');
                            $('#linha-novo-grupo td:first-child').text(optionSelected.text());
                            $('#linha-novo-grupo .select2').remove();
                            $('#linha-novo-grupo .btn.disabled').removeAttr('disabled').removeClass('disabled');

                            $('#modalGrupo-novoGrupo h4.modal-title').text(response.grupo.nome_grupo);
                            $('#formProcedimentos-novoGrupo input[name="id_grupo"]').text(response.grupo.id);
                            $.each(response.listaProcedimentos, function (i, proc) {
                                $('#formProcedimentos-novoGrupo select optgroup').append('<option value="'+proc.id+'">'+proc.nome_procedimento+'</option>');
                            });
                            
                            $('#formProcedimentos-novoGrupo select').addClass('multi-select');
                            aplicarMultiselect($('#formProcedimentos-novoGrupo select'));
                            // $('#formProcedimentos-novoGrupo select').addClass('multi-select').multiSelect({
                            //     selectableOptgroup: true
                            // });
                        }
                    });
                } else {
                    swal('Erro!', 'Todos os campos são obrigatórios', 'error');
                }
            });

            $('.edit-grupo').click(function () {
                var planosgrupos_id = $(this).attr('data-id');
                var quantidade_usos = $(this).closest('.linha-grupo').find('input[name="quantidade"]').val();
                var dias_carencia = $(this).closest('.linha-grupo').find('input[name="carencia"]').val();
                var uso_unico = $(this).closest('.linha-grupo').find('input[name="uso_unico"][type="checkbox"]').is(':checked');
                
                if (planosgrupos_id && quantidade_usos && dias_carencia) {
                    var data = {
                        _token: '{{ csrf_token() }}',
                        planosgrupos_id: planosgrupos_id,
                        quantidade_usos: quantidade_usos,
                        dias_carencia: dias_carencia,
                        uso_unico: uso_unico
                    }
                    $.ajax({
                        url: "{{ route('planos.editGrupo') }}",
                        type: "POST",
                        data: data,
                        success: function (response) {
                            swal('Sucesso!', 'Dados editados com sucesso.', 'success');
                        }
                    });
                } else {
                    swal('Erro!', 'Todos os campos são obrigatórios', 'error');
                }
            });

            $('.add-grupo').click(function () {
                $('#grupos-table tbody tr:first-child').slideDown();
            });

            $('.delete-grupo').click(function () {
                var tr = $(this).closest('tr');
                var id = $(this).attr('data-id');
                swal({
                    title: 'Tem certeza?',
                    text: "Tem certeza que deseja remover este grupo com seus procedimentos deste plano?",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sim',
                    cancelButtonText: 'Não',
                }).then(function () {
                    $.ajax({
                        'url': '{{ route('planos.deleteGrupo') }}',
                        'type': 'post',
                        'data': {
                            'id': id,
                            '_token': '{{ csrf_token() }}'
                        },
                        'success': function (data) {
                            tr.next().remove();
                            tr.remove();
                            swal({
                                title: 'Sucesso!',
                                text: 'Grupo removido!',
                                type: 'success',
                            });
                        }
                    });
                });
            });

            aplicarMultiselect($('.multi-select'));
            // $('.multi-select').multiSelect({
            //     selectableOptgroup: true
            // });

            $('.tree').jstree({
                "core" : {
                    "themes" : {
                        "icons": false,
                        "responsive": true
                    }
                }
            });

            // handle link clicks in tree nodes(support target="_blank" as well)
            $('.tree').on('select_node.jstree', function(e,data) {
                var link = $('#' + data.selected).find('.proc-content a');
                if (link.attr("href") != "#" && link.attr("href") != "javascript:;" && link.attr("href") != "") {
                    var win = window.open(link.attr("href"), '_blank');
                    win.focus();
                }
            });

            $('.portlet .collapse-button').click(function () {
                var target = $(this).attr('data-target').toString();
                console.log(target);
                $(this).toggleClass('btn-info btn-danger');
                $(this).find('i').toggleClass('fa-chevron-right fa-minus');
                $(target).slideToggle('fast');
            });

            $('input.check_plano_credenciado').change(function(e){
                var $confirm = confirm('Deseja modificar a cobertura de atendimento do credenciado?');
                var $_self = $(this);
                if(!$confirm) {
                    $_self.prop('checked', !$_self.prop('checked'));
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }

                var value = $_self.prop('checked')  ? 1 : 0;

                //Habilitar/Desabilitar via AJAX

                $.ajax({
                    url: "{{ route('planosCredenciados.habilitacao') }}",
                    type: "POST",
                    data: {
                        "id_plano": "{{ $planos->id }}",
                        "id_clinica": $_self.data('credenciado'),
                        "habilitado": value,
                        '_token': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {
                        console.log(data);
                    },
                    error: function(data) {
                        console.log(data);
                    }
                })
            })
        });
    </script>

    <script>

        $(document).ready(function() {

            //Handle upload

            $('#salvar_upload').click(function(e) {
                e.preventDefault();
                var $self = $(this);
                $self.addClass('disabled');
                var $target = $($self.data('target'));
                $target.submit();
            })
        });
    </script>

@endsection