export default {
    methods: {
        autoResize(textareaObject, maxHeight) {
            console.log(textareaObject.scrollHeight)
            if (! (textareaObject.scrollHeight + 2 > maxHeight)) {
                textareaObject.style.overflow = 'hidden'
                textareaObject.style.height = 'auto'
                textareaObject.style.height = `${textareaObject.scrollHeight + 1.5}px`
            } else {
                textareaObject.style.overflow = 'auto'
            }
        }
    },
}
