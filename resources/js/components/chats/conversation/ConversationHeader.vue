<template>
    <div class="conversation__header">
        <div class="conversation__title">
            <h3>{{ contactFullName }}</h3>
        </div>
        <div class="conversation__online-status">
            <template v-if="selectedContact.is_online">
                Active now
            </template>
            <template v-else>
                Last active: {{ selectedContact.last_online_at }}
            </template>
        </div>
    </div>
</template>

<script>
export default {
    name: "ConversationHeader",

    props: {
        selectedContact: {
            required: true
        }
    },

    computed: {
        contactFullName() {
            return `${this.selectedContact.first_name} ${this.selectedContact.last_name}`
        }
    }
}
</script>
