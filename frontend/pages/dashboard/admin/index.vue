<template>
  <div class="min-h-screen bg-slate-50 dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 sm:py-8 space-y-6">
      <header class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
        <div>
          <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-[#1D4ED8] dark:text-blue-400">
            {{ $t('adminDash.title') }}
          </h1>
          <span class="mt-2 block w-16 h-1 rounded-full bg-[#FBBF24]" aria-hidden="true" />
          <p class="mt-3 text-base sm:text-lg font-medium text-[#1D4ED8] dark:text-blue-400 max-w-2xl">
            {{ $t('adminDash.subtitle') }}
          </p>
          <p class="mt-2 text-sm font-semibold text-[#1D4ED8] dark:text-blue-400">
            {{ $t('adminDash.greeting', { name: displayName }) }}
          </p>
        </div>
        <div class="flex flex-wrap gap-2">
          <NuxtLink
            to="/"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm font-semibold text-[#1D4ED8] dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-950/40"
          >
            {{ $t('adminDash.viewSite') }}
          </NuxtLink>
          <NuxtLink
            v-if="userStore.isSuperAdmin"
            to="/dashboard/admin/users-management"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-[#1D4ED8] hover:bg-blue-700 text-white text-sm font-semibold shadow-md shadow-blue-600/30"
          >
            {{ $t('adminDash.users') }}
          </NuxtLink>
        </div>
      </header>

      <p v-if="error" class="text-sm text-red-600 bg-red-50 border border-red-100 rounded-lg px-3 py-2">
        {{ error }}
      </p>

      <section>
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
          <article
            v-for="mod in modules"
            :key="mod.to"
            class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 flex flex-col"
          >
            <div class="flex items-start gap-3">
              <span
                class="shrink-0 w-11 h-11 rounded-xl flex items-center justify-center text-white shadow-sm"
                :class="mod.tone"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="mod.icon" />
                </svg>
              </span>
              <div class="min-w-0">
                <h3 class="font-semibold text-gray-900 dark:text-white">{{ mod.title }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ mod.description }}</p>
              </div>
            </div>
            <p class="mt-4 text-sm font-semibold text-[#1D4ED8] dark:text-blue-400">
              {{ $t('adminDash.countLive', { count: loading ? '—' : mod.count }) }}
            </p>
            <div class="mt-auto pt-4 flex gap-2">
              <NuxtLink
                :to="mod.to"
                class="flex-1 text-center px-3 py-2 rounded-lg bg-[#1D4ED8] hover:bg-blue-700 text-white text-sm font-semibold"
              >
                {{ $t('adminDash.manage') }}
              </NuxtLink>
              <NuxtLink
                v-if="mod.publicTo"
                :to="mod.publicTo"
                class="flex-1 text-center px-3 py-2 rounded-lg border border-blue-100 dark:border-blue-900 text-sm font-semibold text-[#1D4ED8] dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-950/40"
              >
                {{ $t('adminDash.viewPage') }}
              </NuxtLink>
            </div>
          </article>
        </div>
      </section>

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
  </div>
</template>

<script setup>
definePageMeta({
  layout: 'default',
  middleware: ['admin'],
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
const users = ref([])
const notifications = ref([])
const unreadCount = ref(0)
const applicationsOpen = ref(true)

const displayName = computed(() => userStore.name || t('nav.admin'))

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

const modules = computed(() => {
  const items = [
  {
    title: t('adminDash.modAdmissions'),
    description: t('adminDash.modAdmissionsDesc'),
    to: '/dashboard/admin/admissions',
    publicTo: '/admission',
    count: applications.value.length,
    tone: 'bg-amber-500',
    icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
  },
  {
    title: t('adminDash.modRequirements'),
    description: t('adminDash.modRequirementsDesc'),
    to: '/dashboard/admin/requirements',
    publicTo: '/academics',
    count: requirements.value.length,
    tone: 'bg-emerald-600',
    icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
  },
  {
    title: t('adminDash.modAnnouncements'),
    description: t('adminDash.modAnnouncementsDesc'),
    to: '/dashboard/admin/announcements',
    publicTo: '/',
    count: activeAnnouncements.value.length,
    tone: 'bg-orange-500',
    icon: 'M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z',
  },
  {
    title: t('adminDash.modEvents'),
    description: t('adminDash.modEventsDesc'),
    to: '/dashboard/admin/event',
    publicTo: '/events',
    count: events.value.length,
    tone: 'bg-pink-500',
    icon: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
  },
  {
    title: t('adminDash.modProjects'),
    description: t('adminDash.modProjectsDesc'),
    to: '/dashboard/admin/project-upload',
    publicTo: '/project',
    count: projects.value.length,
    tone: 'bg-blue-600',
    icon: 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z',
  },
  {
    title: t('adminDash.modUsers'),
    description: t('adminDash.modUsersDesc'),
    to: '/dashboard/admin/users-management',
    publicTo: '',
    count: users.value.length,
    tone: 'bg-slate-700',
    icon: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197',
  },
]

  return userStore.isSuperAdmin
    ? items
    : items.filter((item) => item.to !== '/dashboard/admin/users-management')
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
    const [appsRes, eventsRes, adsRes, projectsRes, reqRes, usersRes, notesRes, settingsRes] =
      await Promise.allSettled([
        apiFetch('/api/admissions'),
        apiFetch('/api/events'),
        apiFetch('/api/announcements', { query: { all: '1' } }),
        apiFetch('/api/projects'),
        apiFetch('/api/requirements'),
        apiFetch('/api/admin/users'),
        apiFetch('/api/notifications', { query: { limit: 8 } }),
        apiFetch('/api/admissions/settings'),
      ])

    if (appsRes.status === 'fulfilled') applications.value = listFrom(appsRes.value)
    if (eventsRes.status === 'fulfilled') events.value = listFrom(eventsRes.value)
    if (adsRes.status === 'fulfilled') announcements.value = listFrom(adsRes.value)
    if (projectsRes.status === 'fulfilled') projects.value = listFrom(projectsRes.value)
    if (reqRes.status === 'fulfilled') requirements.value = listFrom(reqRes.value)
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
      appsRes, eventsRes, adsRes, projectsRes, reqRes, usersRes, notesRes, settingsRes,
    ].filter((item) => item.status === 'rejected')
    if (failed.length === 8) {
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
  ['events', 'announcements', 'projects', 'requirements', 'admissions', 'notifications', 'settings'],
  () => load(true)
)
</script>
