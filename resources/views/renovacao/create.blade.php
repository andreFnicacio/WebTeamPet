@extends('layouts.app')

@section('content')
    <!-- BEGIN VALIDATION STATES-->
    <div class="portlet light portlet-fit portlet-form ">
        <div class="portlet-title">
            <div class="caption">
                <i class="icon-settings font-green-jungle"></i>
                <span class="caption-subject font-green-jungle sbold uppercase">
                  Renovacao
                </span>
            </div>
            <div class="actions" data-target="#renovacao">
                <div class="btn-group btn-group-devided" data-toggle="buttons">
                    <button type="submit" id="save" class="btn green-jungle">Salvar</button>
                    <button type="submit" id="cancel" class="btn red-sunglo">Cancelar</button>
                </div>
            </div>
        </div>
        <div class="portlet-body">
            <!-- BEGIN FORM-->
            {!! Form::open(['route' => ['renovacao.store'], 'method' => 'post', 'class' => 'form-horizontal', 'id' => 'renovacao']) !!}
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
                    @include('renovacao.fields', ['clientes' => $clientes])

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
            var $actions = $('.actions[data-target="#renovacao"]');

            $actions.find('#save').click(function() {
                var target = $actions.attr('data-target');
                if(target != '') {
                    $(target).submit();
                }
            });
            $actions.find('#cancel').click(function() {
                var target = $actions.attr('data-target');
                location.href = "{!! route('renovacao.index') !!}";
                return;
            });

            $('#id_cliente').change(function(e) {
                var id_cliente = $(this).val();
                if(id_cliente) {
                    var $idPet = $('#id_pet');
                    $idPet.attr('disabled', 'disabled');
                    let route = '{{ url('/clientes/') }}' + '/' + id_cliente + '/pets';
                    $.ajax(route, {
                        'method' : 'GET',
                        'data' : {
                            '_token' : '{{ csrf_token() }}',
                        },
                        'success' : function(data) {
                            var $idPet = $('#id_pet');
                            $idPet.html('');
                            $idPet.select2('destroy');
                            var html = '';
                            for(let i = 0; i < data.length; i++) {
                                html += `<option value=${data[i].id} data-plano="${data[i].plano}">${data[i].id} - ${data[i].nome_pet}</option>`;
                            }
                            $idPet.html(html);
                            $idPet.removeAttr('disabled');
                            $idPet.select2();
                        },
                        error : function() {
                            swal('error', 'Erro ao buscar pet.');
                        }
                    });
                }
            });
            $('#id_pet').change(function(e) {
                $('#id_plano').removeAttr('disabled');
                $('#id_plano').val($(this).find('option:selected').data('plano'));
                $('#id_plano').select2('destroy');
                $('#id_plano').select2();
                $('#id_plano').attr('disabled');
            })
        });


    </script>
@endsection