<template>
    <header class="header" :class="classList">
        <div class="logo">
            <router-link :to="{name: 'home'}" class="logo__link">
                <img class="logo__img" src="img/logo.webp" alt="Logo placeholder">
            </router-link>
        </div>
        <nav class="nav">
            <ul class="nav__list">
                <template v-if="!isAuthenticated">
                    <li class="nav__item">
                        <router-link :to="{name: 'login'}" class="nav__link">Login</router-link>
                    </li>
                    <li class="nav__item">
                        <router-link :to="{name: 'register'}" class="nav__link">Register</router-link>
                    </li>
                </template>
                <template v-else>
                    <li class="nav__item">
                        <div class="dropdown" v-click-outside="closeDropdown">
                            <div class="dropdown__toggle" @click="toggleDropdown">
                                <div class="dropdown__avatar">
                                    <user-avatar :username="authUserFullName" :size="35"/>
                                </div>
                                <div class="dropdown__name">
                                    {{ authUser.first_name }}
                                </div>
                                <div class="dropdown__arrow" ref="dropdownArrow">
                                    &#9662;
                                </div>
                            </div>
                            <div class="dropdown__menu" ref="dropdownMenu" @click="closeDropdown">
                                <router-link :to="{name: 'profile'}" class="dropdown__item">
                                    <div class="dropdown__icon">
                                        <font-awesome-icon :icon="['fas', 'user-edit']" fixed-width></font-awesome-icon>
                                    </div>
                                    Edit Profile
                                </router-link>
                                <a href="#" @click.prevent="logout" class="dropdown__item">
                                    <div class="dropdown__icon">
                                        <font-awesome-icon :icon="['fas', 'power-off']" fixed-width></font-awesome-icon>
                                    </div>
                                    Logout
                                </a>
                            </div>
                        </div>
                    </li>
                </template>
            </ul>
        </nav>
    </header>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import vClickOutside from 'click-outside-vue3'
import UserAvatar from '../ui/UserAvatar'

export default {
    name: "AppHeader",

    components: {
        FontAwesomeIcon,
        UserAvatar
    },

    directives: {
        clickOutside: vClickOutside.directive
    },

    props: {
        classList: {
            type: Array,
            required: false
        }
    },

    computed: {
        ...mapGetters({
            isAuthenticated: 'auth/isAuthenticated',
            authUser: 'auth/user'
        }),

        authUserFullName() {
            return `${this.authUser?.first_name} ${this.authUser?.last_name}`
        }
    },

    methods: {
        ...mapActions({
            logoutAction: 'auth/logout'
        }),

        async logout() {
            Echo.leave(`messages.${this.authUser.id}`)
            Echo.leave(`user-notifications.${this.authUser.id}`)
            await this.logoutAction()
            await this.$router.push({ name: 'home' })
        },

        toggleDropdown() {
            this.$refs.dropdownMenu.classList.toggle('dropdown__menu--active')
            this.$refs.dropdownArrow.classList.toggle('dropdown__arrow--rotated')
        },

        closeDropdown() {
            this.$refs.dropdownMenu.classList.remove('dropdown__menu--active')
            this.$refs.dropdownArrow.classList.remove('dropdown__arrow--rotated')
        }
    }
}

</script>
