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
                :class="`message--${message.sender.id === authUser.id ? 'sent' : 'received'}`">

                <div class="message__avatar" v-if="showUserAvatar(index)">
                    <user-avatar :username="selectedContact.full_name"
                                 :img-src="selectedContact.avatar_thumb_url"
                                 :size="35"/>
                </div>

                <div class="message__content" v-tooltip="getMessageTooltipOptions(message)">
                    {{ message.text }}
                </div>

                <div class="message__read-indicator" v-if="showMessageReadIndicator(index)">
                    <span v-if="message.read_at" v-tooltip="`Read at ${formatDate(message.read_at)}`">
                        <font-awesome-icon :icon="['far', 'check-circle']"/>
                    </span>
                    <span v-else v-tooltip="'Unread'">
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

export default {
    name: "MessagesFeed",

    components: {
        InfiniteLoading,
        FontAwesomeIcon,
        UserAvatar
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
            isLoadingMessages: false
        }
    },

    computed: {
        ...mapGetters({
            messages: 'messages/allMessages',
            selectedContact: 'contacts/selectedContact',
            authUser: 'auth/user'
        })
    },

    watch: {
        conversationId() {
            this.listenForWhisperOnConversationChannel()
        },

        selectedContact() {
            if (this.selectedContact !== null) {
                this.resetMessagesState()
                this.$refs.infiniteLoading.stateChanger.reset()
            }
        }
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
                    this.$nextTick(() => {
                        this.scrollToBottom()
                    })

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
        },

        scrollToBottom() {
            this.$refs.messagesList?.scrollIntoView({
                behavior: 'smooth',
                block: 'end',
                inline: 'nearest'
            })
        },

        showUserAvatar(messageIndex) {
            return this.messages[messageIndex].sender.id !== this.authUser.id &&
                (this.messages[messageIndex + 1]?.sender.id === this.authUser.id ||
                    (typeof this.messages[messageIndex + 1] === 'undefined' && !this.typingUser))
        },

        showMessageReadIndicator(messageIndex) {
            return this.messages[messageIndex].sender.id === this.authUser.id &&
                ((this.messages[messageIndex].read_at && !this.messages[messageIndex + 1]?.read_at) ||
                    (!this.messages[messageIndex].read_at &&
                        !this.messages.slice(messageIndex, this.messages.length - 1)
                            .some(message => message.sender.id !== this.authUser.id)))
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
                delay: {
                    show: 500,
                    hide: 0
                }
            }
        }
    }
}
</script>

