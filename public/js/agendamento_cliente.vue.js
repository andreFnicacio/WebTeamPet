+(function () {
    if (!document.getElementById('AgendamentoCliente')) {
        return;
    }
    //Vue.use(VueTheMask);

    window.vueTimesheet = new Vue({
        el: "#AgendamentoCliente",
        data: {
            api: '/api/v1/',
            selection: {
                cidade:    null,
                credenciado: null
            },
            id_guia: null,
            cidades: [],
            credenciados:    [],
            isSaving:    false,
            select2Elements: {
                clinicas: null,
            }
        },
        methods: {
            load: function () {
                this.loadCidades();
                this.loadCredenciados();
                this.init();
            },
            init: function() {
                this.selects();
            },
            selects: function() {
                //$('select.materialize').formSelect();
            },
            select2: function(placeholder, element) {
                if(this.select2Elements[element]) {
                    this.select2Elements[element].select2('destroy');
                }
                this.select2Elements[element] =  $('.select2-vue-'+element)
                    .select2({
                        placeholder: placeholder
                    })
                    .on('select2:select', function (e) {
                        this.dispatchEvent(new Event('change', { target: e.target }));
                    });
            },
            loadCidades: function() {
                var params = {};
                var _self = this;
                this.$http.get(this.api + 'credenciados/cidades', {
                    params: params
                }).then(function (response) {
                    _self.cidades = response.body;
                    _self.$nextTick(function() {
                        _self.select2('CIDADES', 'cidades');
                    });
                }, function (response) {
                    console.error(response);
                    console.error("Error trying to retrieve data on " + _self.api + '/credenciados/cidades');
                });
            },
            loadCredenciados: function() {
                var params = {};
                var _self = this;
                this.$http.get(this.api + 'credenciados', {
                    params: params
                }).then(function (response) {
                    _self.credenciados = response.body;
                }, function (response) {
                    console.error(response);
                    console.error("Error trying to retrieve data on " + _self.api + '/credenciados');
                });
            },
            escolher: function(credenciado) {
                var _self = this;
                this.selection.credenciado = credenciado;
                return this.$http.post(this.api + 'credenciados/agendamentos/atribuir', {
                    id_credenciado: credenciado.id,
                    id_guia: this.id_guia
                }).then(function(response) {
                    _self.openModal('#Agendamentos--modal-sucesso');
                    _self.record.project = null;
                }, function(response) {
                    console.error(response);
                });
            },
            openModal: function(modal) {
                var $modal = $(modal);
                if($modal.length > 0) {
                    $(modal).modal('show');
                }
            },
            closeModal: function(modal) {
                var $modal = $(modal);
                if($modal.length > 0) {
                    $modal.modal('hide');
                }
            },
        },
        computed: {
            filteredCredenciados: function() {
                var _self = this;
                return this.credenciados.filter(function(c) {
                    return c.endereco.indexOf(_self.selection.cidade) > 0;
                });
            }
        },
        mounted: function () {
            this.id_guia = document.getElementById('id_guia').value;
            this.load();
        }
    });
})();