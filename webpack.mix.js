let mix = require('laravel-mix');

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
    'public/js/app.js',
    'resources/assets/js/bezier.js',
    'resources/assets/js/bootstrap-timepicker.min.js',
    'resources/assets/js/datepicker-ui.min.js',
    'resources/assets/js/eviction.js',
    'resources/assets/js/home.js',
    'resources/assets/js/json2.min.js',
    'resources/assets/js/magistrateCreator.js',
    'resources/assets/js/numeric-1.2.6.min.js',
    'resources/assets/js/signaturepad.js',
    'resources/assets/js/timepicker.min.js',
    'resources/assets/js/userManagerment.js'
], 'public/js/courtzip.js').version()
    .js('resources/assets/js/app.js', 'public/js/courtzip.js')
    .sass('resources/assets/sass/app.scss', 'public/css')
    .styles([
        'public/css/app.css',
        'resources/assets/css/bootstrap-timepicker.min.css',
        'resources/assets/css/dashboard.css',
        'resources/assets/css/datatables.min.css',
        'resources/assets/css/datepicker.structure.min.css',
        'resources/assets/css/datepicker.theme.min.css',
        'resources/assets/css/datepicker-ui.min.css',
        'resources/assets/css/eviction.css',
        'resources/assets/css/signaturepad.css',
        'resources/assets/css/timepicker.min.css'
    ], 'public/css/app.css').version();
