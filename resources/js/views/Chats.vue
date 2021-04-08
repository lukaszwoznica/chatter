<template>
    <div class="chats-wrapper">
        <ContactsList/>
    </div>
</template>

<script>
import ContactsList from "../components/chats/ContactsList";

export default {
    name: "Chats",

    components: {
        ContactsList
    }
}
</script>
