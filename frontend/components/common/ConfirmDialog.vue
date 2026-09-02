<template>
  <div
    v-if="request"
    class="fixed inset-0 z-[240] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
    @click.self="resolveConfirm(false)"
  >
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-sm w-full p-6">
      <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ request.title || 'Please confirm' }}</h3>
      <p class="text-sm text-gray-600 dark:text-gray-300 mb-6">{{ request.message }}</p>
      <div class="flex justify-end gap-3">
        <button
          @click="resolveConfirm(false)"
          class="px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
        >
          {{ request.cancelText || 'Cancel' }}
        </button>
        <button
          @click="resolveConfirm(true)"
          class="px-4 py-2 rounded-lg text-white transition-colors"
          :class="request.danger ? 'bg-red-600 hover:bg-red-700' : 'bg-blue-600 hover:bg-blue-700'"
        >
          {{ request.confirmText || 'Confirm' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
const { request, resolveConfirm } = useConfirmDialog()
</script>
