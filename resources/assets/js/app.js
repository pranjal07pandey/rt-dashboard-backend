/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
import LabelComponent from "./components/invoiceManager/label/LabelComponent";
import DocketCreate from "./components/docket/create";
import Vue from 'vue';
import Vuetify from 'vuetify';
import Multiselect from 'vue-multiselect'
import axios from 'axios'
import 'vuetify/dist/vuetify.min.css'

require('./bootstrap');
window.Vue = require('vue');




/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

// Vue.component('example-component', require('./components/Example.vue'));
Vue.component('invoicelabel-component', require('./components/invoiceManager/label/LabelComponent.vue').default);
Vue.component('docket-create-component', require('./components/docket/create.vue').default);
Vue.component('docket-edit-component', require('./components/docket/edit.vue').default);
Vue.component('pulse-loader', require('vue-spinner/src/PulseLoader.vue').default);
Vue.component('assign-docket-component', require('./components/assignDocket/assign.vue').default);
Vue.component('multiselect', Multiselect);
Vue.component('vuetify', Vuetify);
Vue.use(Vuetify);
const vuetify = new Vuetify();

var app = new Vue({
    el: '#app',
    components: {  LabelComponent,DocketCreate,Multiselect

    },
    vuetify,

});