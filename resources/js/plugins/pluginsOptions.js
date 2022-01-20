const PluginsOptions = {
    sweetAlertOptions: {
        buttonsStyling: false,
        customClass: {
            confirmButton: 'button button--primary'
        }
    },

    googleMapsOptions: {
        load: {
            key: process.env.MIX_GOOGLE_API_KEY
        }
    },

    vueTippyOptions: {
        defaultProps: {
            arrow: false,
            theme: 'translucent',
            animation: 'scale-subtle',
            offset: [0, 5],
            role: 'tooltip',
            delay: [100],
        }
    }
}

export default PluginsOptions
