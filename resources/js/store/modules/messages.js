import ApiRoutes from '../../api/routes'

const state = {
    messages: [],
    page: 1,
    lastPage: null
}

const getters = {
    allMessages: state => state.messages,
    currentPage: state => state.page,
    lastPage: state => state.lastPage
}

const mutations = {
    SET_MESSAGES: (state, messages) => state.messages = messages,
    ADD_MESSAGE: (state, message) => state.messages.push(message),
    ADD_MESSAGES_TO_FRONT: (state, messages) => state.messages.unshift(...messages),
    UPDATE_MESSAGE: (state, updatedMessage) => {
        const index = state.messages.findIndex(message => message.id === updatedMessage.id)
        if (index !== -1) {
            state.messages.splice(index, 1, updatedMessage)
        }
    },
    SET_PAGE: (state, page) => state.page = page,
    SET_LAST_PAGE: (state, lastPage) => state.lastPage = lastPage
}

const actions = {
    async fetchMessages({ getters, commit }, { userId, infiniteLoaderContext }) {
        if (getters.lastPage !== null && getters.currentPage > getters.lastPage) {
            infiniteLoaderContext.complete()
            return
        }

        const response = await axios.get(ApiRoutes.Messages.GetConversationMessages(userId, getters.currentPage))
        if (response.data.data.length) {
            commit('SET_PAGE', getters.currentPage + 1)
            commit('SET_LAST_PAGE', response.data.meta.last_page)
            commit('ADD_MESSAGES_TO_FRONT', response.data.data.reverse())
            infiniteLoaderContext.loaded()
        } else {
            infiniteLoaderContext.complete()
        }
    },

    sendMessage({ commit }, message) {
        return new Promise(async (resolve) => {
            const response = await axios.post(ApiRoutes.Messages.SendMessage, message)

            if (response.status === 201) {
                commit('ADD_MESSAGE', response.data.data)
                resolve(response.data.data)
            }
        })
    },

    async markMessageAsRead({ commit }, messageId) {
        const response = await axios.patch(ApiRoutes.Messages.MarkMessageAsRead(messageId))

        if (response.status === 200) {
            commit('UPDATE_MESSAGE', response.data.data)
        }
    },

    setAllPreviousMessagesAsRead({ getters, commit }, latestReadMessage) {
        getters.allMessages.filter(message => {
            return !message.read_at &&
                message.recipient_id === latestReadMessage.recipient_id &&
                Date.parse(message.created_at) <= Date.parse(latestReadMessage.created_at)
        }).map(message => {
            return {
                ...message,
                read_at: latestReadMessage.read_at
            }
        }).forEach(message => commit('UPDATE_MESSAGE', message))
    },

    resetModuleState({ commit }) {
        commit('SET_MESSAGES', [])
        commit('SET_PAGE', 1)
        commit('SET_LAST_PAGE', null)
    }
}

export default {
    namespaced: true,
    state,
    getters,
    mutations,
    actions,
}
