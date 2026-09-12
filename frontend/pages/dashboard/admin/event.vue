<template>
  <div>
    <!-- Loading indicator -->
    <div v-if="isLoading || isSaving" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[60]">
      <div class="bg-white p-4 rounded-lg shadow-lg">
        <div class="flex items-center">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
          <span class="ml-3">{{ isSaving ? (editingEvent ? 'Updating event...' : 'Saving event...') : 'Processing...' }}</span>
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
        Add New Event
      </button>
    </div>

<!-- Empty state -->
    <div v-if="!isLoading && uploadedEvents.length === 0" class="mt-8 text-center py-12 bg-gray-50 rounded-xl border border-dashed border-gray-300">
      <p class="text-gray-500">No events yet. Click "Add New Event" to create one.</p>
    </div>

    <!-- Display Uploaded Events -->
    <div v-if="uploadedEvents.length > 0" class="mt-8">
      <h3 class="text-xl font-bold text-gray-900 mb-4">Recently Uploaded Events</h3>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="event in uploadedEvents" :key="event.id" class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200 hover:shadow-lg transition-shadow">
          <!-- Event Image -->
          <div class="h-48 overflow-hidden bg-gray-100">
            <img 
            :src="event.images && event.images.length > 0 ? mediaUrl(event.images[0]) : ''"
              :alt="event.title"
              class="w-full h-full object-cover"
              @error="handleImageError"
            />
          </div>
          
          <!-- Event Details -->
          <div class="p-4">
            <div class="flex justify-between items-start mb-2">
              <h4 class="text-lg font-semibold text-gray-900">{{ event.title }}</h4>
              <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">
                {{ event.type_label || eventTypes.find(t => t.value === event.type)?.label || event.type }}
              </span>
            </div>
            
            <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ event.description }}</p>
            
            <div class="space-y-2 text-sm text-gray-500">
              <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                {{ formatDate(event.date) }}
                <span v-if="event.time" class="ml-2">at {{ event.time }}</span>
              </div>
              
              <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                {{ event.location }}
              </div>
              
              <div v-if="event.organizer" class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                {{ event.organizer }}
              </div>
            </div>
            
            <div class="mt-3 flex justify-between items-center">
              <span class="px-2 py-1 rounded text-xs font-medium"
                :class="event.status === 'upcoming' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
              >
                {{ event.status }}
              </span>
              
              <div v-if="event.youtubeLink" class="text-red-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                </svg>
              </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="mt-4 flex gap-2">
              <button 
                @click="openUpdateForm(event)"
                class="flex-1 flex items-center justify-center gap-1 px-3 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors text-sm font-medium"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                  <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                </svg>
                Edit
              </button>
              <button 
                @click="deleteEvent(event.id)"
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
      v-if="showUploadModal"
      class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4"
      @click.self="closeForm"
    >
      <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between sticky top-0 bg-white z-10">
          <h3 class="text-2xl font-bold text-gray-900">
            {{ editingEvent ? 'Edit Event' : 'Add New Event' }}
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
          <!-- Image Upload Section -->
          <div>
            <label class="block text-gray-700 font-medium mb-3">Event Images</label>
            
            <!-- Image Type Selection -->
            <div class="mb-4">
              <div class="flex space-x-4">
                <label class="flex items-center">
                  <input 
                    type="radio" 
                    v-model="newEvent.imageType" 
                    value="single" 
                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                  />
                  <span class="ml-2 text-gray-700">Single Image</span>
                </label>
                <label class="flex items-center">
                  <input 
                    type="radio" 
                    v-model="newEvent.imageType" 
                    value="multiple" 
                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                  />
                  <span class="ml-2 text-gray-700">Multiple Images (Slideshow)</span>
                </label>
              </div>
            </div>
            
            <!-- Image Upload Area -->
            <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-blue-500 transition-colors bg-gray-50">
              <input
                ref="imageInput"
                type="file"
                accept="image/*"
                multiple
                @change="handleImage"
                class="hidden"
                id="file-upload"
              />
              <label v-if="imagesPreviewUrls.length === 0" for="file-upload" class="cursor-pointer block">
                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <p class="mt-2 text-sm text-gray-600">
                  <span class="font-medium text-blue-600 hover:text-blue-500">Click to upload</span> or drag and drop
                </p>
                <p class="text-xs text-gray-500 mt-1">PNG, JPG, GIF up to 1MB</p>
              </label>
              <div v-else>
                <p class="text-sm text-gray-600 mb-4">{{ imagesPreviewUrls.length }} image(s) selected</p>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                  <div v-for="(url, index) in imagesPreviewUrls" :key="url + '-' + index" class="relative group">
                    <img :src="url" alt="Preview" class="h-24 w-full object-cover rounded-lg" />
                    <button
                      type="button"
                      @click.stop.prevent="removePreviewImage(index)"
                      class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 opacity-100 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity"
                      aria-label="Remove image"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                      </svg>
                    </button>
                  </div>
                </div>
                <label for="file-upload" class="mt-4 inline-block text-sm text-blue-600 font-medium cursor-pointer">
                  Add more images
                </label>
              </div>
            </div>
          </div>

          <!-- Video Upload Section -->


          <!-- Form Fields -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
              <label class="block text-gray-700 font-medium mb-2">Event Title *</label>
              <input 
                v-model="newEvent.title"
                type="text" 
                placeholder="Enter event title"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>

            <div class="md:col-span-2">
              <label class="block text-gray-700 font-medium mb-2">Description *</label>
              <textarea 
                v-model="newEvent.description"
                rows="4"
                placeholder="Describe the event"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
              ></textarea>
            </div>

            <div>
              <label class="block text-gray-700 font-medium mb-2">Date *</label>
              <input 
                v-model="newEvent.date"
                type="date"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>

            <div>
              <label class="block text-gray-700 font-medium mb-2">Time</label>
              <input 
                v-model="newEvent.time"
                type="time"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>

            <div>
              <label class="block text-gray-700 font-medium mb-2">Location *</label>
              <input 
                v-model="newEvent.location"
                type="text"
                placeholder="Event location"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>

            <div>
              <label class="block text-gray-700 font-medium mb-2">Event Type</label>
              <select 
                v-model="newEvent.type"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
                <option v-for="type in eventTypes" :key="type.value" :value="type.value">
                  {{ type.label }}
                </option>
              </select>
            </div>

            <div>
              <label class="block text-gray-700 font-medium mb-2">Organizer</label>
              <input 
                v-model="newEvent.organizer"
                type="text"
                placeholder="Event organizer"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>

            <div>
              <label class="block text-gray-700 font-medium mb-2">YouTube Link</label>
              <input 
                v-model="newEvent.youtubeLink"
                type="text"
                placeholder="https://youtube.com/..."
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>

            <div>
              <label class="block text-gray-700 font-medium mb-2">Status</label>
              <select 
                v-model="newEvent.status"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
                <option value="upcoming">Upcoming</option>
                <option value="past">Past</option>
              </select>
            </div>
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
            @click="editingEvent ? submitUpdateEvent() : createEvent()"
          >
            <span
              v-if="isSaving"
              class="inline-block h-5 w-5 rounded-full border-2 border-white border-t-transparent animate-spin"
            />
            <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
            </svg>
            {{ isSaving ? (editingEvent ? 'Updating...' : 'Uploading...') : (editingEvent ? 'Update Event' : 'Upload Event') }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useUserStore } from '~/stores/user'
