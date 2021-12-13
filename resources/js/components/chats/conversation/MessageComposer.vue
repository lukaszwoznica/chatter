<template>
    <div class="conversation__composer">
        <form @submit.prevent="submitMessage" class="form">
            <div class="form__group">
                <textarea
                    rows="1"
                    class="form__textarea form__textarea--message"
                    :value="message.text"
                    @input="onInput"
                    @keydown.enter.prevent="submitMessage"
                    placeholder="Type a message">
                </textarea>

                <div class="emoji-picker-wrapper" v-click-outside="closeEmojiPicker">
                    <app-button class="button--emoji-picker" @buttonClick="toggleEmojiPicker">
                        <font-awesome-icon :icon="['fas', 'smile']"/>
                    </app-button>
                    <transition name="fade">
                        <vuemoji-picker
                            @emojiClick="handleEmojiClick"
                            :is-dark="false"
                            v-show="showEmojiPicker"
                            class="emoji-picker"
                        />
                    </transition>
                </div>
            </div>

            <app-button type="submit" v-show="this.message.text" class="button--send-message" :disabled="isSubmitting">
                <font-awesome-icon :icon="['fas', 'arrow-right']"/>
            </app-button>
        </form>
    </div>
</template>

<script>
import AppButton from '../../ui/AppButton'
import textareaAutoResizeMixin from '../../../mixins/TextareaAutoResize'
import { mapActions, mapMutations, mapGetters } from 'vuex'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { VuemojiPicker } from 'vuemoji-picker'
import vClickOutside from 'click-outside-vue3'

export default {
    name: "ConversationComposer",

    mixins: [
        textareaAutoResizeMixin
    ],

    components: {
        AppButton,
        FontAwesomeIcon,
        VuemojiPicker
    },

    directives: {
        clickOutside: vClickOutside.directive
    },

    props: {
        conversationId: {
            required: true
        }
    },

    data() {
        return {
            message: {
                text: '',
                recipient_id: null
            },
            showEmojiPicker: false,
            showSubmitButton: false,
            isSubmitting: false
        }
    },

    computed: {
        ...mapGetters({
            selectedContact: 'contacts/selectedContact',
            authUser: 'auth/user'
        }),
    },

    watch: {
        selectedContact() {
            this.resetMessageData()
        }
    },

    methods: {
        ...mapActions({
            sendMessage: 'messages/sendMessage',
            updateContact: 'contacts/updateContact'
        }),

        async submitMessage() {
            if (this.message.text === '' || this.isSubmitting) {
                return
            }

            try {
                this.isSubmitting = true
                this.message.recipient_id = this.selectedContact.id
                const message = await this.sendMessage(this.message)

                this.updateContact({
                    ...this.selectedContact,
                    last_message: message.created_at
                })

                this.$emit('messageSent')
                this.resetMessageData()
            } catch (error) {
                this.$swal({
                    icon: 'error',
                    titleText: 'Oops!',
                    text: 'Something went wrong while sending a message.'
                })
            } finally {
                this.isSubmitting = false
            }
        },

        onInput(event) {
            this.message.text = event.target.value.trim()
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
        },

        handleEmojiClick(eventDetail) {
            this.message.text += eventDetail.unicode
        },

        toggleEmojiPicker() {
            this.showEmojiPicker = !this.showEmojiPicker
        },

        closeEmojiPicker() {
            this.showEmojiPicker = false
        }
    }
}
</script>
