<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900 p-4 sm:p-6">
    <div class="max-w-5xl mx-auto space-y-6">
      <NuxtLink
        to="/dashboard/admin/admissions"
        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-500 text-white text-sm font-medium shadow-md shadow-blue-600/40"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
        </svg>
        Back to Admission dashboard
      </NuxtLink>
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
          <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Academic Requirements</h1>
          <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            Create, update, and delete levels and documents shown on Academics and Admission pages
          </p>
        </div>
        <button
          type="button"
          class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium"
          @click="openCreate"
        >
          Add Level
        </button>
      </div>

      <!-- Settings -->
      <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 sm:p-5 space-y-3">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Section Title</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
          <input v-model="settings.title" type="text" placeholder="Title" class="input" />
          <input v-model="settings.subtitle" type="text" placeholder="Subtitle" class="input" />
          <input v-model="settings.academic_year" type="text" placeholder="Year e.g. 2025-2026" class="input" />
        </div>
        <button
          type="button"
          class="px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white rounded-lg text-sm"
          :disabled="savingSettings"
          @click="saveSettings"
        >
          {{ savingSettings ? 'Saving...' : 'Save Title' }}
        </button>
      </div>

      <p v-if="error" class="text-sm text-red-600">{{ error }}</p>
      <p v-if="success" class="text-sm text-green-600">{{ success }}</p>

      <div v-if="loading" class="text-gray-500">Loading...</div>

      <div v-else class="space-y-3">
        <div
          v-for="item in levels"
          :key="item.id"
          class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4"
        >
          <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
            <div class="min-w-0">
              <p class="text-xs uppercase tracking-wide text-blue-600 dark:text-blue-400 font-semibold">{{ item.code }}</p>
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ item.name }}</h3>
              <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ item.description }}</p>
              <p class="text-xs text-gray-400 mt-2">{{ (item.urls || []).length }} document(s) · order {{ item.sort_order }}</p>
            </div>
            <div class="flex gap-2 shrink-0">
              <button type="button" class="px-3 py-1.5 text-sm bg-blue-50 text-blue-700 rounded-lg" @click="openEdit(item)">Edit</button>
              <button type="button" class="px-3 py-1.5 text-sm bg-red-50 text-red-700 rounded-lg" @click="remove(item)">Delete</button>
            </div>
          </div>
        </div>

        <p v-if="!levels.length" class="text-gray-500 text-sm">
          No levels yet. Click “Add Level” to publish Academic Requirements on the site.
        </p>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showForm" class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
      <div class="bg-white dark:bg-gray-800 rounded-xl w-full max-w-lg max-h-[90vh] overflow-y-auto p-5 space-y-4">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">
          {{ editingId ? 'Edit Level' : 'Add Level' }}
        </h2>

        <div class="space-y-3">
          <input v-model="form.code" type="text" placeholder="Code (e.g. L1)" class="input" />
          <input v-model="form.name" type="text" placeholder="Name" class="input" />
          <textarea v-model="form.description" rows="3" placeholder="Description" class="input" />
          <input v-model.number="form.sort_order" type="number" min="0" placeholder="Sort order" class="input" />

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Document URLs</label>
            <div v-for="(url, idx) in form.urls" :key="idx" class="flex gap-2 mb-2">
              <input v-model="form.urls[idx]" type="url" placeholder="https://..." class="input" />
              <button type="button" class="px-2 text-red-600" @click="form.urls.splice(idx, 1)">×</button>
            </div>
            <button type="button" class="text-sm text-blue-600" @click="form.urls.push('')">+ Add URL</button>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Upload files (optional)</label>
            <input type="file" multiple accept="image/*,application/pdf" @change="onFiles" />
          </div>
        </div>

        <div class="flex justify-end gap-2 pt-2">
          <button type="button" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600" @click="closeForm">Cancel</button>
          <button
            type="button"
            class="px-4 py-2 rounded-lg bg-blue-600 text-white"
            :disabled="saving"
            @click="saveLevel"
          >
            {{ saving ? 'Saving...' : 'Save' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({
  layout: 'default',
  middleware: ['admin'],
})

const { apiFetch } = useApi()

const levels = ref([])
const loading = ref(true)
const saving = ref(false)
const savingSettings = ref(false)
const error = ref('')
const success = ref('')
const showForm = ref(false)
const editingId = ref(null)
const files = ref([])

const settings = reactive({
  title: '',
  subtitle: '',
  academic_year: '',
})

const form = reactive({
  code: '',
  name: '',
  description: '',
  sort_order: 0,
  urls: [''],
})

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
      error.value = e?.data?.message || e?.message || 'Failed to load requirements'
    }
  } finally {
    if (!silent) loading.value = false
  }
}

