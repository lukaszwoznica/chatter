<template>
    <div class="container">
        <div class="card form-card">
            <div class="card__title">
                <h1>User Login</h1>
            </div>
            <div class="card__content">
                <form class="form" @submit.prevent="submitForm">
                    <div class="form__group">
                        <div class="form__input-group">
                            <input class="form__input" type="email" id="email"
                                   :class="{ 'form__input--invalid': validationErrors?.email }"
                                   v-model="formFields.email" placeholder=" " @input="resetValidationErrors" required>
                            <label class="form__label" for="email">Email</label>
                        </div>
                        <small class="form__error" v-if="validationErrors?.email">
                            {{ validationErrors?.email[0] }}
                        </small>
                    </div>
                    <div class="form__group">
                        <div class="form__input-group">
                            <input class="form__input" type="password" id="password"
                                   :class="{ 'form__input--invalid': validationErrors?.password }"
                                   v-model="formFields.password" placeholder=" " @input="resetValidationErrors"
                                   required>
                            <label class="form__label" for="password">Password</label>
                        </div>
                        <small class="form__error" v-if="validationErrors?.password">
                            {{ validationErrors?.password[0] }}
                        </small>
                        <div class="forgot-password-link">
                            <router-link :to="{name: 'password-recovery'}">Forgot Password?</router-link>
                        </div>
                    </div>

                    <div class="form__button-wrapper">
                        <app-button
                            type="submit"
                            :classList="['button--primary']"
                            :disabled="isSubmitting"
                            :loading="isSubmitting">
                            Login
                        </app-button>
                    </div>
                </form>

                <div class="register-info">
                    Don't have account?
                    <router-link :to="{name: 'register'}">Register Now!</router-link>
                </div>

                <div class="social-login">
                    <hr class="hr-text" data-text="OR">
                    <p class="social-login__info">Login with your social account</p>

                    <div class="social-login__buttons-wrapper">
                        <app-button
                            @click="redirectToOAuthProvider('google')"
                            :classList="['social-login__button', 'social-login__button--google']">
                            <font-awesome-icon :icon="['fab', 'google']" fixed-width/>
                            <p>Google</p>
                        </app-button>
                        <app-button
                            @click="redirectToOAuthProvider('facebook')"
                            :classList="['social-login__button', 'social-login__button--facebook']">
                            <font-awesome-icon :icon="['fab', 'facebook-f']" fixed-width/>
                            <p>Facebook</p>
                        </app-button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <loading
        v-model:active="loggingInWithOAuth"
        background-color="#000"
        color="#fff"
        :opacity="0.4"
        :height="120"
        :width="120"
        :lock-scroll="true"
    />
</template>

<script>
import AppButton from '../components/ui/AppButton'
import { mapActions } from 'vuex'
import ApiRoutes from '../api/routes'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faGoogle, faFacebookF } from '@fortawesome/free-brands-svg-icons'
import Loading from 'vue-loading-overlay'

export default {
    name: "Login",

    components: {
        AppButton,
        FontAwesomeIcon,
        Loading
    },

    data() {
        return {
            formFields: {
                email: '',
                password: '',
            },
            validationErrors: [],
            isSubmitting: false,
            loggingInWithOAuth: false,
            errorAlertOptions: {
                icon: 'error',
                titleText: 'Oops!',
            }
        }
    },

    created() {
        library.add(faGoogle, faFacebookF)

        if (this.$route.name === 'login-oauth') {
            this.loggingWithOAuth = true
            this.submitOAuthCallback(this.$route.params.provider, this.$route.query)
        }
    },

    methods: {
        ...mapActions({
            login: 'auth/login',
            loginOAuth: 'auth/loginOAuth'
        }),

        async submitForm() {
            try {
                this.isSubmitting = true
                await this.login(this.formFields)

                await this.$router.push({ name: 'chats' })
            } catch (error) {
                this.validationErrors = error.response.data.errors
                this.formFields.password = ''
                this.isSubmitting = false
            }
        },

        resetValidationErrors(event) {
            if (this.validationErrors[event.target.id]) {
                this.validationErrors[event.target.id] = []
            }
        },

        async redirectToOAuthProvider(provider) {
            try {
                const response = await axios.get(ApiRoutes.OAuth.GetProviderRedirectUrl(provider))
                window.location.href = response.data.target_url
            } catch (error) {
                this.$swal({...this.errorAlertOptions,
                    text: 'Something went wrong while redirecting to OAuth provider.'
                })
            }
        },

        async submitOAuthCallback(provider, callbackData) {
            try {
                this.loggingInWithOAuth = true
                await this.loginOAuth({ provider, callbackData })

                await this.$router.push({ name: 'chats' })
            } catch (error) {
                this.$swal({...this.errorAlertOptions,
                    text: 'Something went wrong during OAuth authorization.'
                })
            } finally {
                this.loggingInWithOAuth = false
            }
        }
    },
}
</script>
