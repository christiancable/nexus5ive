const elixir = require('laravel-elixir');

require('laravel-elixir-vue-2');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for your application as well as publishing vendor resources.
 |
 */

elixir((mix) => {
    mix.sass(['app.scss', 'extra.scss'])
        .webpack('app.js');

    // we keep the extra styles as an additional file so it can be added to any custom theme in use
    // but if we're not using a them this is ignored in the master layout
    mix.sass('extra.scss', 'public/css/extra.css');
    mix.copy('node_modules/bootstrap-sass/assets/fonts/bootstrap','public/fonts/bootstrap');
});