const openCreate = () => {
  editingId.value = null
  form.code = ''
  form.name = ''
  form.description = ''
  form.sort_order = (levels.value.length || 0) + 1
  form.urls = ['']
  files.value = []
  showForm.value = true
}

const openEdit = (item) => {
  editingId.value = item.id
  form.code = item.code || ''
  form.name = item.name || ''
  form.description = item.description || ''
  form.sort_order = item.sort_order || 0
  form.urls = (item.urls || []).map((u) => u.url || u).filter(Boolean)
  if (!form.urls.length) form.urls = ['']
  files.value = []
  showForm.value = true
}

const closeForm = () => {
  showForm.value = false
}

const onFiles = (e) => {
  files.value = Array.from(e.target.files || [])
}

const saveSettings = async () => {
  savingSettings.value = true
  error.value = ''
  success.value = ''
  try {
    await apiFetch('/api/requirements/settings', {
      method: 'POST',
      body: {
        title: settings.title,
        subtitle: settings.subtitle,
        academic_year: settings.academic_year,
      },
    })
    success.value = 'Section title saved'
    notifyContentChanged(['requirements', 'settings'])
  } catch (e) {
    error.value = e?.data?.message || e?.message || 'Failed to save settings'
  } finally {
    savingSettings.value = false
  }
}

const saveLevel = async () => {
  if (!form.code.trim() || !form.name.trim()) {
    error.value = 'Code and name are required'
    return
  }

  saving.value = true
  error.value = ''
  success.value = ''

  try {
    const fd = new FormData()
    fd.append('code', form.code.trim())
    fd.append('name', form.name.trim())
    fd.append('description', form.description.trim())
    fd.append('sort_order', String(form.sort_order || 0))
    fd.append('urls', JSON.stringify(form.urls.filter((u) => u && u.trim())))
    files.value.forEach((f) => fd.append('files[]', f))

    if (editingId.value) {
      await apiFetch(`/api/requirements/update?id=${editingId.value}`, {
        method: 'POST',
        body: fd,
      })
      success.value = 'Level updated'
    } else {
      await apiFetch('/api/requirements', {
        method: 'POST',
        body: fd,
      })
      success.value = 'Level created'
    }

    showForm.value = false
    notifyContentChanged(['requirements'])
    await load()
  } catch (e) {
    error.value = e?.data?.message || e?.message || 'Failed to save level'
  } finally {
    saving.value = false
  }
}

const { confirmDialog } = useConfirmDialog()

const remove = async (item) => {
  const confirmed = await confirmDialog(`Delete "${item.name}"?`, {
    title: 'Delete level',
    confirmText: 'Delete',
    danger: true,
  })
  if (!confirmed) return
  error.value = ''
  success.value = ''
  try {
    await apiFetch(`/api/requirements?id=${item.id}`, { method: 'DELETE' })
    success.value = 'Level deleted'
    notifyContentChanged(['requirements'])
    await load()
  } catch (e) {
    error.value = e?.data?.message || e?.message || 'Failed to delete'
  }
}

onContentChange(['requirements', 'settings'], () => {
  load(true)
})

onMounted(load)
</script>

<style scoped>
.input {
  @apply w-full px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-white;
}
</style>
