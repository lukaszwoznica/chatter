require('./bootstrap')

import { createApp } from 'vue'
import router from './router'
import store from './store'
import App from './App'
import VTooltip from 'v-tooltip'

import { library } from '@fortawesome/fontawesome-svg-core'
import { faArrowRight, faSearch, faCommentDots, faSpinner } from '@fortawesome/free-solid-svg-icons'
import { faCircle, faCheckCircle, faFrown } from '@fortawesome/free-regular-svg-icons'

library.add(faArrowRight, faSearch, faCircle, faCheckCircle, faCommentDots, faSpinner, faFrown)

store.dispatch('auth/synchronizeAuthenticationState').then(() => {
    const app = createApp({
        components: { App }
    })

    app.use(router)
        .use(store)
        .use(VTooltip, { disposeTimeout: 100 })
        .mount('#app')
})
