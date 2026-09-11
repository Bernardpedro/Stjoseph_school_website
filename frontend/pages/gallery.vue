<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Page header -->
    <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
      <div class="max-w-6xl mx-auto px-4 py-10 text-center">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white">Gallery</h1>
        <p class="mt-2 text-gray-600 dark:text-gray-300">A look at campus life at Saint Joseph TSS Nzuki.</p>
      </div>
    </div>

    <div class="max-w-6xl mx-auto px-4 py-10">
      <!-- Loading -->
      <div v-if="isLoading" class="flex items-center justify-center py-16">
        <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-blue-600"></div>
      </div>

      <!-- Empty state -->
      <div
        v-else-if="images.length === 0"
        class="text-center py-16 bg-white dark:bg-gray-800 rounded-xl border border-dashed border-gray-300 dark:border-gray-700"
      >
        <p class="text-gray-500 dark:text-gray-400">No gallery images yet.</p>
      </div>

      <!-- Grid -->
      <div v-else class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        <div
          v-for="image in images"
          :key="image.id"
          class="aspect-square overflow-hidden rounded-xl bg-gray-100 dark:bg-gray-800 shadow-sm"
        >
          <img
            :src="image.image"
            :alt="image.title || 'Gallery image'"
            class="w-full h-full object-cover"
            loading="lazy"
            @error="handleImageError"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const { apiFetch, mediaUrl } = useApi()

definePageMeta({
  layout: 'default',
})

usePageSeo({
  title: 'Gallery',
  description: 'Photo gallery of campus life at Saint Joseph Technical Secondary School Nzuki.',
})

const images = ref([])
const isLoading = ref(true)

const loadImages = async (silent = false) => {
  try {
    if (!silent) isLoading.value = true
    const res = await apiFetch('/api/galleries')
    images.value = (res.data || []).map((row) => ({
      id: row.id,
      title: row.title,
      image: mediaUrl(row.image),
    }))
  } catch (error) {
    console.error('Error fetching gallery:', error)
  } finally {
    if (!silent) isLoading.value = false
  }
}

onContentChange(['galleries'], () => {
  loadImages(true)
})

onMounted(async () => {
  await loadImages()
})

const handleImageError = (event) => {
  event.target.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="400" height="300"%3E%3Crect fill="%23ddd" width="400" height="300"/%3E%3Ctext fill="%23999" x="50%25" y="50%25" text-anchor="middle" dy=".3em"%3EImage not available%3C/text%3E%3C/svg%3E'
  event.target.onerror = null
}
</script>
