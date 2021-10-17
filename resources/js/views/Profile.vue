<template>
    <div class="container">
        <div class="card profile-card">
            <div class="profile-card__menu">
                <div class="profile-card__info">
                    <div class="profile-card__avatar">
                        <user-avatar :username="authUser?.full_name ?? ''"
                                     :img-src="authUser?.avatar_url"
                                     :size="200"/>
                    </div>
                    <div class="profile-card__name">
                        <h2>{{ authUser?.full_name }}</h2>
                    </div>
                </div>
                <nav class="profile-card__nav">
                    <div class="profile-card__nav__item"
                         :class="activeTab === 'GeneralInfoForm' ? 'profile-card__nav__item--active' : ''">
                        <button class="profile-card__nav__link" @click="activeTab = 'GeneralInfoForm'">
                            <font-awesome-icon :icon="['fas', 'user']" fixed-width/>
                            General Info
                        </button>
                    </div>
                    <div class="profile-card__nav__item"
                         :class="activeTab === 'ChangePasswordForm' ? 'profile-card__nav__item--active' : ''">
                        <button class="profile-card__nav__link" @click="activeTab = 'ChangePasswordForm'">
                            <font-awesome-icon :icon="['fas', 'lock']" fixed-width/>
                            Password
                        </button>
                    </div>
                    <div class="profile-card__nav__item"
                         :class="activeTab === 'ProfilePictureForm' ? 'profile-card__nav__item--active' : ''">
                        <button class="profile-card__nav__link" @click="activeTab = 'ProfilePictureForm'">
                            <font-awesome-icon :icon="['fas', 'image']" fixed-width/>
                            Profile Picture
                        </button>
                    </div>
                </nav>
            </div>
            <div class="profile-card__form-wrapper">
                <component :is="activeTab"/>
            </div>
        </div>
    </div>
</template>

<script>
import GeneralInfoForm from '../components/profile/GeneralInfoForm'
import ChangePasswordForm from '../components/profile/ChangePasswordForm'
import ProfilePictureForm from '../components/profile/ProfilePictureForm'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faUser, faLock, faImage } from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { mapGetters } from 'vuex'
import UserAvatar from '../components/ui/UserAvatar'

export default {
    name: "Profile",

    components: {
        GeneralInfoForm,
        ChangePasswordForm,
        ProfilePictureForm,
        FontAwesomeIcon,
        UserAvatar
    },

    data() {
        return {
            activeTab: 'GeneralInfoForm'
        }
    },

    computed: {
        ...mapGetters({
            authUser: 'auth/user'
        })
    },

    created() {
        library.add(faUser, faLock, faImage)
    }
}
</script>
