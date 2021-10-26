import {createRouter, createWebHistory} from 'vue-router'
import routes from "./routes"
import store from "../store";

const router = createRouter({
    history: createWebHistory(process.env.APP_URL),
    routes
})

router.beforeEach((to, from, next) => {
    const guard = to.meta.guard
    if (!guard) {
        return next()
    }

    return guard({
        to, from, next, store
    })
})

// Disable route transitions after login, logout and first load
router.afterEach((to, from) => {
    if (from.meta.guard?.name !== to.meta.guard?.name) {
        to.meta.transitionName = 'none'
    } else {
        to.meta.transitionName = 'route-change'
    }
})

export default router
