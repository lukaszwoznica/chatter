<template>
    <div class="conversation">
        <template v-if="selectedContact">
            <ConversationHeader/>
            <MessagesFeed :conversation-id="cantorPairConversationId" ref="feed"/>
            <MessageComposer :conversation-id="cantorPairConversationId" @message-sent="scrollFeedToBottom"/>
        </template>
        <template v-else>
            Select contact to start conversation
        </template>
    </div>
</template>

<script>
import ConversationHeader from './ConversationHeader'
import MessagesFeed from './MessagesFeed'
import MessageComposer from './MessageComposer'
import { mapGetters } from 'vuex'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faCircle, faCheckCircle } from '@fortawesome/free-regular-svg-icons'
import { faArrowRight } from '@fortawesome/free-solid-svg-icons'

export default {
    name: "ConversationWrapper",

    components: {
        MessageComposer,
        MessagesFeed,
        ConversationHeader
    },

    data() {
        return {
            previousConversationId: null
        }
    },

    computed: {
        ...mapGetters({
            selectedContact: 'contacts/selectedContact',
            messages: 'messages/allMessages',
            authUser: 'auth/user'
        }),

        cantorPairConversationId() {
            if (this.selectedContact !== null) {
                const x = Math.min(this.authUser.id, this.selectedContact.id)
                const y = Math.max(this.authUser.id, this.selectedContact.id)

                return (0.5 * (x + y) * (x + y + 1)) + y
            }
        }
    },

    watch: {
        selectedContact() {
            if (this.previousConversationId !== null) {
                Echo.leave(`conversation.${this.previousConversationId}`)
            }

            Echo.private(`conversation.${this.cantorPairConversationId}`)
            this.previousConversationId = this.cantorPairConversationId
        }
    },

    created() {
        library.add(faArrowRight, faCircle, faCheckCircle)
    },

    methods: {
        scrollFeedToBottom() {
            this.$refs.feed.scrollToBottom()
        }
    }
}
</script>
