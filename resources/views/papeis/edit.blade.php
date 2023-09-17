@extends('layouts.app')

@section('title')
    @parent
    Papéis de Usuário - Editar - {{ $papeis->name }}
@endsection
@section('content')
    <!-- BEGIN VALIDATION STATES-->
    <div class="portlet light portlet-fit portlet-form ">
        <div class="portlet-title">
            <div class="caption">
                <i class="icon-settings font-green-jungle"></i>
                <span class="caption-subject font-green-jungle sbold uppercase">
                  Papéis de Usuário
                </span>
            </div>
            @permission('edit_papeis')
            <div class="actions" data-target="#papeis">
                <div class="btn-group btn-group-devided" data-toggle="buttons">
                    <button type="submit" id="save" class="btn green-jungle">Salvar</button>
                    <button type="submit" id="cancel" class="btn red-sunglo">Cancelar</button>
                </div>
            </div>
            @endpermission
        </div>
        <div class="portlet-body">
            <!-- BEGIN FORM-->
            {!! Form::model($papeis, [
                                'route' => [
                                    'papeis.update',
                                    $papeis->id
                                ],
                                'method' => 'patch',
                                'class' => 'form-horizontal',
                                'id' => 'papeis'
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
                    @include('papeis.fields')

                </div>
            {!! Form::close() !!}
        </div>
        <!-- END FORM-->
    </div>
    <!-- END VALIDATION STATES-->

    <!-- BEGIN VALIDATION STATES-->
    <div class="portlet light portlet-fit portlet-form">
        <div class="portlet-title">
            <div class="caption">
                <i class="icon-settings font-green-jungle"></i>
                <span class="caption-subject font-green-jungle sbold uppercase">
                  Permissões
                </span>
            </div>
        </div>
        <div class="portlet-body">
            @php
                $permissoes = \App\Models\Permission::orderBy('menu')->get();
                $grupos = $permissoes->groupBy('menu');
            @endphp
            @foreach($grupos as $nome => $grupo)
                <table class="table" style="width: 90%; margin: 0 auto;">
                    <thead>
                        <th class="text-capitalize text-captalize">{{ $nome }}</th>
                        <th class="text-center">Habilitar</th>
                    </thead>
                    <tbody>
                    @foreach($grupo as $permissao)
                        <tr>
                            <td>{{ $permissao->display_name }}</td>
                            <td width="15%" class="text-center">
                                <input type="checkbox" name="habilitar" 
                                       data-permission="{{  $permissao->id }}" data-role="{{ $papeis->id }}"
                                       class="permission_role"
                                       {{ $permissao->roles()->where('role_id', $papeis->id)->exists() ? "checked=checked" : "" }}">
                            </td>    
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <br>
            @endforeach
        </div>
        <!-- END FORM-->
    </div>
    <!-- END VALIDATION STATES-->
@endsection

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            var $actions = $('.actions[data-target="#papeis"]');

            $actions.find('#save').click(function() {
                var target = $actions.attr('data-target');
                if(target != '') {
                    $(target).submit();
                }
            });
            $actions.find('#cancel').click(function() {
                var target = $actions.attr('data-target');
                location.href = "{!! route('papeis.index') !!}";
                return;
            });

            var permissionRoles = $('input.permission_role');
            permissionRoles.each(function(k, v){
                var $input = $(v);
                $input.change(function(e) {
                    var permission = $(this).data('permission');
                    var role = $(this).data('role');
                    var status = $(this).prop('checked');
                    var operation = 'attach';
                    if(!status) {
                        operation = 'detach';
                    }

                    $.ajax({
                        url: "{{ url('/') }}/papeis_permissoes/"+role+"/"+permission+"/"+operation,
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
                    
                    console.log('Permission: ' + permission + ' is attached'  + ' to Role: ' + role);
                });
            });
        });
    </script>
@endsection