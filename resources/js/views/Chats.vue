<template>
    <div class="chats-wrapper" style="display: flex;">
        <ContactsList/>
        <ConversationWrapper/>
    </div>
</template>

<script>
import ContactsList from '../components/chats/contacts/ContactsList'
import ConversationWrapper from '../components/chats/conversation/ConversationWrapper'
import {mapGetters, mapMutations, mapActions} from 'vuex'

export default {
    name: "Chats",

    components: {
        ConversationWrapper,
        ContactsList
    },

    computed: {
        ...mapGetters({
            authUser: 'auth/user',
            selectedContact: 'contacts/selectedContact',
            getContactById: 'contacts/contactById'
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
            addMessage: 'messages/ADD_MESSAGE',
            updateContact: 'contacts/UPDATE_CONTACT'
        }),

        ...mapActions({
            addNewContact: 'contacts/addNewContact',
        }),

        handleIncomingMessage(message) {
            this.updateContactListAfterNewMessage(message)

            if (message.sender.id === this.selectedContact?.id) {
                this.addMessage(message)
            }
        },

        updateContactListAfterNewMessage(message) {
            const senderContact = this.getContactById(message.sender.id)
            if (!senderContact) {
                this.addNewContact({
                    ...message.sender,
                    last_message: message.created_at,
                    unread_messages: 1
                })
                return
            }

            if (this.selectedContact?.id !== senderContact.id) {
                senderContact.unread_messages++
            }
            senderContact.last_message = message.created_at
            this.updateContact(senderContact)
        }
    }
}
</script>
