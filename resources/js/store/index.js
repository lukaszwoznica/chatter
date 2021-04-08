import {createStore} from 'vuex'
import auth from './modules/auth'
import contacts from './modules/contacts'

const store = createStore({
    modules: {
        auth,
        contacts
    }
})

export default store
