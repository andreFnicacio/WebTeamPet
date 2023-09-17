@extends('layouts.app')

@section('css')
    @parent
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <link href="{{ url('/') }}/assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />
    <!-- END PAGE LEVEL PLUGINS -->
    <!-- BEGIN PAGE LEVEL STYLES -->
    <link href="{{ url('/') }}/assets/pages/css/profile.min.css" rel="stylesheet" type="text/css" />
    <!-- END PAGE LEVEL STYLES -->

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.2/croppie.min.css">

    <style>
        .swal2-container {
            z-index: 99999;
        }

        .mb-2 {
            margin-bottom: 10px;
        }

        @media only screen and (min-width: 993px) {
            .profile-sidebar {
                height: 1px;
                width: 245px;
            }

            .dropdown-menu {
                min-width: 114px;
            }
            .profile-sidebar-portlet {
                position: fixed;
                width: inherit;
            }

            .table-historico {
                width: 97%;
            }
        }

        .profile-userpic img {
            float: none;
            margin: 0 auto;
            width: 122.5px;
            height: 122.5px;
            -webkit-border-radius: 50%!important;
            -moz-border-radius: 50%!important;
            border-radius: 150%!important;
        }

    </style>
@endsection

@section('content')

    <div class="">

        <div class="col-xs-12">
            <div class="page-head">
                <div class="page-title"></div>
            </div>
        </div>

        <div class="col-md-12">
            <!-- BEGIN PROFILE SIDEBAR -->
            <div class="profile-sidebar">
                <!-- PORTLET MAIN -->
                <div class="portlet light profile-sidebar-portlet " >
                    <!-- SIDEBAR USERPIC -->
                    <div class="profile-userpic">
                        <img src="{{ $pets->avatar() }}" class="img-responsive" alt=""> </div>
                    <!-- END SIDEBAR USERPIC -->
                    <!-- SIDEBAR USER TITLE -->
                    <div class="profile-usertitle">
                        <div class="profile-usertitle-name"> {{ $pets->nome_pet }} </div>
                        <div class="profile-usertitle-job"> {{ $pets->raca->nome }} </div>
                    </div>
                    <!-- END SIDEBAR USER TITLE -->
                    <!-- SIDEBAR MENU -->
                    <div class="profile-usermenu">
                        <ul class="nav">
                            <li class="active">
                                <a href="#tab_1_1" data-toggle="tab"><i class="fa fa-paw"></i>Dados do pet e cobrança</a>
                            </li>
                            <li>
                                <a href="#tab_1_2" data-toggle="tab"><i class="fa fa-image"></i>Mudar Foto</a>
                            </li>
                            <li>
                                <a href="#tab_1_3" data-toggle="tab"><i class="fa fa-list-alt"></i>
                                    Plano
                                    @if($pets->petsPlanos()->orderBy('id', 'DESC')->first()) 
                                        <i class="fa fa-check pull-right" style="margin-top: 5px; color: green" title="Pet tem plano"></i>
                                    @else
                                        <i class="fa fa-times pull-right" style="margin-top: 5px; color: red" title="Pet não tem plano"></i>
                                    @endif
                                </a>
                                
                            </li>
                        </ul>
                    </div>
                    <!-- END MENU -->
                </div>
                <!-- END PORTLET MAIN -->
            </div>
            <!-- END BEGIN PROFILE SIDEBAR -->
            <!-- BEGIN PROFILE CONTENT -->
            <div class="profile-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet light ">
                            <div class="portlet-title tabbable-line">
                                <div class="caption caption-md">
                                    <i class="icon-globe theme-font hide"></i>
                                    <span class="caption-subject font-blue-madison bold uppercase">
                                        <h4>Perfil do pet</h4>
                                    </span>
                                </div>
                                
                                @permission('edit_pets')
                                <div class="actions" data-target="#pets">
                                    @if($pets->ativo && !$pets->cancelamentoAgendado())
                                        <a class="btn yellow-crusta" href="#modal-cancelamento" data-toggle="modal">
                                            <i class="fa fa-ban"></i> Cancelar o Contrato
                                        </a>
                                    @elseif($pets->cancelamentoAgendado())
                                    <div class="font-lg font-yellow-gold text-center padding-tb-20">
                                        <form action="{{ route('pets.revogarCancelamento', $pets->id) }}" method="post">
                                            {{ csrf_field() }}
                                            <input type="hidden" name="id_cancelamento" value="{{ $pets->cancelamentoAgendado()->id }}">
                                            <button type="submit" style="color: #fff" class="btn btn-default mx-auto yellow-gold margin-bottom-30">
                                                <i class="fa fa-refresh"></i> Revogar Cancelamento Agendado
                                            </button>
                                        </form>
                                    </div>
                                    @else
                                        <a class="btn blue" href="{{ route('pets.reativarPet', $pets->id) }}">
                                            <i class="fa fa-undo"></i> Reativar o Pet
                                        </a>
                                    @endif
                
                                </div>
                                <div id="modal-cancelamento" class="modal fade" tabindex="-1" data-replace="true" style="display: none">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                                <h4 class="modal-title">Cancelamento do Pet</h4>
                                            </div>

                                            <form action="{{ route('pets.cancelamento', $pets->id) }}" method="POST" class="form-horizontal" id="form-cancelamento" enctype="multipart/form-data">
                                                {{ csrf_field() }}
                                                <input type="hidden" name="id_pet" value="{{ $pets->id }}">
                                                <input type="hidden" name="id_usuario" value="{{ Auth::user()->id }}">
                                                <div class="modal-body">

                                                    <div class="form-body">

                                                        <div class="form-group">
                                                            <label class="control-label col-md-4">Data
                                                                <span class="required"> * </span>
                                                            </label>
                                                            <div class="col-md-6">
                                                                <div class="input-group input-medium date date-picker"  data-date-format="dd/mm/yyyy">
                                                                    <input value="{{ date('d/m/Y') }}" name="data_cancelamento" type="text" class="form-control" required>
                                                                    <span class="input-group-btn">
                                                                        <button class="btn default" type="button">
                                                                            <i class="fa fa-calendar"></i>
                                                                        </button>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="form-group">
                                                            <label class="control-label col-md-4">Motivo
                                                                <span class="required"> * </span>
                                                            </label>
                                                            <div class="col-md-6">
                                                                <select name="motivo" id="select_motivo" class="form-control select2-modal" data-parent="form-cancelamento" required>
                                                                    <option value=""></option>
                                                                    @foreach(\App\Models\Cancelamento::MOTIVOS as $key => $value)
                                                                        <option value="{{ $key }}">
                                                                            {{ $value }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="form-group">
                                                            <label class="control-label col-md-4">Justificativa
                                                                <span class="required"> * </span>
                                                            </label>
                                                            <div class="col-md-6">
                                                                <textarea name="justificativa" id="justificativa" rows="3" class="form-control" required></textarea>
                                                            </div>
                                                        </div>

                                                        <div class="form-group">
                                                            <label class="control-label col-md-4">Selecione o arquivo
                                                                <span class="required"> * </span>
                                                            </label>
                                                            <div class="col-md-6">
                                                                <input type="file" class="form-control" name="file" accept="image/x-png,.tiff,image/bmp,image/jpeg,application/pdf,.csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required>
                                                            </div>
                                                        </div>

                                                        <div class="form-group">
                                                            <label class="control-label col-md-4">Autor
                                                                <span class="required"> * </span>
                                                            </label>
                                                            <div class="col-md-6">
                                                                <input type="text" readonly class="form-control" value="{{ Auth::user()->name }}">
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
                                <div class="tab-content">
                                    <!-- PERSONAL INFO TAB -->
                                    <div class="tab-pane active" id="tab_1_1">
                                        @include('pets.perfil_parts.perfil_dados')
                                    </div>
                                    <!-- END PERSONAL INFO TAB -->
                                    <!-- CHANGE AVATAR TAB -->
                                    <div class="tab-pane" id="tab_1_2">
                                        @include('pets.perfil_parts.perfil_mudar_foto')
                                    </div>
                                    <!-- END CHANGE AVATAR TAB -->
                                    <!-- CHANGE PLANOS TRADICIONAIS TAB -->
                                    <div class="tab-pane" id="tab_1_3">
                                        @include('pets.perfil_parts.perfil_planos')
                                    </div>
                                    <!-- END PLANOS TRADICIONAIS TAB -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END PROFILE CONTENT -->
        </div>
    </div>
    <div class="clearfix"></div>
@endsection

@section('scripts')
    @parent
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <script src="{{ url('/') }}/assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
    <script src="{{ url('/') }}/assets/global/plugins/jquery.sparkline.min.js" type="text/javascript"></script>
    <script src="{{ url('/') }}/assets/global/plugins/jcrop/js/jquery.color.js" type="text/javascript"></script>
    <!-- END PAGE LEVEL PLUGINS -->
    <!-- BEGIN PAGE LEVEL SCRIPTS -->
    <script src="{{ url('/') }}/assets/pages/scripts/profile.min.js" type="text/javascript"></script>
    <!-- END PAGE LEVEL SCRIPTS -->

  

    <script src="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.2/croppie.js"></script>
    <script type="text/javascript">

        $.ajaxSetup({
            headers: {    
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        var resize = $('#upload-demo').croppie({
            enableExif: true,
            enableOrientation: true,    
            viewport: { 
                width: 200,
                height: 200,
                type: 'circle'
            },
            boundary: {
                width: 300,
                height: 300
            }
        });
        
        $('#image').on('change', function () { 
            var reader = new FileReader();
                reader.onload = function (e) {
                resize.croppie('bind',{
                    url: e.target.result
                }).then(function(){
                    console.log('jQuery bind complete');
                });
            }
            reader.readAsDataURL(this.files[0]);
        });
        
        $('.upload-image').on('click', function (ev) {
            resize.croppie('result', {
                type: 'canvas',
                size: 'viewport'
            }).then(function (img) {
                $.ajax({
                url: "{{route('pets.avatarCropUpload')}}",
                type: "POST",
                data: {
                    "id_pet": $('input[name="id_pet"]').val(),
                    "image":img
                },
                success: function (data) {
                    window.location.reload();
                }
                });
            });
        });
        
    </script>

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
            }).catch(() => {
                console.log('false');
                return false;
            });

            return false;
        });
    });
</script>

@endsection
