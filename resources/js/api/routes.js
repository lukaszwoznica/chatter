const baseUrl = '/api/v1'

const ApiRoutes = {
    BaseUrl: baseUrl,
    GetCsrfCookie: `${baseUrl}/csrf-cookie`,
    Auth: {
        Login: `${baseUrl}/login`,
        Logout: `${baseUrl}/logout`,
        Register: `${baseUrl}/register`,
        GetAuthenticatedUser: `${baseUrl}/users/auth-user`,
        ForgotPassword: `${baseUrl}/forgot-password`,
        ResetPassword: `${baseUrl}/reset-password`,
        UpdateProfileInfo: `${baseUrl}/user/profile-information`,
        UpdatePassword: `${baseUrl}/user/password`
    },
    Users: {
        Contacts: (userId) => `${baseUrl}/users/${userId}/contacts`,
        Search: (name, page) =>`${baseUrl}/users?search=${name}&page=${page}`,
        Avatar: `${baseUrl}/users/avatar`
    },
    Messages: {
        GetConversationMessages: (userId, page) => `${baseUrl}/messages/${userId}?page=${page}`,
        SendMessage: `${baseUrl}/messages`,
        MarkMessageAsRead: (messageId) => `${baseUrl}/messages/${messageId}`
    },
    FilePond: {
        ApiUrl: `${baseUrl}/filepond/api`
    }
}

export default ApiRoutes
