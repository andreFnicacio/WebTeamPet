@extends('layouts.app')

@section('title')
    @parent
    Procedimentos - Editar - {{ $procedimentos->nome_procedimento }}
@endsection

@section('css')
    @parent
    <style>
        .saving {
            pointer-events: none;
            background-color: #cccccc;
            color: white;
            border-color: #cccccc;
        }
    </style>
    <script>
        window.idProcedimento = {{ $procedimentos->id }};
    </script>
@endsection

@section('content')
    <!-- BEGIN VALIDATION STATES-->
    <div class="portlet light portlet-fit portlet-form ">
        <div class="portlet-title">
            <div class="caption">
                <i class="icon-settings font-green-jungle"></i>
                <span class="caption-subject font-green-jungle sbold uppercase">
                  Procedimentos
                </span>
            </div>
            @permission('edit_procedimentos')
            <div class="actions" data-target="#procedimentos">
                <div class="btn-group btn-group-devided" data-toggle="buttons">
                    <button type="submit" id="save" class="btn green-jungle">Salvar</button>
                    <button type="submit" id="cancel" class="btn red-sunglo">Cancelar</button>
                </div>
            </div>
            @endpermission
        </div>
        <div class="portlet-body">
            <!-- BEGIN FORM-->
            {!! Form::model($procedimentos, [
                                'route' => [
                                    'procedimentos.update',
                                    $procedimentos->id
                                ],
                                'method' => 'patch',
                                'class' => 'form-horizontal',
                                'id' => 'procedimentos'
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
                    @include('procedimentos.fields')

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
                  Vinculos
                </span>
            </div>
        </div>
        <div class="portlet-body">

            <div class="form-body" id="planosProcedimentos">
                <div class="input-group"><input type="text" id="query" placeholder="Buscar..." v-model="search" class="form-control">
                    <span class="input-group-btn"><i class="icon-magnifier"></i></span>
                </div>
                <table class="table table-responsive">
                    <thead>
                        <tr>
                            <th>Plano</th>
                            <th>Existe Vinculo?</th>
                            <th>Valor Credenciado</th>
                            <th>Valor Cliente</th>
                            <th>Tipo de coparticipação</th>
                            <th>Valor da coparticipação</th>
                            <th>Vincular</th>
                            <th>Desvincular</th>
                        </tr>
                    </thead>
                    <tbody>
                            <tr v-for="(pp, index) in filteredPlanosProcedimentos">
                                <td>@{{ pp.plano.id + ' - ' + pp.plano.nome_plano }}</td>
                                <td class="text-center">
                                    <i v-if="pp.vinculado" class="fa fa-circle" data-toggle="tooltip" title="SIM"></i>
                                    <i v-if="!pp.vinculado" class="fa fa-circle-o" data-toggle="tooltip" title="NÃO"></i>

                                <td>
                                    <input type="number" class="col-sm-12 line-input" :class="{ changed: hasChanged(pp, 'valor_credenciado') }" v-model="pp.valor_credenciado">
                                </td>
                                <td>
                                    <input type="number" class="col-sm-12 line-input" :class="{ changed: hasChanged(pp, 'valor_cliente') }" v-model="pp.valor_cliente">
                                </td>
                                <td>
                                    <select v-model="pp.beneficio_tipo" class="form-control select2">
                                        <option value="fixo" selected>FIXO</option>
                                        <option value="percentual">PERCENTUAL</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="number" class="col-sm-12 line-input" :class="{ changed: hasChanged(pp, 'beneficio_valor') }" v-model="pp.beneficio_valor">
                                </td>
                                {{--<td><input type="number" class="col-sm-12 line-input {{ $pp->vinculado? "empty" : "" }}" data-before="{{ !$pp->vinculado? $pp->valor_credenciado : 0 }}" name="valor_credenciado" value="{{ !$pp->vinculado? $pp->valor_credenciado : 0 }}"></td>--}}
                                {{--<td><input type="number" class="col-sm-12 line-input {{ $pp->vinculado? "empty" : "" }}" data-before="{{ !$pp->vinculado? $pp->valor_cliente : 0 }}" name="valor_cliente" value="{{ !$pp->vinculado? $pp->valor_cliente : 0 }}"></td>--}}
                                <td>
                                    <div class="btn btn-circle btn-success" @click="trySave(pp, index)" :class="{ saving: isSaving }">
                                        <span class="fa fa-save"></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn btn-circle btn-danger" @click="remove(pp, index)" :class="{ saving: isSaving }">
                                        <span class="fa fa-trash"></span>
                                    </div>
                                </td>
                            </tr>

                    </tbody>
                </table>

            </div>
        </div>
        <!-- END FORM-->
    </div>
@endsection

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            var $actions = $('.actions[data-target="#procedimentos"]');

            $actions.find('#save').click(function() {
                var target = $actions.attr('data-target');
                if(target != '') {
                    $(target).submit();
                }
            });
            $actions.find('#cancel').click(function() {
                var target = $actions.attr('data-target');
                location.href = "{!! route('procedimentos.index') !!}";
                return;
            });

            $('[data-before]').change(function() {
                if($(this).val() != $(this).data('before')) {
                    $(this).addClass('changed');
                } else {
                    $(this).removeClass('changed');
                }
            });

            $('.salvarVinculo').click(function () {
                var self   = $(this);
                var target = self.data('target');
                if(!target) {
                    return;
                }
                var $form = $(target);
                if($form.length < 1) {
                    return;
                }

                var data   = $form.serialize();
                var action = $form.attr('action');
                var method = $form.attr('method');

                var $input = $form.find('input[data-before]');

                var valid = false;
                for(var i = 0; i < $input.length; i++){
                    if($input[i].val() != $input[i].data('before')) {
                        valid = true;
                    }
                }
                if (!valid){
                    return;
                }

                $.ajax({
                    'url'     : action,
                    'type'    : method,
                    'data'    : data,
                    'success' : function(data) {
                        console.log("Procedimento vinculado com a tabela.\n"+data);
                        $input.data('before', data.valor);
                        $input.removeClass('changed');
                        $input.removeClass('empty');
                        $input.addClass('updated');
                    },
                    'error'   : function(data) {
                        console.log("Erro ao tentar vincular o procedimento com a tabela.\n"+data);
                        $input.removeClass('changed');
                        $input.addClass('error');
                    }
                });
            });
        });
    </script>
@endsection