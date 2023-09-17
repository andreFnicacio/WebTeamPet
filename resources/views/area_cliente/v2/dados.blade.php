@extends('layouts.metronic5')
@section('css')
    <script>
        window.idCliente = "{{ $cliente->id }}";
    </script>
    @parent
    <style>
        button.disabled {
            pointer-events: none;
        }
        tr.pagamento td {
            background: hsla(0, 0%, 90%, 0.3) !important;
            border: none !important;
            padding-bottom: 3px !important;
            padding-top: 3px !important;
        }
        .historico-financeiro tr.cancelado {
            color: hsla(0, 0%, 114%, 1);
            cursor: not-allowed;
            background: hsla(0, 0%, 55%, 0.5);
            -webkit-touch-callout: none; /* iOS Safari */
            -webkit-user-select: none; /* Safari */
            -khtml-user-select: none; /* Konqueror HTML */
            -moz-user-select: none; /* Firefox */
            -ms-user-select: none; /* Internet Explorer/Edge */
            user-select: none; /* Non-prefixed version, currently
                                  supported by Chrome and Opera */
        }

        .control-label {
            font-weight: 400;
        }
    </style>
@endsection
@section('title')
    @parent
    Dados cadastrais
@endsection
@section('content')
    <div id="alteracao_cadastral" class="modal">
        <div class="modal-dialog">
           <div class="modal-content">
              <div class="modal-header">
                 <button type="button" data-dismiss="modal" aria-hidden="true" class="close"></button> 
                 <h4 class="modal-title">Solicitação de alteração</h4>
              </div>
              <div class="modal-body">
                  <form class="form" id="form-alteracao-cadastral">
                      {{ csrf_field() }}
                      <div class="form-body">
                          <div class="form-group col-sm-12">
                              <label class="control-label col-md-4">Descrição
                                  <span class="required"> * </span></label>
                              <div class="col-md-12"><textarea name="corpo" placeholder="Ex.: CEP incorreto. Modifique para: 29000-000" id="corpo" rows="3" class="form-control for-client"></textarea></div>
                          </div>

                          <div class="form-group col-sm-12">
                              <label class="control-label col-md-4">Telefone
                                  <span class="required"> * </span></label>
                              <div class="col-md-12"><input value="{{ $cliente->celular }}" name="celular" class="form-control for-client" type="text"></div>
                          </div>
                          <div class="form-group col-sm-12">
                              <label class="control-label col-md-5">Solicitante
                                  <span class="required"> * </span></label>
                              <div class="col-md-12"><input readonly="readonly" value="{{ Auth::user()->name }}" class="form-control" type="text"></div>
                          </div>
                      </div>
                  </form>
              </div>
              <div class="modal-footer">
                 <button type="button" data-dismiss="modal" class="btn dark btn-outline">Fechar</button>
                 <button type="button" id="solicitar_alteracao" class="btn blue-sharp btn-outline">Solicitar</button>
              </div>
           </div>
        </div>
    </div>

    <!-- BEGIN VALIDATION STATES-->
    <div class="portlet light portlet-fit portlet-form ">
        <div class="portlet-title">
            <div class="actions floating">
                <div class="btn-group btn-group-devided btn m-btn--pill btn-outline-primary btn-sm" data-toggle="buttons">
                    <a href="#alteracao_cadastral" data-toggle="modal" id="save" class="btn blue-sharp">
                        <span class="fa fa-pencil icon"></span>

                        <span class="reveal">Alterar</span>
                    </a>
                </div>
            </div>
        </div>
        <div class="portlet-body">
            @include('clientes.fields_view')
        </div>
        <!-- END FORM-->
    </div>
    <!-- END VALIDATION STATES-->
@endsection

@section('scripts')
    @parent
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('#alteracao_cadastral #solicitar_alteracao').click(function(event) {
                $(this).addClass('loading');
                $.ajax({
                    url: '{{ route('cliente.alterarDados') }}',
                    type: 'POST',
                    data: $("#form-alteracao-cadastral").serialize(),
                })
                .done(function() {
                    //console.log("success");
                    window.location.reload();
                })
                .fail(function() {
                    alert('Erro ao tentar solicitar alteração.');
                })
                .always(function() {
                    $(this).removeClass('loading');
                });
            });
        });
    </script>
@endsection