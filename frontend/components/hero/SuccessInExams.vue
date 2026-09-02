<template>
  <div id="nationWide">
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
      <!-- Header Section -->
      <div class="bg-white dark:bg-gray-800 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
          <div class="text-center">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
              {{ $t('success.title') }}
            </h1>
            <p class="text-lg text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
             {{ $t('success.description') }}
            </p>
          </div>
        </div>
      </div>
  
      <!-- Featured Events Slider -->
      <div class="max-w-4xl mx-auto px-4 sm:px-0 lg:px-0 py-0">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden mb-8">
          <div class="relative h-96 md:h-[500px]" @mouseenter="stopSlideShow" @mouseleave="startSlideShow">
            <!-- Slides -->
            <div
              v-for="(event, index) in featuredEvents"
              :key="event.id"
              class="absolute inset-0 transition-opacity duration-1000"
              :class="{ 'opacity-100': index === currentSlide, 'opacity-0': index !== currentSlide }"
            >
              <div class="block cursor-pointer" @click="handleEventClick(event)">
                
                <NuxtLink :to="`/card-overview?type=hero&id=${event.cardId}`">
                <div class="relative h-full">
                    <img
                    :src="cldOptimize(event.image, 1200)"
                    :alt="event.title"
                    class="w-full h-full object-cover"
                    loading="lazy"
                    @error="handleImageError($event, event)"
                    />
                    <div class="absolute inset-0 bg-black bg-opacity-20"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                      <div class="text-center text-white px-4 max-w-4xl">
                        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                          <div class="flex items-center text-lg">
                        </div>
                        <div class="flex items-center text-lg">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </NuxtLink>
                
              </div>
            </div>
  
            <!-- Navigation arrows -->
            <button
              @click="prevSlide"
              class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 hover:bg-opacity-75 text-white p-2 rounded-full transition-colors duration-200"
            >
  
            </button>
            <button
              @click="nextSlide"
              class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 hover:bg-opacity-75 text-white p-2 rounded-full transition-colors duration-200"
            >
  
            </button>
  
            <!-- Dots indicator -->
            <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2">
              <button
                v-for="(event, index) in featuredEvents"
                :key="index"
                @click="goToSlide(index)"
                class="w-3 h-3 rounded-full transition-colors duration-200"
                :class="{ 'bg-white': index === currentSlide, 'bg-white bg-opacity-50': index !== currentSlide }"
              ></button>
            </div>
          </div>
        </div>
      </div>
  
      <!-- Student Achievement Details Section -->
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
          <div class="px-6 py-8">
            <div class="text-center mb-8">
              <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">
                {{ $t('success.outstandingTitle') }}
              </h2>
              <p class="text-lg text-gray-600 dark:text-gray-300">
                {{ $t('success.congratsText') }}
              </p>
            </div>
            
            <div class="grid md:grid-cols-2 gap-6">
              <!-- Student 1 -->
              <div class="bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-gray-700 dark:to-gray-600 rounded-lg p-6 border-l-4 border-blue-500">
                <div class="flex items-center mb-4">
                  <div class="bg-blue-500 text-white rounded-full w-12 h-12 flex items-center justify-center text-xl font-bold">
                    5th
                  </div>
                  <div class="ml-4">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                      KARANGWA IRAKOZE Roben
                    </h3>
                    <p class="text-gray-600 dark:text-gray-300">{{ $t('success.autProgram') }}</p>
                  </div>
                </div>
                <div class="space-y-2">
                  <div class="flex justify-between items-center">
                    <span class="text-gray-700 dark:text-gray-300">{{ $t('success.nationalRanking') }}</span>
                    <span class="font-bold text-blue-600 dark:text-blue-400">5th {{ $t('success.position') }}</span>
                  </div>
                  <div class="flex justify-between items-center">
                    <span class="text-gray-700 dark:text-gray-300">{{ $t('success.weightedPercentage') }}</span>
                    <span class="font-bold text-green-600 dark:text-green-400">90.21%</span>
                  </div>
                </div>
              </div>
  
              <!-- Student 2 -->
              <div class="bg-gradient-to-br from-green-50 to-emerald-100 dark:from-gray-700 dark:to-gray-600 rounded-lg p-6 border-l-4 border-green-500">
                <div class="flex items-center mb-4">
                  <div class="bg-green-500 text-white rounded-full w-12 h-12 flex items-center justify-center text-xl font-bold">
                    8th
                  </div>
                  <div class="ml-4">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                      Aime SENGA Prosper
                    </h3>
                    <p class="text-gray-600 dark:text-gray-300">{{ $t('success.autProgram') }}</p>
                  </div>
                </div>
                <div class="space-y-2">
                  <div class="flex justify-between items-center">
                    <span class="text-gray-700 dark:text-gray-300">{{ $t('success.nationalRanking') }}</span>
                    <span class="font-bold text-green-600 dark:text-green-400">8th {{ $t('success.position') }}</span>
                  </div>
                  <div class="flex justify-between items-center">
                    <span class="text-gray-700 dark:text-gray-300">{{ $t('success.weightedPercentage') }}</span>
                    <span class="font-bold text-green-600 dark:text-green-400">89.99%</span>
                  </div>
                </div>
              </div>
            </div>
  
            <!-- Achievement Summary -->
            <!-- <div class="mt-8 bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
              <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                Achievement Highlights
              </h4>
              <div class="grid md:grid-cols-2 gap-4 text-center">
                <div class="bg-white dark:bg-gray-600 rounded-lg p-4">
                  <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">Top 10</div>
                  <div class="text-sm text-gray-600 dark:text-gray-300">National Ranking</div>
                </div>
                <div class="bg-white dark:bg-gray-600 rounded-lg p-4">
                  <div class="text-2xl font-bold text-green-600 dark:text-green-400">90%+</div>
                  <div class="text-sm text-gray-600 dark:text-gray-300">Average Score</div>
                </div>
                <div class="bg-white dark:bg-gray-600 rounded-lg p-4">
                  <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">AUT</div>
                  <div class="text-sm text-gray-600 dark:text-gray-300">Program Excellence</div>
                </div>
              </div>
            </div> -->
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

