/**
 * Componente que controla o funcionamento das baixas de parcelas do sistema;
 * @type {*|Vue}
 */
+(function () {
    if (!document.getElementById('cobranca_manual')) {
        return;
    }

    window.vueCobrancaManual = new Vue({
        el: "#cobranca_manual",
        data: {
            incluir_pagamento: false,
            incluir_sf: false,
        },
        computed: {
        },
        methods: {

        },
        mounted: function () {

        }
    });
})();