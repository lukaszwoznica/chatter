<template>
    <div class="container">
        <div class="card form-card">
            <div class="card__title">
                <h1>User Login</h1>
            </div>
            <div class="card__content">
                <form class="form" @submit.prevent="submit">
                    <div class="form__group">
                        <div class="form__input-group">
                            <input class="form__input" type="email" id="email"
                                   :class="{ 'form__input--invalid': validationErrors?.email }"
                                   v-model="formFields.email" placeholder=" " @input="onInputChange" required>
                            <label class="form__label" for="email">Email</label>
                        </div>
                        <small class="form__error" v-if="validationErrors?.email">
                            {{ validationErrors?.email[0]}}
                        </small>
                    </div>
                    <div class="form__group">
                        <div class="form__input-group">
                            <input class="form__input" type="password" id="password"
                                   :class="{ 'form__input--invalid': validationErrors?.password }"
                                   v-model="formFields.password" placeholder=" " @input="onInputChange" required>
                            <label class="form__label" for="password">Password</label>
                        </div>
                        <small class="form__error" v-if="validationErrors?.password">
                            {{ validationErrors?.password[0]}}
                        </small>
                    </div>

                    <div class="form__button-wrapper">
                        <AppButton type="submit" :classList="['button--primary']">
                            Login
                        </AppButton>
                    </div>

                    <div class="forgot-password-link">
                        <router-link :to="{name: 'password-recovery'}">Forgot Password?</router-link>
                    </div>
                </form>
            </div>
        </div>
    </div>
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

            validationErrors: null,
        }
    },

    methods: {
        ...mapActions({
            login: 'auth/login'
        }),

        async submit() {
            try {
                await this.login(this.formFields)
                await this.$router.push({name: 'chats'})
            } catch (e) {
                this.validationErrors = e.response.data.errors
                this.formFields.password = ''
            }
        },

        onInputChange(event) {
            if (this.validationErrors[event.target.id]) {
                this.validationErrors[event.target.id] = null
            }
        }
    },
}
</script>
