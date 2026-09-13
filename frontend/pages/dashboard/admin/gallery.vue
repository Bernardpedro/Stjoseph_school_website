<template>
  <div>
    <!-- Loading indicator -->
    <div v-if="isLoading || isSaving" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[60]">
      <div class="bg-white p-4 rounded-lg shadow-lg">
        <div class="flex items-center">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
          <span class="ml-3">{{ isSaving ? (editingId ? 'Updating image...' : 'Saving image...') : 'Processing...' }}</span>
        </div>
      </div>
    </div>

    <!-- Upload Button - aligned to right -->
    <div class="flex justify-end">
      <button
        @click="openCreateForm"
        class="flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-medium transition-all shadow-md shadow-blue-600/40 hover:shadow-lg hover:shadow-blue-500/50 transform hover:-translate-y-0.5"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
        </svg>
        Add Image
      </button>
    </div>

    <!-- Empty state -->
    <div v-if="!isLoading && items.length === 0" class="mt-8 text-center py-12 bg-gray-50 rounded-xl border border-dashed border-gray-300">
      <p class="text-gray-500">No gallery images yet. Click "Add Image" to create one.</p>
    </div>

    <!-- Display gallery images -->
    <div v-if="items.length > 0" class="mt-8">
      <h3 class="text-xl font-bold text-gray-900 mb-4">Gallery Images</h3>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="item in items" :key="item.id" class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200 hover:shadow-lg transition-shadow">
          <div class="h-48 overflow-hidden bg-gray-100">
            <img
              :src="mediaUrl(item.image)"
              :alt="item.title || 'Gallery image'"
              class="w-full h-full object-cover"
              @error="handleImageError"
            />
          </div>

          <div class="p-4">
            <div class="flex justify-between items-start mb-2">
              <h4 class="text-lg font-semibold text-gray-900">{{ item.title || 'Untitled' }}</h4>
              <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">
                #{{ item.sort_order }}
              </span>
            </div>

            <div class="mt-4 flex gap-2">
              <button
                @click="openEditForm(item)"
                class="flex-1 flex items-center justify-center gap-1 px-3 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors text-sm font-medium"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                  <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                </svg>
                Edit
              </button>
              <button
                @click="deleteItem(item.id)"
                class="flex-1 flex items-center justify-center gap-1 px-3 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors text-sm font-medium"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                Delete
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Upload Modal -->
    <div
      v-if="showModal"
      class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4"
      @click.self="closeForm"
    >
      <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between sticky top-0 bg-white z-10">
          <h3 class="text-2xl font-bold text-gray-900">
            {{ editingId ? 'Edit Image' : 'Add Image' }}
          </h3>
          <button
            @click="closeForm"
            class="p-2 hover:bg-gray-100 rounded-lg transition-colors"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <div class="p-6 space-y-6">
          <div>
            <label class="block text-gray-700 font-medium mb-3">Image</label>
            <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-blue-500 transition-colors bg-gray-50">
              <input
                ref="imageInput"
                type="file"
                accept="image/*"
                @change="handleImageChange"
                class="hidden"
                id="gallery-file-upload"
              />
              <label v-if="!imagePreview" for="gallery-file-upload" class="cursor-pointer block">
                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <p class="mt-2 text-sm text-gray-600">
                  <span class="font-medium text-blue-600 hover:text-blue-500">Click to upload</span> or drag and drop
                </p>
                <p class="text-xs text-gray-500 mt-1">PNG, JPG, GIF up to 1MB</p>
              </label>
              <div v-else>
                <img :src="imagePreview" alt="Preview" class="h-32 mx-auto object-cover rounded-lg" />
                <label for="gallery-file-upload" class="mt-4 inline-block text-sm text-blue-600 font-medium cursor-pointer">
                  Replace image
                </label>
              </div>
            </div>
          </div>

          <div>
            <label class="block text-gray-700 font-medium mb-2">Title</label>
            <input
              v-model="form.title"
              type="text"
              placeholder="Optional caption"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>

          <div>
            <label class="block text-gray-700 font-medium mb-2">Sort Order</label>
            <input
              v-model.number="form.sort_order"
              type="number"
              placeholder="0"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>
        </div>

        <div class="p-6 border-t border-gray-200 flex items-center justify-end gap-3 sticky bottom-0 bg-white">
          <button
            :disabled="isSaving"
            class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-colors disabled:opacity-50"
            @click="closeForm"
          >
            Cancel
          </button>
          <button
            :disabled="isSaving"
            class="px-6 py-3 bg-blue-600 hover:bg-blue-500 text-white rounded-lg font-medium transition-all flex items-center gap-2 shadow-md shadow-blue-600/40 hover:shadow-lg hover:shadow-blue-500/50 disabled:opacity-70"
            @click="editingId ? submitUpdate() : submitCreate()"
          >
            <span
              v-if="isSaving"
              class="inline-block h-5 w-5 rounded-full border-2 border-white border-t-transparent animate-spin"
            />
            <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
            </svg>
            {{ isSaving ? (editingId ? 'Updating...' : 'Uploading...') : (editingId ? 'Update Image' : 'Upload Image') }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useUserStore } from '~/stores/user'

