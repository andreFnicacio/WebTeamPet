@extends('layouts.app')

@section('title')
    @parent
    Usuários - Editar - {{ $user->name }}
@endsection
@section('content')
    <!-- BEGIN VALIDATION STATES-->
    <div class="portlet light portlet-fit portlet-form ">
        <div class="portlet-title">
            <div class="caption">
                <i class="icon-settings font-green-jungle"></i>
                <span class="caption-subject font-green-jungle sbold uppercase">
                  Usuários
                </span>
            </div>
        </div>
        <div class="portlet-body">
            <!-- BEGIN FORM-->
            {!! Form::model($user, [
                                'route' => [
                                    'usuarios.updatesenha',
                                    $user->id
                                ],
                                'method' => 'post',
                                'class' => 'form-horizontal',
                                'id' => 'usuarios'
                            ]);
            !!}
            <div class="form-body">

                <div class="col-md-12" style="margin-bottom: 20px;">
                    <h3 class="block" style="margin-top: 0px;">Dados Gerais</h3>
                </div>

                <div class="form-group">
                    <label class="control-label col-md-3" for="password">
                        Senha
                        <span class="required"> * </span>
                    </label>
                    <div class="col-md-4">
                        <input type="password" name="password" data-required="1" class="form-control" required/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3" for="password">
                        Confirmação de senha
                        <span class="required"> * </span>
                    </label>
                    <div class="col-md-4">
                        <input type="password" name="password_confirmation" data-required="1" class="form-control" required/>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-4 col-md-offset-3">
                        <button type="submit" id="save" class="btn green-jungle">Salvar</button>
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
            var form = $('form#usuarios');
            form.on('submit', function(e) {
                if (form.find('input[name="password"]').val() != form.find('input[name="password_confirmation"]').val()) {
                    e.preventDefault();
                    swal('Atenção!', 'As senhas devem ser iguais!', 'warning');
                }
                if (form.find('input[name="password"]').val().length < 6) {
                    e.preventDefault();
                    swal('Atenção!', 'A senha deve conter pelo menos 6 caracteres!', 'warning');
                }
            });
        });
    </script>
@endsection
