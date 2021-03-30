require('./bootstrap');

import Vue from 'vue'
import VueRouter from 'vue-router'

import router from "./router/index";
import App from "./App";

Vue.use(VueRouter)

const app = new Vue({
    el: '#app',
    router,
    components: { App }
})
