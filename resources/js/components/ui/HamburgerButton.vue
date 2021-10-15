<template>
    <button class="hamburger" @click="onClick" ref="hamburger">
        <span class="hamburger__box">
            <span class="hamburger__inner">
            </span>
        </span>
    </button>
</template>

<script>
export default {
    name: "HamburgerButton",

    methods: {
        onClick() {
            this.toggleActiveClass()
            this.$emit('onHamburgerClick')
        },

        toggleActiveClass() {
            this.$refs.hamburger.classList.toggle('hamburger--active')
        }
    }
}
</script>
