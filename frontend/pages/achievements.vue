<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Page header -->
    <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
      <div class="max-w-6xl mx-auto px-4 py-10 text-center">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white">Our Achievements</h1>
        <p class="mt-2 text-gray-600 dark:text-gray-300">Celebrating the results and recognition earned by Saint Joseph TSS Nzuki.</p>
      </div>
    </div>

    <div class="max-w-6xl mx-auto px-4 py-10">
      <!-- Spotlight: 2025 national ranking -->
      <div class="mb-14 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="relative h-64 sm:h-96">
          <img
            :src="cldOptimize('https://res.cloudinary.com/dck2vzccq/image/upload/v1756885559/result2025_d6d9ey.png', 1200)"
            alt="Two Saint Joseph TSS Nzuki students rank in the national top 10 for 2025"
            class="w-full h-full object-cover"
            loading="lazy"
          />
        </div>

        <div class="p-6 sm:p-8 text-center border-b border-gray-100 dark:border-gray-700">
          <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Celebrating Success: Our Students Among Rwanda's Best</h2>
          <p class="mt-4 text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
            Saint Joseph TSS Nzuki is proud to announce a historic achievement! Two of our students, Karangwa Irakoze Roben and Aime Senga Prosper,
            from the Automobile Technology combination (AUT) in S6, have been ranked among the top 10 students in the nationwide Transport and
            Logistics results for the year 2025. This incredible accomplishment is a testament to their hard work, dedication, and the
            high-quality education provided by our school.
          </p>
        </div>

        <div class="px-6 sm:px-8 py-4 bg-blue-50 dark:bg-blue-950/40 text-center">
          <p class="font-semibold text-blue-700 dark:text-blue-300">
            A Legacy of Excellence: Two Students from Saint Joseph TSS Nzuki Rank in Top 10 Nationwide
          </p>
        </div>

        <div class="p-6 sm:p-8">
          <div class="text-center mb-8">
            <h3 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white mb-2">Outstanding Academic Performance</h3>
            <p class="text-gray-600 dark:text-gray-300">We extend our heartfelt congratulations to our exceptional students who have made history:</p>
          </div>

          <div class="grid md:grid-cols-2 gap-6">
            <div class="bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-gray-700 dark:to-gray-600 rounded-lg p-6 border-l-4 border-blue-500">
              <div class="flex items-center mb-4">
                <div class="bg-blue-500 text-white rounded-full w-12 h-12 flex items-center justify-center text-xl font-bold shrink-0">5th</div>
                <div class="ml-4">
                  <h4 class="text-xl font-bold text-gray-900 dark:text-white">KARANGWA IRAKOZE Roben</h4>
                  <p class="text-gray-600 dark:text-gray-300">Automobile Technology (AUT) - S6</p>
                </div>
              </div>
              <div class="space-y-2">
                <div class="flex justify-between items-center">
                  <span class="text-gray-700 dark:text-gray-300">National Ranking:</span>
                  <span class="font-bold text-blue-600 dark:text-blue-400">5th Position</span>
                </div>
                <div class="flex justify-between items-center">
                  <span class="text-gray-700 dark:text-gray-300">Weighted Percentage:</span>
                  <span class="font-bold text-green-600 dark:text-green-400">90.21%</span>
                </div>
              </div>
            </div>

            <div class="bg-gradient-to-br from-green-50 to-emerald-100 dark:from-gray-700 dark:to-gray-600 rounded-lg p-6 border-l-4 border-green-500">
              <div class="flex items-center mb-4">
                <div class="bg-green-500 text-white rounded-full w-12 h-12 flex items-center justify-center text-xl font-bold shrink-0">8th</div>
                <div class="ml-4">
                  <h4 class="text-xl font-bold text-gray-900 dark:text-white">Aime SENGA Prosper</h4>
                  <p class="text-gray-600 dark:text-gray-300">Automobile Technology (AUT) - S6</p>
                </div>
              </div>
              <div class="space-y-2">
                <div class="flex justify-between items-center">
                  <span class="text-gray-700 dark:text-gray-300">National Ranking:</span>
                  <span class="font-bold text-green-600 dark:text-green-400">8th Position</span>
                </div>
                <div class="flex justify-between items-center">
                  <span class="text-gray-700 dark:text-gray-300">Weighted Percentage:</span>
                  <span class="font-bold text-green-600 dark:text-green-400">89.99%</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Loading -->
      <div v-if="isLoading" class="flex items-center justify-center py-16">
        <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-blue-600"></div>
      </div>

      <!-- Empty state -->
      <div
        v-else-if="achievementYears.length === 0"
        class="text-center py-16 bg-white dark:bg-gray-800 rounded-xl border border-dashed border-gray-300 dark:border-gray-700"
      >
        <p class="text-gray-500 dark:text-gray-400">No achievements published yet.</p>
      </div>

      <!-- Year-grouped grids -->
      <div v-else class="space-y-12">
        <div v-for="group in achievementYears" :key="group.year">
          <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-5 flex items-center gap-2">
            <span class="w-1.5 h-6 bg-blue-600 rounded-full" aria-hidden="true" />
            {{ group.year }}
          </h2>
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            <div
              v-for="item in group.items"
              :key="item.id"
              class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-5"
            >
              <div class="flex items-start gap-3">
                <span class="shrink-0 w-11 h-11 rounded-xl flex items-center justify-center text-white bg-blue-600">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="iconFor(item.category)" />
                  </svg>
                </span>
                <div class="min-w-0">
                  <span v-if="item.category" class="inline-block px-2 py-0.5 rounded-full text-xs font-medium bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 mb-1.5">
                    {{ item.category }}
                  </span>
                  <h3 class="font-semibold text-gray-900 dark:text-white">{{ item.title }}</h3>
                  <p v-if="item.rank" class="text-sm text-gray-500 dark:text-gray-400 mt-1">Rank: {{ item.rank }}</p>
                  <p v-if="item.score !== null" class="text-sm font-semibold text-green-600 dark:text-green-400 mt-1">
                    {{ Number(item.score).toFixed(2) }}%
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const { apiFetch } = useApi()

