/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require("./bootstrap");

window.Vue = require("vue");

require("./nexus");
/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// Vue.component('example-component', require('./components/ExampleComponent.vue'));

Vue.component("userlist", require("./components/Userlist.vue"));
Vue.component("post-compose", require("./components/PostCompose.vue"));
Vue.component("post-preview", require("./components/PostPreview.vue"));
Vue.component("rendered-text", require("./components/RenderedText.vue"));
Vue.component("topic-jump", require("./components/TopicJump.vue"));
// Vue.component('mentions-list', require('./components/Mentions.vue'));
// const files = require.context('./', true, /\.vue$/i)

// files.keys().map(key => {
//     return Vue.component(_.last(key.split('/')).split('.')[0], files(key))
// })

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
  el: "#app"
});

const navigationApp = new Vue({
  el: "#navigationApp"
});

// window.$ = window.jQuery = require('jquery');