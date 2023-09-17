<div id="solicitacao_reembolso" class="modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" data-dismiss="modal" aria-hidden="true" class="close"></button>
                <h4 class="modal-title">Solicitação de Reembolso</h4>
            </div>
            <div class="modal-body">
                <form class="form" id="form-solicitacao-reembolso" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="form-body">
                        <div class="col-sm-12">
                            <div class="col-sm-12">
                                <div id="reembolso-error"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                {{--<div class="form-group col-sm-12">--}}
                                    {{--<label class="control-label col-md-12">Solicitante--}}
                                        {{--<span class="required"> * </span>--}}
                                    {{--</label>--}}
                                    {{--<div class="col-md-12"><input readonly="readonly" value="{{ Auth::user()->name }}" class="form-control" type="text"></div>--}}
                                {{--</div>--}}
                                <div class="form-group col-sm-12">
                                    <label class="control-label col-md-12">Pet
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-12">
                                        <select name="id_pet" class="form-control for-client" required>
                                            @foreach(\App\Models\Pets::petsFromUser() as $pet)
                                                <option value="{{ $pet->id }}">{{ $pet->primeiro_nome }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-sm-12">
                                    <label class="control-label col-md-12">Data do Procedimento
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-12">
                                        <div class="input-group input-medium date date-picker disabled"  data-date-format="dd/mm/yyyy">
                                            <input type="text" class="form-control for-client" name="data_procedimento" required>
                                            <span class="input-group-btn">
                                        <button class="btn default disabled" type="button">
                                            <i class="fa fa-calendar"></i>
                                        </button>
                                    </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-sm-12">
                                    <label class="control-label col-md-12">Documentos
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-12">
                                        <input type="file" class="form-control for-client" name="documentos_reembolso[]" multiple accept="image/x-png,.tiff,image/bmp,image/jpeg,application/pdf" required>
                                        <small>Apenas arquivos em formato de imagem ou pdf.</small>
                                    </div>
                                </div>
                                <div class="form-group col-sm-12">
                                    <label class="control-label col-md-12">Descrição
                                    </label>
                                    <div class="col-md-12">
                                        <textarea name="descricao" placeholder="" id="corpo" rows="3" class="form-control for-client"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group col-sm-12">
                                    <label class="control-label col-md-12">Banco
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-12">
                                        <select name="reembolso_banco" class="form-control for-client" required>
                                            @foreach((new \App\Helpers\Utils)->getBancos() as $cod => $banco)
                                                <option value="{{ $cod }}">{{ $banco }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-sm-12">
                                    <label class="control-label col-md-12">Tipo da Conta
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-12">
                                        <select name="reembolso_tipo_conta" class="form-control for-client" required>
                                            <option value="Corrente">Corrente</option>
                                            <option value="Poupança">Poupança</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-sm-12">
                                    <label class="control-label col-md-12">Titularidade
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-12">
                                        <select name="reembolso_titularidade" class="form-control for-client" required>
                                            <option value="Individual">Individual</option>
                                            <option value="Conjunta">Conjunta</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-sm-12">
                                    <label class="control-label col-md-12">Nome completo
                                        <span class="required"> * </span>
                                        <small>(titular da conta)</small>
                                    </label>
                                    <div class="col-md-12">
                                        <input name="reembolso_nome_completo" class="form-control for-client" required>
                                    </div>
                                </div>
                                <div class="form-group col-sm-12">
                                    <label class="control-label col-md-12">CPF
                                        <span class="required"> * </span>
                                        <small>(titular da conta)</small>
                                    </label>
                                    <div class="col-md-12">
                                        <input name="reembolso_cpf" class="form-control cpf for-client" required>
                                    </div>
                                </div>
                                <div class="form-group col-sm-12">
                                    <label class="control-label col-md-12">Conta
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-12">
                                        <input name="reembolso_conta" class="form-control for-client" required>
                                    </div>
                                </div>
                                <div class="form-group col-sm-12">
                                    <label class="control-label col-md-12">Agência
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-12">
                                        <input name="reembolso_agencia" class="form-control for-client" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn dark btn-outline">Fechar</button>
                <button type="button" form="form-solicitacao-reembolso" id="solicitar_reembolso" class="btn green-dark btn-outline">Solicitar</button>
            </div>
        </div>
    </div>
</div>

@section('scripts')
    @parent
    <script src="{{ url('/') }}/assets/global/plugins/jquery-mask/jquery.mask.min.js" type="text/javascript"></script>
    <script type="text/javascript">
        $(document).ready(function() {

            $('.cpf').mask('000.000.000-00', {reverse: true});

            $('#solicitar_reembolso').click(function(event) {
                $(this).addClass('loading');

                var form = $("#form-solicitacao-reembolso")[0];
                var formData = new FormData(form);

                $( "#form-solicitacao-reembolso" ).validate({
                    errorLabelContainer: '#reembolso-error',
                    wrapper: "li",
                    debug: true,
                    rules: {
                        id_pet: {
                            required: true,
                        },
                        data_procedimento: {
                            required: true,
                        },
                        reembolso_conta: {
                            required: true,
                        },
                        reembolso_agencia: {
                            required: true,
                        },
                        reembolso_cpf: {
                            required: true,
                        },
                        reembolso_banco: {
                            required: true,
                        },
                        reembolso_tipo_conta: {
                            required: true,
                        },
                        reembolso_titularidade: {
                            required: true,
                        },
                        reembolso_nome_completo: {
                            required: true,
                        },
                        documentos_reembolso: {
                            required: true,
                            accept: "image/*,application/pdf"
                        },
                    },
                    messages: {
                        id_pet: {
                            required: "Informe para qual pet será o reembolso.",
                        },
                        data_procedimento: {
                            required: "Informe a data do procedimento.",
                        },
                        reembolso_conta: {
                            required: "Informe a conta para reembolso.",
                        },
                        reembolso_agencia: {
                            required: "Informe a agência para reembolso.",
                        },
                        reembolso_cpf: {
                            required: "Informe o cpf do proprietário(a) da conta para reembolso.",
                        },
                        reembolso_banco: {
                            required: "Informe o banco.",
                        },
                        reembolso_tipo_conta: {
                            required: "Informe o tipo da conta.",
                        },
                        reembolso_titularidade: {
                            required: "Informe a titularidade.",
                        },
                        reembolso_nome_completo: {
                            required: "Informe o nome completo do titular da conta.",
                        },
                        documentos_reembolso: {
                            required: "Adicione pelo menos um documento",
                            accept: "Informe um tipo de arquivo válido!"
                        }
                    }
                });

                if ($( "#form-solicitacao-reembolso" ).valid()) {
                    swal({
                        title: 'Enviando sua Solicitação!',
                        text: 'Aguarde um instante...',
                        allowEscapeKey: false,
                        allowOutsideClick: false,
                        onOpen: () => {
                            swal.showLoading();
                        }
                    });
                    $('#reembolso-error').hide();
                    $.ajax({
                        url: '{{ route('cliente.solicitarReembolso') }}',
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                    })
                        .done(function() {
                            // $('#solicitacao_reembolso').modal('toggle');
                            // swal('Solicitação Enviada!', 'Sua solicitação de reembolso foi enviada para avaliação.', "success");
                            window.location.reload();
                        })
                        .fail(function() {
                            swal('Erro ao tentar solicitar o reembolso.', '', 'error');
                        });
                }
            });
        });
    </script>
@endsection