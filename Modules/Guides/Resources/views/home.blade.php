@extends('layouts.app')

@section('title')
    @parent
    Emissor de guias - Nova guia
@endsection

@section('css')
    @parent
    <style>
        .tipo_atendimento .label {
            display: inline-block;
            margin-top: 8px;
        }

        .field-group.disabled {
            pointer-events: none;
            opacity: 50%;
        }

        .select2-container .select2-results__option[aria-disabled=true] {
            display: none;
        }

        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #fff;
            opacity: 0.8;
            z-index: 9999;
        }

        .loading-overlay .spin-loader {
            height: 100px;
            margin: 25% auto 30px;
            background: url({{ asset('_app_cadastro_cliente/images/loader.gif') }}) no-repeat center center transparent;
            top: 25%;
        }

        .loading-overlay h2 {
            color: #000;
            height: 100px;
            text-align: center;
            font-size: 40px;
        }
    </style>
@endsection

@php
    $selecionaveis = \Modules\Clinics\Entities\Clinicas::where('estado', $clinica->estado)
                                            ->where('selecionavel', 1)
                                            ->where('ativo', 1)
                                            ->orderBy('nome_clinica', 'asc');

    //VET MEDICAL CENTER VITORIA E PRESTADORES
    if(!in_array($clinica->id, [225, 268, 233, 265, 258, 88, 241])) {
        $selecionaveis->where('id', '!=', 270);
    }

    $selecionaveis = $selecionaveis->get();

    if(\Entrust::hasRole('GRUPO_HOSPITALAR')) {
        $grupo = \App\Models\GrupoHospitalar::where('id_usuario', Auth::id())->first();
        $vinculados = $grupo->clinicas()->get();
        $vinculadosSomados = $selecionaveis->merge($vinculados);
    }
@endphp

