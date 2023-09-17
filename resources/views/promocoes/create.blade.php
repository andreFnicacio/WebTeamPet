@extends('layouts.app')

@section('title')
    @parent
    Conveniadas - Nova Conveniada
@endsection
@section('content')
    <!-- BEGIN VALIDATION STATES-->
    <div class="portlet light portlet-fit portlet-form ">
        <div class="portlet-title">
            <div class="caption">
                <i class="icon-settings font-green-jungle"></i>
                <span class="caption-subject font-green-jungle sbold uppercase">
                  Conveniadas
                </span>
            </div>
            @permission('create_clinicas')
            <div class="actions" data-target="#promocoes">
                <div class="btn-group btn-group-devided" data-toggle="buttons">
                    <button type="submit" id="save" class="btn green-jungle">Salvar</button>
                    <button type="submit" id="cancel" class="btn red-sunglo">Cancelar</button>
                </div>
            </div>
            @endpermission
        </div>
        <div class="portlet-body">
            <!-- BEGIN FORM-->
            {!! Form::open(['route' => ['promocoes.store'], 'method' => 'post', 'class' => 'form-horizontal', 'id' => 'promocoes']) !!}
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

                    <div class="form-group">
                        <label class="control-label col-md-3">Ativo?
                            <span class="required"> * </span>
                        </label>
                        <div class="col-md-4">
                            <input type="checkbox" checked name="ativo" class="make-switch" data-on-color="success" data-off-color="danger" data-on-text="Sim" data-off-text="Não" value="1">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3">Cumulativo?
                            <span class="required"> * </span>
                        </label>
                        <div class="col-md-4">
                            <input type="checkbox" name="cumulativo" class="make-switch" data-on-color="success" data-off-color="danger" data-on-text="Sim" data-off-text="Não" value="1">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3" for="nome">
                            Nome
                            <span class="required"> * </span>
                        </label>
                        <div class="col-md-4">
                            <input type="text" name="nome" data-required="1" class="form-control" required/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3" for="dt_inicio">
                            Data de Início
                            <span class="required"> * </span>
                        </label>
                        <div class="col-md-4">
                            <div required class="input-group input-medium date date-picker"  data-date-format="dd/mm/yyyy">
                                <input required type="text" value="" name="dt_inicio" class="form-control" readonly>
                                <span class="input-group-btn">
                             <button class="btn default" type="button">
                                <i class="fa fa-calendar"></i>
                             </button>
                        </span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3" for="dt_termino">
                            Data de Término
                        </label>
                        <div class="col-md-4">
                            <div class="input-group input-medium date date-picker"  data-date-format="dd/mm/yyyy">
                                <input type="text" value="" name="dt_termino" class="form-control" readonly>
                                <span class="input-group-btn">
                             <button class="btn default" type="button">
                                <i class="fa fa-calendar"></i>
                             </button>
                        </span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3" for="tipo_desconto">
                            Tipo de Desconto
                        </label>
                        <div class="col-md-4">
                            <select name="tipo_desconto" id="conveniada-tipo-desconto" data-required="1" class="form-control">
                                <option value="P">Percentual</option>
                                <option value="F">Fixo</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3" for="desconto">
                            Desconto
                        </label>
                        <div class="col-md-4">
                            <input type="text" value="0" maxlength="5" id="desconto" name="desconto" data-required="1" class="form-control money" required />
                        </div>
                    </div>

                </div>
            {!! Form::close() !!}
        </div>
        <!-- END FORM-->
    </div>
    <!-- END VALIDATION STATES-->
@endsection

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            var $actions = $('.actions[data-target="#promocoes"]');

            $actions.find('#save').click(function() {
                var target = $actions.attr('data-target');
                if(target != '') {
                    $(target).submit();
                }
            });
            $actions.find('#cancel').click(function() {
                var target = $actions.attr('data-target');
                location.href = "{!! route('promocoes.index') !!}";
                return;
            });

            $('#conveniada-tipo-desconto').on('change', function() {
                let tipo = $(this).val();

                if(tipo == 'P'){
                    $('#desconto').attr('maxlength','5').val('0');
                }

                if(tipo == 'F'){
                    $('#desconto').attr('maxlength','10').val('0');
                }
            })
        });
    </script>
@endsection