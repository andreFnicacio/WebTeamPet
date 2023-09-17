@extends('layouts.app')

@section('title')
    @parent
    Pusher
@endsection
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="content">
                <div class="clearfix"></div>

                @include('flash::message')

                <div class="clearfix"></div>
                <div class="box box-primary">
                    <div class="box-body">
                        <div class="row-fluid">
                            <div class="col-sm-12">
                                <br>
                                <div class="note note-info">
                                    <h4 class="block">Verificação de envio:</h4>
                                    <p> Estamos checando a lista enviada. Verificamos quais clientes informados estão cadastrados no sistema e possuem o aplicativo instalado para receber as notificações. </p>
                                    <p> Ao clicar em confirmar a mensagem será enviada para todos os clientes presentes na lista abaixo. </p>
                                </div>
                            </div>
                        </div>
                        <div class="portlet">
                            <div class="portlet-body">
                                <table class="table table-responsive" id="preview-push-table">
                                    <thead>
                                    <th>#</th>
                                    <th>Nome</th>
                                    <th class="text-center">Checado?</th>
                                    </thead>
                                    <tbody>
                                    @foreach($clientes as $i => $c)
                                        <tr>
                                            <td>{{ $i+1 }}</td>
                                            <td>{{ $c->nome_cliente }}</td>
                                            <td class="text-center font-green-dark"><span class="fa fa-check"></span></td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="2">Total:</td>
                                        <td  class="text-center">
                                            {{ count($clientes) }}
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="portlet light portlet-fit portlet-form ">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="icon-settings font-green-jungle"></i>
                                    <span class="caption-subject font-green-jungle sbold uppercase">
                                      Confirmar envio
                                    </span>
                                </div>
                                <div class="actions" data-target="#pusher">
                                    <div class="btn-group btn-group-devided" data-toggle="buttons">
                                        <button type="submit" id="save" class="btn green-jungle">Enviar</button>
                                        <button type="submit" id="cancel" class="btn red-sunglo">Cancelar</button>
                                    </div>
                                </div>
                            </div>
                            <div class="portlet-body">
                                <!-- BEGIN FORM-->
                                {!! Form::open(['route' => ['mobile.pusher.send'], 'method' => 'post', 'class' => 'form-horizontal', 'id' => 'pusher', 'enctype' => 'multipart/form-data']) !!}
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
                                            Título
                                            <span class="required"> * </span>
                                        </label>
                                        <div class="col-md-4">
                                            <input type="text" name="title" maxlength="50" data-required="1" class="form-control" required/>
                                            <small>Limite de 50 caracteres.</small>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3" for="cod_procedimento">
                                            Mensagem
                                            <span class="required"> * </span>
                                        </label>
                                        <div class="col-md-4">
                                            <input type="text" name="message" data-required="1" maxlength="150" class="form-control" required />
                                            <small>Limite de 150 caracteres.</small>
                                        </div>
                                    </div>
                                    {!! Form::close() !!}
                                </div>
                                <!-- END FORM-->
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
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