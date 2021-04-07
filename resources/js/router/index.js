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

export default router
