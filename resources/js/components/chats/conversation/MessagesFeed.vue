<template>
    <div class="conversation__feed">
        <infinite-loading
            @infinite="infiniteLoadingHandler"
            direction="top"
            ref="infiniteLoading">

            <template #no-more>
                <span></span>
            </template>

            <template #no-results>
                <p class="infinite-status-prompt__content">
                    <template v-if="!isLoadingMessages && messages.length === 0">
                        There are no messages in this conversation yet.
                        Send your first message to {{ selectedContact.first_name }}.
                    </template>
                </p>
            </template>
        </infinite-loading>

        <ul class="messages" ref="messagesList">
            <li v-for="(message, index) in messages" :key="message.id"
                class="message"
                :class="`message--${message.sender_id === authUser.id ? 'sent' : 'received'}`">

                <div class="message__avatar" v-if="showUserAvatar(index)">
                    <user-avatar :username="selectedContact.full_name"
                                 :img-src="selectedContact.avatar_thumb_url"
                                 :size="35"/>
                </div>

                <div class="message__content" v-tippy="getMessageTooltipOptions(message)">
                    <div v-if="message.is_location" class="message__map">
                        <user-location-map :google-maps-url="message.text" :zoom="11"/>
                    </div>
                    <div class="message__text"
                         :class="{ 'message__text--maps-link': message.is_location}"
                         v-linkify:options="getMessageLinkifyOptions(message)">
                        {{ message.text }}
                    </div>
                </div>

                <div class="message__read-indicator" v-if="showMessageReadIndicator(index)">
                    <span v-if="message.read_at" v-tippy="`Read at ${formatDate(message.read_at)}`">
                        <font-awesome-icon :icon="['far', 'check-circle']"/>
                    </span>
                    <span v-else v-tippy="'Unread'">
                        <font-awesome-icon :icon="['far', 'circle']"/>
                    </span>
                </div>
            </li>

            <li v-if="typingUser" class="message message--typing">
                <div class="message__avatar">
                    <user-avatar :username="selectedContact.full_name"
                                 :img-src="selectedContact.avatar_thumb_url"
                                 :size="35"/>
                </div>
                <div class="message__content">
                    <span class="typing-dot" v-for="index in 3" :key="index"></span>
                </div>
            </li>
        </ul>
    </div>
</template>

<script>
import InfiniteLoading from 'vue-infinite-loading'
import { mapActions, mapGetters, mapMutations } from 'vuex'
import dayjs from 'dayjs'
import isToday from 'dayjs/plugin/isToday'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import UserAvatar from '../../ui/UserAvatar'
import linkify from 'vue-linkify'
import UserLocationMap from './UserLocationMap'
import { directive } from 'vue-tippy'

export default {
    name: "MessagesFeed",

    components: {
        UserLocationMap,
        InfiniteLoading,
        FontAwesomeIcon,
        UserAvatar
    },

    directives: {
        linkify,
        tippy: directive,
    },

    props: {
        conversationId: {
            required: true
        }
    },

    data() {
        return {
            typingUser: null,
            typingClock: null,
            isLoadingMessages: false,
            userTypingAudio: new Audio('/audio/user-typing.mp3')
        }
    },

    computed: {
        ...mapGetters({
            messages: 'messages/allMessages',
            selectedContact: 'contacts/selectedContact',
            authUser: 'auth/user',
            soundsMuted: 'sounds/soundsMuted'
        })
    },

    watch: {
        conversationId() {
            this.listenForWhisperOnConversationChannel()
        },

        'selectedContact.id': function () {
            if (this.selectedContact !== null) {
                this.resetMessagesState()
                this.$refs.infiniteLoading.stateChanger.reset()
            }
        },

        soundsMuted() {
            this.toggleUserTypingAudioMute()
        }
    },

    created() {
        this.prepareUserTypingAudio()
    },

    mounted() {
        this.listenForWhisperOnConversationChannel()
        dayjs.extend(isToday)
    },

    beforeUnmount() {
        if (this.messages.length === 0) {
            this.setSelectedContact(null)
        }
        this.resetMessagesState()
    },

    methods: {
        ...mapActions({
            fetchMessages: 'messages/fetchMessages',
            resetMessagesState: 'messages/resetModuleState',
            updateContact: 'contacts/updateContact'
        }),

        ...mapMutations({
            setSelectedContact: 'contacts/SET_SELECTED_CONTACT'
        }),

        listenForWhisperOnConversationChannel() {
            Echo.private(`conversation.${this.conversationId}`)
                .listenForWhisper('TypingEvent', event => {
                    this.typingUser = event.user
                    this.scrollToBottom()
                    this.userTypingAudio.play()

                    if (this.typingClock) {
                        clearTimeout(this.typingClock)
                    }
                    this.typingClock = setTimeout(() => this.typingUser = null, 800)
                })
        },

        async infiniteLoadingHandler($state) {
            this.isLoadingMessages = true
            await this.fetchMessages({
                userId: this.selectedContact?.id,
                infiniteLoaderContext: $state
            })
            this.isLoadingMessages = false

            this.updateContact({
                ...this.selectedContact,
                unread_messages: 0
            })
        },

        scrollToBottom() {
            this.$nextTick(() => {
                this.$refs.messagesList?.scrollIntoView({
                    behavior: 'smooth',
                    block: 'end',
                    inline: 'nearest'
                })
            })
        },

        showUserAvatar(messageIndex) {
            return this.messages[messageIndex].sender_id !== this.authUser.id &&
                (this.messages[messageIndex + 1]?.sender_id === this.authUser.id ||
                    (typeof this.messages[messageIndex + 1] === 'undefined' && !this.typingUser))
        },

        showMessageReadIndicator(messageIndex) {
            return this.messages[messageIndex].sender_id === this.authUser.id &&
                ((this.messages[messageIndex].read_at && !this.messages[messageIndex + 1]?.read_at) ||
                    (!this.messages[messageIndex].read_at &&
                        !this.messages.slice(messageIndex, this.messages.length - 1)
                            .some(message => message.sender_id !== this.authUser.id)))
        },

        formatDate(date) {
            const dateToFormat = dayjs(date)
            if (!dateToFormat.isValid()) {
                return ''
            }

            let dateFormat = ''
            if (dateToFormat.isToday()) {
                dateFormat = 'HH:mm'
            } else {
                dateFormat = 'D MMMM YYYY HH:mm'
            }

            return dateToFormat.format(dateFormat)
        },

        getMessageTooltipOptions(message) {
            return {
                content: this.formatDate(message.created_at),
                placement: 'auto',
                delay: [500, 100]
            }
        },

        getMessageLinkifyOptions(message) {
            const userName = this.getMessageSenderFirstName(message)

            return {
                className: 'message__link',
                format: value => message.is_location ? `Open ${userName}'s location in Google Maps` : value
            }
        },

        getMessageSenderFirstName(message) {
            return (message.sender_id === this.authUser.id
                    ? this.authUser.first_name
                    : this.selectedContact.first_name
            )
        },

        prepareUserTypingAudio() {
            this.userTypingAudio.volume = 0.5
            if (this.soundsMuted) {
                this.userTypingAudio.muted = true
            }
        },

        toggleUserTypingAudioMute() {
            this.userTypingAudio.muted = !this.userTypingAudio.muted
        }
    }
}
</script>
