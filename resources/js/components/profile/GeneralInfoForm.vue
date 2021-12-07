<template>
    <div class="profile-card__form-wrapper">
        <h1 class="profile-card__form-title">General Information</h1>

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
                    class="button--primary"
                    :disabled="isSubmitting"
                    :loading="isSubmitting">
                    Update Profile
                </app-button>
            </div>
        </form>
    </div>
</template>

<script>
import AppButton from '../ui/AppButton'
import { mapValues } from 'lodash'
import { mapGetters, mapActions } from 'vuex'

export default {
    name: "GeneralInfoForm",

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
                }
            },
            successAlertOptions: {
                icon: 'success',
                titleText: 'Profile Updated!',
                text: 'Your profile information has been successfully updated.'
            },
            validationErrors: [],
            isSubmitting: false
        }
    },

    computed: {
        ...mapGetters({
            authUser: 'auth/user'
        })
    },

    mounted() {
        this.setFormFieldsValues()
    },

    methods: {
        ...mapActions({
            updateProfile: 'auth/updateProfileInfo'
        }),

        async submitForm() {
            try {
                this.isSubmitting = true
                const userData = mapValues(this.formFields, 'value')
                await this.updateProfile(userData)

                this.$swal(this.successAlertOptions)
            } catch (error) {
                this.validationErrors = error.response.data.errors
            } finally {
                this.isSubmitting = false
            }
        },

        resetValidationError(event) {
            if (this.validationErrors[event.target.id] !== '') {
                this.validationErrors[event.target.id] = ''
            }
        },

        setFormFieldsValues() {
            for (let field in this.formFields) {
                this.formFields[field].value = this.authUser[field]
            }
        }
    }
}
</script>