import { ADMIN_EVENT_TYPES } from '~/utils/eventTypes'

const userStore = useUserStore()
const { token } = storeToRefs(userStore)
const { apiFetch, mediaUrl } = useApi()
const toast = useAppToast()
const { confirmDialog } = useConfirmDialog()

definePageMeta({
  layout: 'default',
  middleware: ['admin']
})
// Form state
const showUploadModal = ref(false);
const uploadedEvents = ref([]); // Store uploaded events
const editingEvent = ref(false); // Track which event is being edited
const isLoading = ref(true);
const isSaving = ref(false); 
const eventTypes = ref([...ADMIN_EVENT_TYPES]);

const newEvent = ref({
  title: '',
  description: '',
  date: '',
  time: '',
  location: '',
  type: '',
  organizer: '',
  youtubeLink: '',
  images: '',
  videos: '',
  status: ''
});

const existingImages = ref([]) // URLs from backend
const existingVideos = ref([]) // videos URLs from backend
const newImages = ref([])      // { file, preview }
const newVideos = ref([])      // video objects
const imageInput = ref(null)

// DEFAULT OF UPDATE
const updateEventVar = ref({
  id: '',
  title: '',
  description: '',
  date: '',
  time: '',
  location: '',
  type: '',
  organizer: '',
  youtubeLink: '',
  images: '',
  status: ''
});

