require('./bootstrap');

import {createApp} from 'vue'

import router from "./router";
import store from "./store";
import App from "./App";

const app = createApp({
    components: {App},
    beforeCreate() {
        this.$store.dispatch('auth/synchronizeAuthenticationState')
    }
})

app.use(router)
app.use(store)

app.mount('#app')
