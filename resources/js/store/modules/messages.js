import ApiRoutes from '../../api/routes'

const state = {
    messages: []
}

const getters = {
    allMessages: state => state.messages
}

const mutations = {
    SET_MESSAGES: (state, messages) => state.messages = messages,
    ADD_MESSAGE: (state, message) => state.messages.push(message)
}

const actions = {
    async fetchMessages({commit}, userId) {
        const response = await axios.get(ApiRoutes.Messages.GetConversationMessages(userId))

        commit('SET_MESSAGES', response.data.data)
    },

    async sendMessage({commit}, message) {
        const response = await axios.post(ApiRoutes.Messages.SendMessage, message)

        if (response.status === 201) {
            commit('ADD_MESSAGE', response.data.data)
        }
    },

    resetModuleState({commit}) {
        commit('SET_MESSAGES', [])
    }
}

export default {
    namespaced: true,
    state,
    getters,
    mutations,
    actions,
}
