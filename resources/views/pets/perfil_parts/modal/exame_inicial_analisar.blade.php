<div id="modal-exame-inicial-analisar-{{$ex->id}}" class="modal fade" tabindex="-1" data-replace="true" style="display: none">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Analisar exame inicial</h4>
            </div>

            <form action="{{ route('pets.analisarExameInicial', $pets->id) }}" method="POST" class="form-horizontal" id="form-analisar-exame-inicial-{{$ex->id}}" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="modal-body">

                    <div class="form-body">

                        <div class="form-group">
                            <label class="control-label col-md-4">Data de envio
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input value="{{ $ex->data_envio->format('d/m/Y H:i') }}" type="text" class="form-control" readonly disabled>
                          
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-4">Arquivo
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-6">
                                <a href="{{ url('/') }}/{{ $ex->upload->path }}" target="blank">Visualizar</a>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-4">Observações (opcional)
                            </label>
                            <div class="col-md-6">
                                <textarea name="obs"  class="form-control" >{{$ex->obs}}</textarea>
                            </div>
                        </div>

                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn dark btn-outline">Fechar</button>
                    <button type="submit" class="btn green-meadow btn-outline">Enviar análise</button>
                </div>
            </form>

        </div>
    </div>
</div>