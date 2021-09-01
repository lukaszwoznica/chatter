<template>
    <div class="conversation__composer">
        <form @submit.prevent="submit" class="form">
            <textarea v-model.trim="message.text"
                      rows="1"
                      class="form__textarea form__textarea--message"
                      @input="onInput"
                      @keydown.enter.prevent="submit"
                      placeholder="Type a message">
            </textarea>
            <AppButton type="submit" v-show="this.message.text" :class-list="['send-message-button']">
                <font-awesome-icon :icon="['fas', 'arrow-right']"></font-awesome-icon>
            </AppButton>
        </form>
    </div>
</template>

<script>
import AppButton from '../../ui/AppButton'
import mixinTextareaAutoResize from '../../../mixins/TextareaAutoResize'
import {mapActions, mapMutations} from 'vuex'
import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome'

export default {
    name: "ConversationComposer",

    mixins: [mixinTextareaAutoResize],

    components: {
        AppButton, FontAwesomeIcon
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

        onInput(event) {
            this.autoResize(event, 150)
            this.whisperTypingEvent()
        },

        whisperTypingEvent() {
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
    },

    watch: {
        selectedContact() {
            this.resetMessageData()
        }
    }
}
</script>
