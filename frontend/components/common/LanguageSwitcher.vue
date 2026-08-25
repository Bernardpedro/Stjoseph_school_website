<template>
  <div class="relative" ref="rootEl">
    <button
      type="button"
      class="lang-btn"
      :class="{ 'is-open': open }"
      :aria-label="t('language.select')"
      :aria-expanded="open"
      :title="t(`language.${locale}`)"
      @click="open = !open"
    >
      <span class="text-xs leading-none sm:text-base" aria-hidden="true">{{ currentFlag }}</span>
      <svg
        class="hidden sm:block w-3 h-3 transition-transform"
        :class="{ 'rotate-180': open }"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
        aria-hidden="true"
      >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
      </svg>
    </button>

    <div
      v-if="open"
      class="absolute right-0 mt-1.5 min-w-[3.5rem] rounded-xl border border-blue-100 dark:border-blue-900 bg-white dark:bg-gray-800 shadow-xl shadow-blue-900/10 z-[110] overflow-hidden py-1"
      role="listbox"
      :aria-label="t('language.label')"
    >
      <button
        v-for="loc in localesList"
        :key="loc.code"
        type="button"
        role="option"
        class="w-full flex items-center justify-center px-3 py-2 text-xl leading-none transition-colors"
        :class="loc.code === locale
          ? 'bg-gradient-to-b from-blue-500 to-blue-700 text-white shadow-inner'
          : 'hover:bg-blue-50 dark:hover:bg-blue-950/40'"
        :aria-selected="loc.code === locale"
        :aria-label="t(`language.${loc.code}`)"
        :title="t(`language.${loc.code}`)"
        @click="choose(loc.code)"
      >
        <span aria-hidden="true">{{ flagFor(loc.code) }}</span>
      </button>
    </div>
  </div>
</template>

<script setup>
const { locale, locales, setLocale, t } = useI18n()

const open = ref(false)
const rootEl = ref(null)

const FLAGS = {
  en: '🇬🇧',
  rw: '🇷🇼',
  sw: '🇹🇿',
  fr: '🇫🇷',
  de: '🇩🇪',
}

const flagFor = (code) => FLAGS[code] || '🌐'

const currentFlag = computed(() => flagFor(locale.value || 'en'))

const localesList = computed(() => {
  const list = locales.value || []
  return list.map((l) => (typeof l === 'string' ? { code: l } : l))
})

const choose = async (code) => {
  if (code && code !== locale.value) {
    await setLocale(code)
    notifyContentChanged([
      'events',
      'announcements',
      'projects',
      'requirements',
      'admissions',
      'notifications',
      'settings',
    ])
  }
  open.value = false
}

const onDocClick = (e) => {
  if (rootEl.value && !rootEl.value.contains(e.target)) {
    open.value = false
  }
}

onMounted(() => document.addEventListener('click', onDocClick))
onUnmounted(() => document.removeEventListener('click', onDocClick))
</script>

<style scoped>
.lang-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  padding: 0.4rem 0.7rem;
  border-radius: 9999px;
  font-size: 0.875rem;
  font-weight: 600;
  color: #1d4ed8;
  background: rgba(239, 246, 255, 0.9);
  box-shadow: inset 0 0 0 1px #dbeafe;
  transition: color 0.2s ease, background 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
}
.lang-btn:hover {
  background: #dbeafe;
  color: #1e40af;
  box-shadow: 0 6px 16px -8px rgba(29, 78, 216, 0.55);
  transform: translateY(-1px);
}
.lang-btn.is-open {
  background: linear-gradient(180deg, #3b82f6 0%, #1d4ed8 100%);
  color: #fff;
  box-shadow: 0 10px 20px -10px rgba(29, 78, 216, 0.7);
  transform: translateY(-1px);
}
.dark .lang-btn {
  color: #60a5fa;
  background: rgba(23, 37, 84, 0.45);
  box-shadow: inset 0 0 0 1px rgba(30, 58, 138, 0.7);
}
.dark .lang-btn:hover {
  background: rgba(30, 64, 175, 0.35);
  color: #93c5fd;
}
.dark .lang-btn.is-open {
  color: #fff;
}
@media (max-width: 639px) {
  .lang-btn {
    padding: 0.125rem;
    gap: 0;
    background: transparent;
    box-shadow: none;
    transform: none;
  }
  .lang-btn:hover,
  .lang-btn.is-open {
    background: transparent;
    color: #1d4ed8;
    box-shadow: none;
    transform: none;
  }
  .dark .lang-btn,
  .dark .lang-btn:hover,
  .dark .lang-btn.is-open {
    background: transparent;
    box-shadow: none;
    color: #60a5fa;
  }
}
</style>
