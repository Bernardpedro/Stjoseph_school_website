<template>
  <div class="fixed top-4 right-4 z-[230] flex flex-col gap-2 w-[calc(100%-2rem)] sm:w-auto sm:max-w-sm">
    <transition-group
      enter-active-class="transition duration-200"
      enter-from-class="opacity-0 -translate-y-2"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition duration-150"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-for="t in toasts"
        :key="t.id"
        class="rounded-lg shadow-lg px-4 py-3 text-sm font-medium text-white flex items-start gap-3"
        :class="{
          'bg-green-600': t.type === 'success',
          'bg-red-600': t.type === 'error',
          'bg-gray-900': t.type === 'info',
        }"
      >
        <span class="flex-1">{{ t.message }}</span>
        <button @click="remove(t.id)" class="opacity-70 hover:opacity-100 shrink-0" aria-label="Dismiss">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </transition-group>
  </div>
</template>

<script setup>
const { toasts, remove } = useAppToast()
</script>
