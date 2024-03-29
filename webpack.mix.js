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
     'resources/assets/js/bootstrap.js',
     'resources/assets/js/json2.min.js',
     'node_modules/select2/dist/js/select2.js',
     // 'resources/assets/js/bootstrap.bundle.min.js',
     'resources/assets/js/jquery.slim.min.js',
     'resources/assets/js/timepicker.min.js',
     'resources/assets/js/datepicker-ui.min.js',
     'resources/assets/js/eviction.js',
     'resources/assets/js/datatables.min.js',
     'resources/assets/js/magistrateCreator.js',
     'resources/assets/js/userManagement.js',
     'resources/assets/js/numeric-1.2.6.min.js',
     'resources/assets/js/bezier.js',
     'resources/assets/js/signaturepad.js',
     'resources/assets/js/bootstrap-timepicker.min.js',
     'resources/assets/js/home.js',
     'resources/assets/js/newFile.js',
     'resources/assets/js/getFileFee.js',
     'resources/assets/js/stripe.js',
     'resources/assets/js/generalAdmin.js',
     'resources/assets/js/feeDuplicator.js',
     'resources/assets/js/countyAdmin.js'

 ], 'public/js/app.js').version()
//mix.js('resources/assets/js/app.js', 'public/js')
    .sass('resources/assets/sass/app.scss', 'public/css')
    .styles([
        'public/css/app.css',
        'resources/assets/css/bootstrap-timepicker.min.css',
        'resources/assets/css/bootstrap-reboot.min.css',
        'resources/assets/css/landing-page.min.css',
        'resources/assets/css/simple-line-icons.css',
        'resources/assets/css/timepicker.min.css',
        'resources/assets/css/dashboard.css',
        'resources/assets/css/datatables.min.css',
        'resources/assets/css/datepicker.structure.min.css',
        'resources/assets/css/datepicker.theme.min.css',
        'resources/assets/css/datepicker-ui.min.css',
        'resources/assets/css/eviction.css',
        'resources/assets/css/signaturepad.css',
        'resources/assets/css/stripe.css',
        'node_modules/select2/dist/css/select2.css'

    ], 'public/css/app.css').version();
