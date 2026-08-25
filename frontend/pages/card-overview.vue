<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900 p-4 md:p-8">
    <CardOverView 
      v-if="eventData"
      :card-type="cardType"
      :card-id="cardId"
      :event-data="eventData"
    />
    <div v-else class="flex items-center justify-center h-64">
      <div class="text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto"></div>
        <p class="mt-4 text-gray-600 dark:text-gray-300">Loading event details...</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import CardOverView from '~/components/CardOverView.vue';

definePageMeta({
  layout: 'default'
});

const route = useRoute();
const { fetchEventById } = useEvents()
const cardType = ref('');
const cardId = ref('');
const eventData = ref(null);

const loadEventById = async (eventId) => {
  try {
    return await fetchEventById(eventId)
  } catch (error) {
    console.error('Error fetching event:', error);
    return null;
  }
};

// FIXED: Properly format the media array
const fetchEventData = async () => {



  if (!cardId.value) {
    console.warn('No cardId provided');
    return;
  }

  try {
    const event = await fetchEventById(cardId.value);



    // Save to local storage only for events
    if (cardType.value === 'event') {
      localStorage.setItem(`event_${cardId.value}`, JSON.stringify(event));
    }

    // FIXED: Transform images array to media objects
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

    // Format the data to match what CardOverView expects
    eventData.value = {
      ...event,
      title: event.title || 'No Title',
      description: event.description || 'No description available.',
      category: event.type || 'Event',
      date: event.date || '',
      time: event.time || '',
      location: event.location || 'Location not specified',
      media: mediaArray  // FIXED: Use the transformed media array
    };



  } catch (error) {
    console.error('Error loading event:', error);
    eventData.value = {
      title: 'Error',
      description: 'Failed to load event details. Please try again later.',
      category: 'Error',
      date: '',
      time: '',
      location: '',
      media: []
    };
  }
};

// Watch for changes in route query parameters
const loadEventFromRoute = () => {
  const { type, id } = route.query;

  
  if (type && id) {
    cardType.value = type;
    cardId.value = id;
    fetchEventData();
  }
};

// Watch for route changes
watch(() => route.query, loadEventFromRoute, { immediate: true });

// Watch for changes in cardId and cardType
watch([cardType, cardId], () => {
  if (cardType.value && cardId.value) {
    fetchEventData();
  }
});
</script>