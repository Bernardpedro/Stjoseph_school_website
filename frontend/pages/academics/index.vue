<template>
  <div class="bg-white dark:bg-gray-900">
    <div class="bg-white dark:bg-gray-900">
      <div class="mb-[0px]">
        <HeroSuccessInExams/>
        <Academic :searchQuery="searchQuery" />
      </div>
    </div>
  </div>
</template>

<script setup>
import Academic from '~/components/academic/Academic.vue'

definePageMeta({
  layout: 'default',
})

usePageSeo({
  title: 'Academics',
  description: 'Academic programs and combinations offered at Saint Joseph Technical Secondary School Nzuki.',
})

const route = useRoute()
const searchQuery = computed(() => {
  const q = route.query.search
  return typeof q === 'string' ? q : ''
})
</script>
