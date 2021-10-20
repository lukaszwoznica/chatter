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

    data() {
        return {
            sounds: {
                newMessageAudio: new Audio('/audio/new-message.mp3'),
                newMessageSelectedContactAudio: new Audio('/audio/new-message-selected.mp3'),
                messageReadAudio: new Audio('/audio/message-read.mp3')
            }
        }
    },

    computed: {
        ...mapGetters({
            authUser: 'auth/user',
            selectedContact: 'contacts/selectedContact',
            getContactById: 'contacts/contactById',
            soundsMuted: 'sounds/soundsMuted'
        })
    },

    watch: {
        soundsMuted() {
            this.toggleAudioObjectsMute()
        }
    },

    created() {
        if (this.soundsMuted) {
            this.muteAudioObjects()
        }
    },

    mounted() {
        this.listenForMessageEvents()
        this.listenForUserOnlineStatusNotifications()
    },

    beforeUnmount() {
        Echo.leave(`messages.${this.authUser?.id}`)
    },

    methods: {
        ...mapMutations({
            addMessage: 'messages/ADD_MESSAGE',
            updateMessage: 'messages/UPDATE_MESSAGE'
        }),

        ...mapActions({
            addNewContact: 'contacts/addNewContact',
            markMessageAsRead: 'messages/markMessageAsRead',
            updateContact: 'contacts/updateContact'
        }),

        listenForMessageEvents() {
            Echo.private(`messages.${this.authUser.id}`)
                .listen('NewMessageEvent', event => {
                    this.handleIncomingMessage(event.message)
                })
                .listen('MessagesReadEvent', event => {
                    if (this.selectedContact.id === event.messages[0].recipient.id) {
                        event.messages.forEach(message => this.updateMessage(message))
                        this.sounds.messageReadAudio.play()
                    }
                })
        },

        listenForUserOnlineStatusNotifications() {
            Echo.private(`user-notifications.${this.authUser.id}`)
                .notification(notification => {
                    if (notification.type === 'UserOnlineStatusChangedNotification') {
                        this.updateContact(notification.user)
                    }
                })
        },

        handleIncomingMessage(message) {
            this.updateContactListAfterNewMessage(message)

            if (message.sender.id === this.selectedContact?.id) {
                this.addMessage(message)
                this.markMessageAsRead(message.id)

                this.sounds.newMessageSelectedContactAudio.play()
                this.$refs.conversationWrapper?.scrollFeedToBottom()
            } else {
                this.sounds.newMessageAudio.play()
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
        },

        muteAudioObjects() {
            Object.entries(this.sounds).forEach(([soundName, audioObject]) => {
                audioObject.muted = true
            })
        },

        toggleAudioObjectsMute() {
            Object.entries(this.sounds).forEach(([soundName, audioObject]) => {
                audioObject.muted = !audioObject.muted
            })
        }
    }
}
</script>
