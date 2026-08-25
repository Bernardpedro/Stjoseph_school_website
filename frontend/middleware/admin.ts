export default defineNuxtRouteMiddleware(() => {
  if (import.meta.server) return

  const userStore = useUserStore()
  if (!userStore.token) {
    userStore.hydrate()
  }

  if (!userStore.token) {
    return navigateTo('/auth/login')
  }

  if (!userStore.isAdmin) {
    return navigateTo('/')
  }
})
