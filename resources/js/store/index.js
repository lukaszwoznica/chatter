import {createStore} from 'vuex'
import auth from './modules/auth'
import contacts from './modules/contacts'
import messages from './modules/messages'
import sounds from './modules/sounds'

const store = createStore({
    modules: {
        auth,
        contacts,
        messages,
        sounds
    },
})

export default store
