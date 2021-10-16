<template>
    <div class="container">
        <div class="card form-card">
            <div class="card__title">
                <h1>Reset password</h1>
            </div>
            <div class="card__content">
                <template v-if="!success">
                    <div class="alert" v-if="differentValidationError">
                        {{ differentValidationError[Object.keys(differentValidationError)[0]][0] }}
                    </div>

                    <form class="form" @submit.prevent="submitForm">
                        <div class="form__group">
                            <div class="form__input-group">
                                <input class="form__input" type="password" id="password" placeholder=" "
                                       :class="{ 'form__input--invalid': passwordValidationError !== '' }"
                                       v-model="requestData.password" @input="resetValidationError">
                                <label class="form__label" for="password">New Password</label>
                            </div>
                            <small class="form__error" v-if="passwordValidationError">
                                {{ passwordValidationError }}
                            </small>
                        </div>

                        <div class="form__group">
                            <div class="form__input-group">
                                <input class="form__input" type="password" id="password_confirmation" placeholder=" "
                                       :class="{ 'form__input--invalid': passwordValidationError !== '' }"
                                       v-model="requestData.password_confirmation" @input="resetValidationError">
                                <label class="form__label" for="password_confirmation">Confirm Password</label>
                            </div>
                        </div>

                        <div class="form__button-wrapper">
                            <app-button
                                type="submit"
                                :classList="['button--primary']"
                                :disabled="isSubmitting"
                                :loading="isSubmitting">
                                Reset Password
                            </app-button>
                        </div>
                    </form>
                </template>

                <template v-else>
                    <p>
                        Your password has been successfully reset. You can
                        <router-link :to="{name: 'login'}">login</router-link>
                        now.
                    </p>
                </template>
            </div>
        </div>
    </div>
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
            differentValidationError: null,
            isSubmitting: false
        }
    },

    methods: {
        async submitForm() {
            try {
                this.isSubmitting = true
                await axios.post(ApiRoutes.Auth.ResetPassword, this.requestData)

                this.success = true
            } catch (error) {
                if (error.response.status === 422) {
                    const errors = error.response.data.errors
                    if (errors.password) {
                        this.passwordValidationError = errors.password[0]
                    } else {
                        this.differentValidationError = errors
                    }

                    this.resetPasswordInputs()
                    this.isSubmitting = false
                }
            }
        },

        resetValidationError() {
            if (this.passwordValidationError !== '') {
                this.passwordValidationError = ''
            }
        },

        resetPasswordInputs() {
            this.requestData.password = ''
            this.requestData.password_confirmation = ''
        }
    }
}
</script>
