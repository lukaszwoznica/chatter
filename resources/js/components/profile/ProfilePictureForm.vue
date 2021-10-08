<template>
    <h1 class="profile-card__form-title">Profile Picture</h1>

    <form class="form" @submit.prevent="submitForm" enctype='multipart/form-data'>
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

const FilePond = vueFilePond(
    FilePondPluginFileValidateType,
    FilePondPluginFileValidateSize,
    FilePondPluginImagePreview
)

export default {
    name: "ProfilePictureForm",

    components: {
        FilePond,
        AppButton
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

    methods: {
        async submitForm() {
            if (! this.uploadedAvatarServerId) {
                alert('Avatar image is required')
                return
            }

            try {
                await axios.post(ApiRoutes.Users.UploadAvatar, {
                    avatar_server_id: this.uploadedAvatarServerId
                })

                alert('Profile picture has been successfully updated!')
            } catch (error) {
                alert('Something went wrong')
            }
        },

        setUploadedAvatar(error, file) {
            if (!error) {
                this.uploadedAvatarServerId = file.serverId
            }
        }
    }
}
</script>
