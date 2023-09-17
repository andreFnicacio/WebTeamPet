@extends('layouts.app')

@section('title')
    @parent
    Comunicados para Credenciados
@endsection

@section('css')
    @parent
    <link rel="stylesheet" href="https://unpkg.com/placeholder-loading/dist/css/placeholder-loading.min.css">
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <style>
        .page-content {
            padding-top: 20px !important;
            /* background: white !important; */
        }

        .ql-editor {
            background: white !important;
        }

        .ql-toolbar.ql-snow {
            background: white;
        }

        #editor {
            height: 250px;
        }
    </style>
@endsection

@section('content')
    <div class="portlet light portlet-fit portlet-form ">
        <div class="portlet-title">
            <div class="caption">
                <i class="icon-settings font-green-jungle"></i>
                <span class="caption-subject font-green-jungle sbold uppercase">
                  Comunicados para Credenciados
                </span>
            </div>
            @permission('create_planos')
            <div class="actions" data-target="#comunicado">
                <div class="btn-group btn-group-devided" data-toggle="buttons">
                    <button type="submit" id="save" class="btn green-jungle">Salvar</button>
                    <button type="submit" id="cancel" class="btn red-sunglo">Cancelar</button>
                </div>
            </div>
            @endpermission
        </div>
        <div class="portlet-body">
            <!-- BEGIN FORM-->
            <div class="form-body">

                <div class="col-md-12" style="margin-bottom: 20px;">
                    <h3 class="block" style="margin-top: 0px;">Dados Gerais</h3>
                </div>

                <form action="{{ route('comunicados_credenciados.salvar') }}" id="comunicado" class="form" method="POST">
                    {{ csrf_field() }}
                    <div class="ata-fields">
                        <div class="row">
                            <div class="form-group">
                                <div class="col-sm-5">
                                    <h5>Título: </h5>
                                    <input type="text" name="titulo" required class="ata-title form-control">

                                </div>
                            </div>
                        </div>
                        <br>

                        <input type="hidden" name="corpo" id="corpo" required>
                        {{--<input type="hidden" name="corpo_html" id="corpo_html" required>--}}


                        <div id="editor">

                        </div>

                        <br>
                        {{-- <div class="row">
                            <div class="form-group col-sm-3 " required="">
                                <h5>Data de Publicação:</h5>
                                <div class="input-group date form_datetime form_datetime bs-datetime">
                                    <input type="text" size="16" class="form-control" name="published_at" value="{{ \Carbon\Carbon::now()->format('d F Y - H:i') }}">
                                    <span class="input-group-addon">
                                        <button class="btn default date-set" type="button">
                                            <i class="fa fa-calendar"></i>
                                        </button>
                                    </span>
                                </div>
                                {{-- <input type="datetime" required class="ata-title form-control" value="{{ \Carbon\Carbon::now()->format('Y-m-d\TH:i:sP') }}" name="published_at"> --}}
                                {{-- <br>
                            </div>

                        </div> --}}

                    </div>
                </form>

            </div>
            {!! Form::close() !!}
        </div>
        <!-- END FORM-->
    </div>
@endsection

@section('scripts')
    @parent
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script src="{!! asset('js/lodash.min.js') !!}"></script>
    <script src="{!! asset('js/clipboard.min.js') !!}"></script>
    <script>
        var quill = new Quill('#editor', {
            theme: 'snow'
        });

        $('#comunicado').submit(function(e) {
            $('#corpo').val(JSON.stringify(quill.root.innerHTML));
            // $('#corpo_html').val(quill.root.innerHTML);
        });
    </script>
    <script>
        $(document).ready(function() {
            var $actions = $('.actions[data-target="#comunicado"]');

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