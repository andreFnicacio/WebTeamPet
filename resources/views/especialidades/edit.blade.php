@extends('layouts.app')

@section('title')
    @parent
    Especialidades - Editar - {{ $especialidades->nome }}
@endsection
@section('content')
    <!-- BEGIN VALIDATION STATES-->
    <div class="portlet light portlet-fit portlet-form ">
        <div class="portlet-title">
            <div class="caption">
                <i class="icon-settings font-green-jungle"></i>
                <span class="caption-subject font-green-jungle sbold uppercase">
                  Especialidades
                </span>
            </div>
            @permission('edit_especialidades')
            <div class="actions" data-target="#especialidades">
                <div class="btn-group btn-group-devided" data-toggle="buttons">
                    <button type="submit" class="btn green-jungle">Salvar</button>
                    <button type="submit" class="btn red-sunglo">Cancelar</button>
                </div>
            </div>
            @endpermission
        </div>
        <div class="portlet-body">
            <!-- BEGIN FORM-->
            {!! Form::model($especialidades, [
                            'route' => ['especialidades.update', $especialidades->id],
                            'method' => 'patch',
                            'class' => 'form-horizontal',
                            'id' => 'especialidades'
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
                    @include('especialidades.fields')

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
            var $actions = $('.actions[data-target]');


            $.each($actions, function(k, v) {
                var $v = $(v);
                $v.find('#save').click(function() {
                    var target = $v.attr('data-target');
                    if(target != '') {
                        $(target).submit();
                    }
                });
                $v.find('#cancel').click(function() {
                    var target = $v.attr('data-target');
                    location.href = "{!! route('especialidades.index') !!}";
                    return;
                });
            });

        });
    </script>
@endsection