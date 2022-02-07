import { createRouter, createWebHistory } from 'vue-router'
import routes from './routes'
import store from '../store'

const router = createRouter({
    history: createWebHistory(process.env.APP_URL),
    routes
})

router.beforeEach((to, from, next) => {
    document.title = to.meta.title ?? document.title
    const guard = to.meta.guard

    if (!guard) {
        return next()
    }

    return guard({
        to, from, next, store
    })
})

router.afterEach((to, from) => {
    if (from.meta.guard?.name !== to.meta.guard?.name ||  to.meta.guard?.name === 'auth') {
        to.meta.transitionName = 'none'
    } else {
        to.meta.transitionName = 'route-change'
    }
})

export default router
