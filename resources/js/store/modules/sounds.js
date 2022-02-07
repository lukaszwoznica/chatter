const state = {
    soundsMuted: false
}

const getters = {
    soundsMuted: state => state.soundsMuted
}

const mutations = {
    SET_SOUNDS_MUTED: (state, value) => state.soundsMuted = value,
}

const actions = {
    toggleSounds({ getters, commit }) {
        localStorage.soundsMuted = !getters.soundsMuted
        commit('SET_SOUNDS_MUTED', !getters.soundsMuted)
    },

    synchronizeStateWithLocalStorage({ commit }) {
        const soundsMuted = localStorage.getItem('soundsMuted')
        if (soundsMuted !== null) {
            commit('SET_SOUNDS_MUTED', JSON.parse(soundsMuted))
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
