const baseUrl = '/api/v1'

const ApiRoutes = {
    BaseUrl: baseUrl,
    GetCsrfCookie: `${baseUrl}/csrf-cookie`,
    Auth: {
        Login: `${baseUrl}/login`,
        Logout: `${baseUrl}/logout`,
        Register: `${baseUrl}/register`,
        GetAuthenticatedUser: `${baseUrl}/user`,
        ForgotPassword: `${baseUrl}/forgot-password`,
        ResetPassword: `${baseUrl}/reset-password`,
        UpdateProfileInfo: `${baseUrl}/user/profile-information`,
        UpdatePassword: `${baseUrl}/user/password`
    },
    Users: {
        Contacts: (userId) => `${baseUrl}/contacts/${userId}`,
        Search: (name, page) =>`${baseUrl}/users?search=${name}&page=${page}`,
        UploadAvatar: `${baseUrl}/user/avatar`
    },
    Messages: {
        GetConversationMessages: (userId, page) => `${baseUrl}/messages/${userId}?page=${page}`,
        SendMessage: `${baseUrl}/messages`,
        MarkMessageAsRead: (messageId) => `${baseUrl}/messages/${messageId}`
    },
    FilePond: {
        ApiUrl: `/filepond/api`
    }
}

export default ApiRoutes
