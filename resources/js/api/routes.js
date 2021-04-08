const baseUrl = '/api/v1'

const ApiRoutes = {
    BaseUrl: baseUrl,
    GetCsrfCookie: `${baseUrl}/csrf-cookie`,
    Auth: {
        Login: `${baseUrl}/login`,
        Logout: `${baseUrl}/logout`,
        Register: `${baseUrl}/register`,
        GetAuthenticatedUser: `${baseUrl}/user`,
    },
    Users: {
        Contacts: `${baseUrl}/contacts`,
    },
    Messages: {
        GetConversationMessages: (userId) => `${baseUrl}/messages/${userId}`,
        SendMessage: `${baseUrl}/messages`
    }
}

export default ApiRoutes
