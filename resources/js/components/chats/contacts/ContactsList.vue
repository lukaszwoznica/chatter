<template>
    <div class="contacts">
        <AppButton @onClick="searchVisible = true">
            Search
        </AppButton>
        <div class="contacts__item"
             :class="selectedContact?.id === contact.id ? 'contacts__item--active' : ''"
             v-for="contact in sortedContacts" :key="contact.id"
             style="display: flex"
             @click="selectContact(contact.id)">

            <div class="contacts__avatar">
                <img src="https://via.placeholder.com/50" alt="" class="contacts__avatar__image">
            </div>

            <div class="contacts__item__name">
                {{ contactFullName(contact) }}
            </div>

            <div class="contacts__item__time">
                {{ contact.last_message }}
            </div>

            <div class="contacts__item__unread" v-show="contact.unread_messages > 0">
                {{ `(${contact.unread_messages})` }}
            </div>
        </div>
        <ContactSearchOverlay :visible="searchVisible" @onClose="searchVisible = false"/>
    </div>
</template>

<script>
import {mapGetters, mapActions} from 'vuex'
import AppButton from '../../ui/AppButton'
import ContactSearchOverlay from './ContactSearchOverlay'
import {orderBy} from 'lodash'

export default {
    name: "ContactsList",

    components: {
        ContactSearchOverlay,
        AppButton
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

        contactFullName: (contact) => `${contact.first_name} ${contact.last_name}`
    },

    created() {
        this.fetchContacts()
    }
}
</script>

<style scoped>
    .contacts {
        min-width: 350px;
    }
    .contacts__item--active {
        background-color: #d7d7d7;
        border: 1px solid black
    }
</style>
