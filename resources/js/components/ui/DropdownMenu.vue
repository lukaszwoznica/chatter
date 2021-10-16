<template>
    <div class="dropdown" v-click-outside="closeDropdown">
        <div class="dropdown__toggle" @click="toggleDropdown">
            <slot name="dropdown-toggler">
                Dropdown
            </slot>
            <div class="dropdown__arrow" ref="dropdownArrow">
                &#9662;
            </div>
        </div>
        <div class="dropdown__menu" ref="dropdownMenu" @click="closeDropdown">
            <slot name="dropdown-items"/>
        </div>
    </div>
</template>

<script>
import vClickOutside from "click-outside-vue3";

export default {
    name: "DropdownMenu",

    directives: {
        clickOutside: vClickOutside.directive
    },

    methods: {
        toggleDropdown() {
            this.$refs.dropdownMenu.classList.toggle('dropdown__menu--active')
            this.$refs.dropdownArrow.classList.toggle('dropdown__arrow--rotated')
        },

        closeDropdown() {
            this.$refs.dropdownMenu.classList.remove('dropdown__menu--active')
            this.$refs.dropdownArrow.classList.remove('dropdown__arrow--rotated')
        },
    }
}
</script>
