import ApiRoutes from '../../api/routes';

const state = {
    contacts: [],
    selectedContact: null
}

const getters = {
    allContacts: state => state.contacts,
    selectedContact: state => state.selectedContact
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

    selectContact({state, commit}, contactId) {
        const contact = state.contacts.find(contact => contact.id === contactId)

        commit('SET_SELECTED_CONTACT', contact)
    }
}

export default {
    namespaced: true,
    state,
    getters,
    mutations,
    actions,
}
