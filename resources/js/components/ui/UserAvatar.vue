<template>
    <div class="avatar" :style="style">
        <img v-if="imgSrc" :src="imgSrc" class="avatar__img" alt="Avatar" @error="onImgError">
        <span v-else class="avatar__initials">{{ userInitials }}</span>
    </div>
</template>

<script>
export default {
    name: "UserAvatar",

    props: {
        username: {
            type: String,
            required: true
        },
        imgSrc: {
            type: String
        },
        size: {
            type: Number
        }
    },

    data() {
        return {
            imgError: false,
            backgroundColors: [
                '#F44336', '#3F51B5', '#CDDC39', '#7d7c7c',
                '#FF4081', '#2196F3', '#009688', '#8BC34A',
                '#4CAF50', '#00BCD4', '#9C27B0', '#FF9800',
                '#673AB7', '#795548', '#FF5722', '#607D8B'
            ]
        }
    },

    computed: {
        isImage() {
            return this.imgSrc && !this.imgError
        },

        userInitials() {
            const names = this.username.split(' ')
            let initials = names[0].substring(0, 1).toUpperCase();

            if (names.length > 1) {
                initials += names[names.length - 1].substring(0, 1).toUpperCase();
            }

            return initials;
        },

        style() {
            return {
                backgroundColor: !this.isImage ? this.getRandomBackgroundColor(this.username.length) : 'transparent',
                width: `${this.size}px` || 'inherit',
                height: `${this.size}px` || 'inherit',
                fontSize: this.size ? `${Math.floor(this.size / 2.5)}px` : 'inherit'
            }
        }
    },

    methods: {
        getRandomBackgroundColor(seed) {
            return this.backgroundColors[seed  % this.backgroundColors.length]
        },

        onImgError() {
            this.imgError = true
        }
    }
}
</script>
