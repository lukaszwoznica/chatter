<template>
    <div class="conversation__composer">
        <form @submit.prevent="submit">
            <textarea cols="40" rows="1"
                      v-model.trim="message.text"
                      @input="onTyping"
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
import {mapActions, mapMutations} from 'vuex'

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
        },
        conversationId: {
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
        }),

        ...mapMutations({
           updateContact: 'contacts/UPDATE_CONTACT'
        }),

        async submit() {
            if (this.message.text) {
                try {
                    this.message.recipient_id = this.selectedContact.id
                    const message = await this.sendMessage(this.message)

                    this.updateContact({
                        ...this.selectedContact,
                        last_message: message.created_at
                    })

                    this.resetMessageData()
                } catch (error) {
                    alert('Something went wrong while sending a message.')
                }
            }
        },

        onTyping() {
            const channel = Echo.private(`conversation.${this.conversationId}`);

            setTimeout(() => {
                channel.whisper('TypingEvent', {
                    user: this.authUser
                });
            }, 300)
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
