require('./bootstrap')

import { createApp } from 'vue'
import router from './router'
import store from './store'
import App from './App'
import VTooltip from 'v-tooltip'
import VueCookies from 'vue3-cookies'
import VueLoaders from 'vue-loaders'
import VueSweetalert2 from 'vue-sweetalert2'


store.dispatch('auth/synchronizeAuthenticationState').then(() => {
    const swalGlobalOptions = {
        buttonsStyling: false,
        customClass: {
            confirmButton: 'button button--primary'
        }
    }

    const app = createApp({
        components: { App }
    })

    app.use(router)
        .use(store)
        .use(VTooltip, { disposeTimeout: 100 })
        .use(VueCookies)
        .use(VueLoaders)
        .use(VueSweetalert2, swalGlobalOptions)
        .mount('#app')
})
