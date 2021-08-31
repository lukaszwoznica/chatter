export default {
    methods: {
        autoResize(event, maxHeight) {
            if (! (event.target.scrollHeight + 2 > maxHeight)) {
                event.target.style.overflow = 'hidden'
                event.target.style.height = 'auto'
                event.target.style.height = `${event.target.scrollHeight + 2}px`
            } else {
                event.target.style.overflow = 'auto'
            }
        }
    },
}
