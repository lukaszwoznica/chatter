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
    SET_SELECTED_CONTACT: (state, contact) => state.selectedContact = contact,
    UPDATE_CONTACT: (state, updatedContact) => {
        const index = state.contacts.findIndex(contact => contact.id === updatedContact.id)
        if (index !== -1) {
            state.contacts.splice(index, 1, updatedContact)
        }
    }
}

const actions = {
    async fetchContacts({dispatch, commit, getters, rootGetters}) {
        const authUser = rootGetters['auth/user']

        const response = await axios.get(ApiRoutes.Users.Contacts(authUser.id))

        commit('SET_CONTACTS', response.data.data)
    },

    selectContact({getters, commit}, contactId) {
        const contact = getters.contactById(contactId)
        contact.unread_messages = 0

        commit('UPDATE_CONTACT', contact)
        commit('SET_SELECTED_CONTACT', contact)
    },

    addNewContact({getters, commit}, newContact) {
        commit('ADD_CONTACT', {
            id: newContact.id,
            first_name: newContact.first_name,
            last_name: newContact.last_name,
            email: newContact.email,
            last_online_at: newContact.last_online_at,
            last_message: newContact.last_message ?? null,
            unread_messages: newContact.unread_messages ?? 0
        })
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
