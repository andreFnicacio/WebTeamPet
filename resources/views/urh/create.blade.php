@extends('layouts.app')

@section('content')
    <!-- BEGIN VALIDATION STATES-->
    <div class="portlet light portlet-fit portlet-form ">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-dollar font-green-jungle"></i>
                <span class="caption-subject font-green-jungle sbold uppercase">
                  Novo URH
                </span>
            </div>
            <div class="actions" data-target="#urh">
                <div class="btn-group btn-group-devided" data-toggle="buttons">
                    <button type="submit" id="save" class="btn green-jungle">Salvar</button>
                    {{--<button type="submit" id="cancel" class="btn red-sunglo">Cancelar</button>--}}
                </div>
            </div>
        </div>
        <div class="portlet-body">
            <!-- BEGIN FORM-->
            {!! Form::open(['route' => ['urh.store'], 'method' => 'post', 'class' => 'form-horizontal', 'id' => 'urh']) !!}
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
                    @include('urh.fields')

                </div>
            {!! Form::close() !!}
        </div>
        <!-- END FORM-->
    </div>
    <!-- END VALIDATION STATES-->

    <div class="portlet light portlet-fit portlet-form ">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-line-chart font-green-jungle"></i>
                <span class="caption-subject font-green-jungle sbold uppercase">
                  Lista de URHs
                </span>
            </div>
        </div>
        <div class="portlet-body">
            @include('urh.table')
        </div>
        <!-- END FORM-->
    </div>
@endsection

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            var $actions = $('.actions[data-target="#urh"]');

            $actions.find('#save').click(function() {
                var target = $actions.attr('data-target');
                if(target != '') {
                    $(target).submit();
                }
            });
            $actions.find('#cancel').click(function() {
                var target = $actions.attr('data-target');
                location.href = "{!! route('urh.index') !!}";
                return;
            });

        });
    </script>
@endsection