// const videosPreviewUrls = ref([]);
const imagesPreviewUrls = ref([]);

const handleImage = (event) => {
  const files = Array.from(event.target.files || [])
  files.forEach((file) => {
    const preview = URL.createObjectURL(file)
    newImages.value.push({ file, preview })
    imagesPreviewUrls.value.push(preview)
  })
  if (event.target) event.target.value = ''
}

// Post , creation of event

const createEvent = async () => {
  try {
    if (!token.value) {
      toast.error('You are not logged in')
      return
    }

    if (!userStore.isAdmin) {
      toast.error('You are not allowed to create events')
      return
    }

    const formData = new FormData()
    formData.append('title', newEvent.value.title)
    formData.append('description', newEvent.value.description)
    formData.append('date', newEvent.value.date)
    formData.append('time', newEvent.value.time)
    formData.append('location', newEvent.value.location)
    formData.append('type', newEvent.value.type)
    formData.append('status', newEvent.value.status)
    formData.append('organizer', newEvent.value.organizer)
    formData.append('youtubeLink', newEvent.value.youtubeLink)

    newImages.value.forEach((item) => {
      formData.append('images[]', item.file || item)
    })

    isSaving.value = true
    const res = await apiFetch('/api/events', {
      method: 'POST',
      body: formData
    })

    if (res.success) {
      showUploadModal.value = false
      resetForm()
      notifyContentChanged(['events'])
      await fetchEvents()
    } else {
      toast.error(res.message || 'Failed to create event')
    }
  } catch (error) {
    console.error('Create event error:', error)
    toast.error(error?.data?.message || error.message || 'Failed to create event')
  } finally {
    isSaving.value = false
  }
}

// open update form

  const openUpdateForm = (event) => { 

    editingEvent.value = true
  
    // Store the event being edited
    updateEventVar.value = event

    newEvent.value = {
    title: event.title,
    description: event.description,
    date: event.date,
    time: event.time,
    location: event.location,
    type: event.type,
    status: event.status,
    organizer: event.organizer,
    youtubeLink: event.youtubeLink,
    images: event.images.map(img => mediaUrl(img)),
    }

      // existing images from backend
    existingImages.value = event.images.map(
    img => mediaUrl(img)
  )

    // show them as preview
   imagesPreviewUrls.value = [...existingImages.value]

    // reset new uploads
    newImages.value = []

    showUploadModal.value = true
}

// submit 
const submitUpdateEvent = async () => {
  try {

    if (!token.value) {
      toast.error('You are not logged in')
      return
    }

    if (!userStore.isAdmin) {
      toast.error('You are not allowed to update events')
      return
    }

    const formData = new FormData()
    formData.append('id', String(updateEventVar.value.id))
    formData.append('title', newEvent.value.title)
    formData.append('description', newEvent.value.description)
    formData.append('date', newEvent.value.date)
    formData.append('time', newEvent.value.time)
    formData.append('location', newEvent.value.location)
    formData.append('type', newEvent.value.type)
    formData.append('status', newEvent.value.status)
    formData.append('organizer', newEvent.value.organizer)
    formData.append('youtubeLink', newEvent.value.youtubeLink)


    newImages.value.forEach((item) => {
      formData.append('images[]', item.file || item)
    })

    newVideos.value.forEach((file) => {
      formData.append('videos[]', file)
    })

    formData.append('existingImages', JSON.stringify(existingImages.value))

    isSaving.value = true
    const res = await apiFetch('/api/events/update', {
      method: 'POST',
      body: formData,
      query: { id: updateEventVar.value.id }
    })

    if (res.success) {
      showUploadModal.value = false
      editingEvent.value = false
      resetForm()
      notifyContentChanged(['events'])
      await fetchEvents()
    } else {
      toast.error(`Update failed: ${res.message || 'Unknown error'}`)
      console.error('Update failed with response:', res)
    }
  } catch (error) {
    console.error('Update event error:', error)
    toast.error(`Failed to update event: ${error?.data?.message || error.message || 'Check backend'}`)
  } finally {
    isSaving.value = false
  }
}

