const {mix} = require('laravel-mix');
const argv = require('yargs').argv;

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

// false if not defined, true if defined
let full = false;
if (typeof argv.env !== 'undefined' && typeof argv.env.full !== 'undefined') {
    full = argv.env.full;
}

// Custom processing only
mix.styles(['resources/assets/css/**/*.css'], 'public/css/custom.css')
    .combine([
        // Doesn't depend on anything
        'resources/assets/js/custom/constants.js',
        // Include in proper order
        'resources/assets/js/custom/util.js',
        'resources/assets/js/custom/signalable.js',
        'resources/assets/js/custom/dungeonmap.js',
        'resources/assets/js/custom/mapcontrols.js',
        'resources/assets/js/custom/mapobject.js',
        'resources/assets/js/custom/enemy.js',
        'resources/assets/js/custom/enemypack.js',
        'resources/assets/js/custom/admin/enemyattaching.js',
        'resources/assets/js/custom/admin/admindungeonmap.js',
        'resources/assets/js/custom/admin/adminenemy.js',
        'resources/assets/js/custom/admin/adminenemypack.js',
        // Include the rest
        // 'resources/assets/js/custom/**/*.js'
    ], 'public/js/custom.js');
// .combine(, 'public/js/custom.js');

if (full) {
    mix.js('resources/assets/js/app.js', 'public/js')
        .sass('resources/assets/sass/app.scss', 'public/css')
        // Lib processing
        .styles(['resources/assets/lib/**/*.css'], 'public/css/lib.css')
        .combine('resources/assets/lib/**/*.js', 'public/js/lib.js');
}

mix.sourceMaps();

if (mix.inProduction()) {
    // Copies all tiles as well which takes a while
    mix.copy('resources/assets/images', 'public/images', false);
} else {
    mix.copy('resources/assets/images/lib', 'public/images/lib', false);
    mix.copy('resources/assets/images/classes', 'public/images/classes', false);
    // mix.copy('resources/assets/images/test', 'public/images/test', false);
}