<template>
    <header class="header" :class="classList">
        <div class="logo">
            <router-link :to="{name: 'home'}" class="logo__link">
                <img class="logo__img" src="/img/logo.png" alt="Logo">
                <div class="logo__text">Chatter</div>
            </router-link>
        </div>

        <template v-if="!isNotFoundRoute">
            <nav class="nav">
                <ul class="nav__items">
                    <template v-if="!isAuthenticated">
                        <li class="nav__item nav__item--nested">
                            <ul class="nav__guest-items" ref="guestItemsList">
                                <li class="nav__item">
                                    <router-link :to="{name: 'login'}" class="nav__link">Login</router-link>
                                </li>
                                <li class="nav__item">
                                    <router-link :to="{name: 'register'}" class="nav__link">Register</router-link>
                                </li>
                            </ul>
                        </li>
                        <li class="nav__item nav__item--hamburger">
                            <hamburger-button @onHamburgerClick="toggleMobileNav" ref="hamburgerButton"/>
                        </li>
                    </template>

                    <template v-else>
                        <li class="nav__item">
                            <dropdown-menu>
                                <template #dropdown-toggler>
                                    <div class="dropdown__avatar">
                                        <user-avatar :username="authUser.full_name"
                                                     :img-src="authUser.avatar_thumb_url"
                                                     :size="35"/>
                                    </div>
                                    <div class="dropdown__name">
                                        {{ authUser.first_name }}
                                    </div>
                                </template>

                                <template #dropdown-items>
                                    <router-link :to="{name: 'profile'}" class="dropdown__item">
                                        <div class="dropdown__icon">
                                            <font-awesome-icon :icon="['fas', 'user-edit']"
                                                               fixed-width></font-awesome-icon>
                                        </div>
                                        Edit Profile
                                    </router-link>
                                    <a href="#" @click.prevent="logout" class="dropdown__item">
                                        <div class="dropdown__icon">
                                            <font-awesome-icon :icon="['fas', 'power-off']"
                                                               fixed-width></font-awesome-icon>
                                        </div>
                                        Logout
                                    </a>
                                </template>
                            </dropdown-menu>
                        </li>
                    </template>
                </ul>
            </nav>
        </template>
    </header>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faUserEdit, faPowerOff } from '@fortawesome/free-solid-svg-icons'
import DropdownMenu from '../ui/DropdownMenu'
import UserAvatar from '../ui/UserAvatar'
import HamburgerButton from '../ui/HamburgerButton'

export default {
    name: "AppHeader",

    components: {
        DropdownMenu,
        HamburgerButton,
        FontAwesomeIcon,
        UserAvatar
    },

    props: {
        classList: {
            type: Array,
            required: false
        }
    },

    data() {
        return {
            isMobileNavActive: false
        }
    },

    computed: {
        ...mapGetters({
            isAuthenticated: 'auth/isAuthenticated',
            authUser: 'auth/user'
        }),

        isNotFoundRoute() {
            return this.$route.name === 'not-found'
        }
    },

    watch: {
        $route() {
            if (this.isMobileNavActive) {
                this.toggleMobileNav()
                this.$refs.hamburgerButton.toggleActiveClass()
            }
        }
    },

    mounted() {
        if (this.$refs.guestItemsList) {
            window.addEventListener('resize', this.toggleMobileNavOnResize)
        }
    },

    created() {
        library.add(faUserEdit, faPowerOff)
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

        toggleMobileNav() {
            this.$emit('toggleMobileNav')
            this.isMobileNavActive = !this.isMobileNavActive
            this.$refs.guestItemsList.classList.toggle('nav__guest-items--mobile')
        },

        toggleMobileNavOnResize() {
            const guestItemsList = this.$refs.guestItemsList
            if (!guestItemsList || !this.isMobileNavActive) {
                return
            }

            if (window.innerWidth >= 768) {
                this.$emit('closeMobileNav')
                guestItemsList.classList.remove('nav__guest-items--mobile')
            } else {
                this.$emit('openMobileNav')
                guestItemsList.classList.add('nav__guest-items--mobile')
            }
        }
    }
}
</script>
