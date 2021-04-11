<template>
    <div class="search-overlay" :class="{'search-overlay--visible': visible}">
        <span class="search-overlay__close-button" title="Close" @click="$emit('onClose')">
            x
        </span>
        <div class="search-overlay__container">
            <form>
                <input type="text" placeholder="Search...">
            </form>
        </div>
    </div>
</template>

<script>
import AppButton from "../../ui/AppButton";

export default {
    name: "ContactSearchOverlay",

    components: {
        AppButton
    },

    props: {
        visible: {
            type: Boolean,
            default: false
        }
    },

    methods: {
        closeOverlay() {
            this.visible = false
        }
    },
}
</script>

<style scoped>
.search-overlay {
    height: 100%;
    width: 100%;
    display: none;
    position: fixed;
    z-index: 1;
    top: 0;
    left: 0;
    background-color: rgba(0, 0, 0, 0.8);
}

.search-overlay--visible {
    display: block;
}

.search-overlay__container {
    position: relative;
    display: flex;
    justify-content: center;
    top: 15%;
    width: 100%;
}

form {
    width: 100%;
    text-align: center;
}

input {
    height: 50px;
    width: 50%;
    opacity: 0.9;
}

.search-overlay__close-button {
    position: absolute;
    top: 20px;
    right: 45px;
    font-size: 60px;
    cursor: pointer;
    color: white;
}
</style>
