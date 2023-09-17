function formatMoney(number, decPlaces, decSep, thouSep) {
    decPlaces = isNaN(decPlaces = Math.abs(decPlaces)) ? 2 : decPlaces;
    decSep = typeof decSep === "undefined" ? "," : decSep;
    thouSep = typeof thouSep === "undefined" ? "." : thouSep;
    var sign = number < 0 ? "-" : "";
    var i = String(parseInt(number = Math.abs(Number(number) || 0).toFixed(decPlaces)));
    var j = (j = i.length) > 3 ? j % 3 : 0;

    return sign +
        (j ? i.substr(0, j) + thouSep : "") +
        i.substr(j).replace(/(\decSep{3})(?=\decSep)/g, "$1" + thouSep) +
        (decPlaces ? decSep + Math.abs(number - i).toFixed(decPlaces).slice(2) : "");
}

/**
 * Componente que controla o comportamento da barra superior de busca;
 * @type {*|Vue}
 */
+(function () {
    if (!document.getElementById('emissor-guia')) {
        return;
    }

    window.vueEmissorGuia = new Vue({
        el: "#emissor-guia",
        data: {
            exames: false,
            microchip: '',
            crmv: '',
            pets: [],
            internacao: 0,
            planShowAdmission: true,
            pre_cirurgico: 0,
            procedimentos: [],
            procedimentosInternacao: [],
            prestador: {},
            beneficios: [],
            selected: {
                'id': null,
                'nome_pet': null,
                'id_cliente': null,
                'nome_cliente': null,
                'ativo': null,
                'bichos': false,
                'isento': false,
                'pago': false,
                'planoHabilitadoParaClinica': false,
                'participativo': false,
                'restricao_procedimentos': false,
            }
        },
        computed: {

        },
        methods: {
            resetData: function() {
                var _self = this;
                _self.microchip = '';
                _self.selected = {
                    'id': null,
                    'nome_pet': null,
                    'id_cliente': null,
                    'nome_cliente': null,
                    'ativo': null,
                    'bichos': false,
                    'isento': false,
                    'pago': false,
                    'participativo': false,
                    'planoHabilitadoParaClinica': false,
                    'restricaoProcedimentos': false,
                }
            },
            loadPets: function () {
                if (this.microchip === '' || !this.microchip) {
                    this.pets = [];
                    return;
                }
                var _self = this;
                $('.loading-overlay').show();
                this.$http.get('/pet/' + this.microchip).then(function (response) {
                    $('.loading-overlay').hide();
                    _self.pets = response.body;
                    if (_self.pets.length > 0) {
                        _self.selected = _self.pets[0];

                        _self.procedimentos = _self.selected.procedimentos;

                        $('#procedimentos option').attr('disabled', 'disabled');
                        for(let i = 0; i < _self.procedimentos.length; i++) {
                            let procedimento = $("#procedimentos option[value=" + _self.procedimentos[i].id + "]");
                            procedimento.removeAttr('disabled');
                            procedimento.attr('carencia_cumprida', _self.procedimentos[i].carencia_cumprida);
                        }
                        let $procedimentos = $('#procedimentos');
                        $procedimentos.select2('destroy');
                        $procedimentos.select2({width: '100%'});

                        _self.procedimentosInternacao = _self.selected.procedimentoInternacao;

                        $('#tipo_internacao option').attr('disabled', 'disabled');
                        for(let i = 0; i < _self.procedimentosInternacao.length; i++) {
                            let procedimentosInternacao = $("#tipo_internacao option[value=" + _self.procedimentosInternacao[i].id + "]");
                            procedimentosInternacao.removeAttr('disabled');
                            procedimentosInternacao.attr('carencia_cumprida', _self.procedimentosInternacao[i].carencia_cumprida);
                        }
                        let $procedimentosInternacao = $('#tipo_internacao');
                        $procedimentosInternacao.select2('destroy');
                        $procedimentosInternacao.select2({width: '100%'});


                        if(_self.selected.isento || _self.selected.participativo || _self.selected.restricaoProcedimentos) {
                            _self.procedimentos = _self.selected.procedimentos;
                        }

                        if (_self.selected.participativo) {
                            _self.selected.nome_plano = _self.selected.nome_plano + " - " + "Participativo";
                        } else {
                            _self.selected.nome_plano = _self.selected.nome_plano + " - " + "Integral";
                        }

                        let hideInternForPlans = [74, 75, 76];

                        if (hideInternForPlans.includes(_self.selected.id_plano)) {
                            swal({
                                title: 'Informação',
                                text: "Prezado credenciado, os procedimentos de internação para este plano se encontram dentro das opções de procedimentos.",
                                type: 'info',
                                allowOutsideClick: false,
                                showCancelButton: false
                            }).then(function () {
                                _self.planShowAdmission = false;
                            });
                        } else {
                            _self.planShowAdmission = true;
                        }

                        if (_self.isAdmin() || _self.isAuditor()) {
                            return;
                        }

                        if (_self.selected.statusPagamento !== "Em dia") {
                            swal(
                                'Erro',
                                "Esse cliente não poderá emitir guias. Peça para entrar em contato das 9h as 18h pelo chat em nosso site www.lifepet.com.br. Ao entrar em contato, informe o Erro FI-01.",
                                'error'
                            );

                            this.resetData();
                            return;
                        }

                        if(_self.selected.doencasPreExistentes) {
                            swal({
                                //title: 'Requer Autorização Especial.',
                                title: 'Atenção.',
                                text: "Prezado credenciado, o pet possui doenças pré-existentes: " + _self.selected.doencasPreExistentes + "\nOs procedimentos possuem relação com as patologias citadas?",
                                type: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Sim',
                                cancelButtonText: 'Não',
                                confirmButtonClass: 'btn btn-danger',
                                cancelButtonClass: 'btn btn-info',
                                buttonsStyling: false
                            }).then(function () {
                                    $('#pre-existencia').val(1);
                            }, function (dismiss) {
                                // dismiss can be 'cancel', 'overlay',
                                // 'close', and 'timer'
                                if (dismiss === 'cancel') {
                                    $('#pre-existencia').val(0);
                                }
                            });
                        }

                        if (!_self.selected.planoHabilitadoParaClinica) {
                            swal(
                                'Erro',
                                "Prezado credenciado, informamos que a sua clínica não pode realizar atendimento para esse plano. Oriente o cliente a buscar uma rede compatível com o plano no App Lifepet.",
                                'error'
                            );

                            this.resetData();
                            return;
                        }
                    } else {
                        _self.resetData();
                    }
                }, function (response) {
                    console.error("Error trying to retrieve data on '/pets/'" + _self.microchip);
                });
            },
            loadPrestadores: function () {
                if (this.crmv === '' || !this.crmv) {
                    this.prestador = {};
                    return;
                }
                var _self = this;
                this.$http.get('/api/v1/prestador/' + this.crmv).then(function (response) {
                    _self.prestador = response.body;
                    if (_self.prestador.length > 0) {
                        _self.prestador = _self.prestador[0];
                    }
                }, function (response) {
                    console.error("Error trying to retrieve data on '/prestador/'" + _self.crmv);
                });
            },
            isAdmin: function () {
                return parseInt($('meta[name=isadmin]').attr('content'));
            },
            isAuditor: function () {
                return parseInt($('meta[name=isauditor]').attr('content'));
            },
            fillProcedimentos: function() {
                var procedimentos = $('#procedimentos option:selected').toArray();
                var _self = this;

                if(!(_self.selected.isento || _self.selected.participativo)) {
                    return;
                }

                _self.beneficios = [];

                procedimentos.forEach(function(selected) {
                    var procedimento = _self.procedimentos.find(function(p) {
                        return p.id === parseInt($(selected).attr('value'));
                    });

                    if(procedimento.pivot.beneficio_valor !== null && procedimento.pivot.beneficio_valor !== '') {
                        _self.beneficios.push({
                            procedimento: procedimento,
                            beneficio_tipo: procedimento.pivot.beneficio_tipo,
                            beneficio_valor: procedimento.pivot.beneficio_valor
                        });
                    }
                })
            },
            getBeneficios: function() {
                return this.beneficios.map(function(b) {
                    if(b.beneficio_tipo == 'fixo') {
                        return b.procedimento.nome_procedimento + ': ' + 'R$ ' + formatMoney(b.beneficio_valor);
                    } else {
                        return b.procedimento.nome_procedimento + ': ' + '%' + b.beneficio_valor + ' de desconto.';
                    }
                }).join("<br>");
            },
            procedimentosCarregados() {
                return this.procedimentos;
            },
            procedimentosInternacaoCarregados() {
                return this.procedimentosInternacao;
            },
            procedimentosSelecionados() {
                var _self = this;
                return $('#procedimentos option:selected').toArray().map(function(selected) {
                    return _self.procedimentos.find(function(p) {
                        return p.id === parseInt($(selected).attr('value'));
                    });
                });
            },
            isGuiaExame() {
                var selecionados = this.procedimentosSelecionados();
                if(!selecionados.length) {
                    return false;
                }

                return selecionados.some(function(p) {
                    return p.id_grupo === 10101011;
                });
            },
            totalProcedimentos: function() {
                var procedimentos = $('#procedimentos option:selected').toArray();
                var total = 0;
                var _self = this;
                procedimentos.forEach(function(selected) {
                    var procedimento = _self.procedimentos.find(function(p) {
                        return p.id === parseInt($(selected).attr('value'));
                    });
                    if(procedimento) {
                        total += procedimento.valor_cliente;
                    }
                });

                return total;
            }
        },
        mounted: function () {

        }
    });
})();