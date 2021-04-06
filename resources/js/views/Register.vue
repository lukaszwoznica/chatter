<template>
    <h1>Register Page</h1>
    <form class="form" @submit.prevent="submit">
        <template v-for="formField in formFields">
            <div class="form__group">
                <label class="form__label" :for="formField.id">
                    {{ formField.label }}
                </label>
                <input class="form__input"
                       :type="formField.type"
                       :id="formField.id"
                       v-model="formField.value">
                <small class="form__error" v-if="validationErrors[formField.id]">
                    {{ validationErrors[formField.id][0]}}
                </small>
            </div>
        </template>
        <AppButton type="submit">
            Register
        </AppButton>
    </form>
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
            validationErrors: []
        }
    },

    methods: {
        ...mapActions({
            register: 'auth/register'
        }),

        async submit() {
            try {
                const userData = mapValues(this.formFields, 'value')

                await this.register(userData)
            } catch (error) {
                this.validationErrors = error.response.data.errors
                this.formFields.password.value = ''
                this.formFields.password_confirmation.value = ''

                return
            }

            await this.$router.push({name: 'chats'})
        }
    }
}
</script>
