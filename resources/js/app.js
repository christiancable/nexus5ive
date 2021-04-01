/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require("./bootstrap");

import Vue from 'vue';

require("./nexus");
/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// Vue.component('example-component', require('./components/ExampleComponent.vue'));

Vue.component("userlist", require("./components/Userlist.vue").default);
Vue.component("post-compose", require("./components/PostCompose.vue").default);
Vue.component("post-preview", require("./components/PostPreview.vue").default);
Vue.component("chat-list", require("./components/chat/ChatList.vue").default);
Vue.component("chat-new", require("./components/chat/ChatNew.vue").default);
Vue.component("chat-start", require("./components/chat/ChatStart.vue").default);
Vue.component("chat-post", require("./components/chat/ChatPost.vue").default);
Vue.component(
  "chat-conversation",
  require("./components/chat/ChatConversation.vue").default
);
Vue.component(
  "chat-message",
  require("./components/chat/ChatMessage.vue").default
);
Vue.component(
  "rendered-text",
  require("./components/RenderedText.vue").default
);
Vue.component("search-menu", require("./components/SearchMenu.vue").default);
// Vue.component('mentions-list', require('./components/Mentions.vue'));
// const files = require.context('./', true, /\.vue$/i)

// files.keys().map(key => {
//     return Vue.component(_.last(key.split('/')).split('.')[0], files(key))
// })

/**t
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
