@extends('layouts.app')

@section('title')
    @parent
    Pets - Editar - {{ $pets->nome_pet }}
@endsection

@section('css')
    @parent
    <style>
        .select2-container--bootstrap {
            width: auto !important;
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
                  Pets
                </span>
            </div>
            <div class="actions" data-target="#pets">
                <div class="btn-group btn-group-devided" data-toggle="buttons">
                    <button type="submit" id="save" class="btn green-jungle">Salvar</button>
                    <button type="submit" id="cancel" class="btn red-sunglo">Cancelar</button>
                </div>
            </div>
        </div>
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

                    <div class="alert alert-danger display-hide">
                        <button class="close" data-close="alert"></button> Verifique se você preencheu todos os campos.
                    </div>
                    <div class="alert alert-success display-hide">
                        <button class="close" data-close="alert"></button> Validado com sucesso.
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3">Tutor
                            <span class="required"> * </span>
                        </label>
                        <div class="col-md-4">
                            <input type="text" value="{{ $pets->cliente->nome_cliente }}" required="required" disabled="disabled" data-required="1" class="form-control" />
                            <small>
                                Verifique corretamente o número digitado.
                            </small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3">Pet
                            <span class="required"> * </span>
                        </label>
                        <div class="col-md-4">
                            <input type="text" value="{{ $pets->nome_pet }}" required="required" disabled="disabled" data-required="1" class="form-control" />
                            <small>
                                Verifique corretamente o número digitado.
                            </small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3">Microchip
                            <span class="required"> * </span>
                        </label>
                        <div class="col-md-4">
                            <input type="text" value="{{ $pets->numero_microchip }}" name="numero_microchip" required="required" data-required="1" class="form-control" />
                            <small>
                                Verifique corretamente o número digitado.
                            </small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3">Doenças Pré-existentes
                            <span class="required"> * </span>
                        </label>
                        <div class="col-md-4">
                            <input type="checkbox" {{ $pets->contem_doenca_pre_existente ? "checked" : "" }} name="contem_doenca_pre_existente" class="make-switch" data-on-color="success" data-off-color="danger" data-on-text="Sim" data-off-text="Não">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Cite as doenças
                            <span class="required"> </span>
                        </label>
                        <div class="col-md-4">
                            <textarea name="doencas_pre_existentes" type="text" class="form-control">{{ $pets->doencas_pre_existentes }}</textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Observações
                            <span class="required"> * </span>
                        </label>
                        <div class="col-md-4">
                            <textarea name="observacoes" type="text" class="form-control" >{{ $pets->observacoes }}</textarea>
                        </div>
                    </div>

                    {{--@include('pets.fields')--}}

                </div>
            {!! Form::close() !!}
        </div>

        <!-- END FORM-->

@endsection

@section('scripts')
    @parent

    <script>
        $(document).ready(function() {
            var $actions = $('.actions[data-target]');




            $.each($actions, function(k, v) {
                var $v = $(v);
                $v.find('#save').click(function() {
                    var target = $v.attr('data-target');
                    if(target != '') {
                        var campos = $(target).find('select[required], input[required], textarea[required]');
                        var valid = true;
                        for (var i = 0; i < campos.length; i++){
                            valid &= campos[i].checkValidity();
                        }

                        if(valid) {
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
                $v.find('#cancel').click(function() {
                    var target = $v.attr('data-target');
                    location.href = "{!! route('pets.index') !!}";
                    return;
                });
            });

            $('#single').change(function () {
               var previous = $(this).data('previous');
               if($(this).val() !== previous){
                   [
                       'input[name="data_inicio_contrato"]',
                       'input[name="data_encerramento_contrato"]'
                   ].forEach(function(e) {
                      var input = $(e);
                      input.val('');
                      input.removeAttr('readonly');
                   });
               }

            });

            function imageCircle(option, size) {
                if(!size) {
                    size = 15;
                }
                if(!option.id) {
                    return option.text;
                }
                console.log(option);
                return "<img src='" + $(option.element).data('image') + "' class='img-circle' width='" + size + "'>" + option.text;
            };

            $("#vendedor").select2({
                placeholder: "Selecione um vendedor",
                templateResult: function(option){ return imageCircle(option, 50) },
                templateSelection: function(option){ return imageCircle(option, 20) },
                escapeMarkup: function (m) {
                    return m;
                }
            });

            $('.select2-modal').each(function(k,v) {
                $(v).select2({
                    tags: true,
                    dropdownParent: $("#" + $(v).data('parent'))
                });
            });

            $('#select_excecao_grupo').change(function(e) {
                var liberacaoAutomatica = $(this).find('option:selected').data('liberacao-automatica');
                var diasCarencia = $(this).find('option:selected').data('carencia');
                var quantidadeUsos = $(this).find('option:selected').data('quantidade-usos');

                $('#excecao_liberacao_automatica').bootstrapSwitch('state', liberacaoAutomatica);
                $('#excecao_dias_carencia').val(diasCarencia);
                $('#excecao_quantidade_usos').val(quantidadeUsos);
            });
        });
    </script>
@endsection