@section('content')
    @include('common.swal')

    <div class="loading-overlay" style="display: none;">
        <div class="spin-loader"></div>
        <h2 style="color: black;">
            Carregando informações...
        </h2>

    </div>

    <div class="portlet light portlet-fit portlet-form ">
        <div class="portlet-title">
            <div class="caption">
                <i class="icon-settings font-green-jungle"></i>
                <span class="caption-subject font-green-jungle sbold uppercase">Emitir Guia
                </span>
            </div>
            <div class="actions">
                <div class="btn-group btn-group-devided" data-toggle="buttons">
                    <button type="submit" id="button_emitirGuia" class="btn green-jungle">Salvar</button>
                    <button type="submit" class="btn red-sunglo">Cancelar</button>
                </div>
            </div>


        </div>
        <div class="portlet-body" id="emissor-guia">
            <!-- BEGIN FORM-->
            <form action="{{ route('autorizador.emitirGuia') }}" id="form_emitirGuia" class="form-horizontal"
                  method="POST" enctype="multipart/form-data">
                {{ csrf_field() }}
                <input type="hidden" id="autorizacao" name="autorizacao" value="AUDITORIA">
                <input type="hidden" id="isento" name="isento" value="0" v-model="selected.isento">
                <input type="hidden" id="pago" name="pago" value="false" v-model="selected.pago">

                <button type="submit" class="hidden" style="visibility: hidden;"></button>
                <div class="form-body">
                    <div class="alert alert-danger display-hide">
                        <button class="close" data-close="alert"></button>
                        Verifique se você preencheu todos os campos.
                    </div>
                    <div class="alert alert-success display-hide">
                        <button class="close" data-close="alert"></button>
                        Your form validation is successful!
                    </div>
                    <div class="col-md-12" style="margin-bottom: 20px;"><h3 class="block" style="margin-top: 0px;">Dados
                            Gerais</h3>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Número da Guia
                        </label>
                        <div class="col-md-5">
                            <input type="text" placeholder="Gerado Automaticamente" disabled class="form-control"/>
                        </div>
                    </div>

                    <div class="form-group tipo_atendimento">
                        <label class="control-label col-md-3">Tipo de Atendimento:
                        </label>
                        <div class="col-md-5">
                            <input type="hidden" name="tipo_atendimento" value="{{ $tipo_atendimento }}">
                            @if($tipo_atendimento == \Modules\Guides\Entities\HistoricoUso::TIPO_NORMAL)
                                <span class="label label-sm label-info">NORMAL</span>
                            @elseif($tipo_atendimento == \Modules\Guides\Entities\HistoricoUso::TIPO_EMERGENCIA)
                                <span class="label label-sm label-warning">EMERGÊNCIA</span>
                            @else
                                <span class="label label-sm bg-purple-studio bg-font-purple-studio">ENCAMINHAMENTO</span>
                            @endif

                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Microchip
                            <span class="required"> * </span>
                        </label>
                        <div class="col-md-3">
                            <input name="numero_microchip" v-model="microchip" required type="text" class="form-control"
                                   @if($microchip) value="{{$microchip}}" @endif/>
                        </div>

                        <div class="col-md-2">
                            <a class="btn  green-jungle btn-outline sbold" @click="loadPets()">Buscar Pet</a>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3">Plano
                            <span class="required"> * </span>
                        </label>
                        <div class="col-md-5">
                            <input type="text" name="nome_plano" required v-model="selected.nome_plano" readonly
                                   data-required="1" class="form-control"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3">Nome do Pet
                            <span class="required"> * </span>
                        </label>
                        <div class="col-md-5">
                            <input type="hidden" name="id_pet" required="" v-model="selected.id">
                            <input type="hidden" name="id_plano" required v-model="selected.id_plano"/>
                            <input type="hidden" name="nome_plano" required v-model="selected.nome_plano"/>
                            <input type="text" name="nome_pet" required v-model="selected.nome_pet" readonly
                                   data-required="1" class="form-control"/>
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="control-label col-md-3">Nome do Cliente
                            <span class="required"> * </span>
                        </label>
                        <div class="col-md-5">
                            <input type="hidden" name="id_cliente" required v-model="selected.id_cliente">
                            <input type="text" name="nome_cliente" required v-model="selected.nome_cliente" readonly
                                   data-required="1" class="form-control"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3">Observações:
                        </label>
                        <div class="col-md-5">
                            <textarea name="observacao" id="observacao" rows="5" class="form-control"></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3">Laudo: <span class="required"> * </span>
                        </label>
                        <div class="col-md-5">
                            <textarea name="laudo" id="laudo" rows="5" class="form-control" required
                                      minlength="60"></textarea>
                            <small>A descrição mínima é de 60 caracteres.</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3">Selecione laudos ou imagens, se existirem:</label>
                        <div class="col-md-5">
                            <input type="file" class="form-control" name="file[]"
                                   accept="image/png,image/jpeg,application/pdf" multiple>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3">Veterinário Solicitante:
                            <span class="required"> * </span>
                        </label>
                        <div class="col-md-5">
                            <select id="prestadores" required name="id_prestador" placeholder="Selecione um cadastro"
                                    class="form-control select2">
                                <option></option>
                                @foreach(\Modules\Veterinaries\Entities\Prestadores::all() as $prestador)
                                    <option value="{{ $prestador->id }}">{{ $prestador->nome }}
                                        - {{ $prestador->getCRMV() }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3">Especialidade:
                            <span class="required">  </span>
                        </label>
                        <div class="col-md-5">
                            <select id="especialidades" required name="id_especialidade"
                                    placeholder="Selecione um cadastro" class="form-control select2">
                                <option value="39" selected>39 - Clínico Geral</option>
                                @foreach(\App\Models\Especialidades::whereNotIn('id', ['39'])->get() as $especialidade)
                                    <option value="{{ $especialidade->id }}">{{ $especialidade->id }}
                                        - {{ $especialidade->nome }}</option>
                                @endforeach
                            </select>
                        </div>


                        <div class="col-md-12">
                            <div>
                                <div v-show="planShowAdmission">
                                    <div class="col-md-12" style="margin-bottom: 20px;">
                                        <h3 class="block" style="margin-top: 30px;">Internação</h3>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Haverá internação?
                                            <span class="required"> * </span>
                                        </label>
                                        <div class="col-md-5">
                                            <input type="checkbox" name="internacao" class="make-switch"
                                                   data-on-color="success" data-off-color="danger" data-on-text="Sim"
                                                   data-off-text="Não" value="1" id="internacao"
                                                   onchange="vueEmissorGuia.internacao = !vueEmissorGuia.internacao"
                                                   v-model="internacao"/>
                                        </div>
                                    </div>
                                    <div class="form-group" v-show="internacao">
                                        <label class="control-label col-md-3">Tipo de Internação:
                                        </label>
                                        <div class="col-md-5"
                                             v-bind:class="{ 'disabled' : procedimentosInternacaoCarregados().length < 1 }">
                                            {{--<select id="tipo_internacao" name="tipo_internacao" placeholder="Nenhuma" class="form-control select2" v-if="!selected.bichos">--}}
                                            <select id="tipo_internacao" name="tipo_internacao" placeholder="Nenhuma"
                                                    class="form-control select2">
                                                <option></option>
                                                @foreach(\App\Models\Procedimentos::whereIn('id_grupo', ['20100','99914','99917','99920', '10101037'])->where('ativo', 1)->get() as $procedimento)
                                                    <option value="{{ $procedimento->id }}"
                                                            data-grupo="{{ $procedimento->grupo->id }}">{{ $procedimento->id }}
                                                        - {{ $procedimento->nome_procedimento }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <!--<div class="col-md-1" style="display: none;" v-if="!selected.bichos">-->
                                        <div class="col-md-1" style="display: none;">
                                            <input name="dias_internacao" value="1" placeholder="Nº Dias"
                                                   class="form-control"/>
                                        </div>

                                    </div>
                                </div>

                                <div class="col-md-12" style="margin-bottom: 20px;">
                                    <h3 class="block" style="margin-top: 30px;">Procedimentos</h3>
                                </div>

                                <input type="hidden" name="pre_existencia" id="pre-existencia" value="0">
                                <div class="form-group">
                                    <label class="control-label col-md-3">Procedimentos
                                        <span class="required" v-if="!internacao && !pre_cirurgico"> * </span>
                                    </label>

                                    <div class="col-md-5 field-group"
                                         v-bind:class="{ 'disabled' : procedimentosCarregados().length < 1 }">
                                        <select id="procedimentos"
                                                :required="internacao || pre_cirurgico ? null : 'required'"
                                                name="procedimentos[]" placeholder="Nenhuma"
                                                class="form-control select2" multiple="multiple">
                                            <option></option>
                                            @foreach(\App\Models\Grupos::whereNotIn('id', ['20100','99914','99917','99920', '23100', '10101017', '10101037'])->get() as $grupo)
                                                <optgroup label="{{ $grupo->nome_grupo }}">
                                                    @foreach($grupo->procedimentos()->where('ativo', 1)->get() as $procedimento)
                                                        @if($procedimento->emergencial && $tipo_atendimento != \Modules\Guides\Entities\HistoricoUso::TIPO_EMERGENCIA)
                                                            @continue
                                                        @endif
                                                        <option value="{{ $procedimento->id }}"
                                                                data-grupo="{{ $grupo->id }}">
                                                            {{ $procedimento->id }}
                                                            - {{ $procedimento->nome_procedimento }}
                                                        </option>
                                                    @endforeach
                                                </optgroup>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-md-3">Clínica / Credenciado<span
                                                class="required"> * </span>
                                    </label>
                                    @if(!Entrust::hasRole(['ADMINISTRADOR', 'AUDITORIA']))
                                        <div class="col-md-5">
                                            <select id="id_clinica" required name="id_clinica"
                                                    placeholder="Selecione um cadastro" class="form-control select2">
                                                <option></option>
                                                @if(Entrust::hasRole(['CLINICAS']))
                                                    <option value="{{ $clinica->id }}">{{ $clinica->id . " - " . $clinica->nome_clinica }}</option>
                                                @elseif(Entrust::hasRole(['GRUPO_HOSPITALAR']))
                                                    @foreach($vinculados as $clinica)
                                                        <option value="{{ $clinica->id }}">{{ $clinica->id }}
                                                            - {{ $clinica->nome_clinica }}</option>
                                                    @endforeach
                                                @endif
                                                @if($tipo_atendimento != \Modules\Guides\Entities\HistoricoUso::TIPO_ENCAMINHAMENTO)
                                                    @foreach($selecionaveis as $clinica)
                                                        <option value="{{ $clinica->id }}">{{ $clinica->id }}
                                                            - {{ $clinica->nome_clinica }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    @else
                                        <div class="col-md-5">
                                            <select id="id_clinica" required name="id_clinica"
                                                    placeholder="Selecione um cadastro" class="form-control select2">
                                                <option></option>
                                                <option value="{{ $clinica->id }}">{{ $clinica->id . " - " . $clinica->nome_clinica }}</option>
                                                @foreach(\Modules\Clinics\Entities\Clinicas::orderBy('nome_clinica', 'asc')->get() as $clinica)
                                                    <option value="{{ $clinica->id }}">{{ $clinica->id }}
                                                        - {{ $clinica->nome_clinica }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            @if(\Entrust::hasRole(['ADMINISTRADOR', 'AUDITORIA']))
                                <div class="form-group">
                                    <label class="control-label col-md-3">Data de Realização
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-4">
                                        <div class="input-group input-medium date date-picker"
                                             data-date-format="dd/mm/yyyy">
                                            <input type="text" value="" name="created_at" class="form-control"
                                                   readonly="readonly">
                                            <span class="input-group-btn">
                                                <button class="btn default" type="button">
                                                    <i class="fa fa-calendar"></i>
                                                </button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3">
                                        Guia administrativa?<span class="required"></span>
                                    </label>
                                    <div class="col-md-5">
                                        <input type="hidden" name="administrativa" value="0">

                                        <input type="checkbox" name="administrativa" class="make-switch"
                                               data-on-color="success" data-off-color="danger" data-on-text="Sim"
                                               data-off-text="Não" value="1" id="administrativa"/>
                                        <br>
                                        <small>Marcar como "Guia administrativa" fará com que o processo seja
                                            automaticamente liberado pela auditoria.</small>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3">Emitir cobrança?
                                        <span class="required"> </span>
                                    </label>
                                    <div class="col-md-5">
                                        <input type="hidden" name="emitir_cobranca" value="0">
                                        <input type="checkbox" name="emitir_cobranca" checked value="1">
                                        <br>
                                        <small>Marcar como "emitir cobrança" disparará o processo de cobrança do
                                            procedimento.</small>
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            </form>
            <!-- END FORM-->
        </div>
    </div>
@endsection

@section('scripts')
    @parent
    <script>
        window.emitindoGuia = false;
        window.forcarHabilitado = false;

        $(document).ready(function () {
            vueEmissorGuia.microchip = "{{$microchip}}";
            if (vueEmissorGuia.microchip) {
                vueEmissorGuia.loadPets();
            }

            $('#procedimentos').change(function (e) {
                //Pegar todos os procedimentos
                let procedimentos = $(this).val();
                //Verficiar se existe algum procedimento sem carência cumprida
                var forcarHabilitado = true;
                procedimentos.forEach(function ($p) {
                    $procedimento = $('#procedimentos option[value=' + $p + ']');
                    if ($procedimento.length) {
                        let carenciaCumprida = $procedimento.attr('carencia_cumprida') === 'true';
                        forcarHabilitado = forcarHabilitado && carenciaCumprida;
                    }
                })
                window.forcarHabilitado = forcarHabilitado;
            });

            function emitirGuia(options) {
                if (!options) {
                    var options = {
                        skip: false
                    };
                }

                if (options.skip) {
                    $('#autorizacao').val('AUDITORIA');
                    $('#form_emitirGuia').find('button[type=submit]').click();
                    return;
                }

                //Verificar se vai ou não mostrar o botão de forçar
                var permitirForcarGuia = true;
                //Obter todos os procedimentos selecionados
                /**
                 * Obter dados de contrato do pet
                 *  * Criar função no backend que retorna se o PET pode executar o procedimento naquele instante. Ex: Pet::carenciaCumprida($idPet, $idProcedimento)
                 */
                //Se houver algum procedimento com carência não cumprida, ocultar botão de forçar.

                swal({
                    //title: 'Requer Autorização Especial.',
                    title: 'Atenção.',
                    text: "Os procedimentos selecionados podem necessitar de uma auditoria para serem liberados.\nEssa liberação pode demorar até 24 horas.",
                    type: 'info',
                    showConfirmButton: permitirForcarGuia,
                    showCancelButton: true,
                    showConfirmButton: window.forcarHabilitado,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Forçar',
                    cancelButtonText: 'Continuar',
                    confirmButtonClass: 'btn btn-danger',
                    cancelButtonClass: 'btn btn-info',
                    buttonsStyling: false
                }).then(function () {
                    swal({
                        title: 'Atenção',
                        text: 'Este(s) procedimento(s) estarão sujeitos à glosa.',
                        type: 'info',
                        showCancelButton: true,
                        confirmButtonText: 'Confirmar',
                        cancelButtonText: 'Cancelar',
                    }).then(function () {
                        $('#autorizacao').val('FORCADO');
                        $('#form_emitirGuia').find('button[type=submit]').click();
                    });
                }, function (dismiss) {
                    // dismiss can be 'cancel', 'overlay',
                    // 'close', and 'timer'
                    if (dismiss === 'cancel') {
                        $('#autorizacao').val('AUDITORIA');
                        $('#form_emitirGuia').find('button[type=submit]').click();
                    }
                });
            }

            function emitirGuiaEncaminhamento(options) {
                if (!options) {
                    var options = {
                        skip: false
                    };
                }
                if (options.skip) {
                    $('#autorizacao').val('AUDITORIA');
                    $('#form_emitirGuia').find('button[type=submit]').click();
                }

                swal({
                    title: 'Atenção.',
                    text: "Os procedimentos selecionados podem necessitar de uma auditoria para serem liberados.\nEssa liberação pode demorar até 24 horas.",
                    type: 'info',
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Continuar',
                    confirmButtonClass: 'btn btn-info',
                    buttonsStyling: false
                }).then(function () {
                    $('#autorizacao').val('AUDITORIA');
                    $('#form_emitirGuia').find('button[type=submit]').click();
                });
            }

            function emissaoPadrao(options) {
                var nome_plano = $('input[name="nome_plano"]').val();
                var id_plano = $('input[name="id_plano"]').val();
                var procedimentos = $('#procedimentos').val();

                if (window.emitindoGuia) {
                    console.log('Já existe uma guia no processo de emissão. Abortando.');
                    return;
                }

                window.emitindoGuia = true;

                if ($('input[name="tipo_atendimento"]').val() == 'ENCAMINHAMENTO') {
                    emitirGuiaEncaminhamento(options);
                } else {
                    emitirGuia(options);
                }
            }

            $('#button_emitirGuia').click(function (e) {
                //Checkar se existe um plano de benefícios:
                if (window.vueEmissorGuia.selected.isento) {
                    //Verificar se um benefício foi lançado:
                    if (window.vueEmissorGuia.beneficios.length) {
                        swal({
                            title: 'Informação',
                            html: "Prezado credenciado, o plano desse cliente é participativo e a cobrança pela realização do procedimento deverá ser feita agora, no ato da emissão da guia. <br>" +
                                "O valor que o cliente deverá pagar é: <br><br><br>" +
                                window.vueEmissorGuia.getBeneficios() + "<br>" +
                                "<br><br>Confirma o recebimento?",
                            type: 'info',
                            allowOutsideClick: false,
                            showCancelButton: true,
                            confirmButtonText: 'Sim',
                            cancelButtonText: 'Não',
                        }).then(function (confirm) {
                            if (confirm) {
                                vueEmissorGuia.$set(vueEmissorGuia.selected, 'pago', true);
                                $('#pago').val('true');
                                emissaoPadrao({
                                    skip: true
                                });
                            }
                        }, function (dismiss) {
                            if (dismiss === 'cancel') {
                                //Emitir guia recusada e adicionar justificativa
                                vueEmissorGuia.$set(vueEmissorGuia.selected, 'pago', false);
                                $('#pago').val('false');
                                emissaoPadrao({
                                    skip: true
                                });
                            }
                        });
                    }
                } else {
                    emissaoPadrao({
                        skip: false
                    });
                }
            });

            $('#id_clinica').change(function (e) {
                //Verificar se existem exames na guia.
                if (!vueEmissorGuia.isGuiaExame()) {
                    return false;
                }
                var id_clinica = $(this).val();
                var nome_clinica = $(this).find('option[value=' + id_clinica + ']').html();

                swal({
                    title: 'Informação',
                    html: "Você confirma que a emissão do exame deve ser solicitado para " + nome_clinica + "?",
                    type: 'info',
                    allowOutsideClick: false,
                    showCancelButton: true,
                    confirmButtonText: 'Sim',
                    cancelButtonText: 'Não',
                }).then(function (confirm) {
                    if (confirm) {
                        return true;
                    }
                }, function (dismiss) {
                    if (dismiss === 'cancel') {
                        //Zerar clínica
                        $('#id_clinica').val(null);
                        $('#id_clinica').select2('destroy');
                        $('#id_clinica').select2();
                        return false;
                    }
                });
            });
        });
    </script>

@endsection
