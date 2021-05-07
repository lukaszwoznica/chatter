<template>
    <h1>Password recovery</h1>
    <p>Enter your e-mail address and we'll send you a link to reset your password.</p>
    <form class="form" @submit.prevent="submit">
        <div class="form__group">
            <label class="form__label" for="email">Email</label>
            <input class="form__input" type="email" id="email" v-model="email">
        </div>
        <small class="form__error" v-if="validationError">
            {{ validationError }}
        </small>
        <AppButton type="submit">
            Send
        </AppButton>
    </form>
</template>

<script>
import AppButton from '../components/ui/AppButton'
import ApiRoutes from '../api/routes'

export default {
    name: "PasswordRecovery",

    components: {
        AppButton
    },

    data() {
        return {
            email: '',
            validationError: '',
        }
    },

    methods: {
        async submit() {
            try {
                const response = await axios.post(ApiRoutes.Auth.ForgotPassword, {
                    email: this.email
                })

                if (response.status === 200) {
                    this.email = ''
                    this.validationError = ''
                    alert('We have sent you an e-mail containing your password reset link. Check your inbox.')
                }
            } catch (error) {
                if (error.response.status === 422) {
                    this.validationError = error.response.data.errors.email[0]
                }
            }
        }
    }
}
</script>
