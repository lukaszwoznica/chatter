<template>
    <div class="conversation__header">
        <div class="conversation__header-content">
            <div class="conversation__title">
                <h3>{{ selectedContact.full_name }}</h3>
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
    </div>
</template>

<script>
import dayjs from 'dayjs'
import relativeTime from 'dayjs/plugin/relativeTime'
import { mapGetters } from 'vuex'

export default {
    name: "ConversationHeader",

    computed: {
        ...mapGetters({
            selectedContact: 'contacts/selectedContact'
        }),
    },

    created() {
        dayjs.extend(relativeTime)
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
