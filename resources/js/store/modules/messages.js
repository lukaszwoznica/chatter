import ApiRoutes from '../../api/routes'

const state = {
    messages: []
}

const getters = {
    allMessages: state => state.messages
}

const mutations = {
    SET_MESSAGES: (state, messages) => state.messages = messages,
    ADD_MESSAGE: (state, message) => state.messages.push(message),
    UPDATE_MESSAGE: (state, updatedMessage) => {
        const index = state.messages.findIndex(message => message.id === updatedMessage.id)
        if (index !== -1) {
            state.messages.splice(index, 1, updatedMessage)
        }
    }
}

const actions = {
    async fetchMessages({commit}, userId) {
        const response = await axios.get(ApiRoutes.Messages.GetConversationMessages(userId))

        commit('SET_MESSAGES', response.data.data)
    },

    sendMessage({commit}, message) {
        return new Promise(async  (resolve) => {
            const response = await axios.post(ApiRoutes.Messages.SendMessage, message)

            if (response.status === 201) {
                commit('ADD_MESSAGE', response.data.data)
                resolve(response.data.data)
            }
        })
    },

    async markMessageAsRead({commit}, messageId) {
        const response = await axios.patch(ApiRoutes.Messages.MarkMessageAsRead(messageId))

        if (response.status === 200) {
            commit('UPDATE_MESSAGE', response.data.data)
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
