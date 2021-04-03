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
    async login({dispatch, commit}, credentials) {
        await axios.get(ApiRoutes.GetCsrfCookie)
        await axios.post(ApiRoutes.Auth.Login, credentials)

        dispatch('synchronizeAuthenticationState')
    },

    async logout({commit}) {
        await axios.post(ApiRoutes.Auth.Logout)

        commit('SET_AUTHENTICATED', false)
        commit('SET_USER', null)
    },

    async synchronizeAuthenticationState({commit}) {
        try {
            const response = await axios.get(ApiRoutes.Auth.GetAuthenticatedUser)

            commit('SET_AUTHENTICATED', true)
            commit('SET_USER', response.data)
        } catch (error) {
            commit('SET_AUTHENTICATED', false)
            commit('SET_USER', null)
        }
    }
}

export default {
    namespaced: true,
    state,
    getters,
    mutations,
    actions,
}
