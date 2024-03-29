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
    async fetchContacts({ dispatch, commit, getters, rootGetters }) {
        const authUser = rootGetters['auth/user']

        const response = await axios.get(ApiRoutes.Users.Contacts(authUser.id))

        commit('SET_CONTACTS', response.data.data)
    },

    selectContact({ getters, commit }, contactId) {
        const contact = getters.contactById(contactId)

        commit('UPDATE_CONTACT', contact)
        commit('SET_SELECTED_CONTACT', contact)
    },

    addNewContact({ commit }, newContact) {
        commit('ADD_CONTACT', {
            ...newContact,
            last_message: newContact.last_message ?? null,
            unread_messages: newContact.unread_messages ?? 0
        })
    },

    updateContact({ getters, commit }, contactData) {
        let contact = getters.contactById(contactData.id)
        if (!contact) {
            return
        }

        contact = {
            ...contact,
            ...contactData
        }

        if (contact.id === getters.selectedContact?.id) {
            commit('SET_SELECTED_CONTACT', contact)
        }
        commit('UPDATE_CONTACT', contact)
    },

    resetModuleState({ commit }) {
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
