<template>
    <div class="conversation__title">
        <h3>{{ title }}</h3>
    </div>
</template>

<script>
export default {
    name: "ConversationTitle",

    props: {
        title: String
    }
}
</script>
