@extends('layouts.metronic5')
@section('css')
    @parent
    <style>

        .box-definir-credenciado {
            margin-bottom: 20px;
            box-shadow: 0px 1px 5px 1px #00000033;
            padding: 15px;
        }
        .box-definir-credenciado h6 {
            color: white;
            background: #009cf3;
            padding: 2px 12px;
            font-weight: bold;
            border-radius: 15px;
            width: auto !important;
            display: inline-block;
            margin-bottom: 20px;
        }
        .box-definir-credenciado h6.nome-credenciado {
            color: #009cf3;
            background: white;
            padding: 0;
        }
        .m-portlet .m-portlet__head {
            padding: 0;
        }
        .marcar {
            text-align: center;
            height: 100%;
            color: #009cf3;
            padding-top: 30px;
        }

        .marcar .fa-clock-o {
            font-size: 40pt;
        }

        .marcar span {
            margin-top: 20px;
            display: inline-block;
        }
    </style>
@endsection
@section('title')
    @parent
    Agendar encaminhamento
@endsection
@section('content')
    <div  id="AgendamentoCliente">
        <div class="m-portlet  light  portlet-form ">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <div class="m-portlet__head-title">
						<span class="m-portlet__head-icon">
							<i class="fa fa-calendar"></i>
						</span>
                        <h3 class="m-portlet__head-text">
                            Agendar encaminhamento
                        </h3>
                    </div>
                </div>
            </div>
            <div class="portlet-body">
                <input type="hidden" id="id_guia" value="{{ $encaminhamento->id }}">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="box-definir-credenciado">
                            <h6 class="numero_guia">Guia {{ $encaminhamento->numero_guia }}</h6>
                            <strong>
                                <p style="color: #009cf3">Encontre o credenciado mais próximo: </p>
                            </strong>
                            <select class="select2 for-client form-control select2-vue-cidades" v-model="selection.cidade">
                                <option value="" disabled selected>CIDADES</option>
                                <option v-for="c in cidades" :value="c">@{{ c }}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <h5>Resultados mais próximos: </h5><br>
                    </div>
                </div>
                <div class="row" v-for="c in filteredCredenciados">

                    <div class="col-sm-12">

                        <div class="box-definir-credenciado">
                            <div class="row">
                                <div class="col-sm-10" style="width: 70%">
                                    <h6 class="nome-credenciado">@{{ c.nome }}</h6>
                                    <p class="endereco">
                                        @{{ c.endereco }}
                                    </p>
                                    <p class="telefone">
                                        @{{ c.telefone }}
                                    </p>
                                </div>
                                <div class="col-sm-2" style="width: 30%">
                                    <div class="marcar" @click="escolher(c)">
                                        <i class="fa fa-clock-o"></i>
                                        <br>
                                        <span>ESCOLHER</span>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="Agendamentos--modal-sucesso" class="modal fade" tabindex="-1" data-replace="true" v-if="selection.credenciado">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12 text-center">
                                <span class="icon-check"
                                      style="font-size: 40px;
                                      margin-bottom: 20px;
                                      color: #a4dd83;"></span>
                            </div>
                        </div>
                        <p>Você escolheu a <strong>@{{ selection.credenciado.nome }}</strong>, porém esse credenciado ainda não faz marcação online. Você poderá ligar no telefone @{{ selection.credenciado.telefone_fixo }} e agendar com um atendente.</p>
                    </div>
                    <div class="modal-footer">
                        <a href="javascript:;" class="modal-dismiss btn btn-padrao" data-dismiss="modal">MUDAR CREDENCIADA</a>
                        <a href="{{ route('cliente.home') }}" class="btn btn-finalizar">FINALIZAR</a>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @parent
    <script type="text/javascript" src="{{ mix('js/app.js') }}?{{ time() }}"></script>
    <script src="{{ asset('js/agendamento_cliente.vue.js') }}"></script>
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