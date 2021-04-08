<template>
    <div class="conversation__composer">
        <form @submit.prevent="submit">
            <textarea cols="40" rows="1"
                      v-model.trim="message.text"
                      @keydown.enter.prevent="submit">
            </textarea>
            <AppButton type="submit">
                Send
            </AppButton>
        </form>
    </div>
</template>

<script>
import AppButton from '../../ui/AppButton';
import {mapActions} from 'vuex'

export default {
    name: "ConversationComposer",

    components: {
        AppButton
    },

    props: {
        selectedContact: {
            required: true
        },
        authUser: {
            required: true
        }
    },

    data() {
        return {
            message: {
                text: '',
                recipient_id: null
            }
        }
    },

    methods: {
        ...mapActions({
            sendMessage: 'messages/sendMessage'
        }),

        async submit() {
            try {
                this.message.recipient_id = this.selectedContact.id
                await this.sendMessage(this.message)
            } catch (error) {
                console.log(error)
            }
            this.resetMessage()
        },

        resetMessage() {
            this.message = {
                text: '',
                recipient_id: null
            }
        }
    }
}
</script>
