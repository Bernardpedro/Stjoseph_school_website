<template>
  <div
    v-if="ads.length"
    class="announcement-bar w-full relative z-40 overflow-hidden"
    :class="isDarkMode ? 'is-dark text-slate-100' : 'text-white'"
  >
    <div class="announcement-shine pointer-events-none absolute inset-0" aria-hidden="true" />

    <div class="max-w-screen-xl mx-auto px-3 sm:px-5 py-3.5 sm:py-4 relative">
      <div class="flex flex-col sm:flex-row sm:items-center gap-3.5 sm:gap-5">
        <span
          class="announcement-badge shrink-0 inline-flex items-center self-start gap-1.5 px-3 py-1.5 rounded-md text-[11px] sm:text-xs font-extrabold uppercase tracking-widest shadow-sm"
          :class="isDarkMode ? 'bg-slate-800 text-blue-200' : 'bg-white text-blue-800'"
        >
          <span
            class="announcement-pulse w-2 h-2 rounded-full"
            :class="isDarkMode ? 'bg-blue-300' : 'bg-blue-600'"
            aria-hidden="true"
          />
          {{ $t('announcement.badge') }}
        </span>

        <div class="flex-1 min-w-0 relative min-h-[3.5rem] sm:min-h-[3rem]">
          <transition
            enter-active-class="transition duration-400 ease-out"
            enter-from-class="opacity-0 translate-y-2"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition duration-200 ease-in absolute inset-0"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0 -translate-y-1"
          >
            <div :key="current.id" class="w-full">
              <p class="font-extrabold text-base sm:text-lg md:text-xl leading-snug tracking-tight drop-shadow-sm">
                {{ current.title_i18n || current.title }}
              </p>
              <p
                v-if="current.message_i18n || current.message"
                class="text-sm sm:text-[0.95rem] mt-1 line-clamp-2 leading-relaxed"
                :class="isDarkMode ? 'text-slate-300' : 'text-blue-50/95'"
              >
                {{ current.message_i18n || current.message }}
              </p>
            </div>
          </transition>
        </div>

        <NuxtLink
          :to="currentCtaLink"
          class="announcement-cta shrink-0 inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg text-sm sm:text-base font-bold shadow-md hover:shadow-lg hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200"
          :class="isDarkMode ? 'bg-slate-100 text-slate-900 hover:bg-white' : 'bg-white text-blue-800 hover:bg-blue-50'"
        >
          {{ current.cta_text_i18n || current.cta_text || $t('announcement.applyNow') }}
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
          </svg>
        </NuxtLink>
      </div>

      <div v-if="ads.length > 1" class="flex justify-center gap-2 mt-3">
        <button
          v-for="(ad, idx) in ads"
          :key="ad.id"
          type="button"
          class="h-2 rounded-full transition-all duration-300"
          :class="idx === index
            ? (isDarkMode ? 'w-6 bg-slate-100' : 'w-6 bg-white')
            : (isDarkMode ? 'w-2 bg-slate-400/50 hover:bg-slate-200/70' : 'w-2 bg-white/40 hover:bg-white/70')"
          :aria-label="`Show announcement ${idx + 1}`"
          @click="index = idx; restartTimer()"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
const { apiFetch } = useApi()
const { isDarkMode, initTheme } = useTheme()

const ads = ref([])
const index = ref(0)
let timer = null

const current = computed(() => ads.value[index.value] || ads.value[0] || {
  title: '',
  message: '',
  cta_text: 'Apply Now',
  link: '/admission#application-form',
  id: 0,
})

const currentCtaLink = computed(() => {
  const link = current.value.link
  return link === '/admission' ? '/admission#application-form' : (link || '/admission#application-form')
})

const next = () => {
  if (ads.value.length < 2) return
  index.value = (index.value + 1) % ads.value.length
}

const restartTimer = () => {
  if (timer) clearInterval(timer)
  if (ads.value.length > 1) {
    timer = setInterval(next, 5000)
  }
}

const load = async () => {
  try {
    const res = await apiFetch('/api/announcements')
    ads.value = res?.data || []
    index.value = 0
    restartTimer()
  } catch {
    ads.value = []
  }
}

onContentChange(['announcements'], () => {
  load()
})

onMounted(() => {
  initTheme()
  load()
})
onUnmounted(() => {
  if (timer) clearInterval(timer)
})
</script>

<style scoped>
.announcement-bar {
  background: linear-gradient(105deg, #1e3a8a 0%, #1d4ed8 45%, #2563eb 75%, #1e40af 100%);
  box-shadow: 0 4px 20px rgba(30, 64, 175, 0.35);
}

.announcement-bar.is-dark {
  background: linear-gradient(105deg, #020617 0%, #0f172a 40%, #1e293b 75%, #0b1220 100%);
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.45);
  border-bottom: 1px solid #1e293b;
}

.announcement-shine {
  background: linear-gradient(
    110deg,
    transparent 20%,
    rgba(255, 255, 255, 0.12) 45%,
    transparent 70%
  );
  background-size: 200% 100%;
  animation: announcement-shine 4.5s ease-in-out infinite;
}

.announcement-pulse {
  animation: announcement-pulse 1.6s ease-in-out infinite;
}

@keyframes announcement-shine {
  0%, 100% { background-position: 120% 0; }
  50% { background-position: -20% 0; }
}

@keyframes announcement-pulse {
  0%, 100% { opacity: 1; transform: scale(1); }
  50% { opacity: 0.45; transform: scale(0.85); }
}
</style>
