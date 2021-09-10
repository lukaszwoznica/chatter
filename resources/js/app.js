require('./bootstrap')

import {createApp} from 'vue'
import router from './router'
import store from './store'
import App from './App'
import {library} from '@fortawesome/fontawesome-svg-core'
import {faArrowRight, faSearch} from '@fortawesome/free-solid-svg-icons'

library.add(faArrowRight, faSearch)

store.dispatch('auth/synchronizeAuthenticationState').then(() => {
    const app = createApp({
        components: {App}
    })

    app.use(router)
        .use(store)
        .mount('#app')
})
