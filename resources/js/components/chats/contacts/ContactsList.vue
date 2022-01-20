<template>
    <div class="contacts" ref="contacts">
        <div class="contacts__header" ref="contactsHeader">
            <h2>Contacts</h2>

            <div class="contacts__icons">
                <font-awesome-icon
                    :icon="['fas', 'search']"
                    class="contacts__search-icon"
                    @click="searchVisible = true"
                    v-tippy="'User search'"
                    fixed-width
                />
                <font-awesome-icon
                    :icon="['fas', soundsMuted ? 'volume-mute' : 'volume-up']"
                    class="contacts__sound-icon"
                    :class="{'contacts__sound-icon--mute': soundsMuted}"
                    @click="toggleSounds"
                    v-tippy="'Chat sounds'"
                    fixed-width
                />
            </div>
        </div>

        <ul class="contacts__list" ref="contactsList">
            <li class="contacts__item"
                :class="selectedContact?.id === contact.id ? 'contacts__item--active' : ''"
                v-for="contact in sortedContacts" :key="contact.id"
                @click="selectContact(contact.id)">

                <div class="contacts__avatar">
                    <user-avatar :username="contact.full_name"
                                 :img-src="contact.avatar_thumb_url"
                                 :size="50"/>
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
                    {{ contact.unread_messages < 99 ? contact.unread_messages : '99+' }}
                </div>
            </li>
        </ul>

        <HamburgerButton @onHamburgerClick="toggleContactsActive" ref="hamburgerButton"/>
    </div>

    <ContactSearchOverlay :visible="searchVisible" @onClose="searchVisible = false"/>
    <div class="overlay" ref="overlay"></div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'
import ContactSearchOverlay from './ContactSearchOverlay'
import { orderBy } from 'lodash'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faSearch, faVolumeUp, faVolumeMute } from '@fortawesome/free-solid-svg-icons'
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
            selectedContact: 'contacts/selectedContact',
            soundsMuted: 'sounds/soundsMuted'
        }),

        sortedContacts() {
            return orderBy(this.contacts, ['last_message'], ['desc'])
        }
    },

    watch: {
        selectedContact() {
            if (this.$refs.overlay.classList.contains('overlay--active')) {
                this.toggleContactsActive()
                this.$refs.hamburgerButton.toggleActiveClass()
            }
        }
    },

    created() {
        this.fetchContacts()
        library.add(faSearch, faVolumeUp, faVolumeMute)
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
            selectContact: 'contacts/selectContact',
            toggleSounds: 'sounds/toggleSounds'
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
