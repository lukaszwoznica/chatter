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
                <p>
                    <template v-if="!isLoadingMessages && messages.length === 0">
                        There are no messages in this conversation yet.
                        Send your first message to {{ selectedContact.first_name }}.
                    </template>
                </p>
            </template>
        </infinite-loading>

        <ul class="messages">
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
        </ul>

        <span v-if="typingUser">
            {{ typingUser.first_name }} is typing...
        </span>
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

                    if (this.typingClock) {
                        clearTimeout(this.typingClock)
                    }
                    this.typingClock = setTimeout(() => this.typingUser = null, 1000)
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

