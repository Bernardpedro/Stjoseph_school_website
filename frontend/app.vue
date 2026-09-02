<template>
   <div class="min-h-screen bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100">
    <NuxtLayout>
      <NuxtPage />
    </NuxtLayout>
  </div>
</template>


<script setup>
import { onMounted } from 'vue';
import { useUserStore } from './stores/user';

const userStore = useUserStore()
const { initTheme } = useTheme()
const { locale } = useI18n()

useHead({
  htmlAttrs: {
    lang: locale
  }
})

onMounted(() => {
  userStore.hydrate();
  initTheme()
});
</script>