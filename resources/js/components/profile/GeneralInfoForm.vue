<template>
    <h1 class="profile-card__form-title">General Information</h1>
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
                Update Profile
            </AppButton>
        </div>
    </form>
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
            validationErrors: []
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

        async submit() {
            try {
                const userData = mapValues(this.formFields, 'value')
                await this.updateProfile(userData)

                alert('Profile successfully updated!')
            } catch (error) {
                this.validationErrors = error.response.data.errors
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
