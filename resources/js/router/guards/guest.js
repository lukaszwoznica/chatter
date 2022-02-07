export default async function guest({next, store}) {
    if (await store.getters['auth/isAuthenticated']) {
        return next({name: 'chats'})
    }

    return next()
}
