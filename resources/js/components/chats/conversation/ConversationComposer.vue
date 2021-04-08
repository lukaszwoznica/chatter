<template>
    <div class="conversation__composer">
        <form>
            <textarea cols="40" rows="1"></textarea>
            <AppButton>Send</AppButton>
        </form>
    </div>
</template>

<script>
import AppButton from "../../ui/AppButton";
export default {
    name: "ConversationComposer",

    components: {
        AppButton
    }
}
</script>
