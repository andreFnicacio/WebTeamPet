@extends('layouts.app')

@section('content')
    <!-- BEGIN VALIDATION STATES-->
    <div class="portlet light portlet-fit portlet-form ">
        <div class="portlet-title">
            <div class="caption">
                <i class="icon-settings font-green-jungle"></i>
                <span class="caption-subject font-green-jungle sbold uppercase">
                  Envio de push
                </span>
            </div>
            <div class="actions" data-target="#pusher">
                <div class="btn-group btn-group-devided" data-toggle="buttons">
                    <button type="submit" id="save" class="btn green-jungle">Prosseguir</button>
                    <button type="submit" id="cancel" class="btn red-sunglo">Cancelar</button>
                </div>
            </div>
        </div>
        <div class="portlet-body">
            <!-- BEGIN FORM-->
            {!! Form::open(['route' => ['mobile.pusher.check'], 'method' => 'post', 'class' => 'form-horizontal', 'id' => 'pusher', 'enctype' => 'multipart/form-data']) !!}
            <div class="form-body">

                <div class="alert alert-danger display-hide">
                    <button class="close" data-close="alert"></button> Verifique se você preencheu todos os campos.
                </div>
                <div class="alert alert-success display-hide">
                    <button class="close" data-close="alert"></button> Validado com sucesso.
                </div>
                <div class="col-md-12" style="margin-bottom: 20px;">

                </div>
                <div class="form-group">
                    <label class="control-label col-md-3" for="cod_procedimento">
                        Lista de clientes
                        <span class="required"> * </span>
                    </label>
                    <div class="col-md-4">
                        <input type="file" name="file" data-required="1" class="form-control" required accept=".csv"/>
                        <small>Selecione uma lista em formato .CSV contendo a lista de nomes dos clientes.</small>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3" for="cod_procedimento">
                        Coluna índice
                        <span class="required"> * </span>
                    </label>
                    <div class="col-md-4">
                        <input type="text" placeholder="Cliente" name="index" data-required="1" class="form-control" required />
                        <small>Informe o título da coluna que identifica o nome do cliente.</small>

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
            let actions = $('.actions[data-target="#pusher"]');

            actions.find('#save').click(function () {
                let target = actions.attr('data-target');
                if (target !== '') {
                    $(target).submit();
                }
            });
            actions.find('#cancel').click(function () {
                location.href = "{!! route('mobile.pusher.index') !!}";
            });
        });
    </script>
@endsection