<template>
  <div v-if="userStore.isAdmin" class="relative" ref="rootEl">
    <button
      type="button"
      class="relative p-0.5 text-[#1D4ED8] dark:text-blue-400 sm:p-2 sm:rounded-full sm:text-gray-700 sm:dark:text-gray-200 sm:hover:bg-gray-100 sm:dark:hover:bg-gray-800"
      aria-label="Notifications"
      @click="toggle"
    >
      <svg class="w-3 h-3 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2c0 .5-.2 1-.6 1.4L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
      </svg>
      <span
        v-if="unreadCount > 0"
        class="absolute -top-1 -right-1 min-w-[0.7rem] h-2.5 px-0.5 rounded-full bg-red-600 text-white text-[7px] font-bold flex items-center justify-center sm:-top-0.5 sm:-right-0.5 sm:min-w-[1.1rem] sm:h-4 sm:px-1 sm:text-[10px]"
      >
        {{ unreadCount > 9 ? '9+' : unreadCount }}
      </span>
    </button>

    <div
      v-if="open"
      class="absolute right-0 mt-2 w-80 sm:w-96 max-h-96 overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-xl z-[120]"
    >
      <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Notifications</h3>
        <button
          v-if="unreadCount > 0"
          type="button"
          class="text-xs text-blue-600 hover:underline"
          @click="markAllRead"
        >
          Mark all read
        </button>
      </div>

      <div class="overflow-y-auto max-h-80">
        <p v-if="loading && !items.length" class="px-4 py-6 text-sm text-gray-500 text-center">Loading...</p>
        <p v-else-if="!items.length" class="px-4 py-6 text-sm text-gray-500 text-center">No notifications yet</p>

        <button
          v-for="item in items"
          :key="item.id"
          type="button"
          class="w-full text-left px-4 py-3 border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors"
          :class="{ 'bg-blue-50/70 dark:bg-blue-950/30': !item.is_read }"
          @click="openItem(item)"
        >
          <div class="flex items-start justify-between gap-2">
            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ item.title }}</p>
            <span v-if="!item.is_read" class="mt-1 w-2 h-2 rounded-full bg-blue-600 shrink-0" />
          </div>
          <p v-if="item.message" class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 line-clamp-2">{{ item.message }}</p>
          <p class="text-[11px] text-gray-400 mt-1">{{ formatTime(item.created_at) }}</p>
        </button>
      </div>
    </div>

    <!-- Toast for new notifications -->
    <transition
      enter-active-class="transition duration-200"
      enter-from-class="opacity-0 translate-y-2"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition duration-150"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="toast"
        class="fixed bottom-4 right-4 z-[200] max-w-sm rounded-lg bg-gray-900 text-white shadow-lg px-4 py-3"
      >
        <p class="text-sm font-semibold">{{ toast.title }}</p>
        <p v-if="toast.message" class="text-xs text-gray-300 mt-0.5">{{ toast.message }}</p>
      </div>
    </transition>
  </div>
</template>

<script setup>
const userStore = useUserStore()
const router = useRouter()
const { apiFetch } = useApi()

const open = ref(false)
const loading = ref(false)
const items = ref([])
const unreadCount = ref(0)
const toast = ref(null)
const rootEl = ref(null)

let pollTimer = null
let toastTimer = null
let lastSeenUnread = 0
let hydrated = false

const formatTime = (value) => {
  if (!value) return ''
  try {
    return new Date(value).toLocaleString()
  } catch {
    return value
  }
}

const fetchNotifications = async ({ silent = false } = {}) => {
  if (!userStore.isAdmin || !userStore.token) return

  if (!silent) loading.value = true
  try {
    const res = await apiFetch('/api/notifications', { query: { limit: 20 } })
    const data = res?.data || {}
    items.value = data.items || []
    const count = Number(data.unread_count || 0)
    unreadCount.value = count

    if (hydrated && count > lastSeenUnread) {
      const newest = items.value.find((n) => !n.is_read) || items.value[0]
      if (newest) showToast(newest)
    }
    lastSeenUnread = count
    hydrated = true
  } catch {
    // ignore polling errors (session/network)
  } finally {
    loading.value = false
  }
}

const showToast = (item) => {
  toast.value = { title: item.title, message: item.message }
  clearTimeout(toastTimer)
  toastTimer = setTimeout(() => { toast.value = null }, 4500)

  // Optional browser notification
  if (import.meta.client && typeof Notification !== 'undefined' && Notification.permission === 'granted') {
    try {
      new Notification(item.title, { body: item.message || '' })
    } catch {}
  }
}

const toggle = async () => {
  open.value = !open.value
  if (open.value) {
    await fetchNotifications()
    if (import.meta.client && typeof Notification !== 'undefined' && Notification.permission === 'default') {
      Notification.requestPermission().catch(() => {})
    }
  }
}

const markAllRead = async () => {
  try {
    await apiFetch('/api/notifications/read', {
      method: 'POST',
      body: { all: true },
    })
    items.value = items.value.map((n) => ({ ...n, is_read: 1 }))
    unreadCount.value = 0
    lastSeenUnread = 0
  } catch {}
}

const openItem = async (item) => {
  if (!item.is_read) {
    try {
      await apiFetch('/api/notifications/read', {
        method: 'POST',
        body: { id: item.id },
      })
      item.is_read = 1
      unreadCount.value = Math.max(0, unreadCount.value - 1)
      lastSeenUnread = unreadCount.value
    } catch {}
  }
  open.value = false
  if (item.link) {
    await router.push(item.link)
  }
}

const onDocClick = (e) => {
  if (rootEl.value && !rootEl.value.contains(e.target)) {
    open.value = false
  }
}

const startPolling = () => {
  stopPolling()
  if (!userStore.isAdmin) return
  fetchNotifications({ silent: true })
  pollTimer = setInterval(() => fetchNotifications({ silent: true }), 5000)
}

const stopPolling = () => {
  if (pollTimer) {
    clearInterval(pollTimer)
    pollTimer = null
  }
}

watch(
  () => [userStore.isAdmin, userStore.token],
  () => {
    userStore.hydrate()
    if (userStore.isAdmin && userStore.token) startPolling()
    else {
      stopPolling()
      items.value = []
      unreadCount.value = 0
      hydrated = false
    }
  },
  { immediate: true }
)

onContentChange(['notifications', 'admissions'], () => {
  fetchNotifications({ silent: true })
})

onMounted(() => {
  userStore.hydrate()
  document.addEventListener('click', onDocClick)
})

onUnmounted(() => {
  document.removeEventListener('click', onDocClick)
  stopPolling()
  clearTimeout(toastTimer)
})
</script>
