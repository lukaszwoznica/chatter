<template>
    <div class="conversation__title">
        <h3>{{ title }}</h3>
    </div>
</template>

<script>
export default {
    name: "ConversationHeader",

    props: {
        title: String
    }
}
</script>
