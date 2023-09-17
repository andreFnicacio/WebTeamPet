<div id="modal-exame-inicial-cadastrar" class="modal fade" tabindex="-1" data-replace="true" style="display: none">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Cadastrar exame inicial</h4>
            </div>

            <form action="{{ route('pets.cadastrarExameInicial', $pets->id) }}" method="POST" class="form-horizontal" id="form-cadastrar-exame-inicial" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="modal-body">

                    <div class="form-body">
                        <div class="form-group">
                            <label class="control-label col-md-4">Data de envio
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-6">
                                <div class="input-group input-medium date date-picker"  data-date-format="dd/mm/yyyy">
                                    <input value="{{ date('d/m/Y') }}" name="data_envio" type="text" class="form-control" required>
                                    <span class="input-group-btn">
                                        <button class="btn default" type="button">
                                            <i class="fa fa-calendar"></i>
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-4">Selecione o arquivo
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-6">
                                <input type="file" class="form-control" name="file" accept="image/x-png,.tiff,image/bmp,image/jpeg,application/pdf,.csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-4">Autor
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-6">
                                <input type="text" readonly class="form-control" value="{{ Auth::user()->name }}">
                            </div>
                        </div>

                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn dark btn-outline">Fechar</button>
                    <button type="submit" class="btn green-meadow btn-outline">Enviar</button>
                </div>
            </form>

        </div>
    </div>
</div>