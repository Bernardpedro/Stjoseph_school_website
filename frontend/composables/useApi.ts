import { useUserStore } from '~/stores/user'

type ApiFetchOptions = {
  method?: string
  body?: BodyInit | Record<string, unknown> | null
  headers?: Record<string, string>
  query?: Record<string, string | number | boolean | undefined>
}

export function useApi() {
  const config = useRuntimeConfig()
  const userStore = useUserStore()

  const base = () =>
    String(config.public.apiBase || '')
      .replace(/\/Beno\//g, '/beno/')
      .replace(/\/$/, '')

  const apiUrl = (path: string) => {
    const clean = path.startsWith('/') ? path : `/${path}`
    return `${base()}${clean}`
  }

  const mediaUrl = (path?: string | null) => {
    if (!path) return ''
    if (/^https?:\/\//i.test(path)) return path
    return `${base()}/${String(path).replace(/^\//, '')}`
  }

  const apiFetch = async <T = unknown>(path: string, options: ApiFetchOptions = {}): Promise<T> => {
    if (!userStore.token && import.meta.client) {
      userStore.hydrate()
    }

    const headers: Record<string, string> = { ...(options.headers || {}) }

    let t: ((key: string) => string) | null = null
    try {
      const i18n = useI18n()
      t = i18n.t
      const code = String(i18n.locale.value || 'en')
      headers['X-Nuxt-Locale'] = code
      headers['X-Locale'] = code
      headers['Accept-Language'] = code
    } catch {
      headers['X-Nuxt-Locale'] = 'en'
      headers['X-Locale'] = 'en'
    }

    if (userStore.token) {
      headers.Authorization = `Bearer ${userStore.token}`
    }

    const isFormData = typeof FormData !== 'undefined' && options.body instanceof FormData
    if (!isFormData && options.body != null && !headers['Content-Type']) {
      headers['Content-Type'] = 'application/json'
    }

    try {
      return await $fetch<T>(apiUrl(path), {
        method: options.method || 'GET',
        body: options.body as any,
        headers,
        query: options.query,
      })
    } catch (err: any) {
      const status = err?.statusCode || err?.status || err?.response?.status
      const isAuthRoute = path.includes('/api/auth/login') || path.includes('/api/auth/register')

      // Don't treat failed login/register as an expired session
      if (status === 401 && import.meta.client && !isAuthRoute) {
        userStore.logout()
        await navigateTo('/auth/login')
      }

      // No status means the request never got a response at all (server
      // unreachable, DNS/CORS failure, offline). The raw error at this point
      // is a browser-level exception like `[POST] "https://.../login": <no
      // response> NetworkError...` -- not something to show a user. Log the
      // technical detail for debugging and surface a plain-language message
      // instead. Every call site already falls back through
      // `err?.data?.message || err?.message`, so this propagates automatically.
      if (!status) {
        console.error(`API request failed [${options.method || 'GET'}] ${path}:`, err)
        const friendly: any = new Error(t ? t('common.networkError') : 'Unable to reach the server. Please check your internet connection and try again.')
        friendly.isNetworkError = true
        throw friendly
      }

      throw err
    }
  }

  const applyStoredI18n = <T extends Record<string, any>>(row: T, fields: string[]): T => {
    const next = { ...row }
    for (const field of fields) {
      const translated = next[`${field}_i18n`]
      if (typeof translated === 'string' && translated.trim() !== '') {
        ;(next as any)[field] = translated
      }
    }
    return next
  }

  return { apiUrl, mediaUrl, apiFetch, apiBase: base, applyStoredI18n }
}
