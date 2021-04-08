<template>
    <div class="conversation" style="background-color: lightgray">
        <ConversationTitle :title="selectedContactFullName"/>
        <MessagesFeed :messages="messages"/>
        <MessageComposer/>
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
            messages: 'messages/allMessages'
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
