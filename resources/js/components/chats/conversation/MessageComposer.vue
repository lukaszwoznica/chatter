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
            sendMessage: 'messages/sendMessage',
            setContactOnTop: 'contacts/setContactOnTop'
        }),

        async submit() {
            if (this.message.text) {
                try {
                    this.message.recipient_id = this.selectedContact.id
                    const message = await this.sendMessage(this.message)

                    this.setContactOnTop({
                        ...this.selectedContact,
                        last_message: message.created_at
                    })

                    this.resetMessageData()
                } catch (error) {
                    alert('Something went wrong while sending a message.')
                }
            }
        },

        resetMessageData() {
            this.message = {
                text: '',
                recipient_id: null
            }
        }
    }
}
</script>
