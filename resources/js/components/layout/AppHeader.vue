<template>
    <header class="header">
        <nav class="nav">
            <ul class="nav__list">
                <li class="nav__item">
                    <router-link :to="{name: 'home'}">Home</router-link>
                </li>
                <li class="nav__item">
                    <router-link :to="{name: 'chats'}">Chats</router-link>
                </li>
                <li class="nav__item">
                    <router-link :to="{name: 'login'}">Login</router-link>
                </li>
                <li class="nav__item">
                    <router-link :to="{name: 'register'}">Register</router-link>
                </li>
            </ul>
        </nav>
    </header>
</template>

<script>
export default {
    name: "AppHeader"
}
</script>

<style scoped>
    .nav__item {
        display: inline;
        padding: 1rem;
    }
</style>
