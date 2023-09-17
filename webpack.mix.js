const { mix } = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js([
    'resources/assets/js/app.js',
    'resources/assets/js/topsearch.vue.js',
    'resources/assets/js/pets_planos.vue.js',
    'resources/assets/js/address-search.js',
    'resources/assets/js/emissor_guia.vue.js',
    'resources/assets/js/notas.vue.js',
    'resources/assets/js/procedimentos_planos.vue.js',
    'resources/assets/js/cancelamento_cobrancas.vue.js',
    'resources/assets/js/cobranca_manual.vue.js',
    'resources/assets/js/managed_datatables.js',
], 'public/js')
//.version()
;

mix.less('resources/assets/less/app.less', 'public/css');

