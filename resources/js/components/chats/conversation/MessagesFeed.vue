<template>
    <div class="conversation__feed">
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
export default {
    name: "MessagesFeed",

    props: {
        messages: {
            type: Array,
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
            typingUser: null,
            typingClock: null
        }
    },

    methods: {
        listenForWhisperOnConversationChannel() {
            Echo.private(`conversation.${this.conversationId}`)
                .listenForWhisper('TypingEvent', event => {
                    this.typingUser = event.user

                    if (this.typingClock) {
                        clearTimeout(this.typingClock)
                    }
                    this.typingClock = setTimeout(() => this.typingUser = null, 1000)
                })
        }
    },

    mounted() {
        this.listenForWhisperOnConversationChannel()
    },

    watch: {
        conversationId() {
            this.listenForWhisperOnConversationChannel()
        }
    }
}
</script>

