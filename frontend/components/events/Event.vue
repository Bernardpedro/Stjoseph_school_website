<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { PUBLIC_EVENT_TYPE_FILTERS } from '~/utils/eventTypes'

const {
  events,
  isLoading,
  error: fetchError,
  mediaUrl,
  fetchEvents,
  getCurrentEventImage,
  getImageCount,
  cycleEventImages,
} = useEvents()

const props = defineProps({
  initialSearchQuery: {
    type: String,
    default: ''
  }
});

const searchQuery = ref('');
const selectedEventType = ref('all');
const selectedMonth = ref('all');
const showPastEvents = ref(false);

watch(() => props.initialSearchQuery, (newQuery) => {
  if (newQuery) {
    searchQuery.value = newQuery;
  }
}, { immediate: true });

const currentSlide = ref(0);
const slideInterval = ref(null);
const featuredEvents = ref([]);
const eventTypes = ref([...PUBLIC_EVENT_TYPE_FILTERS]);

const months = ref([
  { value: 'all', label: 'All Months' },
  { value: '01', label: 'January' },
  { value: '02', label: 'February' },
  { value: '03', label: 'March' },
  { value: '04', label: 'April' },
  { value: '05', label: 'May' },
  { value: '06', label: 'June' },
  { value: '07', label: 'July' },
  { value: '08', label: 'August' },
  { value: '09', label: 'September' },
  { value: '10', label: 'October' },
  { value: '11', label: 'November' },
  { value: '12', label: 'December' }
]);

const loadEvents = async (silent = false) => {
  try {
    await fetchEvents({ silent });
    featuredEvents.value = events.value
      .filter(event => event.status === 'upcoming')
      .slice(0, 5);
  } catch (error) {
    console.error('Error fetching events:', error);
  }
};

// Slider functions
const nextSlide = () => {
  currentSlide.value = (currentSlide.value + 1) % featuredEvents.value.length;
};

const prevSlide = () => {
  currentSlide.value = currentSlide.value === 0 ? featuredEvents.value.length - 1 : currentSlide.value - 1;
};

const goToSlide = (index) => {
  currentSlide.value = index;
};

const startSlideShow = () => {
  slideInterval.value = setInterval(nextSlide, 5000);
};

const stopSlideShow = () => {
  if (slideInterval.value) {
    clearInterval(slideInterval.value);
    slideInterval.value = null;
  }
};

// onMounted and onUnmounted
onContentChange(['events'], () => {
  loadEvents(true)
})

onMounted(async () => {
  await loadEvents();
  startSlideShow();
  
  const imageCycleInterval = setInterval(() => cycleEventImages(), 3000);
  
  onUnmounted(() => {
    stopSlideShow();
    clearInterval(imageCycleInterval);
  });
});

// Computed filtered events
const filteredEvents = computed(() => {
  let filtered = events.value;

  if (!showPastEvents.value) {
    filtered = filtered.filter(event => event.status === 'upcoming');
  }

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    filtered = filtered.filter(event =>
      event.title.toLowerCase().includes(query) ||
      event.description.toLowerCase().includes(query) ||
      event.location.toLowerCase().includes(query)
    );
  }

  if (selectedEventType.value !== 'all') {
    filtered = filtered.filter(event => event.type === selectedEventType.value);
  }

  if (selectedMonth.value !== 'all') {
    filtered = filtered.filter(event => {
      const eventMonth = event.date.split('-')[1];
      return eventMonth === selectedMonth.value;
    });
  }

  return filtered.sort((a, b) => {
    if (a.status !== b.status) {
      return a.status === 'upcoming' ? -1 : 1;
    }
    return new Date(b.date) - new Date(a.date);
  });
});

const PAGE_SIZE = 9
const visibleCount = ref(PAGE_SIZE)

const visibleEvents = computed(() => filteredEvents.value.slice(0, visibleCount.value))
const hasMoreEvents = computed(() => visibleCount.value < filteredEvents.value.length)

