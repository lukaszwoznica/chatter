<template>
    <div class="conversation__header">
        <div class="conversation__title">
            <h3>{{ contactFullName }}</h3>
        </div>
        <div class="conversation__online-status">
            <template v-if="selectedContact.is_online">
                Active now
            </template>
            <template v-else-if="selectedContact.last_online_at">
                Active {{ formatLastActiveDate(selectedContact.last_online_at) }}
            </template>
        </div>
    </div>
</template>

<script>
import dayjs from 'dayjs'
import relativeTime from 'dayjs/plugin/relativeTime'

export default {
    name: "ConversationHeader",

    props: {
        selectedContact: {
            required: true
        }
    },

    created() {
        dayjs.extend(relativeTime)
    },

    computed: {
        contactFullName() {
            return `${this.selectedContact.first_name} ${this.selectedContact.last_name}`
        }
    },

    methods: {
        formatLastActiveDate(date) {
            if (!dayjs(date).isValid()) {
                return
            }

            return dayjs(date).fromNow()
        }
    }
}
</script>
