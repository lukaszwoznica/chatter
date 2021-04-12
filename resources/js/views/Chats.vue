<template>
    <div class="chats-wrapper" style="display: flex;">
        <ContactsList/>
        <ConversationWrapper/>
    </div>
</template>

<script>
import ContactsList from '../components/chats/contacts/ContactsList'
import ConversationWrapper from '../components/chats/conversation/ConversationWrapper'
import {mapGetters, mapMutations} from 'vuex'

export default {
    name: "Chats",

    components: {
        ConversationWrapper,
        ContactsList
    },

    computed: {
        ...mapGetters({
            authUser: 'auth/user',
            selectedContact: 'contacts/selectedContact'
        })
    },

    mounted() {
        Echo.private(`messages.${this.authUser.id}`)
            .listen('NewMessageEvent', event => {
                this.handleIncomingMessage(event.message)
            })
    },

    methods: {
        ...mapMutations({
            addMessage: 'messages/ADD_MESSAGE'
        }),

        handleIncomingMessage(message) {
            if (message.sender.id === this.selectedContact?.id) {
                this.addMessage(message)
            } else {
                alert(`You have new message from ${message.sender.first_name} ${message.sender.last_name}`)
            }
        }
    }
}
</script>
