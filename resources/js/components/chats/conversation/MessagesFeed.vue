<template>
    <div class="conversation__feed">
        <ul class="messages">
            <li v-for="(message, index) in messages" :key="message.id"
                class="message"
                :class="`message--${message.sender.id === authUser?.id ? 'sent' : 'received'}`">
                {{ message.text }}
                <span v-if="message.read_at">
                    Read at {{ message.read_at }}
                </span>
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
    span {
        color: green;
    }
</style>
