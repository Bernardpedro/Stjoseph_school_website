<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900 p-4 md:p-8">
    <!-- Back Button -->
    <div class="mb-6">
      <button 
        @click="goBack"
        class="flex items-center space-x-2 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
        </svg>
        <span>Back</span>
      </button>
    </div>

    <!-- Loading State -->
    <div v-if="isLoading" class="flex items-center justify-center h-64">
      <div class="text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto"></div>
        <p class="mt-4 text-gray-600 dark:text-gray-300">Loading details...</p>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="max-w-4xl mx-auto text-center py-12">
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-red-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Error Loading Content</h2>
        <p class="text-gray-600 dark:text-gray-300 mb-4">{{ error }}</p>
        <button 
          @click="fetchEventData"
          class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors"
        >
          Try Again
        </button>
      </div>
    </div>

    <!-- Main Content -->
    <div v-else-if="eventData && eventData.media && eventData.media.length > 0" class="max-w-7xl mx-auto">
      <!-- Header -->
      <div class="mb-6">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-2">
          {{ eventData.title }}
        </h1>
        <p class="text-lg text-gray-600 dark:text-gray-300">{{ eventData.category }}</p>
      </div>

      <!-- Main Content Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Left Column - Large Image -->
        <div class="lg:col-span-8 bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden">
          <div class="relative h-[400px] md:h-[500px] lg:h-[600px]">
            <!-- Main Image Display -->
            <div v-if="currentMedia && currentMedia.type === 'image'" class="w-full h-full">
              <img 
                :src="mediaUrl(currentMedia.src)" 
                :alt="currentMedia.title"
                class="w-full h-full object-cover"
                @error="onImageError"
              >
            </div>

            <!-- Video Display -->
            <div v-if="currentMedia && currentMedia.type === 'video'" class="w-full h-full">
              <video 
                ref="videoPlayer"
                :src="currentMedia.src"
                :muted="isMuted"
                class="w-full h-full object-cover"
                @loadedmetadata="onVideoLoaded"
                @timeupdate="onTimeUpdate"
                @ended="onVideoEnded"
              ></video>
              
              <!-- Video Controls -->
              <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent opacity-0 hover:opacity-100 transition-opacity duration-300">
                <div class="absolute inset-0 flex items-center justify-center">
                  <button 
                    @click="togglePlay"
                    class="bg-white/20 hover:bg-white/30 backdrop-blur-sm rounded-full p-4 transition-colors"
                  >
                    <svg v-if="!isPlaying" xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white ml-1" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                    </svg>
                    <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zM7 8a1 1 0 012 0v4a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v4a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                  </button>
                </div>
              </div>
            </div>

            <!-- Navigation Arrows -->
            <button 
              v-if="eventData.media.length > 1"
              @click="previousMedia"
              class="absolute left-4 top-1/2 -translate-y-1/2 bg-white/20 hover:bg-white/30 backdrop-blur-sm rounded-full p-2 transition-colors z-10"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
              </svg>
            </button>
            
            <button 
              v-if="eventData.media.length > 1"
              @click="nextMedia"
              class="absolute right-4 top-1/2 -translate-y-1/2 bg-white/20 hover:bg-white/30 backdrop-blur-sm rounded-full p-2 transition-colors z-10"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </button>

            <!-- Media Counter -->
            <div class="absolute top-4 left-4 bg-black/50 backdrop-blur-sm text-white px-3 py-1 rounded-full text-sm">
              {{ currentMediaIndex + 1 }} / {{ eventData.media.length }}
            </div>
          </div>

          <!-- Media Title and Description -->
          <div v-if="currentMedia" class="p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
              {{ currentMedia.title }}
            </h2>
            <p class="text-gray-600 dark:text-gray-300">
              {{ currentMedia.description }}
            </p>
          </div>
        </div>

        <!-- Right Column - Thumbnails and Info -->
        <div class="lg:col-span-4 space-y-6">
          <!-- Thumbnails -->
          <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Gallery</h3>
            
            <div class="grid grid-cols-3 gap-3">
              <div 
                v-for="(media, index) in eventData.media" 
                :key="index"
                @click="currentMediaIndex = index"
                class="relative aspect-square bg-gray-100 dark:bg-gray-700 rounded-lg overflow-hidden cursor-pointer hover:ring-2 hover:ring-blue-500 transition-all duration-200"
                :class="{'ring-2 ring-blue-500': index === currentMediaIndex}"
              >
                <!-- Image Thumbnail -->
                <img 
                  v-if="media.type === 'image'"
                  :src="mediaUrl(media.src)" 
                  :alt="media.title"
                  class="w-full h-full object-cover"
                  @error="onImageError"
                />
                
                <!-- Video Thumbnail -->
                <div v-if="media.type === 'video'" class="relative w-full h-full">
                  <img 
                    v-if="media.poster"
                    :src="media.poster" 
                    :alt="media.title"
                    class="w-full h-full object-cover"
                  />
                  <div v-else class="w-full h-full bg-gray-800 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                      <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z" />
                    </svg>
                  </div>
                  
                  <!-- Video Play Icon Overlay -->
                  <div class="absolute inset-0 flex items-center justify-center bg-black/30">
                    <div class="bg-white/90 rounded-full p-2">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-900" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                      </svg>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Description Card -->
          <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">
              About {{ eventData.title }}
            </h3>
            <p class="text-gray-600 dark:text-gray-300 mb-4">
              {{ eventData.description }}
            </p>
            
            <!-- Additional Details -->
            <div class="space-y-3">
              <div v-if="eventData.category" class="flex items-center space-x-2">
                <span class="text-sm text-gray-500">Category:</span>
                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ eventData.category }}</span>
              </div>
              <div v-if="eventData.date" class="flex items-center space-x-2">
                <span class="text-sm text-gray-500">Date:</span>
                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ eventData.date }}</span>
              </div>
              <div v-if="eventData.time" class="flex items-center space-x-2">
                <span class="text-sm text-gray-500">Time:</span>
                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ eventData.time }}</span>
              </div>
              <div v-if="eventData.location" class="flex items-center space-x-2">
                <span class="text-sm text-gray-500">Location:</span>
                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ eventData.location }}</span>
              </div>
              <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-500">Media Items:</span>
                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ eventData.media.length }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- No Media State -->
    <div v-else-if="eventData && (!eventData.media || eventData.media.length === 0)" class="max-w-4xl mx-auto text-center py-12">
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ eventData.title }}</h2>
        <p class="text-gray-600 dark:text-gray-300 mb-4">{{ eventData.description }}</p>
        <p class="text-sm text-gray-500">No media available for this item</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';

