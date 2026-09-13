<template>
  <div>
    <!-- Loading indicator -->
    <div v-if="isLoading" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[60]">
      <div class="bg-white p-4 rounded-lg shadow-lg">
        <div class="flex items-center">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
          <span class="ml-3">Processing...</span>
        </div>
      </div>
    </div>

    <!-- Empty state -->
    <div v-if="!isLoading && items.length === 0" class="mt-2 text-center py-12 bg-gray-50 rounded-xl border border-dashed border-gray-300">
      <p class="text-gray-500">No testimonials yet. They'll show up here once a visitor submits one.</p>
    </div>

    <div v-if="items.length > 0" class="mt-2 overflow-x-auto bg-white rounded-xl border border-gray-200 shadow-sm">
      <table class="w-full text-sm">
        <thead>
          <tr class="border-b border-gray-200 bg-gray-50">
            <th class="text-left py-3 px-4 text-xs font-semibold uppercase tracking-wide text-gray-500">Name</th>
            <th class="text-left py-3 px-4 text-xs font-semibold uppercase tracking-wide text-gray-500">Email</th>
            <th class="text-left py-3 px-4 text-xs font-semibold uppercase tracking-wide text-gray-500">Role</th>
            <th class="text-left py-3 px-4 text-xs font-semibold uppercase tracking-wide text-gray-500">Photo</th>
            <th class="text-left py-3 px-4 text-xs font-semibold uppercase tracking-wide text-gray-500">Message</th>
            <th class="text-left py-3 px-4 text-xs font-semibold uppercase tracking-wide text-gray-500">Rating</th>
            <th class="text-left py-3 px-4 text-xs font-semibold uppercase tracking-wide text-gray-500">Status</th>
            <th class="text-left py-3 px-4 text-xs font-semibold uppercase tracking-wide text-gray-500">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in items" :key="item.id" class="border-b border-gray-100 hover:bg-gray-50">
            <td class="py-3 px-4 font-medium text-gray-900 whitespace-nowrap">{{ item.name }}</td>
            <td class="py-3 px-4 text-gray-500 whitespace-nowrap">{{ item.email || '—' }}</td>
            <td class="py-3 px-4 text-gray-500 whitespace-nowrap">{{ item.role || '—' }}</td>
            <td class="py-3 px-4">
              <img v-if="item.photo" :src="mediaUrl(item.photo)" alt="" class="h-10 w-10 rounded-full object-cover" @error="handleImageError" />
              <span v-else class="text-xs text-gray-400">No photo</span>
            </td>
            <td class="py-3 px-4 text-gray-600 max-w-xs">
              <span class="line-clamp-2">{{ item.message || '—' }}</span>
            </td>
            <td class="py-3 px-4 whitespace-nowrap">
              <span v-if="item.rating" class="text-amber-500 font-semibold">{{ item.rating }}/5</span>
              <span v-else class="text-gray-400">—</span>
            </td>
            <td class="py-3 px-4">
              <span
                class="px-2 py-0.5 rounded-full text-xs font-semibold capitalize"
                :class="item.status === 'approved' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700'"
              >
                {{ item.status }}
              </span>
            </td>
            <td class="py-3 px-4">
              <div class="flex flex-wrap items-center gap-1.5">
                <button
                  v-if="item.status !== 'approved'"
                  type="button"
                  class="px-2.5 py-1 text-xs bg-green-50 text-green-700 rounded-md hover:bg-green-100"
                  :disabled="working"
                  @click="approve(item)"
                >
                  Approve
                </button>
                <button
                  v-else
                  type="button"
                  class="px-2.5 py-1 text-xs bg-amber-50 text-amber-700 rounded-md hover:bg-amber-100"
                  :disabled="working"
                  @click="reject(item)"
                >
                  Reject
                </button>
                <button
                  type="button"
                  class="px-2.5 py-1 text-xs bg-red-50 text-red-700 rounded-md hover:bg-red-100"
                  :disabled="working"
                  @click="remove(item)"
                >
                  Delete
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const { apiFetch, mediaUrl } = useApi()
const toast = useAppToast()
const { confirmDialog } = useConfirmDialog()

definePageMeta({
  layout: 'admin',
  middleware: ['admin'],
  title: 'adminDash.modTestimonials',
  subtitle: 'adminDash.modTestimonialsDesc',
})

const items = ref([])
const isLoading = ref(true)
const working = ref(false)

const loadItems = async (silent = false) => {
  try {
    if (!silent) isLoading.value = true
    const res = await apiFetch('/api/testimonials', { query: { all: 1 } })
    items.value = res?.data || []
  } catch (error) {
    console.error('Error fetching testimonials:', error)
    if (!silent) toast.error('Failed to fetch testimonials. Please try again later.')
  } finally {
    if (!silent) isLoading.value = false
  }
}

onContentChange(['testimonials'], () => {
  loadItems(true)
})

onMounted(async () => {
  await loadItems()
})

const approve = async (item) => {
  working.value = true
  try {
    const res = await apiFetch('/api/testimonials/approve', { method: 'POST', query: { id: item.id } })
    toast.success(res?.message || 'Testimonial approved')
    notifyContentChanged(['testimonials'])
    await loadItems()
  } catch (error) {
    toast.error(error?.data?.message || error?.message || 'Failed to approve testimonial')
  } finally {
    working.value = false
  }
}

const reject = async (item) => {
  working.value = true
  try {
    const res = await apiFetch('/api/testimonials/reject', { method: 'POST', query: { id: item.id } })
    toast.success(res?.message || 'Testimonial rejected')
    notifyContentChanged(['testimonials'])
    await loadItems()
  } catch (error) {
    toast.error(error?.data?.message || error?.message || 'Failed to reject testimonial')
  } finally {
    working.value = false
  }
}

const remove = async (item) => {
  const confirmed = await confirmDialog(`Delete the testimonial from "${item.name}"?`, {
    title: 'Delete testimonial',
    confirmText: 'Delete',
    danger: true,
  })
  if (!confirmed) return

  working.value = true
  try {
    const res = await apiFetch('/api/testimonials/delete', { method: 'POST', query: { id: item.id } })
    toast.success(res?.message || 'Testimonial deleted')
    notifyContentChanged(['testimonials'])
    await loadItems()
  } catch (error) {
    toast.error(error?.data?.message || error?.message || 'Failed to delete testimonial')
  } finally {
    working.value = false
  }
}

const handleImageError = (event) => {
  event.target.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="40" height="40"%3E%3Crect fill="%23ddd" width="40" height="40"/%3E%3C/svg%3E'
  event.target.onerror = null
}
</script>
