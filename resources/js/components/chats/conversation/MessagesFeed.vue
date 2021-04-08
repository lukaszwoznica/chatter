<template>
    <div class="conversation__feed">
        <ul class="messages">
            <li v-for="message in messages" :key="message.id"
                class="message"
                :class="`message--${message.sender.id === authUser?.id ? 'sent' : 'received'}`">
                {{ message.text }}
            </li>
        </ul>
    </div>
</template>

<script>
import {mapGetters} from 'vuex'

export default {
    name: "MessagesFeed",

    props: {
        messages: Array
    },

    computed: {
        ...mapGetters({
            authUser: 'auth/user',
        }),
    }
}
</script>

<style scoped>
    .message--sent {
        color: blue;
    }
</style>
