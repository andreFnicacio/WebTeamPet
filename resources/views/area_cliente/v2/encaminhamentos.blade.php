@extends('layouts.metronic5')
@section('css')
    <script>
        window.idCliente = "{{ $cliente->id }}";
    </script>
    @parent
@endsection
@section('title')
    @parent
    Encaminhamentos
@endsection
@section('content')
    <div class="m-portlet  light  portlet-form " id="encaminhamentos">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
						<span class="m-portlet__head-icon">
							<i class="fa fa-calendar"></i>
						</span>
                    <h3 class="m-portlet__head-text">
                        Encaminhamentos
                    </h3>
                </div>
            </div>
        </div>
        <div class="portlet-body">
            <div class="table-responsive">
                <table class="table table-hover table-checkable order-column datatables-responsive responsive table-responsive dataTable no-footer dtr-inline">
                    <thead>
                    <tr>
                        <th > Guia </th>
                        <th > Pet </th>
                        <th > Emitida em </th>
                        <th > Liberada á partir de </th>
                        <th > Credenciado </th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach($encaminhamentos as $e)
                        <tr>
                            <td>
                                {{ $e->numero_guia }}
                            </td>
                            <td>
                                {{ $e->pet()->first()->nome_pet }}
                            </td>
                            <td>
                                {{ $e->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td>
                                {{ $e->data_liberacao ? $e->data_liberacao->format('d/m/Y H:i') : " - " }}
                            </td>
                            <td>
                                {{ $e->clinica()->first()->nome_clinica }}

                                <a href="javascript:;"
                                   class="btn btn-circle btn-default pull-right toggle-credenciado-modal"
                                   data-toggle="tooltip"
                                   data-original-title="Clique para mudar o prestador"
                                   data-guia="{{ $e->numero_guia }}">MUDAR</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @foreach($encaminhamentos as $e)
    <div id="modal-credenciado-guia-{{$e->numero_guia}}" class="modal fade" tabindex="-1" data-replace="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('cliente.escolherCredenciado') }}">
                    {{ csrf_field() }}
                    <input type="hidden" name="numero_guia" value="{{ $e->numero_guia }}">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">Encaminhamento #<span id="mcg-numero_guia">{{ $e->numero_guia }}</span></h4>
                    </div>
                    <div class="modal-body col-sm-12">
                        <ul class="list lista-procedimento">
                            <li class="item">
                                <strong>Pet: </strong>
                                <span id="mcg-nome_pet">{{ $e->pet()->first()->nome_pet }}</span>
                            </li>
                            <li class="item">
                                <strong>Procedimento: </strong>
                                <span id="mcg-nome_procedimento">{{ $e->procedimento()->first()->nome_procedimento }}</span>
                            </li>
                        </ul>

                        <select name="id_clinica" class="form-control for-client">
                            @foreach($e->pet->plano()->credenciados as $credenciado)
                                <option value="{{ $credenciado->id }}">{{ $credenciado->nome_clinica }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success btn-outline">Enviar</button>
                        <button type="button" data-dismiss="modal" class="btn dark btn-outline">Fechar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach
@endsection

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            $('.toggle-credenciado-modal').click(function () {
                $modal = $('#modal-credenciado-guia-' + $(this).data('guia'));

                // $modal.find('#mcg-nome_pet').text($(this).data('pet'));
                // $modal.find('#mcg-nome_procedimento').text($(this).data('procedimento'));
                // $modal.find('#mcg-numero_guia').text($(this).data('guia'));

                $modal.modal('show');
            })
        });
    </script>
@endsection