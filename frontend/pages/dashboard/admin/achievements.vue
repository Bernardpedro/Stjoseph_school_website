<template>
  <div>
    <!-- Loading indicator -->
    <div v-if="isLoading" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[60]">
      <div class="bg-white p-4 rounded-lg shadow-lg">
        <div class="flex items-center">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
          <span class="ml-3">{{ isSaving ? (editingId ? 'Updating...' : 'Saving...') : 'Processing...' }}</span>
        </div>
      </div>
    </div>

    <div class="flex justify-end">
      <button
        type="button"
        class="flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-medium transition-all shadow-md shadow-blue-600/40 hover:shadow-lg hover:shadow-blue-500/50 transform hover:-translate-y-0.5"
        @click="openCreate"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
        </svg>
        Add Achievement
      </button>
    </div>

    <!-- Empty state -->
    <div v-if="!isLoading && items.length === 0" class="mt-8 text-center py-12 bg-gray-50 rounded-xl border border-dashed border-gray-300">
      <p class="text-gray-500">No achievements yet. Click "Add Achievement" to create one.</p>
    </div>

    <div v-if="items.length > 0" class="mt-8 overflow-x-auto bg-white rounded-xl border border-gray-200 shadow-sm">
      <table class="w-full text-sm">
        <thead>
          <tr class="border-b border-gray-200 bg-gray-50">
            <th class="text-left py-3 px-4 text-xs font-semibold uppercase tracking-wide text-gray-500">Title</th>
            <th class="text-left py-3 px-4 text-xs font-semibold uppercase tracking-wide text-gray-500">Category</th>
            <th class="text-left py-3 px-4 text-xs font-semibold uppercase tracking-wide text-gray-500">Year</th>
            <th class="text-left py-3 px-4 text-xs font-semibold uppercase tracking-wide text-gray-500">Score / Rank</th>
            <th class="text-left py-3 px-4 text-xs font-semibold uppercase tracking-wide text-gray-500">Status</th>
            <th class="text-left py-3 px-4 text-xs font-semibold uppercase tracking-wide text-gray-500">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in items" :key="item.id" class="border-b border-gray-100 hover:bg-gray-50">
            <td class="py-3 px-4 font-medium text-gray-900">{{ item.title }}</td>
            <td class="py-3 px-4">
              <span v-if="item.category" class="px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">{{ item.category }}</span>
              <span v-else class="text-gray-400">—</span>
            </td>
            <td class="py-3 px-4 text-gray-600 whitespace-nowrap">{{ item.academic_year || '—' }}</td>
            <td class="py-3 px-4 text-gray-600 whitespace-nowrap">
              <span v-if="item.score !== null">{{ Number(item.score).toFixed(2) }}%</span>
              <span v-if="item.score !== null && item.rank"> </span>
              <span v-if="item.rank">({{ item.rank }})</span>
              <span v-if="item.score === null && !item.rank" class="text-gray-400">—</span>
            </td>
            <td class="py-3 px-4">
              <span
                class="px-2 py-0.5 rounded-full text-xs font-semibold"
                :class="item.is_published ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'"
              >
                {{ item.is_published ? 'Published' : 'Hidden' }}
              </span>
            </td>
            <td class="py-3 px-4">
              <div class="flex items-center gap-1.5">
                <button type="button" class="px-2.5 py-1 text-xs bg-blue-50 text-blue-700 rounded-md hover:bg-blue-100" @click="openEdit(item)">Edit</button>
                <button type="button" class="px-2.5 py-1 text-xs bg-red-50 text-red-700 rounded-md hover:bg-red-100" @click="remove(item)">Delete</button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal -->
    <div
      v-if="showModal"
      class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4"
      @click.self="closeForm"
    >
      <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between sticky top-0 bg-white z-10">
          <h3 class="text-xl font-bold text-gray-900">{{ editingId ? 'Edit Achievement' : 'Add Achievement' }}</h3>
          <button type="button" class="p-2 hover:bg-gray-100 rounded-lg transition-colors" @click="closeForm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <div class="p-6 space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Title *</label>
            <input v-model="form.title" type="text" class="field" placeholder="e.g. Top 10 national ranking" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Category</label>
            <input v-model="form.category" type="text" class="field" placeholder="e.g. National Exam Results" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Academic Year</label>
            <input v-model="form.academic_year" type="text" class="field" placeholder="e.g. 2025-2026" />
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1.5">Score (%)</label>
              <input v-model="form.score" type="number" step="0.01" min="0" max="100" class="field" placeholder="90.21" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1.5">Rank</label>
              <input v-model="form.rank" type="text" class="field" placeholder="e.g. 5th" />
            </div>
          </div>
          <div class="flex items-center gap-6">
            <label class="flex items-center gap-2 text-sm text-gray-700">
              <input v-model="form.is_published" type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
              Published
            </label>
            <label class="flex items-center gap-2 text-sm text-gray-700">
              <input v-model="form.is_featured" type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
              Featured
            </label>
          </div>
        </div>

        <div class="p-6 border-t border-gray-200 flex items-center justify-end gap-3 sticky bottom-0 bg-white">
          <button type="button" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700" :disabled="isSaving" @click="closeForm">Cancel</button>
          <button
            type="button"
            class="px-5 py-2 rounded-lg bg-blue-600 hover:bg-blue-500 text-white font-medium disabled:opacity-60"
            :disabled="isSaving"
            @click="submitForm"
          >
            {{ isSaving ? 'Saving...' : (editingId ? 'Update' : 'Save') }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'

const { apiFetch } = useApi()
const toast = useAppToast()
const { confirmDialog } = useConfirmDialog()

definePageMeta({
  layout: 'admin',
  middleware: ['admin'],
  title: 'adminDash.modAchievements',
  subtitle: 'adminDash.modAchievementsDesc',
})

const items = ref([])
const isLoading = ref(true)
const isSaving = ref(false)
const showModal = ref(false)
const editingId = ref(null)

const emptyForm = () => ({
  title: '',
  category: '',
  academic_year: '',
  score: '',
  rank: '',
  is_published: true,
  is_featured: false,
})

const form = reactive(emptyForm())

const loadItems = async (silent = false) => {
  try {
    if (!silent) isLoading.value = true
    const res = await apiFetch('/api/achievements', { query: { all: 1 } })
    items.value = res?.data || []
  } catch (error) {
    console.error('Error fetching achievements:', error)
    if (!silent) toast.error('Failed to fetch achievements. Please try again later.')
  } finally {
    if (!silent) isLoading.value = false
  }
}

onContentChange(['achievements'], () => {
  loadItems(true)
})

onMounted(async () => {
  await loadItems()
})

const openCreate = () => {
  editingId.value = null
  Object.assign(form, emptyForm())
  showModal.value = true
}

const openEdit = (item) => {
  editingId.value = item.id
  Object.assign(form, {
    title: item.title || '',
    category: item.category || '',
    academic_year: item.academic_year || '',
    score: item.score !== null && item.score !== undefined ? item.score : '',
    rank: item.rank || '',
    is_published: !!item.is_published,
    is_featured: !!item.is_featured,
  })
  showModal.value = true
}

const closeForm = () => {
  showModal.value = false
}

const submitForm = async () => {
  if (!form.title.trim()) {
    toast.error('Title is required.')
    return
  }

  isSaving.value = true
  try {
    const body = new FormData()
    body.append('title', form.title.trim())
    body.append('category', form.category.trim())
    body.append('academic_year', form.academic_year.trim())
    body.append('score', form.score === '' ? '' : String(form.score))
    body.append('rank', form.rank.trim())
    body.append('is_published', form.is_published ? '1' : '0')
    body.append('is_featured', form.is_featured ? '1' : '0')

    if (editingId.value) {
      body.append('id', String(editingId.value))
      await apiFetch('/api/achievements/update', { method: 'POST', body, query: { id: editingId.value } })
      toast.success('Achievement updated')
    } else {
      await apiFetch('/api/achievements', { method: 'POST', body })
      toast.success('Achievement created')
    }

    notifyContentChanged(['achievements'])
    showModal.value = false
    await loadItems()
  } catch (error) {
    toast.error(error?.data?.message || error?.message || 'Failed to save achievement')
  } finally {
    isSaving.value = false
  }
}

const remove = async (item) => {
  const confirmed = await confirmDialog(`Delete "${item.title}"?`, {
    title: 'Delete achievement',
    confirmText: 'Delete',
    danger: true,
  })
  if (!confirmed) return

  try {
    await apiFetch('/api/achievements/delete', { method: 'POST', query: { id: item.id } })
    toast.success('Achievement deleted')
    notifyContentChanged(['achievements'])
    await loadItems()
  } catch (error) {
    toast.error(error?.data?.message || error?.message || 'Failed to delete achievement')
  }
}
</script>

<style scoped>
.field {
  @apply w-full px-3 py-2 text-sm rounded-lg border border-gray-300 bg-white text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent;
}
</style>