// Reactive variables
const currentSlide = ref(0)
const slideInterval = ref(null)

// Featured events with images for slider
const featuredEvents = ref([
  {
    id: 1,
    cardId: 'results2025',
    title: 'A Legacy of Excellence: Two Students from Saint Joseph TSS Nzuki Rank in Top 10 Nationwide',
    description: '',
    image: 'https://res.cloudinary.com/dck2vzccq/image/upload/v1756885559/result2025_d6d9ey.png',
    date: '2025',
    location: ''
  }
])

// Navigation functions
const nextSlide = () => {
  currentSlide.value = (currentSlide.value + 1) % featuredEvents.value.length
}

const prevSlide = () => {
  currentSlide.value = currentSlide.value === 0 ? featuredEvents.value.length - 1 : currentSlide.value - 1
}

const goToSlide = (index) => {
  currentSlide.value = index
}

// Auto slideshow functions
const startSlideShow = () => {
  slideInterval.value = setInterval(() => {
    nextSlide()
  }, 5000) // Change slide every 5 seconds
}

const stopSlideShow = () => {
  if (slideInterval.value) {
    clearInterval(slideInterval.value)
    slideInterval.value = null
  }
}

// Utility functions
const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const handleEventClick = (event) => {
}

const handleImageError = (event, eventData) => {
  console.error('Image failed to load:', eventData.image)
  // You can set a fallback image here
  event.target.src = 'https://via.placeholder.com/800x600/cccccc/666666?text=Image+Not+Available'
}

// Lifecycle hooks
onMounted(() => {
  startSlideShow()
})

onUnmounted(() => {
  stopSlideShow()
})
</script>

<style scoped>
/* Additional styles if needed */
.transition-opacity {
  transition-property: opacity;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 1000ms;
}
</style>