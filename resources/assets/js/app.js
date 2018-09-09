
/**
 * First we will load all of this project's JavaScript dependencies which
 * include Vue and Vue Resource. This gives a great starting point for
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

/**
* custom nexus js
*/
require('./nexus');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('userlist', require('./components/Userlist.vue'));
Vue.component('post-compose', require('./components/PostCompose.vue'));
Vue.component('post-preview', require('./components/PostPreview.vue'));
Vue.component('rendered-text', require('./components/RenderedText.vue'));

const app = new Vue({
    el: '#app'
});
