import auth from './guards/auth'
import guest from './guards/guest'

const routes = [
    {
        path: '/',
        name: 'home',
        component: () => import('../views/Home'),
        meta: {
            guard: guest,
            title: 'Chatter'
        }
    },
    {
        path: '/login',
        name: 'login',
        component: () => import('../views/Login'),
        meta: {
            guard: guest,
            title: 'Login to Chatter'
        }
    },
    {
        path: '/login/oauth/:provider',
        name: 'login-oauth',
        component: () => import('../views/Login'),
        meta: {
            guard: guest,
            title: 'Login to Chatter'
        }
    },
    {
        path: '/register',
        name: 'register',
        component: () => import('../views/Register'),
        meta: {
            guard: guest,
            title: 'Join Chatter'
        }
    },
    {
        path: '/password-recovery',
        name: 'password-recovery',
        component: () => import('../views/PasswordRecovery'),
        meta: {
            guard: guest,
            title: 'Forgotten password - Chatter'
        }
    },
    {
        path: '/reset-password/:email/:token',
        name: 'reset-password',
        component: () => import('../views/ResetPassword'),
        meta: {
            guard: guest,
            title: 'Reset password - Chatter'
        }
    },
    {
        path: '/chats',
        name: 'chats',
        component: () => import('../views/Chats'),
        meta: {
            guard: auth,
            title: 'Chatter'
        }
    },
    {
        path: '/profile',
        name: 'profile',
        component: () => import('../views/Profile'),
        meta: {
            guard: auth,
            title: 'Your profile - Chatter'
        }
    },
    {
        path: '/:catchAll(.*)',
        name: 'not-found',
        component: () => import('../views/NotFound'),
        meta: {
            title: 'Page not found - Chatter'
        }
    }
]

export default routes
