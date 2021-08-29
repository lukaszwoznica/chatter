<template>
    <div class="app-wrapper">
        <AppHeader :classList="isChatsRoute ? ['header--chats'] : ''"/>
        <main class="main" :class="isChatsRoute ? 'main--p0' : ''">
            <router-view/>
        </main>
        <template v-if="!isChatsRoute">
            <AppFooter/>
            <div class="bg-gradient"></div>
            <div class="bg-shape bg-shape--circle"></div>
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
        isChatsRoute() {
            return this.$route.name === 'chats'
        }
    }
}
</script>
