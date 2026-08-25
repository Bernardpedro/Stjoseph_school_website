const CHANNEL = 'sj-live'
const EVENT = 'sj:content-changed'

const LABELS: Record<string, string> = {
  events: 'Events updated',
  announcements: 'Announcement updated',
  projects: 'Projects updated',
  requirements: 'Requirements updated',
  admissions: 'Admissions updated',
  notifications: 'New notification',
  settings: 'Site content updated',
}

export function notifyContentChanged(keys: string[]) {
  if (!import.meta.client || !keys.length) return
  window.dispatchEvent(new CustomEvent(EVENT, { detail: { keys } }))
  try {
    new BroadcastChannel(CHANNEL).postMessage({ keys })
  } catch {
    // BroadcastChannel is unavailable in some browsers.
  }
}

export function onContentChange(watchKeys: string[], handler: (keys: string[]) => void) {
  if (!import.meta.client) return

  const listener = (event: Event) => {
    const keys = (event as CustomEvent<{ keys?: string[] }>).detail?.keys || []
    if (keys.includes('*') || keys.some((key) => watchKeys.includes(key))) {
      handler(keys)
    }
  }

  onMounted(() => window.addEventListener(EVENT, listener))
  onUnmounted(() => window.removeEventListener(EVENT, listener))
}

export function useLiveSync() {
  const { apiFetch } = useApi()
  const toast = useState<{ title: string } | null>('sj-live-toast', () => null)
  const stamps = useState<Record<string, string>>('sj-live-stamps', () => ({}))
  const started = useState('sj-live-started', () => false)

  let pollTimer: ReturnType<typeof setInterval> | null = null
  let toastTimer: ReturnType<typeof setTimeout> | null = null
  let channel: BroadcastChannel | null = null

  const showToast = (keys: string[]) => {
    const title = keys.map((key) => LABELS[key] || 'Content updated').filter((v, i, a) => a.indexOf(v) === i)[0]
    toast.value = { title }
    if (toastTimer) clearTimeout(toastTimer)
    toastTimer = setTimeout(() => {
      toast.value = null
    }, 3500)
  }

  const emit = (keys: string[]) => {
    window.dispatchEvent(new CustomEvent(EVENT, { detail: { keys } }))
  }

  const refreshStamps = async () => {
    try {
      const res = await apiFetch<{ success: boolean; data: Record<string, string> }>('/api/sync')
      stamps.value = res?.data || {}
    } catch {
      // Ignore brief network errors while polling.
    }
  }

  const check = async () => {
    try {
      const res = await apiFetch<{ success: boolean; data: Record<string, string> }>('/api/sync')
      const next = res?.data || {}
      const prev = stamps.value
      const changed = Object.keys(next).filter((key) => prev[key] && prev[key] !== next[key])
      stamps.value = next
      if (changed.length) {
        showToast(changed)
        emit(changed)
      }
    } catch {
      // Ignore brief network errors while polling.
    }
  }

  const start = () => {
    if (!import.meta.client || pollTimer) return
    started.value = true

    try {
      channel = new BroadcastChannel(CHANNEL)
      channel.onmessage = (message) => {
        const keys = message.data?.keys
        if (Array.isArray(keys) && keys.length) {
          showToast(keys)
          emit(keys)
          refreshStamps()
        }
      }
    } catch {
      channel = null
    }

    check()
    pollTimer = setInterval(check, 4000)
  }

  const stop = () => {
    if (pollTimer) clearInterval(pollTimer)
    if (toastTimer) clearTimeout(toastTimer)
    channel?.close()
    started.value = false
  }

  return { toast, start, stop, notifyContentChanged }
}
