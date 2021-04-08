import {createStore} from 'vuex'
import auth from './modules/auth'
import contacts from './modules/contacts'
import messages from './modules/messages'

const store = createStore({
    modules: {
        auth,
        contacts,
        messages
    }
})

export default store
