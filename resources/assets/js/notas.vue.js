+(function () {
    if (!document.getElementById('notas')) {
        return;
    }

    window.vueNotas = new Vue({
        el: "#notas",
        data: {
            nota: {},
            idCliente: null,
            notas: []
        },
        methods: {
            load: function () {
                var _self = this;
                this.$http.get('/notas/' + this.idCliente).then(function (response) {
                    _self.notas = response.body;
                    Vue.nextTick(function() {
                        $('[data-toggle="tooltip"], .tooltips').tooltip();
                    });

                }, function (response) {
                    console.error("Error trying to retrieve data on '/api/v1/notas/'" + _self.idCliente);
                });
            },
            salvar: function () {
                jQuery('#salvar_nota').addClass('disabled');
                var _self = this;
                this.$http.post('/notas/' + this.idCliente, this.nota).then(function (response) {
                    this.load();
                    this.nota.corpo = "";
                    jQuery('#nova_nota').modal('hide');
                    jQuery('#salvar_nota').removeClass('disabled');
                }, function (response) {
                    console.error("Error trying to retrieve data on '/notas/'" + _self.idCliente);
                });
            },
            excluirNota: function(n) {
                var _self = this;

                var prompted = prompt('Digite EXCLUIR para executar a ação.');
                if(!prompted || prompted.toUpperCase() !== 'EXCLUIR') {
                    return false;
                }

                this.$http.post('/notas/excluir', {
                    idNota: n.id
                }).then(function (response) {
                    this.load();
                    this.nota.corpo = "";
                    jQuery('#nova_nota').modal('hide');
                    jQuery('#salvar_nota').removeClass('disabled');
                }, function (response) {
                    console.error("Error trying to retrieve data on /notas/" + _self.idCliente);
                });
            }
        },
        mounted: function () {
            this.idCliente = window.idCliente;
            this.load();
        }
    });
})();

+(function () {
    if (!document.getElementById('notas-planos')) {
        return;
    }

    window.vueNotas = new Vue({
        el: "#notas-planos",
        data: {
            nota: {},
            idPlano: null,
            notas: []
        },
        methods: {
            load: function () {
                var _self = this;
                this.$http.get('/notas-planos/' + this.idPlano).then(function (response) {
                    _self.notas = response.body;
                    Vue.nextTick(function() {
                        $('[data-toggle="tooltip"], .tooltips').tooltip();
                    });

                }, function (response) {
                    console.error("Error trying to retrieve data on /api/v1/notas-planos/" + _self.idPlano);
                });
            },
            salvar: function () {
                jQuery('#salvar_nota').addClass('disabled');
                var _self = this;
                this.$http.post('/notas-planos/' + this.idPlano, this.nota).then(function (response) {
                    this.load();
                    this.nota.corpo = "";
                    jQuery('#nova_nota').modal('hide');
                    jQuery('#salvar_nota').removeClass('disabled');
                }, function (response) {
                    console.error("Error trying to retrieve data on /api/v1/notas-planos/" + _self.idPlano);
                });
            },
            excluirNota: function(n) {
                var _self = this;

                var prompted = prompt('Digite EXCLUIR para executar a ação.');
                if(!prompted || prompted.toUpperCase() !== 'EXCLUIR') {
                    return false;
                }

                this.$http.post('/notas-planos/excluir', {
                    idNota: n.id
                }).then(function (response) {
                    this.load();
                    this.nota.corpo = "";
                    jQuery('#nova_nota').modal('hide');
                    jQuery('#salvar_nota').removeClass('disabled');
                }, function (response) {
                    console.error("Error trying to retrieve data on /api/v1/notas-planos/" + _self.idPlano);
                });
            }
        },
        mounted: function () {
            this.idPlano = window.idPlano;
            this.load();
        }
    });
})();