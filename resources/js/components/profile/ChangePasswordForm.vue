<template>
    <h1 class="profile-card__form-title">Password Change</h1>
    <form class="form" @submit.prevent="submit">
        <div class="form__group" v-for="formField in formFields" :key="formField.id">
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
        <div class="form__button-wrapper">
            <AppButton type="submit" :classList="['button--primary']">
                Update Password
            </AppButton>
        </div>
    </form>
</template>

<script>
import AppButton from '../ui/AppButton'
import { mapValues } from "lodash";

export default {
    name: "ChangePasswordForm",

    components: {
        AppButton
    },

    data() {
        return {
            formFields: {
                current_password: {
                    id: 'current_password',
                    value: '',
                    type: 'password',
                    label: 'Current Password',
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
                }
            },
            validationErrors: []
        }
    },

    methods: {
        async submit() {
            try {
                const userData = mapValues(this.formFields, 'value')
                console.log(userData)
                // await this.register(userData)
                // await this.$router.push({name: 'chats'})
            } catch (error) {
                this.validationErrors = error.response.data.errors
            }
        },

        resetValidationError(event) {
            if (this.validationErrors[event.target.id] !== '') {
                this.validationErrors[event.target.id] = ''
            }
        }
    }
}
</script>
