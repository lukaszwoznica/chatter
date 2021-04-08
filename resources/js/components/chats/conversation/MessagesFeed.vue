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
export default {
    name: "MessagesFeed",

    props: {
        messages: {
            type: Array,
            required: true
        },
        authUser: {
            required: true
        }
    }
}
</script>

<style scoped>
    .message--sent {
        color: blue;
    }
</style>