// Fetch events from API when component mounts
onContentChange(['events'], () => {
  fetchEvents(true)
})

onMounted(async () => {
  await fetchEvents();
});

// Fetch all events from API
const fetchEvents = async (silent = false) => {
  try {
    if (!token.value || !userStore.isAdmin) {
      if (!silent) toast.error('You are not allowed to get events')
      return
    }

    if (!silent) isLoading.value = true
    const res = await apiFetch('/api/events')
    uploadedEvents.value = res.data || []
  } catch (error) {
    console.error('Error fetching :', error)
    if (!silent) toast.error('Failed to fetch events. Please try again later.')
  } finally {
    if (!silent) isLoading.value = false
  }
}

const removePreviewImage = (index) => {
  const removedUrl = imagesPreviewUrls.value[index]
  if (!removedUrl) return

  const existingIndex = existingImages.value.indexOf(removedUrl)
  if (existingIndex !== -1) {
    existingImages.value.splice(existingIndex, 1)
  }

  const newIndex = newImages.value.findIndex((item) => item.preview === removedUrl)
  if (newIndex !== -1) {
    URL.revokeObjectURL(newImages.value[newIndex].preview)
    newImages.value.splice(newIndex, 1)
  }

  imagesPreviewUrls.value.splice(index, 1)
  if (imageInput.value) imageInput.value.value = ''
}

// Delete an event
const deleteEvent = async (eventId) => {
  const confirmDelete = await confirmDialog('Are you sure you want to delete this event?', {
    title: 'Delete event',
    confirmText: 'Delete',
    danger: true,
  })
  if (!confirmDelete) return

  try {
    if (!token.value) {
      toast.error('You are not logged in')
      return
    }

    if (!userStore.isAdmin) {
      toast.error('You are not allowed to delete events')
      return
    }

    const formData = new FormData()
    formData.append('id', eventId)

    const res = await apiFetch('/api/events/delete', {
      method: 'POST',
      body: formData
    })

    if (res.success) {
      toast.success(res.message || 'Event deleted')
      notifyContentChanged(['events'])
      await fetchEvents()
    } else {
      toast.error(res.message || 'Delete failed')
    }
  } catch (error) {
    console.error('Delete event error:', error)
    toast.error('Failed to delete event.')
  }
}

const resetForm = () => {
  editingEvent.value = false
  updateEventVar.value = {
    id: '',
    title: '',
    description: '',
    date: '',
    time: '',
    location: '',
    type: '',
    organizer: '',
    youtubeLink: '',
    images: '',
    status: '',
  }
  newEvent.value = {
    title: '',
    description: '',
    date: '',
    time: '',
    location: '',
    type: '',
    status: '',
    organizer: '',
    youtubeLink: '',
    images: '',
    videos: '',
    imageType: 'multiple',
  }
  imagesPreviewUrls.value = []
  existingImages.value = []
  newImages.value = []
  newVideos.value = []
  if (imageInput.value) imageInput.value.value = ''
}

const openCreateForm = () => {
  resetForm()
  showUploadModal.value = true
}

const closeForm = () => {
  showUploadModal.value = false
  resetForm()
}

// Format date for display
const formatDate = (dateString) => {
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return new Date(dateString).toLocaleDateString(undefined, options);
};


const handleImageError = (event) => {
  console.error('Image failed to load:', event.target.src);
  // Use a data URI placeholder instead
  event.target.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="400" height="300"%3E%3Crect fill="%23ddd" width="400" height="300"/%3E%3Ctext fill="%23999" x="50%25" y="50%25" text-anchor="middle" dy=".3em"%3EImage not available%3C/text%3E%3C/svg%3E';
  event.target.onerror = null; // Prevent infinite loop
};
</script>
