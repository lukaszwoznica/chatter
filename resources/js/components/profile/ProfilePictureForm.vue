<template>
    <div class="profile-card__form-wrapper">
        <div class="profile-card__form-header">
            <h1 class="profile-card__form-title">Profile Picture</h1>
            <app-button
                class="profile-card__remove-avatar-button"
                :disabled="!authUser.avatar_url"
                :loading="isRemovingAvatar"
                v-tippy="'Remove current avatar'"
                loader-color="#cc0000"
                @buttonClick="submitAvatarRemove">
                <font-awesome-icon :icon="['fas', 'trash-alt']" fixed-width/>
            </app-button>
        </div>

        <form class="form" @submit.prevent="submitAvatarUpdate" enctype='multipart/form-data'>
            <div class="form__group">
                <file-pond
                    name="file"
                    label-idle="Drag & Drop your avatar here or <span class='filepond--label-action'>Browse</span>"
                    accepted-file-types="image/jpeg, image/png, image/gif"
                    image-crop-aspect-ratio="1:1"
                    image-resize-target-width="200"
                    image-resize-target-height="200"
                    max-file-size="1MB"
                    ref="filepond"
                    :server="serverOptions"
                    :allow-multiple="false"
                    @processfile="setUploadedAvatar"
                />
            </div>
            <div class="form__button-wrapper">
                <app-button
                    type="submit"
                    class="button--primary"
                    :disabled="isSubmittingAvatar || !uploadedAvatarServerId"
                    :loading="isSubmittingAvatar">
                    Update Avatar
                </app-button>
            </div>
        </form>
    </div>
</template>

<script>
import AppButton from '../ui/AppButton'
import vueFilePond from 'vue-filepond'
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type'
import FilePondPluginFileValidateSize from 'filepond-plugin-file-validate-size'
import FilePondPluginImagePreview from 'filepond-plugin-image-preview'
import FilePondPluginImageCrop from 'filepond-plugin-image-crop'
import FilePondPluginImageResize from 'filepond-plugin-image-resize'
import FilePondPluginImageTransform from 'filepond-plugin-image-transform'
import ApiRoutes from '../../api/routes'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faTrashAlt } from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { mapGetters, mapMutations } from 'vuex'

const FilePond = vueFilePond(
    FilePondPluginFileValidateType,
    FilePondPluginFileValidateSize,
    FilePondPluginImagePreview,
    FilePondPluginImageCrop,
    FilePondPluginImageResize,
    FilePondPluginImageTransform
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
            isSubmittingAvatar: false,
            isRemovingAvatar: false,
            uploadedAvatarServerId: null,
            serverOptions: {
                url: ApiRoutes.FilePond.ApiUrl,
                process: '/process',
                revert: '/process',
                headers: {
                    'X-XSRF-TOKEN': this.$cookies.get('XSRF-TOKEN'),
                }
            },
            updateSuccessAlertOptions: {
                icon: 'success',
                titleText: 'Profile picture updated!',
                text: 'Your profile picture has been successfully updated.',
            },
            removeSuccessAlertOptions: {
                icon: 'success',
                titleText: 'Profile picture removed!',
                text: 'Your profile picture has been successfully removed.',
            },
            errorAlertOptions: {
                icon: 'error',
                titleText: 'Oops!',
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
                return
            }

            try {
                this.isSubmittingAvatar = true
                const response = await axios.post(ApiRoutes.Users.Avatar, {
                    avatar_server_id: this.uploadedAvatarServerId
                })

                this.updateAuthUserAvatarUrls(response.data.data.avatar_url, response.data.data.avatar_thumb_url)
                this.$refs.filepond.removeFiles()
                this.$swal(this.updateSuccessAlertOptions)
            } catch (error) {
                this.$swal({...this.errorAlertOptions,
                    text: 'Something went wrong while updating your profile picture.'
                })
            } finally {
                this.isSubmittingAvatar = false
            }
        },

        async submitAvatarRemove() {
            try {
                this.isRemovingAvatar = true
                await axios.delete(ApiRoutes.Users.Avatar)

                this.updateAuthUserAvatarUrls('', '')
                this.$swal(this.removeSuccessAlertOptions)
            } catch (error) {
                this.$swal({...this.errorAlertOptions,
                    text: 'Something went wrong while removing your profile picture.'
                })
            } finally {
                this.isRemovingAvatar = false
            }
        },

        updateAuthUserAvatarUrls(avatarUrl, avatarThumbUrl) {
            this.setAuthUser({...this.authUser,
                'avatar_url': avatarUrl,
                'avatar_thumb_url': avatarThumbUrl
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
