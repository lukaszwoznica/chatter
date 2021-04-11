<template>
    <div class="contacts">
        <AppButton @onClick="searchVisible = true">
            Search
        </AppButton>
        <div class="contacts__item"
             v-for="contact in contacts" :key="contact.id"
             style="display: flex"
             @click="selectContact(contact.id)">
            <div class="contacts__avatar">
                <img src="https://via.placeholder.com/50" alt="" class="contacts__avatar__image">
            </div>
            <div class="contacts__item__top">
                <div class="contacts__item__name">
                    {{ contactFullName(contact) }}
                </div>
                <div class="contacts__item__time">

                </div>
            </div>
            <div class="contacts__item__bottom">
                <div class="contacts__item__message">

                </div>
                <div class="contacts__item__status">
                </div>
            </div>
        </div>
        <ContactSearchOverlay :visible="searchVisible" @onClose="searchVisible = false"/>
    </div>
</template>

<script>
import {mapGetters, mapActions} from 'vuex'
import AppButton from '../../ui/AppButton';
import ContactSearchOverlay from './ContactSearchOverlay';

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
