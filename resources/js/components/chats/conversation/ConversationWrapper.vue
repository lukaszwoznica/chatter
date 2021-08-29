<template>
    <div class="conversation">
        <template v-if="selectedContact">
            <ConversationHeader :selected-contact="selectedContact"/>
            <MessagesFeed :messages="messages"
                          :auth-user="authUser"
                          :conversation-id="cantorPairConversationId"/>
            <MessageComposer :auth-user="authUser"
                             :selected-contact="selectedContact"
                             :conversation-id="cantorPairConversationId"/>
        </template>
        <template v-else>
            Select contact to start conversation
        </template>
    </div>
</template>

<script>
import ConversationHeader from "./ConversationHeader";
import MessagesFeed from "./MessagesFeed";
import MessageComposer from "./MessageComposer";
import {mapGetters, mapActions} from "vuex"

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

        selectedContactFullName() {
            if (this.selectedContact !== null) {
                return `${this.selectedContact.first_name} ${this.selectedContact.last_name}`
            }
        },

        cantorPairConversationId() {
            if (this.selectedContact !== null) {
                const x = Math.min(this.authUser.id, this.selectedContact.id)
                const y = Math.max(this.authUser.id, this.selectedContact.id)

                return (0.5 * (x + y) * (x + y + 1)) + y
            }
        }
    },

    methods: {
        ...mapActions({
            fetchMessages: 'messages/fetchMessages'
        }),
    },

    watch: {
        selectedContact() {
            if (this.selectedContact !== null) {
                this.fetchMessages(this.selectedContact.id)
            }

            if (this.previousConversationId !== null) {
                Echo.leave(`conversation.${this.previousConversationId}`)
            }

            Echo.private(`conversation.${this.cantorPairConversationId}`)
            this.previousConversationId = this.cantorPairConversationId
        }
    }
}
</script>
