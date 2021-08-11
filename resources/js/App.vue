<template>
    <div class="app-wrapper">
        <AppHeader/>
        <main class="main">
            <router-view/>
        </main>
        <AppFooter/>
        <template v-if="currentRouteName !== 'chats'">
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
        currentRouteName() {
            return this.$route.name
        }
    }
}
</script>
