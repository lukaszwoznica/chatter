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
                :class="`message--${message.sender.id === authUser?.id ? 'sent' : 'received'}`">
                <div class="message__content">
                    {{ message.text }}
                </div>
                <!--                <span v-if="message.read_at && index === messages.length - 1">-->
                <!--                    Read at {{ message.read_at }}-->
                <!--                </span>-->
            </li>
            <li v-if="typingUser" class="message message--typing">
                <div class="message__content">
                    <span class="typing-dot" v-for="index in 3" :key="index"></span>
                </div>
            </li>
        </ul>
    </div>
</template>

<script>
import InfiniteLoading from 'vue-infinite-loading'
import {mapActions, mapGetters} from 'vuex'

export default {
    name: "MessagesFeed",

    components: {
        InfiniteLoading
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

    methods: {
        ...mapActions({
            fetchMessages: 'messages/fetchMessages',
            resetMessagesState: 'messages/resetModuleState'
        }),

        listenForWhisperOnConversationChannel() {
            Echo.private(`conversation.${this.conversationId}`)
                .listenForWhisper('TypingEvent', event => {
                    this.typingUser = event.user
                    this.$nextTick(() => {
                        this.scrollToBottom()
                        console.log('scroll')
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
            this.$nextTick(() => {
                this.$refs.messagesList.scrollIntoView({
                    behavior: "smooth",
                    block: "end",
                    inline: "nearest"
                })
            })
        }
    },

    mounted() {
        this.listenForWhisperOnConversationChannel()
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
    }
}
</script>

