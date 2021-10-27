import auth from './guards/auth'
import guest from './guards/guest'

const routes = [
    {
        path: '/',
        name: 'home',
        component: () => import('../views/Home'),
        meta: {
            guard: guest
        }
    },
    {
        path: '/login',
        name: 'login',
        component: () => import('../views/Login'),
        meta: {
            guard: guest
        }
    },
    {
        path: '/login/oauth/:provider',
        name: 'login-oauth',
        component: () => import('../views/Login'),
        meta: {
            guard: guest
        }
    },
    {
        path: '/register',
        name: 'register',
        component: () => import('../views/Register'),
        meta: {
            guard: guest
        }
    },
    {
        path: '/password-recovery',
        name: 'password-recovery',
        component: () => import('../views/PasswordRecovery'),
        meta: {
            guard: guest
        }
    },
    {
        path: '/reset-password/:email/:token',
        name: 'reset-password',
        component: () => import('../views/ResetPassword'),
        meta: {
            guard: guest
        }
    },
    {
        path: '/chats',
        name: 'chats',
        component: () => import('../views/Chats'),
        meta: {
            guard: auth
        }
    },
    {
        path: '/profile',
        name: 'profile',
        component: () => import('../views/Profile'),
        meta: {
            guard: auth
        }
    },
    {
        path: '/:catchAll(.*)',
        name: 'not-found',
        component: () => import('../views/NotFound')
    }
]

export default routes
