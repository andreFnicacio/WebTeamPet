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
            @permission('edit_usuarios')
            <div class="actions" data-target="#planos">
                <div class="btn-group btn-group-devided" data-toggle="buttons">
                    <button type="submit" id="save" class="btn green-jungle">Salvar</button>
                    <button type="submit" id="cancel" class="btn red-sunglo">Cancelar</button>
                </div>
            </div>
            @endpermission
        </div>
        <div class="portlet-body">
            <!-- BEGIN FORM-->
            {!! Form::model($user, [
                                'route' => [
                                    'usuarios.update',
                                    $user->id
                                ],
                                'method' => 'patch',
                                'class' => 'form-horizontal',
                                'id' => 'usuarios'
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
                @include('usuarios.fields', [
                    'edit' => true,
                    'user' => $user
                ])

            </div>
            {!! Form::close() !!}
        </div>
        <!-- END FORM-->
    </div>
    <!-- END VALIDATION STATES-->

    <!-- BEGIN VALIDATION STATES-->
    <div class="portlet light portlet-fit portlet-form ">
        <div class="portlet-title">
            <div class="caption">
                <i class="icon-settings font-green-jungle"></i>
                <span class="caption-subject font-green-jungle sbold uppercase">
                  Papéis Habilitados
                </span>
            </div>
        </div>
        <div class="portlet-body">
            <!-- BEGIN FORM-->
            {!! Form::model($user, [
                                'route' => [
                                    'usuarios.update',
                                    $user->id
                                ],
                                'method' => 'patch',
                                'class' => 'form-horizontal',
                                'id' => 'usuarios'
                            ]);
            !!}
            <div class="form-body">

                <div class="alert alert-danger display-hide">
                    <button class="close" data-close="alert"></button> Verifique se você preencheu todos os campos.
                </div>
                <div class="alert alert-success display-hide">
                    <button class="close" data-close="alert"></button> Validado com sucesso.
                </div>

                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Papel</th>
                            <th class="text-center">Habilitado?</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(\App\Models\Role::all() as $role)
                            <tr>
                                <td>{{ $role->display_name }}</td>
                                <td width="15%" class="text-center">
                                    <input type="checkbox" name="habilitar"
                                           data-role="{{  $role->id }}" data-user="{{ $user->id }}"
                                           class="user_role"
                                           {{ $user->roles()->where('role_id', $role->id)->exists() ? "checked=checked" : "" }} />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
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
            var $actions = $('.actions[data-target="#usuarios"]');

            $actions.find('#save').click(function() {
                var target = $actions.attr('data-target');
                if(target != '') {
                    $(target).submit();
                }
            });
            $actions.find('#cancel').click(function() {
                var target = $actions.attr('data-target');
                location.href = "{!! route('usuarios.index') !!}";
                return;
            });


            var userRoles = $('input.user_role');
            userRoles.each(function(k, v){
                var $input = $(v);
                $input.change(function(e) {
                    var user = $(this).data('user');
                    var role = $(this).data('role');
                    var status = $(this).prop('checked');
                    var operation = 'attach';
                    if(!status) {
                        operation = 'detach';
                    }

                    $.ajax({
                        url: "{{ url('/') }}/usuarios_papeis/"+user+"/"+role+"/"+operation,
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            '_token': $('meta[name="csrf-token"]').attr('content')
                        }
                    })
                    .done(function(data) {
                        console.log("success");
                    })
                    .fail(function(data) {
                        console.log("error");
                    });
                });
            });
        });
    </script>
@endsection