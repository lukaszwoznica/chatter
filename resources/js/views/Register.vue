<template>
    <h1>Register Page</h1>
    <form class="form" @submit.prevent>
        <div class="form__group">
            <label class="form__label" for="first-name">First Name</label>
            <input class="form__input" type="text" id="first-name">
        </div>
        <div class="form__group">
            <label class="form__label" for="last-name">Last Name</label>
            <input class="form__input" type="text" id="last-name">
        </div>
        <div class="form__group">
            <label class="form__label" for="email">Email</label>
            <input class="form__input" type="email" id="email">
        </div>
        <div class="form__group">
            <label class="form__label" for="password">Password</label>
            <input class="form__input" type="password" id="password">
        </div>
        <div class="form__group">
            <label class="form__label" for="confirm-password">Confirm Password</label>
            <input class="form__input" type="password" id="confirm-password">
        </div>

        <AppButton type="submit">
            Register
        </AppButton>
    </form>
</template>

<script>
import AppButton from "../components/ui/AppButton"

export default {
    name: "Register",
    components: {AppButton}
}
</script>
