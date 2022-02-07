<template>
    <div class="container container--404">
        <h1>404</h1>
        <h2>Page Not Found</h2>
        <h4>Oops, it looks like the page you're looking for doesn't exist.</h4>
        <app-button class="button--primary" @click="redirectHome">
            Go To Homepage
        </app-button>
    </div>
</template>

<script>
import AppButton from '../components/ui/AppButton'

export default {
    name: "NotFound",

    components: {
        AppButton
    },

    methods: {
        redirectHome() {
            this.$router.push({ name: 'home' })
        }
    }
}
</script>
