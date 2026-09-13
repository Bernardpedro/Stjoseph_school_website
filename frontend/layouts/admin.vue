<template>
  <div class="min-h-screen bg-slate-50 dark:bg-gray-900">
    <!-- Mobile backdrop -->
    <div
      v-if="sidebarOpen"
      class="fixed inset-0 bg-black/50 z-40 lg:hidden"
      @click="sidebarOpen = false"
    />

    <!-- Sidebar -->
    <aside
      class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 flex flex-col transition-transform duration-200 lg:translate-x-0"
      :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    >
      <div class="px-5 py-5 border-b border-gray-100 dark:border-gray-700 flex items-center gap-2.5">
        <img
          :src="cldOptimize('https://res.cloudinary.com/dck2vzccq/image/upload/v1752774384/TssLogo_hoag31.jpg', 100)"
          class="h-9 w-9 object-cover rounded-full shrink-0"
          alt="School logo"
        />
        <div class="min-w-0">
          <p class="text-sm font-bold text-[#1D4ED8] dark:text-blue-400 truncate">{{ $t('common.schoolName') }}</p>
          <p class="text-[11px] text-gray-400">{{ $t('adminDash.title') }}</p>
        </div>
      </div>

      <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
        <NuxtLink
          v-for="item in navItems"
          :key="item.to"
          :to="item.to"
          class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors"
          :class="isActive(item.to)
            ? 'bg-blue-50 text-[#1D4ED8] dark:bg-blue-950/40 dark:text-blue-400'
            : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50'"
          @click="sidebarOpen = false"
        >
          <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
          </svg>
          <span class="truncate">{{ item.label }}</span>
        </NuxtLink>
      </nav>

      <div class="px-3 py-4 border-t border-gray-100 dark:border-gray-700 space-y-1">
        <NuxtLink
          to="/"
          class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50"
          @click="sidebarOpen = false"
        >
          <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
          </svg>
          <span>{{ $t('adminDash.viewSite') }}</span>
        </NuxtLink>
        <button
          type="button"
          class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30"
          @click="handleLogout"
        >
          <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
          </svg>
          <span>{{ $t('nav.signOut') }}</span>
        </button>
      </div>
    </aside>

    <!-- Main column -->
    <div class="lg:pl-64">
      <header class="sticky top-0 z-30 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between gap-4 px-4 sm:px-6 py-3">
          <div class="flex items-center gap-3 min-w-0">
            <button
              type="button"
              class="lg:hidden p-2 -ml-2 text-gray-500 dark:text-gray-300"
              aria-label="Open menu"
              @click="sidebarOpen = true"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
              </svg>
            </button>
            <div class="min-w-0">
              <h1 class="text-lg font-bold text-gray-900 dark:text-white truncate">{{ pageTitle }}</h1>
              <p v-if="pageSubtitle" class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ pageSubtitle }}</p>
            </div>
          </div>

          <div class="flex items-center gap-3 shrink-0">
            <span class="hidden sm:block text-sm font-medium text-gray-700 dark:text-gray-200">
              {{ userStore.name || $t('nav.admin') }}
            </span>
            <div class="w-9 h-9 rounded-lg bg-[#1D4ED8] text-white flex items-center justify-center font-semibold text-sm shrink-0">
              {{ avatarLetter }}
            </div>
          </div>
        </div>
      </header>

      <main>
        <slot />
      </main>
    </div>

    <CommonLiveToast />
    <CommonAppToast />
    <CommonConfirmDialog />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useUserStore } from '~/stores/user'

const route = useRoute()
const { t } = useI18n()
const userStore = useUserStore()
const { initTheme } = useTheme()

onMounted(() => {
  userStore.hydrate()
  initTheme()
})

const sidebarOpen = ref(false)

const navItems = computed(() => {
  const items = [
    { label: t('adminDash.title'), to: '/dashboard/admin', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' },
    { label: t('adminDash.modAdmissions'), to: '/dashboard/admin/admissions', icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z' },
    { label: t('adminDash.modRequirements'), to: '/dashboard/admin/requirements', icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' },
    { label: t('adminDash.modAnnouncements'), to: '/dashboard/admin/announcements', icon: 'M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z' },
    { label: t('adminDash.modEvents'), to: '/dashboard/admin/event', icon: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z' },
    { label: t('adminDash.modProjects'), to: '/dashboard/admin/project-upload', icon: 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z' },
    { label: t('adminDash.modGallery'), to: '/dashboard/admin/gallery', icon: 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M14 8h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z' },
    { label: t('adminDash.modSms'), to: '/dashboard/admin/sms', icon: 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z' },
    { label: t('adminDash.modTestimonials'), to: '/dashboard/admin/testimonials', icon: 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.958a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.37 2.448a1 1 0 00-.363 1.118l1.287 3.957c.3.922-.755 1.688-1.538 1.118l-3.37-2.447a1 1 0 00-1.175 0l-3.37 2.447c-.783.57-1.838-.196-1.539-1.118l1.287-3.957a1 1 0 00-.363-1.118l-3.37-2.448c-.783-.57-.38-1.81.588-1.81h4.163a1 1 0 00.95-.69l1.287-3.958z' },
    { label: t('adminDash.modAchievements'), to: '/dashboard/admin/achievements', icon: 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z' },
  ]

  if (userStore.isSuperAdmin) {
    items.push({
      label: t('adminDash.modUsers'),
      to: '/dashboard/admin/users-management',
      icon: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197',
    })
  }

  return items
})

const isActive = (to) => route.path === to || route.path.startsWith(`${to}/`)

const pageTitle = computed(() => (route.meta.title ? t(route.meta.title) : t('adminDash.title')))
const pageSubtitle = computed(() => (route.meta.subtitle ? t(route.meta.subtitle) : ''))

const avatarLetter = computed(() => (userStore.name || 'A').trim().charAt(0).toUpperCase())

const handleLogout = () => {
  userStore.logout()
  navigateTo('/auth/login')
}
</script>
