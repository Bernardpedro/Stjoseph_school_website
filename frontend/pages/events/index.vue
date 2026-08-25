<template>
  <div class="bg-white dark:bg-gray-900">
    <div class="bg-white dark:bg-gray-900">
      <div class="mb-[0px]">
      <!-- <HeroSection/> -->
      <eventsEvent :initialSearchQuery="searchQuery"/>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';

const route = useRoute();
const searchQuery = ref('');

// Watch for changes in the route query parameters
watch(() => route.query.search, (newSearchQuery) => {
  if (newSearchQuery) {
    searchQuery.value = newSearchQuery;
  } else {
    searchQuery.value = '';
  }
}, { immediate: true });

definePageMeta({
  layout:'default'
});

</script>
