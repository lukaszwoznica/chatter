<template>
    <button class="button"
            :class="classList"
            :type="type">
        <slot>Button</slot>
    </button>
</template>

<script>
export default {
    name: "AppButton",
    props: {
        type: {
            type: String,
            default: 'button'
        },
        classList: {
            type: Array
        }
    }
}
</script>
