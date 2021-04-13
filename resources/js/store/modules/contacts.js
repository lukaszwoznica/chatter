import ApiRoutes from '../../api/routes';

const state = {
    contacts: [],
    selectedContact: null
}

const getters = {
    allContacts: state => state.contacts,
    selectedContact: state => state.selectedContact,
    contactById: state => contactId => {
        return state.contacts.find(contact => contact.id === contactId)
    }
}

const mutations = {
    SET_CONTACTS: (state, contacts) => state.contacts = contacts,
    ADD_CONTACT: (state, contact) => state.contacts.unshift(contact),
    SET_SELECTED_CONTACT: (state, contact) => state.selectedContact = contact
}

const actions = {
    async fetchContacts({commit}) {
        const response = await axios.get(ApiRoutes.Users.Contacts)

        commit('SET_CONTACTS', response.data.data)
    },

    selectContact({getters, commit}, contactId) {
        const contact = getters.contactById(contactId)

        commit('SET_SELECTED_CONTACT', contact)
    },

    addNewContact({getters, commit}, newContact) {
        commit('ADD_CONTACT', {
            id: newContact.id,
            first_name: newContact.first_name,
            last_name: newContact.last_name,
            email: newContact.email,
            last_message: newContact.last_message ?? null,
            unread_messages: newContact.unread_messages ?? 0
        })
    },

    setContactOnTop({getters, dispatch, commit}, topContact) {
        const contacts = getters.allContacts.filter(contact => contact.id !== topContact.id)

        commit('SET_CONTACTS', contacts)
        dispatch('addNewContact', topContact)
    },

    resetModuleState({commit}) {
        commit('SET_CONTACTS', [])
        commit('SET_SELECTED_CONTACT', null)
    }
}

export default {
    namespaced: true,
    state,
    getters,
    mutations,
    actions,
}
