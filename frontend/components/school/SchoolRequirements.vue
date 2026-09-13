<template>
  <div class="requirements-page" :class="{ compact, 'is-dark': isDarkMode }">
    <div class="container">
      <header class="header">
        <h1>{{ settings.title_i18n || settings.title || $t('academics.requirementsTitle') }}</h1>
        <p>{{ settings.subtitle_i18n || settings.subtitle || $t('academics.requirementsSubtitle') }}</p>
      </header>

      <div v-if="loading" class="status">{{ $t('academics.requirementsLoading') }}</div>
      <div v-else-if="error" class="status error">{{ error }}</div>
      <div v-else-if="!levels.length" class="status">{{ $t('academics.requirementsEmpty') }}</div>

      <div v-else class="requirements-grid">
        <div
          v-for="level in levels"
          :key="level.id"
          class="requirement-card"
          @click="downloadLevel(level)"
        >
          <div class="card-icon">📚</div>
          <h3>{{ level.name_i18n || level.name }}</h3>
          <p>{{ level.description_i18n || level.description }}</p>
          <div v-if="settings.academic_year" class="card-meta">
            <span class="card-meta-icon">📅</span>
            {{ settings.academic_year }}
          </div>
          <button class="download-btn" type="button" @click.stop="downloadLevel(level)">
            {{ $t('common.download') }}
            <span class="download-icon">⬇️</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  compact: {
    type: Boolean,
    default: false,
  },
})

const { apiFetch, mediaUrl } = useApi()
const { isDarkMode, initTheme } = useTheme()
const toast = useAppToast()

const levels = ref([])
const settings = reactive({
  title: '',
  subtitle: '',
  academic_year: '',
})
const loading = ref(true)
const error = ref('')

const load = async (silent = false) => {
  if (!silent) loading.value = true
  error.value = ''
  try {
    const [levelsRes, settingsRes] = await Promise.all([
      apiFetch('/api/requirements'),
      apiFetch('/api/requirements/settings'),
    ])
    levels.value = levelsRes?.data || []
    Object.assign(settings, settingsRes?.data || {})
  } catch (e) {
    if (!silent) {
      error.value = e?.data?.message || e?.message || 'Could not load requirements'
    }
  } finally {
    if (!silent) loading.value = false
  }
}

const downloadLevel = async (level) => {
  const urls = (level.urls || [])
    .map((u) => mediaUrl(u.url || u))
    .filter(Boolean)

  if (!urls.length) {
    toast.info('No documents available for this level.')
    return
  }

  for (let i = 0; i < urls.length; i++) {
    const url = urls[i]
    const ext = url.includes('.pdf') ? 'pdf' : (url.match(/\.(png|jpe?g|webp|gif)(?:\?|$)/i)?.[1] || 'jpg')
    const code = level.code || 'level'
    const year = settings.academic_year || 'docs'
    const filename = urls.length > 1
      ? `Requirements-${code}-${year}-Page${i + 1}.${ext}`
      : `Requirements-${code}-${year}.${ext}`

    // Open/download via anchor — avoid fetch() (CORS blocks localhost:3000 → Apache uploads)
    const link = document.createElement('a')
    link.href = url
    link.download = filename
    link.target = '_blank'
    link.rel = 'noopener noreferrer'
    link.style.display = 'none'
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)

    if (i < urls.length - 1) {
      await new Promise((resolve) => setTimeout(resolve, 400))
    }
  }
}

onContentChange(['requirements', 'settings'], () => {
  load(true)
})

onMounted(() => {
  initTheme()
  load()
})
</script>

<style scoped>
.requirements-page {
  padding: 2rem 0;
}

.requirements-page.compact {
  padding: 0;
}

.requirements-page.compact .header {
  margin-bottom: 1rem;
}

.requirements-page.compact .header h1 {
  font-size: 1.5rem;
}

.requirements-page.compact .header p {
  font-size: 0.95rem;
}

.requirements-page.compact .requirements-grid {
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 1rem;
}

.container {
  max-width: 1000px;
  margin: 0 auto;
}

.header {
  text-align: center;
  margin-bottom: 3rem;
}

.header h1 {
  color: #2c3e50;
  font-size: 2.5rem;
  margin-bottom: 0.5rem;
  font-weight: 700;
}

.header p {
  color: #7f8c8d;
  font-size: 1.1rem;
  font-weight: 500;
}

.status {
  text-align: center;
  color: #7f8c8d;
  padding: 2rem 0;
}

.status.error {
  color: #c0392b;
}

.requirements-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 2rem;
  align-items: stretch;
}

.requirement-card {
  background: white;
  border-radius: 1rem;
  padding: 2rem;
  text-align: center;
  box-shadow: 0 10px 35px -12px rgba(29, 78, 216, 0.15);
  border: 1px solid #e5e7eb;
  transition: transform 0.25s ease, box-shadow 0.25s ease;
  cursor: pointer;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.requirement-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 16px 35px -15px rgba(29, 78, 216, 0.30);
}

.card-icon {
  width: 56px;
  height: 56px;
  border-radius: 0.75rem;
  background: rgba(29, 78, 216, 0.10);
  color: #1D4ED8;
  font-size: 1.4rem;
  margin: 0 auto 1.1rem;
  display: flex;
  align-items: center;
  justify-content: center;
}

.requirement-card h3 {
  color: #1D4ED8;
  font-size: 1.15rem;
  margin-bottom: 0.75rem;
  font-weight: 700;
}

.requirement-card p {
  color: #6b7280;
  margin-bottom: 1rem;
  line-height: 1.6;
  font-size: 0.95rem;
  flex-grow: 1;
}

.card-meta {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.4rem;
  color: #6b7280;
  font-size: 0.82rem;
  margin-bottom: 1.5rem;
}

.download-btn {
  background: #1D4ED8;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 9999px;
  font-weight: 700;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  margin: 0 auto;
  width: 100%;
  min-width: 140px;
  font-size: 0.9rem;
  transition: background 0.2s ease, transform 0.15s ease;
}

.download-btn:hover {
  background: #163a85;
  transform: translateY(-1px);
}

@media (max-width: 768px) {
  .requirements-page {
    padding: 1rem;
  }

  .container {
    padding: 2rem 1rem;
  }

  .header h1 {
    font-size: 2rem;
  }

  .requirements-grid {
    grid-cols: 1fr;
    grid-template-columns: 1fr;
    gap: 1.5rem;
  }

  .requirements-page.compact .requirements-grid {
    grid-template-columns: 1fr;
    gap: 0.85rem;
  }
}

.requirements-page.is-dark .header h1 {
  color: #f8fafc;
}

.requirements-page.is-dark .requirement-card h3 {
  color: #60a5fa;
}

.requirements-page.is-dark .header p,
.requirements-page.is-dark .status,
.requirements-page.is-dark .requirement-card p,
.requirements-page.is-dark .card-meta {
  color: #94a3b8;
}

.requirements-page.is-dark .status.error {
  color: #fca5a5;
}

.requirements-page.is-dark .requirement-card {
  background: #111827;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.35);
  border-color: #1f2937;
}

.requirements-page.is-dark .requirement-card:hover {
  box-shadow: 0 16px 35px -15px rgba(96, 165, 250, 0.35);
}

.requirements-page.is-dark .card-icon {
  background: rgba(96, 165, 250, 0.15);
  color: #60a5fa;
}

.requirements-page.is-dark .download-btn {
  background: #2563eb;
}

.requirements-page.is-dark .download-btn:hover {
  background: #1d4ed8;
}
</style>