definePageMeta({
  layout: 'default',
})

usePageSeo({
  title: 'Achievements',
  description: 'Academic achievements, rankings, and recognition earned by Saint Joseph Technical Secondary School Nzuki.',
})

const achievementYears = ref([])
const isLoading = ref(true)

const CATEGORY_ICONS = {
  'general statistics': 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
  'national exam results': 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
  'top student': 'M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422A12.083 12.083 0 0121 15.5c0 1.657-4.03 3-9 3s-9-1.343-9-3a12.083 12.083 0 012.84-4.922L12 14z',
  ranking: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
  'spiritual life': 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z',
}
const DEFAULT_ICON = 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'

const iconFor = (category) => {
  const key = String(category || '').trim().toLowerCase()
  return CATEGORY_ICONS[key] || DEFAULT_ICON
}

const loadAchievements = async () => {
  try {
    isLoading.value = true
    const res = await apiFetch('/api/achievements')
    const rows = res?.data || []
    const byYear = new Map()
    rows.forEach((item) => {
      const year = item.academic_year || 'Other'
      if (!byYear.has(year)) byYear.set(year, [])
      byYear.get(year).push(item)
    })
    achievementYears.value = Array.from(byYear.entries())
      .sort((a, b) => b[0].localeCompare(a[0]))
      .map(([year, items]) => ({ year, items }))
  } catch (error) {
    console.error('Error fetching achievements:', error)
  } finally {
    isLoading.value = false
  }
}

onContentChange(['achievements'], () => {
  loadAchievements()
})

onMounted(() => {
  loadAchievements()
})
</script>