const userStore = useUserStore()
const { token } = storeToRefs(userStore)
const { apiFetch, mediaUrl } = useApi()
const toast = useAppToast()
const { confirmDialog } = useConfirmDialog()

definePageMeta({
  layout: 'admin',
  middleware: ['admin'],
  title: 'adminDash.modGallery',
  subtitle: 'adminDash.modGalleryDesc',
})

const items = ref([])
const isLoading = ref(true)
const isSaving = ref(false)
const showModal = ref(false)
const editingId = ref(null)
const imageInput = ref(null)

const form = ref({ title: '', sort_order: 0 })
const imageFile = ref(null)
const imagePreview = ref('')
const existingImage = ref('')

const handleImageChange = (event) => {
  const file = event.target.files?.[0]
  if (!file) return
  imageFile.value = file
  imagePreview.value = URL.createObjectURL(file)
  if (event.target) event.target.value = ''
}

const loadItems = async (silent = false) => {
  try {
    if (!silent) isLoading.value = true
    const res = await apiFetch('/api/galleries')
    items.value = res.data || []
  } catch (error) {
    console.error('Error fetching gallery:', error)
    if (!silent) toast.error('Failed to fetch gallery images. Please try again later.')
  } finally {
    if (!silent) isLoading.value = false
  }
}

onContentChange(['galleries'], () => {
  loadItems(true)
})

onMounted(async () => {
  await loadItems()
})

const resetForm = () => {
  editingId.value = null
  form.value = { title: '', sort_order: 0 }
  imageFile.value = null
  imagePreview.value = ''
  existingImage.value = ''
  if (imageInput.value) imageInput.value.value = ''
}

const openCreateForm = () => {
  resetForm()
  showModal.value = true
}

const openEditForm = (item) => {
  editingId.value = item.id
  form.value = { title: item.title || '', sort_order: item.sort_order || 0 }
  existingImage.value = item.image
  imagePreview.value = mediaUrl(item.image)
  imageFile.value = null
  showModal.value = true
}

const closeForm = () => {
  showModal.value = false
  resetForm()
}

const submitCreate = async () => {
  if (!token.value) {
    toast.error('You are not logged in')
    return
  }
  if (!userStore.isAdmin) {
    toast.error('You are not allowed to add gallery images')
    return
  }
  if (!imageFile.value) {
    toast.error('An image is required.')
    return
  }

  try {
    const formData = new FormData()
    formData.append('title', form.value.title)
    formData.append('sort_order', String(form.value.sort_order || 0))
    formData.append('image', imageFile.value)

    isSaving.value = true
    const res = await apiFetch('/api/galleries', {
      method: 'POST',
      body: formData
    })

    if (res.success) {
      showModal.value = false
      resetForm()
      notifyContentChanged(['galleries'])
      await loadItems()
    } else {
      toast.error(res.message || 'Failed to add gallery image')
    }
  } catch (error) {
    console.error('Create gallery image error:', error)
    toast.error(error?.data?.message || error.message || 'Failed to add gallery image')
  } finally {
    isSaving.value = false
  }
}

const submitUpdate = async () => {
  if (!token.value) {
    toast.error('You are not logged in')
    return
  }
  if (!userStore.isAdmin) {
    toast.error('You are not allowed to update gallery images')
    return
  }

  try {
    const formData = new FormData()
    formData.append('id', String(editingId.value))
    formData.append('title', form.value.title)
    formData.append('sort_order', String(form.value.sort_order || 0))
    if (imageFile.value) {
      formData.append('image', imageFile.value)
    } else {
      formData.append('existingImage', existingImage.value)
    }

    isSaving.value = true
    const res = await apiFetch('/api/galleries/update', {
      method: 'POST',
      body: formData
    })

    if (res.success) {
      showModal.value = false
      resetForm()
      notifyContentChanged(['galleries'])
      await loadItems()
    } else {
      toast.error(res.message || 'Update failed')
    }
  } catch (error) {
    console.error('Update gallery image error:', error)
    toast.error(error?.data?.message || error.message || 'Failed to update gallery image')
  } finally {
    isSaving.value = false
  }
}

const deleteItem = async (id) => {
  const confirmed = await confirmDialog('Delete this image?', {
    title: 'Delete image',
    confirmText: 'Delete',
    danger: true,
  })
  if (!confirmed) return

  if (!token.value) {
    toast.error('You are not logged in')
    return
  }
  if (!userStore.isAdmin) {
    toast.error('You are not allowed to delete gallery images')
    return
  }

  try {
    const formData = new FormData()
    formData.append('id', id)

    const res = await apiFetch('/api/galleries/delete', {
      method: 'POST',
      body: formData
    })

    if (res.success) {
      toast.success(res.message || 'Image deleted')
      notifyContentChanged(['galleries'])
      await loadItems()
    } else {
      toast.error(res.message || 'Delete failed')
    }
  } catch (error) {
    console.error('Delete gallery image error:', error)
    toast.error('Failed to delete image.')
  }
}

const handleImageError = (event) => {
  event.target.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="400" height="300"%3E%3Crect fill="%23ddd" width="400" height="300"/%3E%3Ctext fill="%23999" x="50%25" y="50%25" text-anchor="middle" dy=".3em"%3EImage not available%3C/text%3E%3C/svg%3E'
  event.target.onerror = null
}
</script>
