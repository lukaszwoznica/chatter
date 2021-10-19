<template>
    <div class="chats-container">
        <ContactsList/>
        <ConversationWrapper ref="conversationWrapper"/>
    </div>
</template>

<script>
import ContactsList from '../components/chats/contacts/ContactsList'
import ConversationWrapper from '../components/chats/conversation/ConversationWrapper'
import { mapGetters, mapMutations, mapActions } from 'vuex'

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
            .listen('MessagesReadEvent', event => {
                if (this.selectedContact.id === event.messages[0].recipient.id) {
                    event.messages.forEach(message => this.updateMessage(message))
                }
            })

        Echo.private(`user-notifications.${this.authUser.id}`)
            .notification(notification => {
                if (notification.type === 'UserOnlineStatusChangedNotification') {
                    this.updateContactOnlineStatus(notification.user)
                }
            })
    },

    beforeUnmount() {
        Echo.leave(`messages.${this.authUser.id}`)
    },

    methods: {
        ...mapMutations({
            addMessage: 'messages/ADD_MESSAGE',
            updateContact: 'contacts/UPDATE_CONTACT',
            updateMessage: 'messages/UPDATE_MESSAGE'
        }),

        ...mapActions({
            addNewContact: 'contacts/addNewContact',
            markMessageAsRead: 'messages/markMessageAsRead',
            updateContactOnlineStatus: 'contacts/updateContactOnlineStatus'
        }),

        handleIncomingMessage(message) {
            this.updateContactListAfterNewMessage(message)

            if (message.sender.id === this.selectedContact?.id) {
                this.addMessage(message)
                this.markMessageAsRead(message.id)
                this.$nextTick(() => {
                    this.$refs.conversationWrapper?.scrollFeedToBottom()
                })
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
