<template>
    <div class="contacts" ref="contacts">
        <div class="contacts__header">
            <h2>Contacts</h2>

            <font-awesome-icon :icon="['fas', 'search']"
                               class="contacts__search-icon"
                               @click="searchVisible = true">
            </font-awesome-icon>
        </div>

        <ul class="contacts__list">
            <li class="contacts__item"
                 :class="selectedContact?.id === contact.id ? 'contacts__item--active' : ''"
                 v-for="contact in sortedContacts" :key="contact.id"
                 @click="selectContact(contact.id)">

                <div class="contacts__avatar">
                    <img src="https://via.placeholder.com/500" alt="" class="contacts__avatar__image">
                    <div class="contacts__online-indicator" v-show="contact.is_online">
                        &#9679;
                    </div>
                </div>

                <div class="contacts__name">
                    {{ contactFullName(contact) }}
                </div>

                <div class="contacts__last-message">
                    {{ formatLastMessageDate(contact.last_message) }}
                </div>

                <div class="contacts__unread-messages" v-show="contact.unread_messages > 0">
                    {{ contact.unread_messages }}
                </div>
            </li>
        </ul>

        <ContactSearchOverlay :visible="searchVisible" @onClose="searchVisible = false"/>
        <HamburgerButton @onHamburgerClick="toggleActive"></HamburgerButton>
    </div>
    <div class="overlay" ref="overlay"></div>
</template>

<script>
import {mapGetters, mapActions} from 'vuex'
import ContactSearchOverlay from './ContactSearchOverlay'
import {orderBy} from 'lodash'
import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome'
import dayjs from 'dayjs'
import isToday from 'dayjs/plugin/isToday'
import weekOfYear from 'dayjs/plugin/weekOfYear'
import HamburgerButton from "../../ui/HamburgerButton";

export default {
    name: "ContactsList",

    components: {
        ContactSearchOverlay,
        FontAwesomeIcon,
        HamburgerButton
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

    methods: {
        ...mapActions({
            fetchContacts: 'contacts/fetchContacts',
            selectContact: 'contacts/selectContact'
        }),

        contactFullName: (contact) => `${contact.first_name} ${contact.last_name}`,

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

        toggleActive() {
            this.$refs.contacts.classList.toggle('contacts--active')
            this.$refs.overlay.classList.toggle('overlay--active')
        }
    },

    created() {
        this.fetchContacts()
    },

    mounted() {
        dayjs.extend(isToday)
        dayjs.extend(weekOfYear)

        window.addEventListener('resize', () => {
            if (!this.$refs.contacts.classList.contains('contacts--active')) {
                return
            }

            if (window.innerWidth > 992) {
                this.$refs.overlay.classList.remove('overlay--active')
            } else {
                this.$refs.overlay.classList.add('overlay--active')
            }
        })
    }
}
</script>
