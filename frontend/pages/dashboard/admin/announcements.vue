<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900 p-4 sm:p-6">
    <div class="max-w-4xl mx-auto space-y-6">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
          <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Announcements / Ads</h1>
          <p class="text-sm text-gray-500 mt-1">These rotate every 5 seconds on the website as application calls</p>
        </div>
        <button
          type="button"
          class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium"
          @click="openCreate"
        >
          Add Announcement
        </button>
      </div>

      <p v-if="error" class="text-sm text-red-600">{{ error }}</p>
      <p v-if="success" class="text-sm text-green-600">{{ success }}</p>
      <div v-if="loading" class="text-gray-500">Loading...</div>

      <div v-else class="space-y-3">
        <div
          v-for="item in items"
          :key="item.id"
          class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4"
        >
          <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
            <div class="min-w-0">
              <div class="flex flex-wrap items-center gap-2">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ item.title }}</h3>
                <span
                  class="text-xs px-2 py-0.5 rounded-full font-medium"
                  :class="item.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600'"
                >
                  {{ item.is_active ? 'Active' : 'Inactive' }}
                </span>
              </div>
              <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">{{ item.message }}</p>
              <p class="text-xs text-gray-400 mt-2">
                CTA: {{ item.cta_text }} · Link: {{ item.link }} · Order: {{ item.sort_order }}
              </p>
            </div>
            <div class="flex gap-2 shrink-0">
              <button type="button" class="px-3 py-1.5 text-sm bg-blue-50 text-blue-700 rounded-lg" @click="openEdit(item)">Edit</button>
              <button type="button" class="px-3 py-1.5 text-sm bg-red-50 text-red-700 rounded-lg" @click="remove(item)">Delete</button>
            </div>
          </div>
        </div>

        <p v-if="!items.length" class="text-sm text-gray-500">No announcements yet. Add one to show on the site.</p>
      </div>
    </div>

    <div v-if="showForm" class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
      <div class="bg-white dark:bg-gray-800 rounded-xl w-full max-w-lg max-h-[90vh] overflow-y-auto p-5 space-y-4">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">
          {{ editingId ? 'Edit Announcement' : 'Add Announcement' }}
        </h2>

        <input v-model="form.title" type="text" placeholder="Title *" class="field" />
        <textarea v-model="form.message" rows="3" placeholder="Message / call for application" class="field" />
        <input v-model="form.cta_text" type="text" placeholder="Button text (e.g. Apply Now)" class="field" />
        <input v-model="form.link" type="text" placeholder="Link (e.g. /admission)" class="field" />
        <input v-model.number="form.sort_order" type="number" min="0" placeholder="Sort order" class="field" />
        <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
          <input v-model="form.is_active" type="checkbox" />
          Active (show on website)
        </label>

        <div class="flex justify-end gap-2 pt-2">
          <button type="button" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600" @click="showForm = false">Cancel</button>
          <button type="button" class="px-4 py-2 rounded-lg bg-blue-600 text-white" :disabled="saving" @click="save">
            {{ saving ? 'Saving...' : 'Save' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({
  layout: 'admin',
  middleware: ['admin'],
  title: 'adminDash.modAnnouncements',
  subtitle: 'adminDash.modAnnouncementsDesc',
})

const { apiFetch } = useApi()

const items = ref([])
const loading = ref(true)
const saving = ref(false)
const error = ref('')
const success = ref('')
const showForm = ref(false)
const editingId = ref(null)

const form = reactive({
  title: '',
  message: '',
  cta_text: 'Apply Now',
  link: '/admission#application-form',
  sort_order: 0,
  is_active: true,
})

const load = async (silent = false) => {
  if (!silent) loading.value = true
  error.value = ''
  try {
    const res = await apiFetch('/api/announcements', { query: { all: '1' } })
    items.value = res?.data || []
  } catch (e) {
    if (!silent) {
      error.value = e?.data?.message || e?.message || 'Failed to load announcements'
    }
  } finally {
    if (!silent) loading.value = false
  }
}

const openCreate = () => {
  editingId.value = null
  form.title = ''
  form.message = ''
  form.cta_text = 'Apply Now'
  form.link = '/admission#application-form'
  form.sort_order = (items.value.length || 0) + 1
  form.is_active = true
  showForm.value = true
}

const openEdit = (item) => {
  editingId.value = item.id
  form.title = item.title || ''
  form.message = item.message || ''
  form.cta_text = item.cta_text || 'Apply Now'
  form.link = item.link || '/admission#application-form'
  form.sort_order = item.sort_order || 0
  form.is_active = !!item.is_active
  showForm.value = true
}

const save = async () => {
  if (!form.title.trim()) {
    error.value = 'Title is required'
    return
  }
  saving.value = true
  error.value = ''
  success.value = ''
  try {
    const body = {
      title: form.title.trim(),
      message: form.message.trim(),
      cta_text: form.cta_text.trim() || 'Apply Now',
      link: form.link.trim() || '/admission#application-form',
      sort_order: form.sort_order || 0,
      is_active: form.is_active ? 1 : 0,
    }
    if (editingId.value) {
      await apiFetch(`/api/announcements/update?id=${editingId.value}`, { method: 'POST', body })
      success.value = 'Announcement updated'
    } else {
      await apiFetch('/api/announcements', { method: 'POST', body })
      success.value = 'Announcement created'
    }
    showForm.value = false
    notifyContentChanged(['announcements'])
    await load()
  } catch (e) {
    error.value = e?.data?.message || e?.message || 'Failed to save'
  } finally {
    saving.value = false
  }
}

const { confirmDialog } = useConfirmDialog()

const remove = async (item) => {
  const confirmed = await confirmDialog(`Delete "${item.title}"?`, {
    title: 'Delete announcement',
    confirmText: 'Delete',
    danger: true,
  })
  if (!confirmed) return
  try {
    await apiFetch(`/api/announcements?id=${item.id}`, { method: 'DELETE' })
    success.value = 'Announcement deleted'
    notifyContentChanged(['announcements'])
    await load()
  } catch (e) {
    error.value = e?.data?.message || e?.message || 'Failed to delete'
  }
}

onContentChange(['announcements'], () => {
  load(true)
})

onMounted(load)
</script>

<style scoped>
.field {
  width: 100%;
  padding: 0.5rem 0.75rem;
  font-size: 0.875rem;
  border-radius: 0.5rem;
  border: 1px solid #d1d5db;
  background: #fff;
  color: #111827;
}
:global(.dark) .field {
  border-color: #4b5563;
  background: #111827;
  color: #fff;
}
</style>
