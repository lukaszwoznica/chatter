require('./bootstrap')

import {createApp} from 'vue'

import router from './router'
import store from './store'
import App from './App'

store.dispatch('auth/synchronizeAuthenticationState').then(() => {
    const app = createApp({
        components: {App}
    })

    app.use(router)
        .use(store)
        .mount('#app')
})
