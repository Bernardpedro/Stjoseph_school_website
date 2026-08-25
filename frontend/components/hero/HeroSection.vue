<template> 
  <div class="relative w-full h-screen flex flex-col">
    <!-- Static Background -->
    <div class="absolute inset-0 w-full h-full bg-cover bg-center">
      <img class="" :src="backgroundImage" alt="Background Image" />
      <!-- Overlay to ensure featuresCards visibility -->
      <div class="absolute inset-0 bg-black/30"></div>
    </div>

    <!-- Welcome Message -->
    <div class="relative z-10 pt-4 sm:pt-6 md:pt-8 lg:pt-12 xl:pt-16 text-center px-4">
      <h1 class="text-lg sm:text-xl md:text-2xl lg:text-3xl xl:text-4xl font-bold text-white mb-1 sm:mb-2 md:mb-3 lg:mb-4 drop-shadow-lg">
        Welcome to Saint Joseph TSS Nzuki
      </h1>
      <p class="text-xs sm:text-sm md:text-base lg:text-lg xl:text-xl text-white mb-2 sm:mb-3 md:mb-4 lg:mb-6 xl:mb-8 max-w-xs sm:max-w-sm md:max-w-md lg:max-w-2xl xl:max-w-3xl mx-auto drop-shadow-md">
        Discover our latest events and activities
      </p>
    </div>

    <!-- Spacer to push cards to bottom -->
    <div class="flex-grow"></div>

    <!-- featuresCards Container -->
    <div class="relative z-10 pb-16 sm:pb-20 md:pb-24 lg:pb-28 xl:pb-32">
      <!-- Loading state -->
      <div v-if="isLoading" class="absolute inset-0 flex items-center justify-center">
        <div class="text-center">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto"></div>
          <p class="mt-4 text-white">Loading events...</p>
        </div>
      </div>

      <!-- Error state -->
      <div v-else-if="error" class="absolute inset-0 flex items-center justify-center">
        <div class="text-center bg-red-500/20 backdrop-blur-sm p-6 rounded-lg">
          <p class="text-white text-lg">Error loading events: {{ error }}</p>
          <button 
            @click="loadLatest" 
            class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
          >
            Retry
          </button>
        </div>
      </div>

      <!-- Single slide with latest 3 events -->
      <div v-else class="container mx-auto px-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 w-full">
          <div
            v-for="event in latestEvents"
            :key="event.id"
            class="bg-white dark:bg-gray-800 text-black dark:text-white backdrop-blur-sm rounded-lg shadow-xl p-4 sm:p-6 transform transition-transform hover:scale-105"
          >
            <NuxtLink :to="`/${event.id}`" class="block">
              <h3 class="text-sm sm:text-base md:text-lg lg:text-xl text-blue-800 font-bold mb-2 sm:mb-3 text-center">{{ event.title }}</h3>
              <div class="relative">
                <img
                  :src="mediaUrl(getCurrentEventImage(event))"
                  :alt="event.title"
                  class="w-full h-48 object-cover rounded-lg mb-3 sm:mb-4"
                  @error="handleImageError"
                />
                <!-- New badge for latest events -->
                <div class="absolute top-2 right-2 bg-blue-600 text-white text-xs px-2 py-1 rounded-full">
                  NEW
                </div>
              </div>
            </NuxtLink>
          </div>
        </div>
      </div>
    </div>

    <!-- Link to events page -->
    <div class="relative z-10 pb-4 sm:pb-6 md:pb-8 text-center">
      <NuxtLink 
        to="/events" 
        class="inline-flex items-center px-3 sm:px-4 md:px-6 py-2 sm:py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200 shadow-lg text-xs sm:text-sm md:text-base"
      >
        View All Events
        <svg class="w-3 h-3 sm:w-4 sm:h-4 md:w-5 md:h-5 ml-1 sm:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
        </svg>
      </NuxtLink>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const {
  isLoading,
  error,
  mediaUrl,
  fetchEvents,
  getCurrentEventImage,
  cycleEventImages,
  sortEventsNewestFirst,
} = useEvents()

const backgroundImage = 'https://res.cloudinary.com/dck2vzccq/image/upload/v1766038842/PortailsReal2_mgw5vf.jpg';
const latestEvents = ref([]);

const loadLatest = async (silent = false) => {
  const list = await fetchEvents({ silent })
  latestEvents.value = sortEventsNewestFirst(list).slice(0, 3)
}

const handleImageError = (event) => {
  console.error('Image failed to load:', event.target.src);
  event.target.src = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0MDAiIGhlaWdodD0iMzAwIiB2aWV3Qm94PSIwIDAgNDAwIDMwMCI+CiAgPHJlY3Qgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgZmlsbD0iI2RkZCIvPgogIDx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBmb250LWZhbWlseT0iQXJpYWwsIHNhbnMtc2VyaWYiIGZvbnQtc2l6ZT0iMjQiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGRvbWluYW50LWJhc2VsaW5lPSJtaWRkbGUiIGZpbGw9IiM5OTkiPkltYWdlIG5vdCBhdmFpbGFibGU8L3RleHQ+Cjwvc3ZnPg==';
  event.target.onerror = null;
};

let imageCycleInterval = null

onContentChange(['events'], () => {
  loadLatest(true)
})

onMounted(async () => {
  await loadLatest();
  imageCycleInterval = setInterval(() => cycleEventImages(latestEvents.value), 3000);
});

onUnmounted(() => {
  if (imageCycleInterval) clearInterval(imageCycleInterval)
});
</script>