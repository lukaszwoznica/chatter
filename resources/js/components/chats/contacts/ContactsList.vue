<template>
    <div class="contacts" ref="contacts">
        <div class="contacts__header" ref="contactsHeader">
            <h2>Contacts</h2>

            <font-awesome-icon :icon="['fas', 'search']"
                               class="contacts__search-icon"
                               @click="searchVisible = true">
            </font-awesome-icon>
        </div>

        <ul class="contacts__list" ref="contactsList">
            <li class="contacts__item"
                :class="selectedContact?.id === contact.id ? 'contacts__item--active' : ''"
                v-for="contact in sortedContacts" :key="contact.id"
                @click="selectContact(contact.id)">

                <div class="contacts__avatar">
                    <user-avatar :username="contact.full_name" :size="50"/>
                    <div class="contacts__online-indicator" v-show="contact.is_online">
                        &#9679;
                    </div>
                </div>

                <div class="contacts__name">
                    {{ contact.full_name }}
                </div>

                <div class="contacts__last-message">
                    {{ formatLastMessageDate(contact.last_message) }}
                </div>

                <div class="contacts__unread-messages" v-show="contact.unread_messages > 0">
                    {{ contact.unread_messages }}
                </div>
            </li>
        </ul>

        <HamburgerButton @onHamburgerClick="toggleContactsActive"></HamburgerButton>
    </div>

    <ContactSearchOverlay :visible="searchVisible" @onClose="searchVisible = false"/>
    <div class="overlay" ref="overlay"></div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'
import ContactSearchOverlay from './ContactSearchOverlay'
import { orderBy } from 'lodash'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import dayjs from 'dayjs'
import isToday from 'dayjs/plugin/isToday'
import weekOfYear from 'dayjs/plugin/weekOfYear'
import HamburgerButton from '../../ui/HamburgerButton'
import UserAvatar from '../../ui/UserAvatar'

export default {
    name: "ContactsList",

    components: {
        ContactSearchOverlay,
        FontAwesomeIcon,
        HamburgerButton,
        UserAvatar
    },

    data() {
        return {
            searchVisible: false
        }
    },

    computed: {
        ...mapGetters({
            contacts: 'contacts/allContacts',
            selectedContact: 'contacts/selectedContact'
        }),

        sortedContacts() {
            return orderBy(this.contacts, ['last_message'], ['desc'])
        }
    },

    created() {
        this.fetchContacts()
    },

    mounted() {
        dayjs.extend(isToday)
        dayjs.extend(weekOfYear)

        window.addEventListener('resize', this.toggleContactsActiveOnWindowResize)
        this.$refs.contactsList.addEventListener('scroll', this.toggleContactsHeaderBorderBottomOnScroll)
    },

    beforeUnmount() {
        window.removeEventListener('resize', this.toggleContactsActiveOnWindowResize)
    },

    methods: {
        ...mapActions({
            fetchContacts: 'contacts/fetchContacts',
            selectContact: 'contacts/selectContact'
        }),

        formatLastMessageDate(date) {
            const now = dayjs()
            const lastMessage = dayjs(date)
            let dateFormat = ''
            if (!lastMessage.isValid()) {
                return
            }

            if (lastMessage.isToday()) {
                dateFormat = 'HH:mm'
            } else if (now.week() === lastMessage.week() && now.year() === lastMessage.year()) {
                dateFormat = 'ddd'
            } else if (now.year() === lastMessage.year()) {
                dateFormat = 'DD.MM'
            } else {
                dateFormat = 'DD.MM.YYYY'
            }

            return lastMessage.format(dateFormat)
        },

        toggleContactsActive() {
            this.$refs.contacts.classList.toggle('contacts--active')
            this.$refs.overlay.classList.toggle('overlay--active')
        },

        toggleContactsActiveOnWindowResize() {
            if (!this.$refs.contacts.classList.contains('contacts--active')) {
                return
            }

            if (window.innerWidth > 992) {
                this.$refs.overlay.classList.remove('overlay--active')
            } else {
                this.$refs.overlay.classList.add('overlay--active')
            }
        },

        toggleContactsHeaderBorderBottomOnScroll() {
            if (this.$refs.contactsList.scrollTop !== 0) {
                this.$refs.contactsHeader.classList.add('contacts__header--border')
            } else {
                this.$refs.contactsHeader.classList.remove('contacts__header--border')
            }
        }
    }
}
</script>
