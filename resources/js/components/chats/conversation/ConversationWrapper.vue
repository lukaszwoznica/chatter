<template>
    <div class="conversation" style="background-color: lightgray">
        <ConversationTitle :title="selectedContactFullName"/>
        <ConversationFeed :messages="messages"/>
        <ConversationComposer/>
    </div>
</template>

<script>
import ConversationTitle from "./ConversationTitle";
import ConversationFeed from "./ConversationFeed";
import ConversationComposer from "./ConversationComposer";
import {mapGetters, mapActions} from "vuex"

export default {
    name: "ConversationWrapper",

    components: {
        ConversationComposer,
        ConversationFeed,
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
