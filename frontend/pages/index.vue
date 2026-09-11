<template>
  <div class="bg-white dark:bg-gray-900">
    <div class="bg-white dark:bg-gray-900">
      <div class="mb-[200px] sm:mb-[10px] md:mb-[350px] lg:mb-[420px]">
      <HeroSection/>
      </div>
      <HeroSuccessInExams/>
      <ProjectsPartners
        :isSearchActive="isSearchActive"
        :searchResults="partnerSearchResults"
      />
      <SchoolDescription/>
      <Academic :searchQuery="searchQuery" />
      <SchoolCompound :searchQuery="searchQuery" />
      <!-- <EventsEvent :initialSearchQuery="searchQuery" /> -->
      <SchoolTestimonials/>
      <!-- <Contact/> -->
    </div>
  </div>
</template>

<script setup>
import Contact from '~/components/contact/Contact.vue'
import ProjectsPartners from '~/components/projects/Partners.vue'

definePageMeta({
  layout: 'default',
})

usePageSeo({
  description: 'Saint Joseph Technical Secondary School Nzuki - quality technical and vocational education in Ruhango District, Southern Province, Rwanda. Explore our academic programs, admissions, and school life.',
})

const route = useRoute()
const searchQuery = computed(() => {
  const q = route.query.search
  return typeof q === 'string' ? q : ''
})
const isSearchActive = computed(() => searchQuery.value.trim() !== '')
const partnerSearchResults = computed(() => {
  if (!isSearchActive.value) return {}
  const q = searchQuery.value.toLowerCase().trim()
  const keywords = ['partner', 'partners', 'rheinland', 'project', 'projects', 'pfalz']
  return {
    partners: keywords.some((k) => k.includes(q) || q.includes(k)),
  }
})

watch(
  searchQuery,
  (q) => {
    if (!q.trim()) return
    nextTick(() => {
      const el =
        document.querySelector('.search-highlight') ||
        document.getElementById('nationWide') ||
        null
      if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center' })
    })
  },
  { immediate: true }
)
</script>