const loadMoreEvents = () => {
  visibleCount.value += PAGE_SIZE
}

watch([searchQuery, selectedEventType, selectedMonth, showPastEvents], () => {
  visibleCount.value = PAGE_SIZE
})

// Helper functions
const formatDate = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};

const formatTime = (timeString) => {
  if (!timeString) return '';
  const [hours, minutes] = timeString.split(':');
  const time = new Date();
  time.setHours(parseInt(hours), parseInt(minutes));
  return time.toLocaleTimeString('en-US', {
    hour: 'numeric',
    minute: '2-digit',
    hour12: true
  });
};

const getEventTypeColor = (type) => {
  const colors = {
    academic: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
    sports: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
    cultural: 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
    meeting: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
    exam: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
    religious: 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-300',
    wibabara: 'bg-pink-100 text-pink-800 dark:bg-pink-900 dark:text-pink-300',
    community: 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300',
    trip: 'bg-teal-100 text-teal-800 dark:bg-teal-900 dark:text-teal-300',
    indakomwa: 'bg-cyan-100 text-cyan-800 dark:bg-cyan-900 dark:text-cyan-300',
    technical: 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300',
    fashion: 'bg-lime-100 text-lime-800 dark:bg-lime-900 dark:text-lime-300',
    basketball: 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-300',
    gisenyi: 'bg-fuchsia-100 text-fuchsia-800 dark:bg-fuchsia-900 dark:text-fuchsia-300',
    'germany-mayors': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
    'bishop-visit': 'bg-rose-100 text-rose-800 dark:bg-rose-900 dark:text-rose-300',
    'andrea-family-visit': 'bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-300',
    'dining-room': 'bg-sky-100 text-sky-800 dark:bg-sky-900 dark:text-sky-300',
    'legacy-of-excellence': 'bg-violet-100 text-violet-800 dark:bg-violet-900 dark:text-violet-300',
    'completed-dining-room': 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-300',
  };
  return colors[type] || colors.academic;
};

const getEventIcon = (type) => {
  const icons = {
    academic: '📚',
    sports: '⚽',
    cultural: '🎭',
    meeting: '👥',
    exam: '📝',
    religious: '⛪',
    wibabara: '🎉',
    technical: '🔧',
    community: '🤝',
    indakomwa: '🎉',
    gisenyi: '🏞️',
    trip: '🚍',
    fashion: '👗',
    visit: '🏛️',
    basketball: '🏀',
    'germany-mayors': '🇩🇪',
    'bishop-visit': '⛪',
    'andrea-family-visit': '👨‍👩‍👧‍👦',
    'dining-room': '🍽️',
    'legacy-of-excellence': '🏆',
    'completed-dining-room': '🍽️',
  };
  return icons[type] || '📅';
};

const clearFilters = () => {
  searchQuery.value = '';
  selectedEventType.value = 'all';
  selectedMonth.value = 'all';
  showPastEvents.value = false;
};

const handleImageError = (event) => {
  console.error('Image failed to load:', event.target.src);
  event.target.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="400" height="300"%3E%3Crect fill="%23ddd" width="400" height="300"/%3E%3Ctext fill="%23999" x="50%25" y="50%25" text-anchor="middle" dy=".3em"%3EImage not available%3C/text%3E%3C/svg%3E';
  event.target.onerror = null;
};
</script>

