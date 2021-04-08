import ApiRoutes from '../../api/routes'

const state = {
    messages: []
}

const getters = {
    allMessages: state => state.messages
}

const mutations = {
    SET_MESSAGES: (state, messages) => state.messages = messages
}

const actions = {
    async fetchMessages({commit}, userId) {
        const response = await axios.get(ApiRoutes.Messages.GetConversationMessages(userId))

        commit('SET_MESSAGES', response.data.data)
    }
}

export default {
    namespaced: true,
    state,
    getters,
    mutations,
    actions,
}
