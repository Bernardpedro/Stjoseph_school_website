<script setup>
const { t } = useI18n()
const route = useRoute()
const { isDarkMode, toggleTheme, initTheme } = useTheme()

onMounted(() => initTheme())

const isTabActive = (href) =>
  route.path === href || route.path.startsWith(`${href}/`)

const navigation = computed(() => [
  { name: t('nav.admission'), href: '/admission' },
  { name: t('nav.academics'), href: '/academics' },
  { name: t('nav.events'), href: '/events' },
  { name: t('nav.gallery'), href: '/gallery' },
  { name: t('nav.achievements'), href: '/achievements' },
  { name: t('nav.contact'), href: '/contacts' },
])

const hovered = ref('')
const iframeReady = ref(false)
let openTimer = null
let closeTimer = null

const previewSrc = (href) => {
  const [path] = href.split('?')
  return `${path}?preview=1`
}

const previewAlign = (index) =>
  index >= navigation.value.length - 2 ? 'right-0' : 'left-1/2 -translate-x-1/2'

const openPreview = (href) => {
  clearTimeout(closeTimer)
  clearTimeout(openTimer)
  openTimer = setTimeout(() => {
    if (hovered.value !== href) iframeReady.value = false
    hovered.value = href
  }, 140)
}

const closePreview = () => {
  clearTimeout(openTimer)
  closeTimer = setTimeout(() => {
    hovered.value = ''
    iframeReady.value = false
  }, 160)
}

onUnmounted(() => {
  clearTimeout(openTimer)
  clearTimeout(closeTimer)
})
</script>

<template>
  <nav class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 relative z-50 overflow-visible sticky top-0">
    <div class="max-w-screen-xl mx-auto px-2 sm:px-4 py-2">
      <div class="flex items-center gap-2">
        <!-- Menu + logo -->
        <div class="flex items-center gap-1 sm:gap-2 shrink-0 min-w-0">
          <LayoutSideBar />
          <NuxtLink to="/" class="flex items-center gap-1.5 sm:gap-2 min-w-0">
            <img
              :src="cldOptimize('https://res.cloudinary.com/dck2vzccq/image/upload/v1752774384/TssLogo_hoag31.jpg', 100)"
              class="h-8 w-8 sm:h-9 sm:w-9 md:h-10 md:w-10 object-cover rounded-full shrink-0"
              alt="School logo"
            />
            <span class="hidden md:block text-base lg:text-lg font-semibold text-[#1D4ED8] dark:text-blue-400 truncate">
              {{ $t('common.schoolName') }}
            </span>
          </NuxtLink>
        </div>

        <!-- Search: desktop/tablet in the middle -->
        <div class="hidden sm:block flex-1 min-w-0 mx-2 md:mx-4">
          <CommonSearch />
        </div>

        <!-- Right actions -->
        <div class="ml-auto flex items-center gap-1.5 sm:gap-2 shrink-0">
          <div class="sm:hidden">
            <CommonSearch compact />
          </div>
          <ul class="hidden lg:flex items-center gap-1">
            <li
              v-for="(page, index) in navigation"
              :key="page.href"
              class="relative"
              @mouseenter="openPreview(page.href)"
              @mouseleave="closePreview"
              @focusin="openPreview(page.href)"
              @focusout="closePreview"
            >
              <NuxtLink
                :to="page.href"
                class="nav-tab"
                :class="{
                  'is-active': isTabActive(page.href),
                  'is-hovered': hovered === page.href && !isTabActive(page.href),
                }"
              >
                {{ page.name }}
              </NuxtLink>

              <Transition
                enter-active-class="transition duration-150 ease-out"
                enter-from-class="opacity-0 translate-y-1"
                enter-to-class="opacity-100 translate-y-0"
                leave-active-class="transition duration-100 ease-in"
                leave-from-class="opacity-100 translate-y-0"
                leave-to-class="opacity-0 translate-y-1"
              >
                <div
                  v-if="hovered === page.href"
                  class="absolute top-full z-[80] pt-2"
                  :class="previewAlign(index)"
                >
                  <div
                    class="w-[320px] rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-2xl ring-1 ring-black/5 cursor-pointer"
                    role="link"
                    :aria-label="t('nav.pagePreview', { page: page.name })"
                    @click="navigateTo(page.href)"
                  >
                    <div class="flex items-center gap-1.5 px-2.5 py-1.5 bg-gray-100 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                      <span class="w-2 h-2 rounded-full bg-red-400" aria-hidden="true" />
                      <span class="w-2 h-2 rounded-full bg-amber-400" aria-hidden="true" />
                      <span class="w-2 h-2 rounded-full bg-green-400" aria-hidden="true" />
                      <span class="ml-1 min-w-0 truncate text-[10px] font-medium text-gray-500 dark:text-gray-400">
                        {{ page.name }}
                      </span>
                    </div>
                    <div class="relative w-[320px] h-[200px] overflow-hidden bg-gray-100 dark:bg-gray-800">
                      <div
                        v-if="!iframeReady"
                        class="absolute inset-0 z-10 animate-pulse bg-gradient-to-br from-gray-100 via-gray-50 to-gray-200 dark:from-gray-800 dark:via-gray-800 dark:to-gray-700"
                      />
                      <iframe
                        :src="previewSrc(page.href)"
                        class="page-preview-iframe absolute top-0 left-0 border-0 pointer-events-none bg-white dark:bg-gray-900"
                        :title="t('nav.pagePreview', { page: page.name })"
                        tabindex="-1"
                        loading="lazy"
                        @load="iframeReady = true"
                      />
                    </div>
                  </div>
                </div>
              </Transition>
            </li>
          </ul>

          <button
            type="button"
            class="inline-flex w-8 h-8 items-center justify-center text-[#1D4ED8] dark:text-blue-400 sm:w-9 sm:h-9 sm:border sm:border-blue-200 sm:dark:border-blue-800 sm:rounded-lg sm:bg-white sm:dark:bg-gray-800 sm:hover:bg-blue-50 sm:dark:hover:bg-blue-950/50 transition-colors"
            :aria-label="isDarkMode ? t('common.lightMode') : t('common.darkMode')"
            :title="isDarkMode ? t('common.lightMode') : t('common.darkMode')"
            @click="toggleTheme"
          >
            <svg v-if="isDarkMode" class="w-6 h-6 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <svg v-else class="w-6 h-6 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
            </svg>
          </button>
          <CommonLanguageSwitcher />
          <CommonNotificationBell />
          <ProfileHeader />
        </div>
      </div>
    </div>
  </nav>
