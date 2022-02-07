require('./bootstrap')

import { createApp } from 'vue'
import router from './router'
import store from './store'
import App from './App'
import VueCookies from 'vue3-cookies'
import VueLoaders from 'vue-loaders'
import VueSweetalert2 from 'vue-sweetalert2'
import VueGoogleMaps from '@fawmi/vue-google-maps'
import VueTippy from 'vue-tippy'
import PluginsOptions from './plugins/pluginsOptions'


const runApp = async () => {
    await store.dispatch('sounds/synchronizeStateWithLocalStorage')
    await store.dispatch('auth/synchronizeAuthenticationState')

    const app = createApp({
        components: { App }
    })

    app.use(router)
        .use(store)
        .use(VueCookies)
        .use(VueLoaders)
        .use(VueSweetalert2, PluginsOptions.sweetAlertOptions)
        .use(VueGoogleMaps, PluginsOptions.googleMapsOptions)
        .use(VueTippy, PluginsOptions.vueTippyOptions)
        .mount('#app')
}

runApp()
