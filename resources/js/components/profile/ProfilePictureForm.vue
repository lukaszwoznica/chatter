<template>
    <div class="profile-card__form-header">
        <h1 class="profile-card__form-title">Profile Picture</h1>
        <button class="profile-card__remove-avatar-button"
                :disabled="!authUser.avatar_url"
                v-tooltip.auto="'Remove current avatar'"
                @click="submitAvatarDelete">
            <font-awesome-icon :icon="['fas', 'trash-alt']" fixed-width/>
        </button>
    </div>

    <form class="form" @submit.prevent="submitAvatarUpdate" enctype='multipart/form-data'>
        <div class="form__group">
            <file-pond
                name="file"
                label-idle="Drag & Drop your avatar here or <span class='filepond--label-action'>Browse</span>"
                accepted-file-types="image/jpeg, image/png, image/gif"
                max-file-size="1MB"
                ref="filepond"
                :server="serverOptions"
                :allow-multiple="false"
                @processfile="setUploadedAvatar"
            />
        </div>
        <div class="form__button-wrapper">
            <AppButton type="submit" :classList="['button--primary']">
                Update Avatar
            </AppButton>
        </div>
    </form>
</template>

<script>
import AppButton from '../ui/AppButton'
import vueFilePond from 'vue-filepond'
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type'
import FilePondPluginFileValidateSize from 'filepond-plugin-file-validate-size'
import FilePondPluginImagePreview from 'filepond-plugin-image-preview'
import ApiRoutes from '../../api/routes'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faTrashAlt } from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { mapGetters, mapMutations } from 'vuex'

const FilePond = vueFilePond(
    FilePondPluginFileValidateType,
    FilePondPluginFileValidateSize,
    FilePondPluginImagePreview
)

export default {
    name: "ProfilePictureForm",

    components: {
        FilePond,
        AppButton,
        FontAwesomeIcon
    },

    computed: {
        ...mapGetters({
            authUser: 'auth/user'
        })
    },

    data() {
        return {
            uploadedAvatarServerId: null,
            serverOptions: {
                url: ApiRoutes.FilePond.ApiUrl,
                process: '/process',
                revert: '/process',
                headers: {
                    'X-XSRF-TOKEN': this.$cookies.get('XSRF-TOKEN'),
                }
            }
        }
    },

    created() {
        library.add(faTrashAlt)
    },

    methods: {
        ...mapMutations({
            setAuthUser: 'auth/SET_USER'
        }),

        async submitAvatarUpdate() {
            if (!this.uploadedAvatarServerId) {
                alert('Avatar image is required')
                return
            }

            try {
                const response = await axios.post(ApiRoutes.Users.Avatar, {
                    avatar_server_id: this.uploadedAvatarServerId
                })

                this.$refs.filepond.removeFiles()
                this.updateAuthUserAvatarUrl(response.data.data.avatar_url)
                alert('Profile picture has been successfully updated!')
            } catch (error) {
                alert('Something went wrong.')
            }
        },

        async submitAvatarDelete() {
            try {
                await axios.delete(ApiRoutes.Users.Avatar)

                this.updateAuthUserAvatarUrl('')
                alert('Profile picture has been successfully removed!')
            } catch (error) {
                alert('Something went wrong.')
            }
        },

        updateAuthUserAvatarUrl(avatarUrl) {
            this.setAuthUser({...this.authUser,
                'avatar_url': avatarUrl
            })
        },

        setUploadedAvatar(error, file) {
            if (!error) {
                this.uploadedAvatarServerId = file.serverId
            }
        }
    }
}
</script>
