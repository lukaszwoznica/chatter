import Home from '../views/Home'
import Login from '../views/Login'
import Register from '../views/Register'
import PasswordRecovery from '../views/PasswordRecovery'
import ResetPassword from '../views/ResetPassword'
import Chats from '../views/Chats'
import Profile from '../views/Profile'
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
        path: '/login/oauth/:provider',
        name: 'login-oauth',
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
        path: '/password-recovery',
        name: 'password-recovery',
        component: PasswordRecovery,
        meta: {
            guard: guest
        }
    },
    {
        path: '/reset-password/:email/:token',
        name: 'reset-password',
        component: ResetPassword,
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
        path: '/profile',
        name: 'profile',
        component: Profile,
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
