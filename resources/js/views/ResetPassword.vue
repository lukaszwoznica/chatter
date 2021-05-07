<template>
    <h1>Reset password</h1>

    <template v-if="!success">
        <p>Set your new password.</p>

        <div class="alert" v-if="differentValidationError">
            {{ differentValidationError[Object.keys(differentValidationError)[0]][0] }}
        </div>

        <form class="form" @submit.prevent="submit" >
            <div class="form__group">
                <label class="form__label" for="password">New Password</label>
                <input class="form__input" type="password" id="password"
                       v-model="requestData.password">
                <small class="form__error" v-if="passwordValidationError">
                    {{ passwordValidationError }}
                </small>
            </div>
            <div class="form__group">
                <label class="form__label" for="password_confirmation">Confirm Password</label>
                <input class="form__input" type="password" id="password_confirmation"
                       v-model="requestData.password_confirmation">
            </div>

            <AppButton type="submit">
                Reset Password
            </AppButton>
        </form>
    </template>

    <template v-else>
        <p>
            Your password has been successfully reset. You can <router-link :to="{name: 'login'}">login</router-link> now.
        </p>
    </template>
</template>

<script>
import AppButton from '../components/ui/AppButton'
import ApiRoutes from '../api/routes'

export default {
    name: "ResetPassword",

    components: {
        AppButton
    },

    data() {
        return {
            requestData: {
                password: '',
                password_confirmation: '',
                email: this.$route.params.email,
                token: this.$route.params.token
            },
            success: false,
            passwordValidationError: '',
            differentValidationError: null
        }
    },

    methods: {
        async submit() {
            try {
                const response = await axios.post(ApiRoutes.Auth.ResetPassword, this.requestData)

                if (response.status === 200) {
                    this.success = true
                }
            } catch (error) {
                if (error.response.status === 422) {
                    const errors = error.response.data.errors

                    if (errors.password) {
                        this.passwordValidationError = errors.password[0]
                    } else {
                        this.differentValidationError = errors
                    }

                    this.requestData.password = ''
                    this.requestData.password_confirmation = ''
                }
            }
        }
    }
}
</script>
