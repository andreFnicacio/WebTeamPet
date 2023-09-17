
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

// require('./bootstrap');
var Vue = require('vue');
window.Vue = Vue;
var vueResource = require('vue-resource');
Vue.use(vueResource);
Vue.http.headers.common['X-CSRF-TOKEN'] = $('meta[name="csrf-token"]').attr('content');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
//
// Vue.component('example', require('./components/Example.vue'));
//
// const app = new Vue({
//     el: '#app'
// });

+(function() {
    $(document).ready(function() {
        $.ajaxSetup({
            // headers: {
            //     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            // }
        });

        /**
         * @name: SetupPageActions
         */
        (function() {
            var $items = $('.page-actions .dropdown-menu.searchable-toggle a');
            var $currentSearchable = $('#current-searchable');
            $items.click(function() {
                var $_self = $(this);
                var $title = $_self.find('span.title').html();
                $currentSearchable.text($title);
                $currentSearchable.data('search', $_self.data('search'));
            });
        })();

        /**
         * Setup DatePickers
         */
        (function() {
            var $datepickers = $('.date.date-picker.input-group');

            $datepickers.dblclick(function() {
                var self = $(this);
                var today = new Date();
                var dd = today.getDate();
                var mm = today.getMonth()+1; //January is 0!
                var yyyy = today.getFullYear();
                if(dd<10){
                    dd='0'+dd;
                }
                if(mm<10){
                    mm='0'+mm;
                }
                var todayString = dd+'/'+mm+'/'+yyyy;

                self.datepicker("setDate", todayString);
            })
        })();

        /**
         * Disable mouse scrolling increments over input[type=number]
         */
        (function() {
            $('form').on('focus', 'input[type=number]', function (e) {
                $(this).on('mousewheel.disableScroll', function (e) {
                    e.preventDefault()
                });
            }).on('blur', 'input[type=number]', function (e) {
                $(this).off('mousewheel.disableScroll');
            });
        })();
    });
})();

