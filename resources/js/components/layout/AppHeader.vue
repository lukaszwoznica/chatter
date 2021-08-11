<template>
    <header class="header">
        <div class="logo">
            <router-link :to="{name: 'home'}" class="logo__link">
                <img class="logo__img" src="img/logo.webp" alt="Logo placeholder">
            </router-link>
        </div>
        <nav class="nav">
            <ul class="nav__list">
                <template v-if="!isAuthenticated">
                    <li class="nav__item">
                        <router-link :to="{name: 'login'}">Login</router-link>
                    </li>
                    <li class="nav__item">
                        <router-link :to="{name: 'register'}">Register</router-link>
                    </li>
                </template>
                <template v-else>
                    <li class="nav__item">
                        <a href="#" @click.prevent="logout">Logout</a>
                    </li>
                </template>
            </ul>
        </nav>
    </header>
</template>

<script>
import {mapGetters, mapActions} from 'vuex'

export default {
    name: "AppHeader",

    computed: {
        ...mapGetters({
            isAuthenticated: 'auth/isAuthenticated',
            authUser: 'auth/user'
        })
    },

    methods: {
        ...mapActions({
            logoutAction: 'auth/logout'
        }),

        async logout() {
            Echo.leave(`messages.${this.authUser.id}`)
            Echo.leave(`user-notifications.${this.authUser.id}`)
            await this.logoutAction()
            await this.$router.push({name: 'home'})
        }
    }
}

</script>

<style scoped>
.nav__item {
    display: inline;
    padding: 1rem;
}
</style>
