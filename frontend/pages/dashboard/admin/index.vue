<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 sm:py-8 space-y-6">
      <p v-if="error" class="text-sm text-red-600 bg-red-50 border border-red-100 rounded-lg px-3 py-2">
        {{ error }}
      </p>

      <section>
        <div class="flex items-center justify-between mb-3">
          <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200">{{ $t('adminDash.overview') }}</h2>
          <span
            class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-full"
            :class="applicationsOpen
              ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300'
              : 'bg-amber-50 text-amber-800 dark:bg-amber-950/40 dark:text-amber-300'"
          >
            <span class="w-1.5 h-1.5 rounded-full" :class="applicationsOpen ? 'bg-emerald-500' : 'bg-amber-500'" />
            {{ applicationsOpen ? $t('adminDash.appsOpen') : $t('adminDash.appsClosed') }}
          </span>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3">
          <NuxtLink
            v-for="card in statCards"
            :key="card.to"
            :to="card.to"
            class="rounded-xl border bg-white dark:bg-gray-800 p-4 hover:shadow-md hover:border-blue-300 dark:hover:border-blue-700 transition-all"
            :class="card.alert
              ? 'border-amber-300 dark:border-amber-700'
              : 'border-gray-200 dark:border-gray-700'"
          >
            <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-500">{{ card.label }}</p>
            <p class="mt-1 text-2xl font-bold tabular-nums" :class="card.color">
              {{ loading ? '—' : card.count }}
            </p>
          </NuxtLink>
        </div>
      </section>

      <section class="grid lg:grid-cols-2 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
          <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <h2 class="font-semibold text-gray-900 dark:text-white">{{ $t('adminDash.recentApps') }}</h2>
            <NuxtLink to="/dashboard/admin/admissions" class="text-sm font-semibold text-[#1D4ED8] hover:underline">
              {{ $t('adminDash.seeAll') }}
            </NuxtLink>
          </div>
          <ul v-if="recentApps.length" class="divide-y divide-gray-100 dark:divide-gray-700">
            <li v-for="app in recentApps" :key="app.id">
              <NuxtLink
                to="/dashboard/admin/admissions"
                class="flex items-center gap-3 px-5 py-3 hover:bg-blue-50/60 dark:hover:bg-blue-950/20"
              >
                <div class="min-w-0 flex-1">
                  <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ app.student_name }}</p>
                  <p class="text-xs text-gray-500 truncate">
                    {{ app.program_label || app.program || '—' }} · {{ formatShortDate(app.created_at) }}
                  </p>
                </div>
                <span class="shrink-0 text-[11px] font-semibold px-2 py-0.5 rounded-full" :class="statusClass(app.status)">
                  {{ statusLabel(app.status) }}
                </span>
              </NuxtLink>
            </li>
          </ul>
          <p v-else class="px-5 py-8 text-sm text-gray-400 text-center">{{ $t('adminDash.noApps') }}</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
          <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <h2 class="font-semibold text-gray-900 dark:text-white">{{ $t('adminDash.activity') }}</h2>
            <span v-if="unreadCount" class="text-xs font-semibold text-amber-700 bg-amber-50 dark:bg-amber-950/40 px-2 py-0.5 rounded-full">
              {{ $t('adminDash.unread', { count: unreadCount }) }}
            </span>
          </div>
          <ul v-if="recentNotes.length" class="divide-y divide-gray-100 dark:divide-gray-700">
            <li v-for="note in recentNotes" :key="note.id" class="px-5 py-3">
              <p class="text-sm font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                <span v-if="!note.is_read" class="w-1.5 h-1.5 rounded-full bg-[#1D4ED8] shrink-0" />
                {{ note.title }}
              </p>
              <p v-if="note.message" class="text-xs text-gray-500 mt-0.5 line-clamp-2">{{ note.message }}</p>
              <p class="text-[11px] text-gray-400 mt-1">{{ formatShortDate(note.created_at) }}</p>
            </li>
          </ul>
          <p v-else class="px-5 py-8 text-sm text-gray-400 text-center">{{ $t('adminDash.noActivity') }}</p>
        </div>
      </section>
    </div>
</template>

<script setup>
definePageMeta({
  layout: 'admin',
  middleware: ['admin'],
  title: 'adminDash.title',
  subtitle: 'adminDash.subtitle',
})

const { t } = useI18n()
const { apiFetch } = useApi()
const userStore = useUserStore()

const loading = ref(true)
const error = ref('')
const applications = ref([])
const events = ref([])
const announcements = ref([])
const projects = ref([])
const requirements = ref([])
const galleries = ref([])
const users = ref([])
const notifications = ref([])
const unreadCount = ref(0)
const applicationsOpen = ref(true)

const pendingCount = computed(() =>
  applications.value.filter((app) => String(app.status || '').toLowerCase() === 'pending').length
)

const activeAnnouncements = computed(() =>
  announcements.value.filter((item) => Number(item.is_active) === 1)
)

const statCards = computed(() => {
  const cards = [
  {
    label: t('adminDash.pendingApps'),
    count: pendingCount.value,
    to: '/dashboard/admin/admissions',
    color: pendingCount.value ? 'text-amber-600' : 'text-gray-900 dark:text-white',
    alert: pendingCount.value > 0,
  },
  {
    label: t('adminDash.allApps'),
    count: applications.value.length,
    to: '/dashboard/admin/admissions',
    color: 'text-gray-900 dark:text-white',
  },
  {
    label: t('adminDash.events'),
    count: events.value.length,
    to: '/dashboard/admin/event',
    color: 'text-gray-900 dark:text-white',
  },
  {
    label: t('adminDash.announcements'),
    count: activeAnnouncements.value.length,
    to: '/dashboard/admin/announcements',
    color: 'text-gray-900 dark:text-white',
  },
  {
    label: t('adminDash.projects'),
    count: projects.value.length,
    to: '/dashboard/admin/project-upload',
    color: 'text-gray-900 dark:text-white',
  },
  {
    label: t('adminDash.gallery'),
    count: galleries.value.length,
    to: '/dashboard/admin/gallery',
    color: 'text-gray-900 dark:text-white',
  },
  {
    label: t('adminDash.staff'),
    count: users.value.length,
    to: '/dashboard/admin/users-management',
    color: 'text-gray-900 dark:text-white',
  },
]

  return userStore.isSuperAdmin
    ? cards
    : cards.filter((card) => card.to !== '/dashboard/admin/users-management')
})

const recentApps = computed(() => applications.value.slice(0, 6))
const recentNotes = computed(() => notifications.value.slice(0, 6))

const listFrom = (res) => {
  const data = res?.data
  if (Array.isArray(data)) return data
  if (Array.isArray(data?.items)) return data.items
  return []
}

const load = async (silent = false) => {
  if (!silent) loading.value = true
  error.value = ''
  try {
    const [appsRes, eventsRes, adsRes, projectsRes, reqRes, galleriesRes, usersRes, notesRes, settingsRes] =
      await Promise.allSettled([
        apiFetch('/api/admissions'),
        apiFetch('/api/events'),
        apiFetch('/api/announcements', { query: { all: '1' } }),
        apiFetch('/api/projects'),
        apiFetch('/api/requirements'),
        apiFetch('/api/galleries'),
        apiFetch('/api/admin/users'),
        apiFetch('/api/notifications', { query: { limit: 8 } }),
        apiFetch('/api/admissions/settings'),
      ])

    if (appsRes.status === 'fulfilled') applications.value = listFrom(appsRes.value)
    if (eventsRes.status === 'fulfilled') events.value = listFrom(eventsRes.value)
    if (adsRes.status === 'fulfilled') announcements.value = listFrom(adsRes.value)
    if (projectsRes.status === 'fulfilled') projects.value = listFrom(projectsRes.value)
    if (reqRes.status === 'fulfilled') requirements.value = listFrom(reqRes.value)
    if (galleriesRes.status === 'fulfilled') galleries.value = listFrom(galleriesRes.value)
    if (usersRes.status === 'fulfilled') users.value = listFrom(usersRes.value)
    if (notesRes.status === 'fulfilled') {
      const data = notesRes.value?.data || {}
      notifications.value = Array.isArray(data.items) ? data.items : listFrom(notesRes.value)
      unreadCount.value = Number(data.unread_count || 0)
    }
    if (settingsRes.status === 'fulfilled') {
      const settings = settingsRes.value?.data || {}
      applicationsOpen.value = settings.applications_open !== false && settings.applications_open !== '0'
    }

    const failed = [
      appsRes, eventsRes, adsRes, projectsRes, reqRes, galleriesRes, usersRes, notesRes, settingsRes,
    ].filter((item) => item.status === 'rejected')
    if (failed.length === 9) {
      error.value = t('adminDash.loadError')
    }
  } catch (e) {
    error.value = e?.data?.message || e?.message || t('adminDash.loadError')
  } finally {
    if (!silent) loading.value = false
  }
}

const statusLabel = (status) => {
  const key = String(status || 'pending').toLowerCase()
  const map = {
    pending: t('adminDash.statusPending'),
    reviewed: t('adminDash.statusReviewed'),
    accepted: t('adminDash.statusAccepted'),
    rejected: t('adminDash.statusRejected'),
  }
  return map[key] || status
}

const statusClass = (status) => {
  const map = {
    pending: 'bg-amber-100 text-amber-800',
    reviewed: 'bg-blue-100 text-blue-800',
    accepted: 'bg-green-100 text-green-800',
    rejected: 'bg-red-100 text-red-800',
  }
  return map[String(status || '').toLowerCase()] || 'bg-gray-100 text-gray-700'
}

const formatShortDate = (value) => {
  if (!value) return '—'
  try {
    return new Date(value).toLocaleString()
  } catch {
    return value
  }
}

onMounted(() => {
  userStore.hydrate()
  load()
})

onContentChange(
  ['events', 'announcements', 'projects', 'requirements', 'galleries', 'admissions', 'notifications', 'settings'],
  () => load(true)
)
</script>