</template>

<style scoped>
.nav-tab {
  position: relative;
  display: inline-flex;
  align-items: center;
  padding: 0.4rem 0.9rem;
  border-radius: 9999px;
  font-size: 0.875rem;
  font-weight: 600;
  letter-spacing: 0.02em;
  line-height: 1.25;
  color: #1d4ed8;
  white-space: nowrap;
  transition: color 0.2s ease, background-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
}
.nav-tab:hover,
.nav-tab.is-hovered {
  background: #dbeafe;
  color: #1e40af;
  box-shadow: 0 6px 16px -8px rgba(29, 78, 216, 0.55);
  transform: translateY(-1px);
}
.nav-tab.is-active {
  background: linear-gradient(180deg, #3b82f6 0%, #1d4ed8 100%);
  color: #fff;
  box-shadow: 0 10px 20px -10px rgba(29, 78, 216, 0.7);
  transform: translateY(-1px);
}
.nav-tab.is-active::after {
  content: '';
  position: absolute;
  left: 22%;
  right: 22%;
  bottom: 4px;
  height: 2px;
  border-radius: 9999px;
  background: #fbbf24;
}
.dark .nav-tab {
  color: #60a5fa;
}
.dark .nav-tab:hover,
.dark .nav-tab.is-hovered {
  background: rgba(30, 64, 175, 0.35);
  color: #93c5fd;
}
.dark .nav-tab.is-active {
  color: #fff;
}

.page-preview-iframe {
  width: 1280px;
  height: 800px;
  transform: scale(0.25);
  transform-origin: top left;
}
</style>
