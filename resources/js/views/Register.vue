<template>
    <div class="container">
        <div class="card form-card">
            <div class="card__title">
                <h1>Create Account</h1>
            </div>
            <div class="card__content">
                <form class="form" @submit.prevent="submitForm">
                    <template v-for="formField in formFields">
                        <div class="form__group">
                            <div class="form__input-group">
                                <input class="form__input"
                                       :type="formField.type"
                                       :id="formField.id"
                                       v-model="formField.value"
                                       :class="{ 'form__input--invalid': validationErrors[formField.id] }"
                                       @input="resetValidationError"
                                       placeholder=" " required>
                                <label class="form__label" :for="formField.id">
                                    {{ formField.label }}
                                </label>
                            </div>
                            <small class="form__error" v-if="validationErrors[formField.id]">
                                {{ validationErrors[formField.id][0] }}
                            </small>
                        </div>
                    </template>
                    <div class="form__button-wrapper">
                        <app-button
                            type="submit"
                            :classList="['button--primary']"
                            :disabled="isSubmitting"
                            :loading="isSubmitting">
                            Register
                        </app-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import AppButton from "../components/ui/AppButton"
import {mapActions} from 'vuex'
import {mapValues} from 'lodash'

export default {
    name: "Register",

    components: {
        AppButton
    },

    data() {
        return {
            formFields: {
                first_name: {
                    id: 'first_name',
                    value: '',
                    type: 'text',
                    label: 'First Name'
                },
                last_name: {
                    id: 'last_name',
                    value: '',
                    label: 'Last Name',
                    type: 'text'
                },
                email: {
                    id: 'email',
                    value: '',
                    type: 'email',
                    label: 'Email'
                },
                password: {
                    id: 'password',
                    value: '',
                    type: 'password',
                    label: 'Password',
                },
                password_confirmation: {
                    id: 'password_confirmation',
                    value: '',
                    type: 'password',
                    label: 'Confirm Password',
                },
            },
            validationErrors: [],
            isSubmitting: false
        }
    },

    methods: {
        ...mapActions({
            register: 'auth/register'
        }),

        async submitForm() {
            try {
                this.isSubmitting = true
                const userData = mapValues(this.formFields, 'value')
                await this.register(userData)

                await this.$router.push({name: 'chats'})
            } catch (error) {
                this.validationErrors = error.response.data.errors
                this.resetPasswordFields()
                this.isSubmitting = false
            }
        },

        resetValidationError(event) {
            if (this.validationErrors[event.target.id] !== '') {
                this.validationErrors[event.target.id] = ''
            }
        },

        resetPasswordFields() {
            this.formFields.password.value = ''
            this.formFields.password_confirmation.value = ''
        }
    }
}
</script>
