const mix = require("laravel-mix");

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

 mix.webpackConfig({
    resolve: {
        fallback: { "timers": require.resolve("timers-browserify") }
    }
});


mix
  .js("resources/js/app.js", "public/js").vue()
  .sass("resources/sass/app.scss", "public/css")

  // keep as an extra file too for when we use themes
  .sass("resources/sass/extra.scss", "public/css")

  // themes
  .sass("resources/sass/excelsior.scss", "public/css")
  .sass("resources/sass/hotdog.scss", "public/css")
  .sass("resources/sass/halloween.scss", "public/css");

if (process.env.NODE_ENV === "testing") {
  mix.disableNotifications();
} else {
  mix.version();
}
