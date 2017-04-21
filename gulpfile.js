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
    mix.sass('app.scss')
       .webpack('app.js');
    mix.sass('extra.scss', 'public/css/extra.css');
    mix.sass('spooky.scss', 'public/css/spooky.css');
    mix.copy('node_modules/bootstrap-sass/assets/fonts/bootstrap','public/fonts/bootstrap');
});
