export const useUserStore = defineStore('user', {
  state: () => ({
    name: null as string | null,
    role: null as string | null,
    token: null as string | null,
  }),

  getters: {
    isAuthenticated: (state) => Boolean(state.token),
    isSuperAdmin: (state) => state.role === 'super_admin',
    isAdmin: (state) => state.role === 'admin' || state.role === 'super_admin',
  },

  actions: {
    setUser(payload: { name: string; role: string; token: string }) {
      this.name = payload.name
      this.role = payload.role
      this.token = payload.token

      if (import.meta.client) {
        localStorage.setItem('name', payload.name)
        localStorage.setItem('role', payload.role)
        localStorage.setItem('token', payload.token)
      }
    },

    logout() {
      this.name = null
      this.role = null
      this.token = null
      if (import.meta.client) {
        localStorage.removeItem('name')
        localStorage.removeItem('role')
        localStorage.removeItem('token')
      }
    },

    hydrate() {
      if (!import.meta.client) return
      this.name = localStorage.getItem('name')
      this.role = localStorage.getItem('role')
      this.token = localStorage.getItem('token')
    },
  },
})
