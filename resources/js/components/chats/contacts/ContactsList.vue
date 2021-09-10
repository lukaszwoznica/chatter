<template>
    <div class="contacts">
        <div class="contacts__header">
            <h2>Contacts</h2>

            <font-awesome-icon :icon="['fas', 'search']"
                               class="contacts__search-icon"
                               @click="searchVisible = true">
            </font-awesome-icon>
        </div>
        <div class="contacts__item"
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
        </div>

        <ContactSearchOverlay :visible="searchVisible" @onClose="searchVisible = false"/>
    </div>
</template>

<script>
import {mapGetters, mapActions} from 'vuex'
import ContactSearchOverlay from './ContactSearchOverlay'
import {orderBy} from 'lodash'
import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome'
import dayjs from 'dayjs'
import isToday from 'dayjs/plugin/isToday'
import weekOfYear from 'dayjs/plugin/weekOfYear'

export default {
    name: "ContactsList",

    components: {
        ContactSearchOverlay,
        FontAwesomeIcon
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
        }
    },

    created() {
        this.fetchContacts()
    },

    mounted() {
        dayjs.extend(isToday)
        dayjs.extend(weekOfYear)
    }
}
</script>
