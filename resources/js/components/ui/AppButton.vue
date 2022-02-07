<template>
    <button
        class="button"
        :type="type"
        :disabled="disabled"
        @click="$emit('buttonClick')"
    >
        <span :style="{visibility: !loading ? 'visible' : 'hidden'}">
            <slot>
                Button
            </slot>
        </span>
        <span v-if="loading" class="button__loader">
            <vue-loaders-ball-clip-rotate :color="loaderColor"/>
        </span>
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
        disabled: {
            type: Boolean,
            default: false
        },
        loading: {
            type: Boolean,
            default: false
        },
        loaderColor: {
            type: String,
            default: '#ffffff'
        }
    }
}
</script>
