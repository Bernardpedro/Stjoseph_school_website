<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-12 px-6 lg:px-20">
    
    <!-- Academic  -->
    <AcademicCombinations :searchQuery="searchQuery"/>

    <!-- School Requirements -->
    <SchoolRequirements />
  </div>
</template>

<script setup lang="ts">
import AcademicCombinations from './AcademicCombinations.vue'

const props = defineProps({
  searchQuery: {
    type: String,
    default: '',
  },
})

const searchQuery = computed(() => props.searchQuery || '')
</script>