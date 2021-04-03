<template>
    <h1>Login Page</h1>
    <form class="form" @submit.prevent>
        <div class="form__group">
            <label class="form__label" for="email">Email</label>
            <input class="form__input" type="email" id="email">
        </div>
        <div class="form__group">
            <label class="form__label" for="password">Password</label>
            <input class="form__input" type="password" id="password">
        </div>

        <AppButton type="submit">
            Login
        </AppButton>
    </form>
</template>

<script>
import AppButton from "../components/ui/AppButton"

export default {
    name: "Login",
    components: {AppButton}
}
</script>
