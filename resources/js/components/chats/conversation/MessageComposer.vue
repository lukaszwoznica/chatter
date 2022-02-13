<template>
    <div class="conversation__composer">
        <app-button
            class="button--send-location"
            @click="sendCurrentLocationMessage"
            v-tippy="'Send your current location'"
            :disabled="isSubmitting">

            <font-awesome-icon :icon="['fas', 'map-marker-alt']"/>
        </app-button>

        <form @submit.prevent="submitForm" class="form">
            <div class="form__group">
                <textarea
                    rows="1"
                    class="form__textarea form__textarea--message"
                    :value="message.text"
                    @input="onInput"
                    @keydown.enter.prevent="submitForm"
                    placeholder="Type a message">
                </textarea>

                <div class="emoji-picker-wrapper" v-click-outside="closeEmojiPicker">
                    <app-button
                        class="button--emoji-picker"
                        @buttonClick="toggleEmojiPicker"
                        v-tippy="'Choose an emoji'">

                        <font-awesome-icon :icon="['fas', 'smile']"/>
                    </app-button>
                </div>
            </div>

            <transition name="fade">
                <vuemoji-picker
                    @emojiClick="handleEmojiClick"
                    :is-dark="false"
                    v-show="showEmojiPicker"
                    class="emoji-picker"/>
            </transition>

            <div class="form__button-wrapper" :class="{'form__button-wrapper--visible': this.message.text}">
                <app-button
                    type="submit"
                    v-tippy="'Send a message'"
                    class="button--send-message"
                    :disabled="isSubmitting">

                    <font-awesome-icon :icon="['fas', 'arrow-right']"/>
                </app-button>
            </div>
        </form>
    </div>
</template>

<script>
import AppButton from '../../ui/AppButton'
import textareaAutoResizeMixin from '../../../mixins/TextareaAutoResize'
import { mapActions, mapGetters } from 'vuex'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { VuemojiPicker } from 'vuemoji-picker'
import vClickOutside from 'click-outside-vue3'

export default {
    name: "MessageComposer",

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
                recipient_id: null,
                is_location: false
            },
            errorAlertOptions: {
                icon: 'error',
                titleText: 'Oops!',
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

        submitForm() {
            this.submitMessage(this.message)
        },

        async submitMessage(messageData) {
            if (messageData.text === '' || this.isSubmitting) {
                return
            }

            try {
                this.isSubmitting = true
                const message = await this.sendMessage(messageData)

                this.updateContact({
                    ...this.selectedContact,
                    last_message: message.created_at
                })

                this.$emit('messageSent')
                this.resetMessageData()
            } catch (error) {
                this.$swal({
                    ...this.errorAlertOptions,
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
                recipient_id: this.selectedContact?.id
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
        },

        sendCurrentLocationMessage() {
            if (!navigator.geolocation) {
                return this.$swal({
                    ...this.errorAlertOptions,
                    text: 'Geolocation is not supported by your browser.'
                })
            }

            navigator.geolocation.getCurrentPosition(position => {
                this.submitMessage({
                    ...this.message,
                    text: this.generateGoogleMapsUrl(position.coords.latitude, position.coords.longitude),
                    is_location: true
                })
            }, this.handleGeolocationError)
        },

        handleGeolocationError(error) {
            let errorMessage = ''

            switch (error.code) {
                case error.PERMISSION_DENIED:
                    errorMessage = 'User denied the request for Geolocation.'
                    break
                case error.POSITION_UNAVAILABLE:
                    errorMessage = 'Location information is unavailable.'
                    break
                case error.TIMEOUT:
                    errorMessage = 'The request to get user location timed out.'
                    break
                default:
                    errorMessage = 'An unknown error occurred.'
            }

            this.$swal({
                ...this.errorAlertOptions,
                text: errorMessage
            })
        },

        generateGoogleMapsUrl(latitude, longitude) {
            return `https://google.com/maps/search/?api=1&query=${latitude},${longitude}`
        }
    }
}
</script>
