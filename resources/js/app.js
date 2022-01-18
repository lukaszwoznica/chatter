require('./bootstrap')

import { createApp } from 'vue'
import router from './router'
import store from './store'
import App from './App'
import VTooltip from 'v-tooltip'
import VueCookies from 'vue3-cookies'
import VueLoaders from 'vue-loaders'
import VueSweetalert2 from 'vue-sweetalert2'
import VueGoogleMaps from '@fawmi/vue-google-maps'


const runApp = async () => {
    await store.dispatch('sounds/synchronizeStateWithLocalStorage')
    await store.dispatch('auth/synchronizeAuthenticationState')

    const swalGlobalOptions = {
        buttonsStyling: false,
        customClass: {
            confirmButton: 'button button--primary'
        }
    }

    const googleMapsGlobalOptions = {
        load: {
            key:process.env.MIX_GOOGLE_API_KEY
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
        .use(VueGoogleMaps, googleMapsGlobalOptions)
        .mount('#app')
}

runApp()
