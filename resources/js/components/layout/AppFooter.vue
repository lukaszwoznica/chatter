<template>
    <footer class="footer">
        <p class="footer__text">
            &copy; {{ currentYear }} <strong>Chatter</strong> by Łukasz Woźnica
        </p>
    </footer>
</template>

<script>
import dayjs from 'dayjs'

export default {
    name: "AppFooter",

    computed: {
        currentYear() {
            return dayjs().year()
        }
    }
}
</script>
