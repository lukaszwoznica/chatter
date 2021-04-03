<template>
    <h1>Login Page</h1>
    <div class="alert" v-show="validationError">
        {{ validationError }}
    </div>
    <form class="form" @submit.prevent="submit">
        <div class="form__group">
            <label class="form__label" for="email">Email</label>
            <input class="form__input" type="email" id="email" v-model="formFields.email">
        </div>
        <div class="form__group">
            <label class="form__label" for="password">Password</label>
            <input class="form__input" type="password" id="password" v-model="formFields.password">
        </div>

        <AppButton type="submit">
            Login
        </AppButton>
    </form>
</template>

<script>
import AppButton from "../components/ui/AppButton"
import {mapActions} from 'vuex'

export default {
    name: "Login",

    components: {
        AppButton
    },

    data() {
        return {
            formFields: {
                email: '',
                password: '',
            },

            validationError: '',
        }
    },

    methods: {
        ...mapActions({
            login: 'auth/login'
        }),

        async submit() {
            try {
                await this.login(this.formFields)
            } catch (e) {
                this.validationError = e.response.data.errors.email[0]
                this.formFields.password = ''
                return
            }

            await this.$router.push({name: 'chats'})
        }
    }
}
</script>
