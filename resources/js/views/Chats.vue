<template>
    <div class="chats-wrapper" style="display: flex;">
        <ContactsList/>
        <ConversationWrapper/>
    </div>
</template>

<script>
import ContactsList from "../components/chats/ContactsList";
import ConversationWrapper from "../components/chats/conversation/ConversationWrapper";

export default {
    name: "Chats",

    components: {
        ConversationWrapper,
        ContactsList
    }
}
</script>
