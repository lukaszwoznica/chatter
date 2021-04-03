import Home from '../views/Home'
import Login from "../views/Login"
import Register from "../views/Register";
import Chats from "../views/Chats";

const routes = [
    {
        path: '/',
        name: 'home',
        component: Home,
    },
    {
        path: '/login',
        name: 'login',
        component: Login,
    },
    {
        path: '/register',
        name: 'register',
        component: Register,
    },
    {
        path: '/chats',
        name: 'chats',
        component: Chats,
    },
]

export default routes
