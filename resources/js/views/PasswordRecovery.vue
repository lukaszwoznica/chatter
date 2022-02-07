<template>
    <div class="container">
        <div class="card form-card">
            <div class="card__title">
                <h1>Password Recovery</h1>
            </div>
            <div class="card__content">
                <p class="form-info">
                    Enter your e-mail address below and we'll send you a link to reset your password.
                </p>
                <form class="form" @submit.prevent="submitForm">
                    <div class="form__group">
                        <div class="form__input-group">
                            <input class="form__input" type="email" id="email" v-model="email"
                                   :class="{ 'form__input--invalid': validationError !== '' }"
                                   placeholder=" " @input="resetValidationError" required>
                            <label class="form__label" for="email">Email</label>
                        </div>
                        <small class="form__error" v-if="validationError">
                            {{ validationError }}
                        </small>
                    </div>

                    <div class="form__button-wrapper">
                        <app-button
                            type="submit"
                            class="button--primary"
                            :disabled="isSubmitting"
                            :loading="isSubmitting">
                            Send
                        </app-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
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
            isSubmitting: false,
            successAlertOptions: {
                icon: 'success',
                timer: 8000,
                timerProgressBar: true,
                titleText: 'Check Your Email',
                text: 'We have sent you an email containing your password reset link. Check your inbox.',
                confirmButtonText: 'Back To Login'
            }
        }
    },

    methods: {
        async submitForm() {
            try {
                this.isSubmitting = true
                await axios.post(ApiRoutes.Auth.ForgotPassword, { email: this.email })

                this.email = ''
                this.resetValidationError()
                await this.showSuccessAlertAndRedirectToLogin()
            } catch (error) {
                if (error.response.status === 422) {
                    this.validationError = error.response.data.errors.email[0]
                }
            } finally {
                this.isSubmitting = false
            }
        },

        resetValidationError() {
            if (this.validationError !== '') {
                this.validationError = ''
            }
        },

        async showSuccessAlertAndRedirectToLogin() {
            await this.$swal(this.successAlertOptions)
            await this.$router.push({ name: 'login' })
        }
    }
}
</script>
