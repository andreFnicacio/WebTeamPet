+(function () {
    if (!document.getElementById('planosProcedimentos')) {
        return;
    }

    window.vuePlanosProcedimentos = new Vue({
        el: "#planosProcedimentos",
        data: {
            search: '',
            api: '/api/v1/planosProcedimentos/',
            idProcedimento: -1,
            planosProcedimentos: [],
            isSaving: false
        },
        methods: {
            hasChanged: function(pp, which) {
                if(typeof pp[which] === 'undefined') {
                    return false;
                }

                return pp[which] != pp[which + '_original'];
            },
            load: function () {
                var _self = this;
                this.$http.get(this.api + 'findByProcedimento/' + this.idProcedimento).then(function (response) {
                    _self.planosProcedimentos = response.body;
                    this.tooltip();
                }, function (response) {
                    console.error("Error trying to retrieve data on " + this.api + 'findByProcedimento/' + _self.idProcedimento);
                });
            },
            trySave: function (pp, index) {
                this.isSaving = true;
                var _self = this;
                this.$http.post(this.api + 'vincular', {
                    id_planos_procedimentos: pp.id_vinculo,
                    valor_cliente: pp.valor_cliente,
                    valor_credenciado: pp.valor_credenciado,
                    beneficio_tipo: pp.beneficio_tipo,
                    beneficio_valor: pp.beneficio_valor,
                    id_plano: pp.plano.id,
                    id_procedimento: this.idProcedimento
                }).then(function (response) {
                    Vue.set(vuePlanosProcedimentos.planosProcedimentos, index, response.body);
                    _self.tooltip();

                    _self.isSaving = false;
                }, function (response) {
                    console.error("Error trying to retrieve data on " + this.api + 'vincular/');
                    _self.isSaving = false;
                });
            },
            remove: function(pp, index) {
                this.isSaving = true;
                var _self = this;
                this.$http.post(this.api + 'desvincular', {
                    id_planos_procedimentos: pp.id_vinculo,
                    valor_cliente: pp.valor_cliente,
                    valor_credenciado: pp.valor_credenciado,
                    id_plano: pp.plano.id,
                    id_procedimento: this.idProcedimento
                }).then(function (response) {
                    _self.load();
                    _self.isSaving = false;
                }, function (response) {
                    console.error("Error trying to retrieve data on " + this.api + 'vincular/');
                    _self.isSaving = false;
                });
            },
            tooltip: function() {
                this.$nextTick(function() {
                    $('[data-toggle=tooltip]').tooltip();
                });
            },
        },
        computed: {
            filteredPlanosProcedimentos: function() {
                var _self = this;
                return this.planosProcedimentos.filter(function(pp) {
                    if(_self.search === '') {
                        return true;
                    }
                    return pp.plano.nome_plano.toUpperCase().indexOf(_self.search.toUpperCase().trim()) > -1;
                });
            }
        },
        mounted: function () {
            this.idProcedimento = window.idProcedimento;
            this.load();
        }
    });

})();