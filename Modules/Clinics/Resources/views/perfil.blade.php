@extends('layouts.app')

@section('title')
    Clínicas
@endsection

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
                <div class="portlet light profile-sidebar-portlet ">
                    <!-- SIDEBAR USERPIC -->
                    <div class="profile-userpic">
                        <img src="{{ $clinica->avatar() }}" class="img-responsive" alt=""> </div>
                    <!-- END SIDEBAR USERPIC -->
                    <!-- SIDEBAR USER TITLE -->
                    <div class="profile-usertitle">
                        <div class="profile-usertitle-name"> {{ $clinica->nome_clinica }} </div>
                        <div class="profile-usertitle-job">{{ $clinica->cidade }}</div>
                    </div>
                    <!-- END SIDEBAR USER TITLE -->
                    <!-- SIDEBAR MENU -->
                    <div class="profile-usermenu">
                        <ul class="nav">
                            <li class="active">
                                <a href="#tab_1_1" data-toggle="tab"><i class="fa fa-user"></i>Dados</a>
                            </li>
                            <li>
                                <a href="#tab_1_2" data-toggle="tab"><i class="fa fa-image"></i>Mudar Foto</a>
                            </li>
                            <li>
                                <a href="#tab_1_3" data-toggle="tab"><i class="fa fa-user-md"></i>Prestadores</a>
                            </li>
                            <li>
                                <a href="#tab_1_4" data-toggle="tab"><i class="fa fa-desktop"></i>Acesso ao Sistema</a>
                            </li>
                            <li>
                                <a href="#tab_1_5" data-toggle="tab"><i class="fa fa-book"></i>Planos</a>
                            </li>
                            <li>
                                <a href="#tab_1_6" data-toggle="tab"><i class="fa fa-tags"></i>Categorias</a>
                            </li>
                            <li>
                                <a href="#tab_1_7" data-toggle="tab"><i class="fa fa-tags"></i>Restrições</a>
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
                                        <h4>Perfil do credenciado</h4>
                                    </span>
                                </div>
                                <div class="actions">
                                    <a href="{{ route('clinicas.index') }}">
                                        <button class="btn green"><i class="fa fa-chevron-left"></i> Voltar à lista</button>
                                    </a>
                                </div>
                            </div>
                            <div class="portlet-body">
                                <div class="tab-content">
                                    <!-- PERSONAL INFO TAB -->
                                    <div class="tab-pane active" id="tab_1_1">
                                        @include('clinics::perfil_parts.perfil_dados')
                                    </div>
                                    <!-- END PERSONAL INFO TAB -->
                                    <!-- CHANGE AVATAR TAB -->
                                    <div class="tab-pane" id="tab_1_2">
                                        @include('clinics::perfil_parts.perfil_mudar_foto')
                                    </div>
                                    <!-- END CHANGE AVATAR TAB -->
                                    <!-- PRESTADORES TAB -->
                                    <div class="tab-pane" id="tab_1_3">
                                        @include('clinics::perfil_parts.perfil_prestadores')
                                    </div>
                                    <!-- END PRESTADORES TAB -->
                                    <!-- ACESSO TAB -->
                                    <div class="tab-pane" id="tab_1_4">
                                        @include('clinics::perfil_parts.perfil_acesso')
                                    </div>
                                    <!-- END ACESSO TAB -->
                                    <!-- PLANOS TAB -->
                                    <div class="tab-pane" id="tab_1_5">
                                        @include('clinics::perfil_parts.perfil_planos')
                                    </div>
                                    <!-- END PLANOS TAB -->
                                    <!-- CATEGORIAS TAB -->
                                    <div class="tab-pane" id="tab_1_6">
                                        @include('clinics::perfil_parts.perfil_categorias')
                                    </div>
                                    <!-- END CATEGORIAS TAB -->
                                    <!-- CATEGORIAS TAB -->
                                    <div class="tab-pane" id="tab_1_7">
                                        @include('clinics::perfil_parts.perfil_restricoes', ['clinica' => $clinica])
                                    </div>
                                    <!-- END CATEGORIAS TAB -->
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

    <script>
        $(document).ready(function() {
            $('#salvar_upload').click(function(e) {
                e.preventDefault();
                var $self = $(this);
                $self.addClass('disabled');
                var $target = $($self.data('target'));
                $target.submit();
            });

            $('.btn-deleteUpload').click(function (e) {
                e.preventDefault();
                var modal = $(this).closest('.modal');
                var form = $(this).closest('form');
                var formData = {};
                var closestTr = form.closest('tr');

                $(form.serializeArray()).each(function(i, field){
                    formData[field.name] = field.value;
                });

                var id_upload = formData.id_upload;
                var senha = formData.senha;
                var justificativa = formData.justificativa;

                if (senha === false || justificativa === false) {
                    return false;
                } else {
                    if (senha === "" || justificativa === "") {
                        if (senha === "") {
                            swal("A senha é obrigatória!", '', 'error');
                            return false
                        }
                        if (justificativa === "") {
                            swal("A justificativa é obrigatória!", '', 'error');
                            return false
                        }
                    } else {

                        $.post(form.attr('action'), {
                            _token: '{{ csrf_token() }}',
                            id_upload: id_upload,
                            senha: senha,
                            justificativa: justificativa,
                        }, function (data) {
                            swal({
                                title: data.msg.title,
                                text: data.msg.text,
                                type: data.msg.type
                            });
                            if (data.msg.type !== 'error') {
                                modal.modal('hide');
                                closestTr.hide();
                            }
                        });

                    }
                }
                return false;
            });

        });
    </script>

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
                url: "{{route('clinicas.avatarCropUpload')}}",
                type: "POST",
                data: {
                    "id_clinica": $('input[name="id_clinica"]').val(),
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
            var $clinica = $('.clinica-limite-mensal');
            $clinica.dblclick(function(e) {
                var _self = $(this);
                if(_self.attr('readonly')) {
                    _self.removeAttr('readonly');
                }
            });

            $clinica.blur(function(e) {
                var _self = $(this);
                if(_self.attr('readonly')) {
                    return;
                }

                var $before = $(this).data('before');
                var $grupo = $(this).data('grupo');
                var $clinica = $(this).data('clinica');
                var $limite = $(this).val();

                if($before == $limite) {
                    _self.attr('readonly', 'readonly');
                    return;
                }

                _self.attr('readonly', 'readonly');
                //Update database
                var urlAtualizarLimite = '{{ route('clinicas.atualizarLimite') }}';

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });


                $.ajax({
                    url: urlAtualizarLimite,
                    type: 'POST',
                    data: {
                        id_clinica: $clinica,
                        id_grupo: $grupo,
                        limite: $limite
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                })
                .done(function(data) {
                    _self.data('before', data.limite);

                    M.toast({
                        html: "Limite atualizado."
                    });
                })
                .fail(function(data) {
                    M.toast({
                        html: "Ouve um erro ao tentar atualizar o limite."
                    });
                    console.log(data);
                });
            });
        });
    </script>

     <script type="text/javascript">
         $(function() {
             $(".select2-tag").select2({
                 tags: true,
                 tokenSeparators: [',']
             })
         });
     </script>
@endsection
