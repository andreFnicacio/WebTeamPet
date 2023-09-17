<div class="portlet light portlet-form " id="cliente_documentos">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-id-card font-yellow-crusta"></i>
            <span class="caption-subject font-yellow-crusta sbold uppercase">Documentos</span>
        </div>
        <div class="actions">
            <div class="row">
                <div class="col-md-offset-0 col-md-12">
                    <a class="btn  green-jungle btn-outline sbold" href="#enviarDocumentosModal" data-toggle="modal"><i class="icon-plus red-sunglo"></i> Enviar Documentos</a>
                </div>
            </div>
        </div>
    </div>
    <div class="portlet-body">
        <div class="table-responsive">
            @if($cliente->documentos)
                <form action="{{ route('clientes.atualizarDocumentos', ['idCliente' => $cliente->id]) }}" id="form_atualizar_documentos" method="POST" class="form-inline margin-bottom-40">
                    {{ csrf_field() }}
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th width="20%">Origem</th>
                                <th width="20%">Documento</th>
                                <th width="20%">Arquivo(s)</th>
                                <th width="35%">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cliente->documentos as $documento)
                                <tr>
                                    <td>{{ $documento->id }}</td>
                                    <td>Cliente</td>
                                    <td>{{ $documento->nome }}</td>
                                    <td>
                                        @foreach ($documento->uploads()->get() as $file)
                                            <a href="{{ url('/') }}/{{ $file->path }}" class="btn btn-xs blue font-white tooltips" target="_blank" data-toggle="tooltip" title="{{ $file->description }} - Enviado por: {{ $file->user->name }} em {{ $file->created_at->format('d/m/Y H:i') }}">
                                                <i class="fa fa-paperclip"></i>
                                            </a>
                                        @endforeach
                                    </td>
                                    <td>
                                        @if ($documento->avaliacao_obrigatoria && $documento->status == \App\Models\DocumentosClientes::STATUS_ENVIADO)
                                            <div class="item_documento" data-id="{{ $documento->id }}" data-radio="documentos_clientes[{{ $documento->id }}][status]">
                                                <div class="md-radio-inline">
                                                    <div class="md-radio has-success">
                                                        <input type="radio" id="documento_cliente_1_{{ $documento->id }}" value="1" name="documentos_clientes[{{ $documento->id }}][status]" data-id="{{ $documento->id }}" class="md-radiobtn radio_aprovacao" required>
                                                        <label for="documento_cliente_1_{{ $documento->id }}">
                                                            <span></span>
                                                            <span class="check"></span>
                                                            <span class="box"></span> Aprovado </label>
                                                    </div>
                                                    <div class="md-radio has-error">
                                                        <input type="radio" id="documento_cliente_0_{{ $documento->id }}" value="0" name="documentos_clientes[{{ $documento->id }}][status]" data-id="{{ $documento->id }}" class="md-radiobtn radio_aprovacao" required>
                                                        <label for="documento_cliente_0_{{ $documento->id }}">
                                                            <span></span>
                                                            <span class="check"></span>
                                                            <span class="box"></span> Recusado </label>
                                                    </div>
                                                    <div class="form-group form-md-line-input has-error" style="width: 50%">
                                                        <input type="text" class="form-control" id="motivo_reprovacao_cliente_{{ $documento->id }}" name="documentos_clientes[{{ $documento->id }}][motivo_reprovacao]" placeholder="Motivo da recusa" required disabled style="width: 100%; display:none;">
                                                        <span class="help-block">Obrigatório*</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            @if ($documento->status == \App\Models\DocumentosClientes::STATUS_REPROVADO)
                                                <div class="label label-danger" data-toggle="tooltip" title="{{ $documento->id_usuario_reprovacao ? 'Recusado por: ' . $documento->usuarioReprovacao->name . ($documento->data_reprovacao ? ' em ' . $documento->data_reprovacao->format('d/m/Y \à\s H:i') : '') : 'Recusado automaticamente' }}">
                                                    {{ $documento->status }}
                                                </div>
                                            @elseif ($documento->status == \App\Models\DocumentosClientes::STATUS_APROVADO)
                                                <div class="label label-success" data-toggle="tooltip" title="{{ $documento->id_usuario_aprovacao ? 'Aprovado por: ' . $documento->usuarioAprovacao->name . ($documento->data_aprovacao ? ' em ' . $documento->data_aprovacao->format('d/m/Y \à\s H:i') : '') : 'Aprovado automaticamente' }}">
                                                    {{ $documento->status }}
                                                </div>
                                            @elseif ($documento->status == \App\Models\DocumentosClientes::STATUS_ENVIADO)
                                                <div class="label label-info" data-toggle="tooltip" title="Aguardando avaliação">
                                                    {{ $documento->status }}
                                                </div>
                                            @else
                                                <div class="label label-warning" data-toggle="tooltip" title="Aguardando envio dos documentos">
                                                    {{ $documento->status }}
                                                </div>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            @foreach($cliente->pets as $pet)
                                @foreach($pet->documentos as $documento)
                                    <tr>
                                        <td>{{ $documento->id }}</td>
                                        <td>
                                            <a href="{{ route('pets.edit', $pet->id) }}" target="_blank">{{ $pet->nome_pet }}</a>
                                        </td>
                                        <td>{{ $documento->nome }}</td>
                                        <td>
                                            @foreach ($documento->uploads()->get() as $file)
                                                <a href="{{ url('/') }}/{{ $file->path }}" class="btn btn-xs blue font-white tooltips" target="_blank" data-toggle="tooltip" title="{{ $file->description }} - Enviado por: {{ $file->user->name }} em {{ $file->created_at->format('d/m/Y H:i') }}">
                                                    <i class="fa fa-paperclip"></i>
                                                </a>
                                            @endforeach
                                        </td>
                                        <td>
                                            @if ($documento->avaliacao_obrigatoria && $documento->status == \App\Models\DocumentosClientes::STATUS_ENVIADO)
                                                <div class="item_documento" data-id="{{ $documento->id }}" data-radio="documentos_pets[{{ $documento->id }}][status]">
                                                    <div class="md-radio-inline">
                                                        <div class="md-radio has-success">
                                                            <input type="radio" id="documento_pet_1_{{ $documento->id }}" value="1" name="documentos_pets[{{ $documento->id }}][status]" data-id="{{ $documento->id }}" class="md-radiobtn radio_aprovacao" required>
                                                            <label for="documento_pet_1_{{ $documento->id }}">
                                                                <span></span>
                                                                <span class="check"></span>
                                                                <span class="box"></span> Aprovado </label>
                                                        </div>
                                                        <div class="md-radio has-error">
                                                            <input type="radio" id="documento_pet_0_{{ $documento->id }}" value="0" name="documentos_pets[{{ $documento->id }}][status]" data-id="{{ $documento->id }}" class="md-radiobtn radio_aprovacao" required>
                                                            <label for="documento_pet_0_{{ $documento->id }}">
                                                                <span></span>
                                                                <span class="check"></span>
                                                                <span class="box"></span> Recusado </label>
                                                        </div>
                                                        <div class="form-group form-md-line-input has-error" style="width: 50%">
                                                            <input type="text" class="form-control" id="motivo_reprovacao_pet_{{ $documento->id }}" name="documentos_pets[{{ $documento->id }}][motivo_reprovacao]" placeholder="Motivo da recusa" required disabled style="width: 100%; display:none;">
                                                            <span class="help-block">Obrigatório*</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                @if ($documento->status == \App\Models\DocumentosClientes::STATUS_REPROVADO)
                                                    <div class="label label-danger" data-toggle="tooltip" title="{{ $documento->id_usuario_reprovacao ? 'Recusado por: ' . $documento->usuarioReprovacao->name . ($documento->data_reprovacao ? ' em ' . $documento->data_reprovacao->format('d/m/Y \à\s H:i') : '') : 'Recusado automaticamente' }}">
                                                        {{ $documento->status }}
                                                    </div>
                                                @elseif ($documento->status == \App\Models\DocumentosClientes::STATUS_APROVADO)
                                                    <div class="label label-success" data-toggle="tooltip" title="{{ $documento->id_usuario_aprovacao ? 'Aprovado por: ' . $documento->usuarioAprovacao->name . ($documento->data_aprovacao ? ' em ' . $documento->data_aprovacao->format('d/m/Y \à\s H:i') : '') : 'Aprovado automaticamente' }}">
                                                        {{ $documento->status }}
                                                    </div>
                                                @elseif ($documento->status == \App\Models\DocumentosClientes::STATUS_ENVIADO)
                                                    <div class="label label-info" data-toggle="tooltip" title="Aguardando avaliação">
                                                        {{ $documento->status }}
                                                    </div>
                                                @else
                                                    <div class="label label-warning" data-toggle="tooltip" title="Aguardando envio dos documentos">
                                                        {{ $documento->status }}
                                                    </div>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                    <div class="margin-top-10 text-center">
                        <button type="button" class="btn btn-lg green" id="btn_enviar_avaliacao">Enviar avaliação</button>
                    </div>
                </form>

                <div id="enviarDocumentosModal" class="modal fade" data-replace="true" style="display: none;">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                <h4 class="modal-title">Enviar Documentos</h4>
                            </div>
                            <form class="form" action="{{ route('clientes.enviarDocumentos', ['idCliente' => $cliente->id]) }}" enctype="multipart/form-data" method="POST" >
                                {{ csrf_field() }}
                                <div class="modal-body">
                                    <div class="form-group form-md-radios">
                                        <label>Selecione o documento:</label>
                                        <div class="md-radio-list">
                                            @foreach($cliente->documentos as $documento)
                                                <div class="md-radio">
                                                    <input type="radio" id="enviar_doc_cliente-{{ $documento->id }}" name="id_documento" class="md-radiobtn radio-doc" value="{{ $documento->id }}" data-required="1">
                                                    <input type="radio" class="hidden" name="tipo_documento" value="clientes">
                                                    <label for="enviar_doc_cliente-{{ $documento->id }}">
                                                        <span></span>
                                                        <span class="check"></span>
                                                        <span class="box"></span> {{ $documento->nome }}
                                                    </label>
                                                </div>
                                            @endforeach
                                            @foreach($cliente->pets as $pet)
                                                @foreach($pet->documentos as $documento)
                                                    <div class="md-radio">
                                                        <input type="radio" id="enviar_doc_pet-{{ $documento->id }}" name="id_documento" class="md-radiobtn radio-doc" value="{{ $documento->id }}" data-required="1">
                                                        <input type="radio" class="hidden" name="tipo_documento" value="pets">
                                                        <label for="enviar_doc_pet-{{ $documento->id }}">
                                                            <span></span>
                                                            <span class="check"></span>
                                                            <span class="box"></span> {{ $documento->nome }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="form-body">
                                        <div class="row">
                                            <div class="form-group col-sm-12">
                                                <label class="control-label">Arquivos do documento selecionado acima:
                                                    <span class="required"> * </span>
                                                </label>
                                                <input type="file" multiple name="documentos_novos_uploads[]" class="form-control" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" data-dismiss="modal" class="btn dark btn-outline">Fechar</button>
                                    <button type="submit" class="btn green-jungle btn-outline">Salvar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @else
                <h3>Nenhum documento enviado</h3>
            @endif
        </div>
    </div>
</div>

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            $('.documentos_novos_uploads').change(function() {

                var formData = new FormData();
                formData.append('documentos_novos_uploads', $(this).prop('files'));
                formData.append('documento_id', $(this).data('id'));
                formData.append('_token', "{{csrf_token()}}");
                $.ajax({
                    url: "{{ route('clientes.enviarDocumentos', ['idCliente' => $cliente->id]) }}",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    cache: false,
                    processData:false,
                    success: function(data) {
                        location.reload();
                    },
                    error: function(e) {
                        swal('Erro!', 'Erro no envio dos arquivos', 'error');
                    }
                });
            });

            function submitDocumentosAvaliacao() {
                swal({
                    title: 'Tem certeza?',
                    text: "Qualquer recusa será informada para o cliente!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sim!',
                    cancelButtonText: 'Não!'
                }).then((result) => {
                    $('#form_atualizar_documentos').submit();
                });
            }

            $('#btn_enviar_avaliacao').on('click', function () {

                $('#form_atualizar_documentos .item_documento').each(function() {
                    var doc_id = $(this).data('id');
                    // var radio = $('input[name="'+$(this).data('radio')+'"]');
                    // if (!radio.is(':checked')) {
                    //     radio.focus();
                    //     swal('Atenção!', 'Todos os documentos devem ser avaliados!', 'error');
                    //     return false;
                    // }

                    if ($('input[name="'+$(this).data('radio')+'"][value=0]').is(':checked')) {
                        var motivo = $(this).parent().parent().find('input[type="text"]');
                        if(motivo.val() == '') {
                            motivo.focus();
                            swal('Atenção!', 'O motivo da recusa é obrigatório!', 'error');
                            return false;
                        }
                    }
                    submitDocumentosAvaliacao();
                });

            });

            $('.radio_aprovacao').on('change', function () {
                var doc_id = $(this).data('id');
                var input_motivo = $(this).parent().parent().find('input[type="text"]');
                if ($(this).val() == 1) {
                    input_motivo.prop('readonly', true).prop('disabled', true).val('');
                    input_motivo.hide();
                } else {
                    input_motivo.prop('readonly', false).prop('disabled', false);
                    input_motivo.show();
                }
            });

            $('#enviarDocumentosModal .radio-doc').click(function () {
                $('#enviarDocumentosModal .radio-doc').prop('checked', false);
                $(this).next().click();
                $(this).prop('checked', true);
            });
        });
    </script>
@endsection
