+(function () {
    if (!document.getElementById('LogViewer')) {
        return;
    }
    //Vue.use(VueTheMask);

    window.vueLogViewer = new Vue({
        el: "#LogViewer",
        data: {
            api: 'log/api',
            selection: {
                area:    null,
                event:       null,
                priority: null,
            },
            logs: [],
            isLoading:    false,
            select2Elements: {
                area: null,
                event: null,
                priority: null
            },
            area: [
                "A"
            ],
            event: [
                "E"
            ],
            priority: [
                "P"
            ],

        },
        methods: {
            load: function () {
                this.init();
            },
            init: function() {
                this.selects();
            },
            selects: function() {
                $('select.materialize').formSelect();
            },
            select2: function(placeholder, element) {
                // if(this.select2Elements[element]) {
                //     this.select2Elements[element].select2('destroy');
                // }
                // this.select2Elements[element] =  $('.select2-vue-'+element)
                //     .select2({
                //         placeholder: placeholder
                //     })
                //     .on('select2:select', function (e) {
                //         this.dispatchEvent(new Event('change', { target: e.target }));
                //     });
            },
            loadDepartments: function() {
                var params = {};
                var _self = this;
                this.$http.get(this.api + '/departamentos', {
                    params: params
                }).then(function (response) {
                    _self.departments = response.body;
                    _self.$nextTick(function() {
                        _self.init();
                        _self.select2('DEPARTAMENTOS', 'departments');
                    });
                }, function (response) {
                    console.error(response);
                    console.error("Error trying to retrieve data on " + _self.api + '/departamentos');
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
            refresh: function() {

            }
        },
        computed: {

        },
        mounted: function () {
            this.load();
        }
    });
})();