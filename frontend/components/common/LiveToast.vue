<template>
  <transition
    enter-active-class="transition duration-200"
    enter-from-class="opacity-0 translate-y-2"
    enter-to-class="opacity-100 translate-y-0"
    leave-active-class="transition duration-150"
    leave-from-class="opacity-100"
    leave-to-class="opacity-0"
  >
    <div
      v-if="toast"
      class="fixed bottom-4 right-4 z-[220] max-w-sm rounded-lg bg-gray-900 text-white shadow-lg px-4 py-3"
    >
      <p class="text-sm font-semibold">{{ toast.title }}</p>
      <p class="text-xs text-gray-300 mt-0.5">The page updated automatically.</p>
    </div>
  </transition>
</template>

<script setup>
const { toast, start } = useLiveSync()

onMounted(() => start())
</script>
