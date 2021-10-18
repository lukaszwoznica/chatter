<template>
    <div class="app-wrapper">
        <AppHeader :classList="isAuthRoute ? ['header--auth'] : []"
                   @toggleMobileNav="toggleMobileNav"
                   @openMobileNav="openMobileNav"
                   @closeMobileNav="closeMobileNav"/>

        <main class="main" :class="isAuthRoute ? 'main--auth' : ''" ref="main">
            <router-view/>
        </main>

        <AppFooter v-if="!isChatsRoute"/>

        <template v-if="!isAuthRoute">
            <div class="bg-gradient" ref="bgGradient"></div>
            <div class="bg-shape bg-shape--circle" ref="bgCircleShape"></div>
            <div class="bg-shape bg-shape--eclipse"></div>
        </template>
    </div>
</template>

<script>
import AppFooter from "./components/layout/AppFooter";
import AppHeader from "./components/layout/AppHeader";

export default {
    name: "App",

    components: {
        AppHeader,
        AppFooter
    },

    computed: {
        isAuthRoute() {
            return ['chats', 'profile'].includes(this.$route.name)
        },

        isChatsRoute() {
            return this.$route.name === 'chats'
        }
    },

    methods: {
        toggleMobileNav() {
            this.$refs.bgCircleShape.classList.toggle('bg-shape--nav-circle')
            this.$refs.bgGradient.classList.toggle('bg-gradient--overlay')
            this.$refs.main.classList.toggle('main--hidden')
        },

        openMobileNav() {
            this.$refs.bgCircleShape.classList.add('bg-shape--nav-circle')
            this.$refs.bgGradient.classList.add('bg-gradient--overlay')
            this.$refs.main.classList.add('main--hidden')
        },

        closeMobileNav() {
            this.$refs.bgCircleShape.classList.remove('bg-shape--nav-circle')
            this.$refs.bgGradient.classList.remove('bg-gradient--overlay')
            this.$refs.main.classList.remove('main--hidden')
        }
    }
}
</script>
