+(function() {
    if(typeof RenewalSettings === 'undefined') {
        console.log("Problema ao carregar os dados de configuração de renovação.");
        return;
    }

    const RenewalStatuses = {
        initial: 'INITIAL',
        open: 'OPEN',
        processing: 'PROCESSING',
        canceling: 'CANCELING',
        closed: 'CLOSED',
        complete: 'COMPLETE'
    };
    window.RenewalVuer = {
        data() {
            return {
                base: RenewalSettings.base,
                renewals: [],
                RenewalStatuses: RenewalStatuses,
                text: '',
                avaliableFilters: {},
                selectedFilters: {
                    ano: null,
                    mes: null,
                    renewed: false,
                    recent: false,
                    anual: true,
                    monthly: true
                },
                loading: false,
                soundManager: RenewalSettings.soundManager,
                timer: null
            }
        },
        computed: {
            filteredRenewals() {
                return this.filterLoaded(this.renewals);
            }
        },
        methods: {
            cancelRenewal(renewal) {
                renewal.detailed = null;
                renewal.status = RenewalStatuses.initial;
            },
            calculateTotalRenewal(renewal) {
                let totalMensal = (renewal.calculed.valor_mensal_original * (1+renewal.detailed.reajuste/100));
                totalMensal = totalMensal * (1 - (renewal.calculed.desconto/100));

                renewal.calculed.total_mensal = totalMensal;
                renewal.calculed.total_anual = totalMensal * 12;
            },
            autoFillValorMensal(renewal) {
                renewal.calculed.valor_mensal_original = renewal.plano.valor;
                if(!renewal.calculed.desconto) {
                    renewal.calculed.desconto = 0;
                }
            },
            loadRenewalDetails(renewal) {
                this.startProcessingRenewal(renewal, 'Obtendo detalhes.');
                var self = this;
                var r = renewal;
                var params = {
                    id_pet: renewal.pet.id
                };

                axios.get(this.base + '/details', {
                    params: params
                }).then(response => {
                    let details = null;

                    if(response.data) {
                        details = response.data;
                    }
                    r.detailed = details;
                    r.status = RenewalStatuses.open;

                    this.$nextTick(function() {
                        this.stopProcessingRenewal(r);
                    });
                }, response => {
                    console.log('Não foi possível carregar os dados de renovação.');

                    this.$nextTick(function() {
                        this.stopProcessingRenewal(r);
                    });
                });
            },
            formatNumber(number) {
                return number.toLocaleString('pt-br');
            },
            formatNumberToMoney(number) {
                if(number === null) {
                    return ' - ';
                }
                return number.toLocaleString('pt-br', {
                    style: 'currency',
                    currency: 'BRL'
                });
            },
            getPreviews: function() {
                var self = this;
                this.loading = true;
                axios.get(this.base + '/previews', {
                    params: self.getSearchParams()
                }).then(response => {
                    let renewals = [];

                    if(response.data) {
                        renewals = response.data;
                    }
                    this.renewals = renewals.filter(function(r) {
                        return r !== null;
                    });

                    this.$nextTick(function() {
                        this.loading = false;
                        this.initTooltips();
                    });
                }, response => {
                    console.log('Não foi possível carregar os dados de renovação.');

                    this.$nextTick(function() {
                        this.loading = false;
                    });
                })
            },
            getSearchParams: function() {
                let params = {};
                params.text = this.text;

                if(this.selectedFilters.ano) {
                    params.ano = this.selectedFilters.ano;
                }
                if(this.selectedFilters.mes) {
                    params.mes = this.selectedFilters.mes;
                }

                return params;
            },
            openAdvancedFiltersModal: function() {
                $('#advanced-filter-modal').modal('show');
            },
            loadSounds: function() {
                this.soundManager.audio = new Audio(this.soundManager.url);
            },
            applyFilters: function() {
                this.getPreviews();
            },
            filterLoaded: function(renewals) {
                const self = this;
                if(!this.selectedFilters.renewed) {
                    renewals = renewals.filter(function(r) { return r !== null; }).filter(function(r) {
                         return !r.renovacao.renovado;
                    });
                }

                if(!this.selectedFilters.anual) {
                    renewals = renewals.filter(function(r) {
                        return !r.anual;
                    })
                }

                if(!this.selectedFilters.monthly) {
                    renewals = renewals.filter(function(r) {
                        return !r.mensal;
                    })
                }

                if(!this.selectedFilters.recent) {
                    renewals = renewals.filter(function(r) {
                        return self.closeToExpireContract(r);
                    })
                }

                if(this.text !== '') {
                    renewals = renewals.filter(function(r) {
                        let key = r.pet.nome + r.tutor.nome + r.pet.id + r.tutor.id;

                        return key.toUpperCase().indexOf(self.text.toUpperCase()) > -1;
                    });
                }

                return renewals;
            },
            contractAgeMessage: function(renewal) {
                if(this.closeToExpireContract(renewal)) {
                    return "Contrato próximo de vencer ou vencido.";
                } else {
                    return "Contratação recente com data inferior a 9 meses. Atenção redobrada.";
                }
            },
            closeToExpireContract(renewal) {
                let now = moment();
                let contractDate = moment(renewal.data_inicio_contrato, 'DD/MM/YYYY');
                let diff = contractDate.diff(now, 'months');
                return diff < -9;
            },
            initTooltips() {
                $('[data-toggle="tooltip"]').tooltip();
            },
            startProcessingRenewal(renewal, message) {
                renewal.processing = true;
                renewal.processingMessage = message;
            },
            stopProcessingRenewal(renewal) {
                this.$nextTick(function() {
                    renewal.processing = false;
                    renewal.processingMessage = null;
                });
            },
            getValorFaturado(renewal) {
                return renewal.detailed.faturado;
            },
            validateRenewal(renewal) {
                if(renewal.calculed.valor_mensal_original === null || renewal.calculed.valor_mensal_original < 1) {
                    return false;
                }

                return (renewal.calculed.total_mensal >= 0 || renewal.calculed.total_anual >= 0);
            },
            confirmRenewal(renewal) {
                renewal.processing = true;

                if(!this.validateRenewal(renewal)) {
                    this.alert('Oops!', 'Existem dados incompletos. Revise a renovação antes de enviá-la.');
                    this.$nextTick(() => { renewal.processing = false; });
                    return;
                }

                const formData = {
                    "id_pet": renewal.pet.id,
                    "regime" : renewal.regime,
                    "valor_original" : renewal.calculed.valor_mensal_original,
                    "valorBase" : renewal.detailed.faturado,
                    "reajuste" : renewal.detailed.reajuste,
                    "desconto" : renewal.calculed.desconto,
                    "parcelas" : renewal.calculed.parcelas,
                    "anual" : renewal.calculed.total_anual,
                    "mensal" : renewal.calculed.total_mensal,
                    "_token" : window.RenewalSettings.token,
                    //"competencia_ano": moment().format('Y'),
                    //"competencia_mes": moment().format('MM')
                };

                this.startProcessingRenewal(renewal, 'Enviando dados.');

                const r = renewal;
                const _self = this;
                //Build renewal data
                axios({
                    method: 'post',
                    url: this.base + '/new',
                    data: formData
                }).then(response => {
                    //alert success
                    _self.startProcessingRenewal(r, 'Renovação concluída. Atualizando.');
                    setTimeout(function() {
                        //update 'renovacao' data
                        r.renovacao = response.data.renovacao;
                        //update 'status' data
                        r.status = response.data.status;
                        //update detailed
                        r.detailed = null;

                        _self.stopProcessingRenewal(r);
                    }, 2000);
                }, response => {
                    //error
                    _self.stopProcessingRenewal(r);
                    //alert error
                    this.alert('Oops!', 'Não foi possível salvar os dados de renovação pois ocorreu um erro de servidor.');
                })
            },
            skipRenewal(renewal) {
                const _self = this;
                this.startProcessingRenewal(renewal, 'Aguardando confirmação.');
                swal({
                    title: "Atenção",
                    html: `Confirma que o pet ${renewal.pet.nome} não passará por renovação?`,
                    buttonsStyling: false,
                    showCancelButton: true,
                    confirmButtonText: 'Sim',
                    cancelButtonText: 'Não',
                    reverseButtons: true,
                    allowOutsideClick: false
                }).then((result) => {
                    if(result) {
                        const r = renewal;
                        const formData = {
                            "id_pet": r.pet.id,
                            "valor": r.plano.valor,
                            "regime": r.regime,
                            "valor_original" : r.plano.valor,
                            "valorBase" : r.plano.valor,
                            "valor_bruto" : r.plano.valor,
                            "reajuste" : 0,
                            "desconto" : 0,
                            "parcelas" : 1,
                            "anual" : r.plano.valor * 12,
                            "mensal" : r.plano.valor,
                            "_token" : window.RenewalSettings.token,
                            "competencia_ano": moment().format('Y'),
                            "competencia_mes": moment().format('MM')
                        };

                        this.startProcessingRenewal(r, 'Enviando dados.');

                        //Build renewal data
                        axios({
                            method: 'post',
                            url: this.base + '/skip',
                            data: formData
                        }).then(response => {
                            //alert success
                            _self.startProcessingRenewal(r, 'Registro de não optante concluído. Atualizando.');
                            setTimeout(function() {
                                //update 'renovacao' data
                                r.renovacao = response.data.renovacao;
                                //update 'status' data
                                r.status = response.data.status;
                                //update detailed
                                r.detailed = null;

                                _self.stopProcessingRenewal(r);
                            }, 2000);
                        }, response => {
                            //error
                            _self.stopProcessingRenewal(r);
                            //alert error
                            this.alert('Oops!', 'Não foi possível salvar os dados de renovação pois ocorreu um erro de servidor.');
                        })
                    } else {
                        this.startProcessingRenewal(renewal, 'Cancelando processo.');

                        setTimeout(function() {
                            _self.stopProcessingRenewal(r);
                        }, 2000);
                    }
                }, function(dismiss) {
                    _self.startProcessingRenewal(renewal, 'Cancelando processo.');

                    setTimeout(function() {
                        _self.stopProcessingRenewal(renewal);
                    }, 2000);
                });
            },
            alert(title, message) {
                return swal({
                    title: title,
                    html: message,
                    buttonsStyling: false
                });
            }
        },
        mounted() {
            this.loadSounds();
            this.applyFilters();

            this.soundManager.audio.play();
        }
    };
})();


