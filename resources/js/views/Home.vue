<template>
    <div class="home">
        <div class="home__text">
            <h1 class="home__title">Welcome To Chatter</h1>
            <h4 class="home__desc">
                A real-time chat application built with Laravel, Vue.js and Pusher.
                Create your account and start chatting with other users now.
            </h4>
            <div class="home__cta">
                <router-link :to="{name: 'register'}" class="cta-button">Join Now</router-link>
            </div>
        </div>
        <div class="home__animation">
            <v-lottie-player
                name="chatAnim"
                width="100%" height="500px"
                path="/lottie/home-chat-anim.json"
                loop autoplay>
            </v-lottie-player>
        </div>
    </div>
</template>

<script>
import VueLottiePlayer from 'vue-lottie-player'

export default {
    name: "Home",

    components: {
        vLottiePlayer: VueLottiePlayer
    }
}
</script>
