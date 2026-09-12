<template>
  <div>
    <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 dark:text-white mb-4 sm:mb-6 px-2 text-center">
      {{ sectionTitle }}
    </h2>

    <div class="flex justify-center mb-6 sm:mb-8 mt-0">
      <div class="grid grid-cols-1 place-items-center gap-6 sm:gap-8">
        <div class="group relative bg-white dark:bg-gray-900 rounded-xl sm:rounded-2xl shadow-lg hover:shadow-xl sm:hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-1 sm:hover:-translate-y-2 overflow-hidden w-full max-w-3xl">
          <div class="absolute inset-0 bg-gradient-to-br from-green-400/20 to-blue-500/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
          <div class="relative p-0 sm:p-6 lg:p-4">
            <NuxtLink to="/project" class="block">
              <div class="relative w-full h-48 sm:h-60 md:h-72 lg:h-80 rounded-lg sm:rounded-2xl mb-4 sm:mb-6 overflow-hidden bg-gradient-to-br from-blue-200/50 to-indigo-200/50 dark:from-blue-800/30 dark:to-indigo-800/30">
                <div class="flex items-center justify-center w-full h-full">
                  <img :src="cldOptimize(partnerImage, 500)" alt="Partner project" class="max-w-full max-h-full object-contain" loading="lazy" />
                </div>
              </div>

              <p class="text-sm sm:text-base text-gray-600 dark:text-gray-300 mb-4 sm:mb-6 leading-relaxed">
                {{ sectionDescription }}
              </p>
            </NuxtLink>

            <div
              v-if="isSearchActive && searchResults && searchResults.partners"
              class="mt-3 sm:mt-4 p-2 sm:p-3 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded-lg text-xs sm:text-sm"
            >
              {{ $t('partners.searchFound') }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
const { apiFetch } = useApi()
const { t } = useI18n()

defineProps({
  isSearchActive: {
    type: Boolean,
    default: false,
  },
  searchResults: {
    type: Object,
    default: () => ({}),
  },
})

const partnerImage =
  'https://res.cloudinary.com/dck2vzccq/image/upload/v1752738861/rlp_ruanda_logo_jt9mcf.png'

const settings = reactive({
  title: '',
  description: '',
})

const sectionTitle = computed(() => settings.title || t('partners.title'))
const sectionDescription = computed(() => settings.description || t('partners.description'))

const load = async () => {
  try {
    const settingsRes = await apiFetch('/api/projects/settings')
    Object.assign(settings, settingsRes?.data || {})
  } catch {
    // Keep the original static picture and default copy
  }
}

onContentChange(['settings', 'projects'], () => {
  load()
})

onMounted(load)
</script>
