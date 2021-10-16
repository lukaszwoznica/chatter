<template>
    <h1 class="profile-card__form-title">Password Change</h1>
    <form class="form" @submit.prevent="submitForm">
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
            <app-button
                type="submit"
                :classList="['button--primary']"
                :disabled="isSubmitting"
                :loading="isSubmitting">
                Update Password
            </app-button>
        </div>
    </form>
</template>

<script>
import AppButton from '../ui/AppButton'
import { mapValues } from "lodash";
import ApiRoutes from "../../api/routes";

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
            validationErrors: [],
            isSubmitting: false
        }
    },

    methods: {
        async submitForm() {
            try {
                this.isSubmitting = true
                const userData = mapValues(this.formFields, 'value')
                await axios.put(ApiRoutes.Auth.UpdatePassword, userData)

                alert('Password successfully updated!')
            } catch (error) {
                if (error.response.status === 422) {
                    this.validationErrors = error.response.data.errors
                }
            }

            this.resetInputValues()
            this.isSubmitting = false
        },

        resetValidationError(event) {
            if (this.validationErrors[event.target.id] !== '') {
                this.validationErrors[event.target.id] = ''
            }
        },

        resetInputValues() {
            for (let field in this.formFields) {
                this.formFields[field].value = ''
            }
        }
    }
}
</script>
