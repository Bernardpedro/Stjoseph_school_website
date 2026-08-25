<template>
  <div
    class="min-h-screen bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100"
    :class="{ 'page-preview-shell': isPreview }"
  >
    <template v-if="!isPreview">
      <HeroBanner />
      <Header />
    </template>

    <div>
      <CommonAnnouncementAds v-if="!isPreview" />
      <slot />
      <Footer v-if="!isPreview" />
    </div>

    <CommonLiveToast v-if="!isPreview" />
  </div>
</template>

<script setup>
import HeroBanner from '~/components/hero/HeroBanner.vue'
import Header from '~/components/layout/Header.vue'
import Footer from '~/components/layout/Footer.vue'

const route = useRoute()
const isPreview = computed(() => String(route.query.preview ?? '') === '1')
const { initTheme } = useTheme()

onMounted(() => initTheme())

watch(isPreview, (preview) => {
  if (!import.meta.client) return
  document.documentElement.classList.toggle('page-preview', preview)
}, { immediate: true })

onUnmounted(() => {
  if (import.meta.client) {
    document.documentElement.classList.remove('page-preview')
  }
})
</script>

<style>
html.page-preview,
html.page-preview body {
  overflow: hidden !important;
  height: 800px;
}
.page-preview-shell {
  min-height: 800px !important;
}
</style>
