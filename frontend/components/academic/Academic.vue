<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-12 px-6 lg:px-20">
    
    <!-- Academic  -->
    <AcademicCombinations :searchQuery="searchQuery"/>

    <!-- Combinations -->
    <section id="academic-program" class="mb-16">
      <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 mb-8 text-center">
        {{ $t('academics.combinations') }}
      </h2>
      <div class="grid md:grid-cols-2 gap-8">
        <div
          v-for="program in combinations"
          :key="program.name"
          class="bg-white dark:bg-gray-800 shadow-lg rounded-2xl p-6 hover:shadow-2xl transition"
          :class="{ 'ring-2 ring-blue-400 search-highlight': matchesSearch(program.name, program.focus, program.facilities, program.careers) }"
        >
          <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">{{ program.name }}</h3>
          <ul class="list-disc pl-6 text-gray-700 dark:text-gray-300 space-y-1">
            <li><strong>{{ $t('academics.focus') }}:</strong> {{ program.focus }}</li>
            <li><strong>{{ $t('academics.facilities') }}:</strong> {{ program.facilities }}</li>
            <li><strong>{{ $t('academics.careers') }}:</strong> {{ program.careers }}</li>
          </ul>
        </div>
      </div>
    </section>

    <!-- Short Courses -->
    <section class="mb-16">
      <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 mb-8 text-center">
        {{ $t('academics.shortCourses') }}
      </h2>
      <div class="grid md:grid-cols-2 gap-8">
        <div
          v-for="course in shortCourses"
          :key="course.name"
          class="bg-white dark:bg-gray-800 shadow-lg rounded-2xl p-6 hover:shadow-2xl transition"
          :class="{ 'ring-2 ring-blue-400 search-highlight': matchesSearch(course.name, course.skills, course.facilities, course.careers) }"
        >
          <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">{{ course.name }}</h3>
          <ul class="list-disc pl-6 text-gray-700 dark:text-gray-300 space-y-1">
            <li><strong>{{ $t('academics.skills') }}:</strong> {{ course.skills }}</li>
            <li><strong>{{ $t('academics.facilities') }}:</strong> {{ course.facilities }}</li>
            <li><strong>{{ $t('academics.careers') }}:</strong> {{ course.careers }}</li>
          </ul>
        </div>
      </div>
    </section>

    <!-- School Requirements -->
    <SchoolRequirements />

    <!-- Achievements -->
    <section>
      <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 mb-8 text-center">
        {{ $t('academics.achievements') }}
      </h2>
      <p class="text-gray-700 dark:text-gray-300 max-w-3xl mx-auto leading-relaxed text-center">
        {{ $t('academics.achievementsText') }}
      </p>
    </section>
  </div>
</template>

<script setup lang="ts">
import AcademicCombinations from './AcademicCombinations.vue'

const { t } = useI18n()

const props = defineProps({
  searchQuery: {
    type: String,
    default: '',
  },
})

const searchQuery = computed(() => props.searchQuery || '')

const matchesSearch = (...parts: string[]) => {
  const q = searchQuery.value.trim().toLowerCase()
  if (!q) return false
  return parts.some((p) => String(p || '').toLowerCase().includes(q) || q.includes(String(p || '').toLowerCase()))
}

const combinations = computed(() => [
  {
    name: t('academics.autoName'),
    focus: t('academics.autoFocus'),
    facilities: t('academics.autoFacilities'),
    careers: t('academics.autoCareers'),
  },
  {
    name: t('academics.buildName'),
    focus: t('academics.buildFocus'),
    facilities: t('academics.buildFacilities'),
    careers: t('academics.buildCareers'),
  },
])

const shortCourses = computed(() => [
  {
    name: t('academics.tailorName'),
    skills: t('academics.tailorSkills'),
    facilities: t('academics.tailorFacilities'),
    careers: t('academics.tailorCareers'),
  },
  {
    name: t('academics.carpName'),
    skills: t('academics.carpSkills'),
    facilities: t('academics.carpFacilities'),
    careers: t('academics.carpCareers'),
  },
])
</script>