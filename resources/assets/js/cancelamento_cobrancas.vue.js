/**
 * Componente que controla o funcionamento das baixas de parcelas do sistema;
 * @type {*|Vue}
 */
+(function () {
    if (!document.getElementById('nova_baixa')) {
        return;
    }

    window.vueCancelamentoCobrancas = new Vue({
        el: "#nova_baixa",
        data: {
            is_acordo: false,
            acordo: null,
            justificativa: "",
            cobrancas: [],
            selectedCobrancas: []
        },
        computed: {
            cobrancasNaoSelecionadas: function() {
                var selected = this.selectedCobrancas;
                return this.cobrancas.filter(function(c) {
                    var has = !(selected.indexOf(c.id+"") > -1);
                    return has;
                });
            }
        },
        methods: {
            buildSelectAcordo: function () {
                if(this.is_acordo) {
                    Vue.nextTick(function() {
                        $('#acordo').select2();
                    });
                }
            },
            refreshSelect: function() {
                Vue.nextTick(function() {
                    $('#acordo').select2();
                });
            }
        },
        mounted: function () {
            this.cobrancas = window.cobrancas;
        }
    });
})();