<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Loading Indicator -->
    <div v-if="isLoading" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white p-6 rounded-lg shadow-lg">
        <div class="flex items-center">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
          <span class="ml-4 text-lg">{{ $t('events.loading') }}</span>
        </div>
      </div>
    </div>

    <!-- Featured Events Slider -->
    <div v-if="featuredEvents.length > 0" class="max-w-4xl mx-auto px-4 sm:px-0 lg:px-0 py-8">
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden mb-8">
        <div class="relative h-96 md:h-[500px]" @mouseenter="stopSlideShow" @mouseleave="startSlideShow">
          <!-- Slides -->
          <div
            v-for="(event, index) in featuredEvents"
            :key="event.id"
            class="absolute inset-0 transition-opacity duration-1000"
            :class="{ 'opacity-100': index === currentSlide, 'opacity-0': index !== currentSlide }"
          >
            <div class="relative h-full">
              <img
                :src="cldOptimize(mediaUrl(getCurrentEventImage(event)), 1200)"
                :alt="event.title"
                class="w-full h-full object-cover"
                loading="lazy"
                @error="handleImageError"
              />
              <div class="absolute inset-0 bg-black bg-opacity-40"></div>
              <div class="absolute inset-0 flex items-center justify-center">
                <div class="text-center text-white px-4 max-w-4xl">
                  <h2 class="text-3xl md:text-4xl font-bold mb-4">{{ event.title }}</h2>
                  <p class="text-lg md:text-xl mb-6 opacity-90">{{ event.description }}</p>
                  <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                    <div class="flex items-center text-lg">
                      <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                      </svg>
                      {{ formatDate(event.date) }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Navigation arrows -->
          <button
            v-if="featuredEvents.length > 1"
            @click="prevSlide"
            class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 hover:bg-opacity-75 text-white p-2 rounded-full transition-colors duration-200"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
          </button>
          <button
            v-if="featuredEvents.length > 1"
            @click="nextSlide"
            class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 hover:bg-opacity-75 text-white p-2 rounded-full transition-colors duration-200"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
          </button>

          <!-- Dots indicator -->
          <div v-if="featuredEvents.length > 1" class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2">
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

    <!-- Filters Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
          <!-- Search Input -->
          <div>
            <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              {{ $t('events.searchEvents') }}
            </label>
            <input
              id="search"
              v-model="searchQuery"
              type="text"
              :placeholder="$t('events.searchPlaceholder')"
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400"
            />
          </div>

          <!-- Event Type Filter -->
          <div>
            <label for="eventType" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              {{ $t('events.eventType') }}
            </label>
            <select
              id="eventType"
              v-model="selectedEventType"
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
            >
              <option v-for="type in eventTypes" :key="type.value" :value="type.value">
                {{ type.label }}
              </option>
            </select>
          </div>

          <!-- Month Filter -->
          <div>
            <label for="month" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              {{ $t('events.month') }}
            </label>
            <select
              id="month"
              v-model="selectedMonth"
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
            >
              <option v-for="month in months" :key="month.value" :value="month.value">
                {{ month.label }}
              </option>
            </select>
          </div>

          <!-- Show Past Events Toggle -->
          <div class="flex items-end">
            <label class="flex items-center">
              <input
                v-model="showPastEvents"
                type="checkbox"
                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
              />
              <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ $t('events.showPastEvents') }}</span>
            </label>
          </div>
        </div>

        <!-- Clear Filters Button -->
        <div class="flex justify-between items-center">
          <button
            @click="clearFilters"
            class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-md transition-colors duration-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
          >
            {{ $t('events.clearFilters') }}
          </button>
          <span class="text-sm text-gray-500 dark:text-gray-400">
            {{ $t('events.showingCount', { visible: visibleEvents.length, total: filteredEvents.length }) }}
          </span>
        </div>
      </div>

      <!-- Fetch Error -->
      <div v-if="fetchError" class="text-center py-12">
        <div class="max-w-md mx-auto">
          <svg class="mx-auto h-12 w-12 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
          </svg>
          <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">{{ $t('events.loadError') }}</h3>
          <p class="mt-2 text-gray-500 dark:text-gray-400">{{ fetchError }}</p>
          <button
            @click="loadEvents()"
            class="mt-4 px-4 py-2 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 transition-colors duration-200"
          >
            {{ $t('events.retry') }}
          </button>
        </div>
      </div>

      <!-- Events Grid -->
      <div v-else-if="filteredEvents.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div 
          v-for="event in visibleEvents" 
          :key="event.id"
          class="bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200 overflow-hidden border border-gray-200 dark:border-gray-700"
        >
          <NuxtLink 
            :to="`/${event.id}`" 
            class="block"
          >
            <!-- Event Image -->
            <div class="relative h-48 overflow-hidden">
              <img
                :src="cldOptimize(mediaUrl(getCurrentEventImage(event)), 500)"
                :alt="event.title"
                class="w-full h-full object-cover transition-transform duration-300 hover:scale-105"
                loading="lazy"
                @error="handleImageError"
              />
              
              <!-- Image Counter -->
              <div v-if="getImageCount(event)" class="absolute bottom-2 right-2 bg-black bg-opacity-50 text-white text-xs px-2 py-1 rounded">
                {{ getImageCount(event) }}
              </div>

              <!-- Event Type Badge -->
              <div class="absolute top-4 left-4">
                <span
                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-white bg-opacity-90"
                  :class="getEventTypeColor(event.type)"
                >
                  {{ event.type_label || eventTypes.find(t => t.value === event.type)?.label || event.type }}
                </span>
              </div>
              
              <!-- Past Event Badge -->
              <div v-if="event.status === 'past'" class="absolute top-4 right-4">
                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-900 bg-opacity-75 text-white">
                  {{ $t('events.pastEvent') }}
                </span>
              </div>
            </div>

            <!-- Event Content -->
            <div class="p-6">
              <div class="flex items-center space-x-2 mb-3">
                <span class="text-2xl">{{ getEventIcon(event.type) }}</span>
              </div>

              <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">
                {{ event.title }}
              </h3>

              <p class="text-gray-600 dark:text-gray-300 mb-4 line-clamp-3">
                {{ event.description }}
              </p>

              <!-- Event Details -->
              <div class="space-y-2">
                <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                  </svg>
                  {{ formatDate(event.date) }}
                </div>

                <div v-if="event.time" class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  {{ formatTime(event.time) }}
                </div>

                <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                  </svg>
                  {{ event.location }}
                </div>

                <div v-if="event.organizer" class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                  </svg>
                  {{ $t('events.organizedBy') }} {{ event.organizer }}
                </div>
              </div>
            </div>

            <!-- Event Footer -->
            <div class="px-6 py-3 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600">
              <div class="flex justify-between items-center">
                <span class="text-xs text-gray-500 dark:text-gray-400">
                  {{ event.status === 'upcoming' ? $t('events.upcomingEvent') : $t('events.pastEvent') }}
                </span>
                <span class="text-xs text-blue-600 dark:text-blue-400 font-medium">
                  {{ $t('events.viewDetails') }} →
                </span>
              </div>
            </div>
          </NuxtLink>
          
          <!-- YouTube Link (outside of NuxtLink to prevent navigation conflict) -->
          <div v-if="event.youtubeLink" class="px-6 pb-3 -mt-2">
            <a
              :href="event.youtubeLink"
              target="_blank"
              rel="noopener noreferrer"
              class="text-sm font-medium text-red-600 hover:text-red-500 dark:text-red-400 dark:hover:text-red-300 transition-colors duration-200 flex items-center"
            >
              <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 24 24">
                <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
              </svg>
              {{ $t('events.watchOnYoutube') }}
            </a>
          </div>
        </div>
      </div>

      <div v-if="hasMoreEvents" class="flex justify-center mt-10 mb-4">
        <button
          type="button"
          class="px-8 py-3 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-sm transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900"
          @click="loadMoreEvents"
        >
          {{ $t('events.moreEvents') }}
        </button>
      </div>

      <!-- No Events Found -->
      <div v-else class="text-center py-12">
        <div class="max-w-md mx-auto">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
          </svg>
          <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">{{ $t('events.noEventsFound') }}</h3>
          <p class="mt-2 text-gray-500 dark:text-gray-400">
            {{ $t('events.noMatch') }}
          </p>
          <button
            @click="clearFilters"
            class="mt-4 px-4 py-2 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 transition-colors duration-200"
          >
            {{ $t('events.clearAllFilters') }}
          </button>
        </div>
      </div>
    </div>

    <!-- Quick Stats Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ $t('events.statistics') }}</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
          <div class="text-center">
            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
              {{ events.filter(e => e.status === 'upcoming').length }}
            </div>
            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $t('events.upcomingEvents') }}</div>
          </div>
          <div class="text-center">
            <div class="text-2xl font-bold text-green-600 dark:text-green-400">
              {{ events.filter(e => e.status === 'past').length }}
            </div>
            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $t('events.pastEvents') }}</div>
          </div>
          <div class="text-center">
            <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
              {{ new Set(events.map(e => e.type)).size }}
            </div>
            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $t('events.eventTypes') }}</div>
          </div>
          <div class="text-center">
            <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">
              {{ events.length }}
            </div>
            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $t('events.totalEvents') }}</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Additional YouTube Content Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">{{ $t('events.followUs') }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- YouTube Card -->
          <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-lg p-6 text-white">
            <div class="flex items-center mb-4">
              <svg class="w-8 h-8 mr-3" fill="currentColor" viewBox="0 0 24 24">
                <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
              </svg>
              <h3 class="text-xl font-bold">YouTube</h3>
            </div>
            <p class="mb-4 text-red-100">{{ $t('events.youtubeDesc') }}</p>
            <a
              href="https://www.youtube.com/@saintjosephtssNzuki?sub_confirmation=1"
              target="_blank"
              rel="noopener noreferrer"
              class="inline-flex items-center px-4 py-2 bg-white text-red-600 font-medium rounded-md hover:bg-gray-100 transition-colors duration-200"
            >
              {{ $t('events.subscribeNow') }}
            </a>
          </div>

          <!-- X (Twitter) Card -->
          <div class="bg-black rounded-lg p-6 text-white">
            <div class="flex items-center mb-4">
              <svg class="w-8 h-8 mr-3" fill="currentColor" viewBox="0 0 24 24">
                <path d="M20.447 3.181c-.793.352-1.644.59-2.538.698a4.44 4.44 0 0 0 1.947-2.453 8.877 8.877 0 0 1-2.81 1.074A4.424 4.424 0 0 0 12.001 2c-2.447 0-4.432 1.986-4.432 4.432 0 .348.04.687.116 1.012A12.563 12.563 0 0 1 3.161 3.392a4.425 4.425 0 0 0 1.369 5.91 4.403 4.403 0 0 1-2.008-.555v.056c0 2.176 1.548 3.992 3.602 4.403a4.436 4.436 0 0 1-2.001.076c.564 1.763 2.2 3.046 4.139 3.082a8.879 8.879 0 0 1-5.495 1.893c-.357 0-.71-.021-1.057-.062a12.538 12.538 0 0 0 6.794 1.992c8.15 0 12.604-6.75 12.604-12.604 0-.192-.005-.384-.013-.575a9.02 9.02 0 0 0 2.214-2.292z"/>
              </svg>
              <h3 class="text-xl font-bold">X (Twitter)</h3>
            </div>
            <p class="mb-4 text-gray-300">{{ $t('events.twitterDesc') }}</p>
            <a
              href="https://x.com/@tssnzuki"
              target="_blank"
              rel="noopener noreferrer"
              class="inline-flex items-center px-4 py-2 bg-white text-black font-medium rounded-md hover:bg-gray-100 transition-colors duration-200"
            >
              {{ $t('events.followUsBtn') }}
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

::-webkit-scrollbar {
  width: 6px;
}

::-webkit-scrollbar-track {
  background: #f1f1f1;
}

::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}

* {
  transition: color 0.2s ease, background-color 0.2s ease, border-color 0.2s ease;
}

input:focus,
select:focus,
button:focus {
  outline: 2px solid #3b82f6;
  outline-offset: 2px;
}

.bg-white:hover {
  transform: translateY(-1px);
}

img {
  transition: transform 0.3s ease;
}

@media (prefers-reduced-motion: reduce) {
  * {
    transition: none !important;
  }
  
  img {
    transition: none !important;
  }
}
</style>