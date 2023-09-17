@extends('layouts.app')
@section('css')
    @parent
    <style>
        button.disabled {
            pointer-events: none;
        }
    </style>
@endsection
@section('title')
    @parent
    Documentos Internos
@endsection
@section('content')
    @permission('ver_documentos_internos')
    <br>
    <br>
    <div class="portlet  light  portlet-form " id="arquivos">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-folder-open-o font-red-sunglo"></i>
                <span class="caption-subject font-red-sunglo sbold uppercase">Documentos Internos</span>
            </div>
            @permission('criar_documentos_internos')
            <div class="actions">
                <div class="row">
                    <div class="col-md-offset-0 col-md-12">

                        <a class="btn  green-jungle btn-outline sbold tooltips" data-placement="top" data-original-title="Carregar" href="#novo_upload" data-toggle="modal"><i class="fa fa-upload red-sunglo"></i></a>

                    </div>
                </div>
            </div>
            @endpermission
        </div>
        <div class="portlet-body">
            <div class="table-responsive">
                <table class="table col-md-12 historico-financeiro">
                    <thead>
                    <tr>
                        <th> </th>
                        <th > Criação </th>
                        <th > Descrição </th>
                        <th > Tamanho </th>
                        <th > Autor </th>
                        <th >  </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach(\App\Models\Uploads::where('bind_with', 'documentos')->get() as $file)
                        <tr>
                            <td></td>
                            <td>{{ $file->created_at->format('d/m/Y H:i:s') }}</td>
                            <td>{{ nl2br($file->description) }}</td>
                            <td>{{ number_format($file->size/1024, 2, ",", ".") }}KB</td>
                            <td>{{ $file->user()->first()->name }}</td>
                            <td ><a class="btn btn-xs blue font-white tooltips" data-placement="top" data-original-title="Baixar" href="{{ url('/') }}/{{ $file->path }}" type="download" download="{{ $file->original_name }}" ><span  class="fa fa-download tooltips"></span></a></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div id="novo_upload" class="modal fade" tabindex="-1" data-replace="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Novo upload</h4>
                </div>
                <div class="modal-body">
                    <form id="form_upload" class="form" action="{{ route('documentos_internos.upload') }}" method="POST" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="form-body">
                            <div class="form-group col-sm-12">
                                <label class="control-label col-md-5">Selecione o arquivo
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-12">
                                    <input type="file" class="form-control" name="file" accept="image/x-png,.tiff,image/bmp,image/jpeg,application/pdf,.csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel,application/msword,.doc, .docx,.ppt, .pptx,.txt,.pdf" required>
                                </div>
                            </div>
                            <div class="form-group col-sm-12">
                                <label class="control-label col-md-3">Descrição

                                </label>
                                <div class="col-md-12">
                                    <input type="text" name="description" class="form-control" value="" placeholder="Descrição do arquivo">
                                </div>
                            </div>
                            <div class="form-group col-sm-12">
                                <label class="control-label col-md-3">Visibilidade
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-12">
                                    {{ Form::hidden('publico',0) }}
                                    <input type="checkbox" name="publico" class="make-switch" data-on-color="success" data-off-color="danger" data-on-text="Público" data-off-text="Privado" value="1">
                                </div>
                            </div>
                            <div class="form-group col-sm-12">
                                <label class="control-label col-md-3">Plano
                                </label>
                                <div class="col-md-12">
                                    <select name="id_plano" id="" class="select2">
                                        <option></option>
                                        @foreach(\App\Models\Planos::all() as $plano))
                                            <option value="{{ $plano->id }}">{{ $plano->id . ' - ' . $plano->nome_plano }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-sm-12">
                                <label class="control-label col-md-3">Autor
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-12">
                                    <input type="text" readonly class="form-control" value="{{ Auth::user()->name }}">
                                </div>
                            </div>
                        </div>



                    </form>

                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn dark btn-outline">Fechar</button>
                    <button type="button" id="salvar_upload" data-target="#form_upload" class="btn green-jungle btn-outline">Salvar</button>
                </div>
            </div>
        </div>
    </div>
    @endpermission
    <!-- END VALIDATION STATES-->

@endsection

@section('scripts')
    @parent

    <script>

        $(document).ready(function() {

            //Handle upload

            $('#salvar_upload').click(function(e) {
                e.preventDefault();
                var $self = $(this);
                $self.addClass('disabled');
                var $target = $($self.data('target'));
                $target.submit();
            })
        });
    </script>
@endsection