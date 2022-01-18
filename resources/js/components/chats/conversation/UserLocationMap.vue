<template>
    <GMapMap v-if="!urlError" :center="coords" :zoom="zoom" map-type-id="terrain" :options="googleMapOptions">
        <GMapMarker :position="coords"/>
    </GMapMap>
    <div class="map-error" v-else>{{ urlError }}</div>
</template>

<script>
export default {
    name: "UserLocationMap",

    props: {
        googleMapsUrl: {
            type: String,
            required: true
        },
        zoom: {
            type: Number,
            default: 10
        }
    },

    data() {
        return {
            coords: {
                lat: null,
                lng: null
            },
            googleMapOptions: {
                zoomControl: false,
                mapTypeControl: false,
                scaleControl: false,
                streetViewControl: false,
                rotateControl: false,
                fullscreenControl: true,
                disableDefaultUi: true,
                gestureHandling: 'cooperative'
            },
            urlError: null
        }
    },

    created() {
        if (this.validateUrl(this.googleMapsUrl)) {
            this.parseCoordsFromGoogleMapsUrl(this.googleMapsUrl)
        } else {
            this.urlError = 'Invalid Google Maps URL'
        }
    },

    methods: {
        parseCoordsFromGoogleMapsUrl(url) {
            const coordsArray = (new URL(url)).searchParams.get('q')?.split(',', 2)
            Object.keys(this.coords).forEach(key => this.coords[key] = parseFloat(coordsArray?.shift()))
        },

        validateUrl(url) {
            const pattern = "(?:https?:\\/\\/)?(?:www\\.)?(?:google\\.com\\/maps\\?q=)" +
                "(?:[+-]?([0-9]*[.])?[0-9]+),(?:[+-]?([0-9]*[.])?[0-9]+)"

            return new RegExp(pattern, 'i').test(url)
        }
    }
}
</script>
