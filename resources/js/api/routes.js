const baseUrl = process.env.MIX_API_BASE_URL

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
    OAuth: {
        GetProviderRedirectUrl: (provider) => `${baseUrl}/oauth/${provider}`,
        HandleCallback: (provider) => `${baseUrl}/oauth/${provider}/callback`
    },
    Users: {
        Contacts: (userId) => `${baseUrl}/users/${userId}/contacts`,
        Search: (name, page) =>`${baseUrl}/users?search=${name}&page=${page}`,
        GetById: (userId) => `${baseUrl}/users/${userId}`,
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
