+(function(){
    const Providers = {
        'postmon' : 'https://api.postmon.com.br/v1/cep/{{cep}}',
        'viacep' : 'https://viacep.com.br/ws/{{cep}}/json/',
        get: function (provider) {
            return this[provider];
        },
        build: function(provider, cep) {
            return provider.replace(/\{\{cep\}\}/g, cep);
        }
    };
    function updateAddressFields(data, mappings) {
        var map = mappings || {
            "bairro"    : "#bairro",
            "localidade": "#cidade",
            "logradouro": "#logradouro",
            "uf"        : "#uf"
        };
        var field = "";
        var f;
        for(field in map) {
            if(map.hasOwnProperty(field)) {
              f = map[field];
              $(f).val(data[field]);
            }
        }
    }

    $(document).ready(function() {

        var $trigger = $('.address-search-trigger');
        $trigger.click(function () {
            var $cep = $(this).closest('form').find('[name=cep]');
            var $url = Providers.build(Providers.viacep, $cep.val());
            $.ajaxSetup({
                'headers': {}
            });
            $.ajax({
                'url': $url,
                'type': 'get',
                'dataType': 'json'
            })
                .done(function(data) {
                    updateAddressFields(data);
                })
                .fail(function() {

                })
                .always(function() {

                });
        });

        var $trigger = $('.address-search-trigger-blur');
        $trigger.blur(function () {
            var $cep = $(this);
            var $url = Providers.build(Providers.viacep, $cep.val());
            $.ajaxSetup({
                'headers': {}
            });
            $.ajax({
                'url': $url,
                'type': 'get',
                'dataType': 'json'
            })
                .done(function(data) {
                    updateAddressFields(data);
                })
                .fail(function() {

                })
                .always(function() {

                });
        });
    });
})();