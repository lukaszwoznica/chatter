<template>
    <div class="conversation" style="background-color: lightgray">
        <template v-if="selectedContact">
            <ConversationTitle :title="selectedContactFullName"/>
            <MessagesFeed :messages="messages" :auth-user="authUser"/>
            <MessageComposer :auth-user="authUser" :selected-contact="selectedContact"/>
        </template>
        <template v-else>
            Select contact to start conversation
        </template>
    </div>
</template>

<script>
import ConversationTitle from "./ConversationTitle";
import MessagesFeed from "./MessagesFeed";
import MessageComposer from "./MessageComposer";
import {mapGetters, mapActions} from "vuex"

export default {
    name: "ConversationWrapper",

    components: {
        MessageComposer,
        MessagesFeed,
        ConversationTitle
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
        }
    },

    methods: {
        ...mapActions({
            fetchMessages: 'messages/fetchMessages'
        }),
    },

    watch: {
        selectedContact() {
            this.fetchMessages(this.selectedContact.id)
        }
    }
}
</script>
