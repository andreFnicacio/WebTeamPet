/**
 * Componente que controla o comportamento da barra superior de busca;
 * @type {*|Vue}
 */
+(function () {
    if (!document.getElementById('page-top')) {
        return;
    }
    window.topSearch = new Vue({
        el: "#page-top",
        data: {
            searchables: [],
            current: {}
        },
        methods: {
            loadSearchables: function () {
                this.$http.get('/api/v1/searchables').then(function (response) {
                    this.searchables = response.body;
                    if (this.searchables.length > 0) {
                        var selected = this.searchables[0];
                        var $group = document.querySelector('[name="route-group"]');
                        if (typeof $group !== "undefined") {
                            $group = $group.content;
                            var found = this.searchables.find(function (s) {
                                return s.name === $group;
                            });
                            if (found) {
                                selected = found;
                            }
                        }
                        this.setCurrent(selected);
                    }
                }, function (response) {
                    console.error("Error trying to retrieve data on '/searchables'");
                });
            },
            setCurrent: function (searchable) {
                this.current = searchable;
            }
        },
        mounted: function () {
            this.loadSearchables();
        }
    });
})();