const { fetchEventById, mediaUrl } = useEvents()

definePageMeta({
  layout: 'default'
});

// Router
const route = useRoute();
const router = useRouter();

// State
const eventData = ref(null);
const currentMediaIndex = ref(0);
const isPlaying = ref(false);
const isMuted = ref(false);
const videoPlayer = ref(null);
const isLoading = ref(true);
const error = ref(null);

// Computed
const currentMedia = computed(() => {
  if (!eventData.value || !eventData.value.media || eventData.value.media.length === 0) return null;
  return eventData.value.media[currentMediaIndex.value];
});

// Methods
const goBack = () => {
  router.back();
};

const nextMedia = () => {
  if (eventData.value && eventData.value.media && currentMediaIndex.value < eventData.value.media.length - 1) {
    currentMediaIndex.value++;
    isPlaying.value = false;
  }
};

const previousMedia = () => {
  if (eventData.value && eventData.value.media && currentMediaIndex.value > 0) {
    currentMediaIndex.value--;
    isPlaying.value = false;
  }
};

const togglePlay = () => {
  if (videoPlayer.value) {
    if (isPlaying.value) {
      videoPlayer.value.pause();
    } else {
      videoPlayer.value.play();
    }
    isPlaying.value = !isPlaying.value;
  }
};

const onVideoLoaded = () => {
  // Video loaded
};

const onTimeUpdate = () => {
  // Video time update
};

const onVideoEnded = () => {
  isPlaying.value = false;
};

const onImageError = (event) => {
  console.error('Image failed to load:', event.target.src);
};

// Fetch and format event data
const fetchEventData = async (silent = false) => {
  if (!silent) isLoading.value = true;
  error.value = null;
  
  const eventId = route.params.id;
  
  if (!eventId) {
    error.value = 'No event ID provided';
    isLoading.value = false;
    return;
  }

  try {
    const event = await fetchEventById(eventId);
    if (!event) {
      if (!silent) {
        error.value = 'Event not found';
        isLoading.value = false;
      }
      return;
    }

    // Transform images array to media objects
    const mediaArray = [];
    if (event.images && Array.isArray(event.images)) {
      event.images.forEach((image, index) => {
        mediaArray.push({
          type: 'image',
          src: image,
          title: `${event.title || 'Event'} Image ${index + 1}`,
          description: event.description || 'Event image'
        });
      });
    }

    // Add YouTube video if available
    if (event.youtubeLink) {
      mediaArray.push({
        type: 'video',
        src: event.youtubeLink,
        title: event.title || 'Event Video',
        description: 'Watch the event video'
      });
    }

    // Format the data
    eventData.value = {
      ...event,
      title: event.title || 'No Title',
      description: event.description || 'No description available.',
      category: event.type || 'Event',
      date: event.date || '',
      time: event.time || '',
      location: event.location || 'Location not specified',
      media: mediaArray
    };

    if (!silent) isLoading.value = false;

  } catch (err) {
    console.error('Error loading event:', err);
    if (!silent) {
      error.value = err.message || 'Failed to load event details. Please try again later.';
      isLoading.value = false;
    }
  }
};

// Event listeners for keyboard navigation
const addEventListeners = () => {
  document.addEventListener('keydown', handleKeyPress);
};

const removeEventListeners = () => {
  document.removeEventListener('keydown', handleKeyPress);
};

// Keyboard navigation
const handleKeyPress = (e) => {
  if (!eventData.value) return;
  
  switch (e.key) {
    case 'ArrowLeft':
      previousMedia();
      break;
    case 'ArrowRight':
      nextMedia();
      break;
    case 'Escape':
      goBack();
      break;
    case ' ':
      if (currentMedia.value?.type === 'video') {
        e.preventDefault();
        togglePlay();
      }
      break;
  }
};

// Watch for route parameter changes
watch(() => route.params.id, (newId) => {
  if (newId) {
    fetchEventData();
  }
}, { immediate: true });

onContentChange(['events'], () => {
  fetchEventData(true)
})

// Lifecycle hooks
onMounted(() => {
  fetchEventData();
  addEventListeners();
});

onUnmounted(() => {
  removeEventListeners();
});
</script>