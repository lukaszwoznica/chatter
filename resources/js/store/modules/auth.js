import ApiRoutes from "../../api/routes";

const state = {
    authenticated: false,
    user: null,
}

const getters = {
    isAuthenticated: state => state.authenticated,
    user: state => state.user,
}

const mutations = {
    SET_AUTHENTICATED: (state, value) => state.authenticated = value,
    SET_USER: (state, user) => state.user = user,
}

const actions = {
    async login({dispatch}, credentials) {
        await axios.get(ApiRoutes.GetCsrfCookie)
        await axios.post(ApiRoutes.Auth.Login, credentials)
        await dispatch('synchronizeAuthenticationState')
    },

    async logout({dispatch}) {
        await axios.post(ApiRoutes.Auth.Logout)

        dispatch('clearUserState')
    },

    async register({dispatch}, userData) {
        await axios.get(ApiRoutes.GetCsrfCookie)
        await axios.post(ApiRoutes.Auth.Register, userData)
        await dispatch('synchronizeAuthenticationState')
    },

    async synchronizeAuthenticationState({commit, dispatch}) {
        try {
            const response = await axios.get(ApiRoutes.Auth.GetAuthenticatedUser)

            commit('SET_AUTHENTICATED', true)
            commit('SET_USER', response.data.data)
        } catch (error) {
            dispatch('resetModuleState')
        }
    },

    resetModuleState({commit}) {
        commit('SET_AUTHENTICATED', false)
        commit('SET_USER', null)
    },

    clearUserState({dispatch}) {
        dispatch('resetModuleState')
        dispatch('contacts/resetModuleState', null, {root: true})
        dispatch('messages/resetModuleState', null, {root: true})
    }
}

export default {
    namespaced: true,
    state,
    getters,
    mutations,
    actions,
}
