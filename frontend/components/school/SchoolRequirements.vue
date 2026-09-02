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
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 2rem;
}

.requirements-page.compact {
  min-height: 0;
  padding: 0;
  background: transparent;
  border-radius: 1rem;
  overflow: hidden;
}

.requirements-page.compact .container {
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
  border: 1px solid #e5e7eb;
  padding: 1.25rem 1.25rem 1.35rem;
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
  background: rgba(255, 255, 255, 0.95);
  border-radius: 20px;
  padding: 3rem;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
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
  border-radius: 15px;
  padding: 2rem;
  text-align: center;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
  cursor: pointer;
  border: 2px solid transparent;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.requirement-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 16px 32px rgba(0, 0, 0, 0.12);
  border-color: #3498db;
}

.card-icon {
  font-size: 3rem;
  margin-bottom: 1rem;
}

.requirement-card h3 {
  color: #2c3e50;
  font-size: 1.3rem;
  margin-bottom: 1rem;
  font-weight: 600;
}

.requirement-card p {
  color: #7f8c8d;
  margin-bottom: 1.5rem;
  line-height: 1.6;
  font-size: 0.95rem;
  flex-grow: 1;
}

.download-btn {
  background: linear-gradient(135deg, #3498db, #2980b9);
  color: white;
  border: none;
  padding: 0.8rem 1.5rem;
  border-radius: 25px;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  margin: 0 auto;
  min-width: 140px;
  font-size: 0.9rem;
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

.requirements-page.is-dark {
  background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 55%, #111827 100%);
}

.requirements-page.is-dark.compact {
  background: transparent;
}

.requirements-page.is-dark .container {
  background: #1f2937;
  color: #f3f4f6;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.35);
}

.requirements-page.is-dark.compact .container {
  background: #1f2937;
  border-color: #374151;
}

.requirements-page.is-dark .header h1,
.requirements-page.is-dark .requirement-card h3 {
  color: #f8fafc;
}

.requirements-page.is-dark .header p,
.requirements-page.is-dark .status,
.requirements-page.is-dark .requirement-card p {
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
  border-color: #60a5fa;
  box-shadow: 0 16px 32px rgba(0, 0, 0, 0.45);
}

.requirements-page.is-dark .download-btn {
  background: linear-gradient(135deg, #2563eb, #1d4ed8);
}
</style>
