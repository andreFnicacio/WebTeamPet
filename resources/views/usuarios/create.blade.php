@extends('layouts.app')


@section('title')
    @parent
    Usuários - Novo usuário
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
            @permission('create_usuarios')
            <div class="actions" data-target="#usuarios">
                <div class="btn-group btn-group-devided" data-toggle="buttons">
                    <button type="submit" id="save" class="btn green-jungle">Salvar</button>
                    <button type="submit" id="cancel" class="btn red-sunglo">Cancelar</button>
                </div>
            </div>
            @endpermission
        </div>
        <div class="portlet-body">
            <!-- BEGIN FORM-->
            {!! Form::open(['route' => ['usuarios.store'], 'method' => 'post', 'class' => 'form-horizontal', 'id' => 'usuarios']) !!}
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
                @include('usuarios.fields')

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
                location.href = "{!! route('planos.index') !!}";
                return;
            });
        });
    </script>
@endsection