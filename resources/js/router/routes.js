import Home from '../views/Home'
import Login from '../views/Login'
import Register from '../views/Register'
import Chats from '../views/Chats'
import NotFound from '../views/NotFound'

import auth from './guards/auth'
import guest from './guards/guest'

const routes = [
    {
        path: '/',
        name: 'home',
        component: Home,
        meta: {
            guard: guest
        }
    },
    {
        path: '/login',
        name: 'login',
        component: Login,
        meta: {
            guard: guest
        }
    },
    {
        path: '/register',
        name: 'register',
        component: Register,
        meta: {
            guard: guest
        }
    },
    {
        path: '/chats',
        name: 'chats',
        component: Chats,
        meta: {
            guard: auth
        }
    },
    {
        path: '/:catchAll(.*)',
        name: 'not-found',
        component: NotFound
    }
]

export default routes
