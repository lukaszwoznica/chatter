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
        ResetPassword: `${baseUrl}/reset-password`
    },
    Users: {
        Contacts: (userId) => `${baseUrl}/contacts/${userId}`,
        Search: (name, page) =>`${baseUrl}/users?search=${name}&page=${page}`,
    },
    Messages: {
        GetConversationMessages: (userId, page) => `${baseUrl}/messages/${userId}?page=${page}`,
        SendMessage: `${baseUrl}/messages`,
        MarkMessageAsRead: (messageId) => `${baseUrl}/messages/${messageId}`
    }
}

export default ApiRoutes
