<template>
  <div class="relative" :class="compact ? 'w-auto' : 'w-full'" ref="rootEl">
    <button
      v-if="compact"
      type="button"
      class="inline-flex w-8 h-8 items-center justify-center text-[#1D4ED8] dark:text-blue-400"
      :aria-label="$t('search.label')"
      :aria-expanded="expanded"
      @click="toggleCompact"
    >
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 20 20" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
      </svg>
    </button>

    <div
      v-if="!compact || expanded"
      :class="compact ? 'mobile-search-panel' : ''"
      :style="compact ? panelStyle : undefined"
    >
    <label :for="inputId" class="sr-only">
      {{ $t('search.label') }}
    </label>
    <div class="relative">
      <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
        <svg
          class="w-4 h-4 text-[#1D4ED8] dark:text-blue-400"
          aria-hidden="true"
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 20 20"
        >
          <path
            stroke="currentColor"
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"
          />
        </svg>
      </div>
      <input
        ref="inputEl"
        v-model="searchQuery"
        type="search"
        :id="inputId"
        autocomplete="off"
        inputmode="search"
        enterkeyhint="search"
        :class="compact
          ? 'header-search block w-full py-2.5 px-10 text-base font-medium text-[#1D4ED8] placeholder:text-blue-400 border-0 rounded-full bg-white ring-1 ring-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-600 dark:bg-gray-800 dark:ring-blue-900/60 dark:text-blue-300 dark:placeholder:text-blue-500'
          : 'header-search block w-full py-2 px-3 ps-9 text-sm font-medium text-[#1D4ED8] placeholder:text-blue-400 border-0 rounded-full bg-blue-50/80 ring-1 ring-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:bg-white dark:bg-blue-950/40 dark:ring-blue-900/60 dark:text-blue-300 dark:placeholder:text-blue-500 dark:focus:bg-gray-800 dark:focus:ring-blue-400'"
        :placeholder="$t('search.placeholder')"
        @focus="open = true"
        @keydown.enter.prevent="goFirstResult"
        @keydown.escape="closeCompact"
        @keydown.down.prevent="moveHighlight(1)"
        @keydown.up.prevent="moveHighlight(-1)"
      />
      <button
        v-if="compact"
        type="button"
        class="absolute inset-y-0 end-0 flex items-center pe-3 text-[#1D4ED8] dark:text-blue-400"
        :aria-label="$t('common.cancel')"
        @click="closeCompact"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>

    <div
      v-if="open && searchQuery.trim().length >= 2"
      class="mt-1.5 z-[100] max-h-[min(18rem,50dvh)] overflow-y-auto overflow-x-hidden rounded-xl border border-blue-100 bg-white shadow-xl shadow-blue-900/10 dark:border-blue-900 dark:bg-gray-800"
      :class="compact ? 'relative' : 'absolute left-0 right-0'"
    >
      <div v-if="loading" class="px-3 py-3 text-sm text-gray-500 dark:text-gray-400">
        {{ $t('search.searching') }}
      </div>

      <div v-else-if="!results.length" class="px-3 py-3 text-sm text-gray-500 dark:text-gray-400 break-words">
        {{ $t('search.noResults', { query: searchQuery.trim() }) }}
      </div>

      <ul v-else class="py-1">
        <li v-for="(item, index) in results" :key="item.id">
          <button
            type="button"
            class="w-full text-left px-3 py-2.5 hover:bg-blue-50 dark:hover:bg-blue-950/40 transition-colors"
            :class="{ 'bg-blue-50 dark:bg-gray-700': index === highlightIndex }"
            @mousedown.prevent="goToResult(item)"
            @mouseenter="highlightIndex = index"
          >
            <div class="flex items-start justify-between gap-2">
              <span class="text-sm font-medium text-[#1D4ED8] dark:text-blue-300 break-words">
                {{ item.title }}
              </span>
              <span class="shrink-0 text-[10px] uppercase tracking-wide text-blue-600 dark:text-blue-400 font-semibold mt-0.5">
                {{ item.category }}
              </span>
            </div>
            <p v-if="item.description" class="mt-0.5 text-xs text-gray-500 dark:text-gray-400 break-words">
              {{ item.description }}
            </p>
          </button>
        </li>
      </ul>

      <p v-if="error" class="px-3 py-2 text-[11px] text-amber-600 dark:text-amber-400 border-t border-gray-100 dark:border-gray-700 break-words">
        {{ error }}
      </p>
    </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, nextTick, onMounted, onUnmounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'

const props = defineProps({
  compact: { type: Boolean, default: false },
})

const router = useRouter()
const route = useRoute()
const { search, clear, results, loading, error } = useGlobalSearch()

const searchQuery = ref(typeof route.query.search === 'string' ? route.query.search : '')
const open = ref(false)
const expanded = ref(false)
const highlightIndex = ref(0)
const rootEl = ref(null)
const inputEl = ref(null)
const inputId = props.compact ? 'mobile-search' : 'default-search'
const panelStyle = ref({})
let searchTimeout = null

const placePanel = () => {
  if (!props.compact || !rootEl.value) return
  const nav = rootEl.value.closest('nav')
  const rect = (nav || rootEl.value).getBoundingClientRect()
  panelStyle.value = {
    top: `${Math.round(rect.bottom + 6)}px`,
  }
}

const close = () => {
  open.value = false
  highlightIndex.value = 0
}

const closeCompact = () => {
  close()
  if (props.compact) {
    expanded.value = false
    window.removeEventListener('scroll', placePanel, true)
    window.removeEventListener('resize', placePanel)
  }
}

const openCompact = async () => {
  expanded.value = true
  open.value = true
  await nextTick()
  placePanel()
  window.addEventListener('scroll', placePanel, true)
  window.addEventListener('resize', placePanel)
  inputEl.value?.focus()
}

const toggleCompact = () => {
  if (expanded.value) {
    closeCompact()
    return
  }
  openCompact()
}

const goToResult = async (item) => {
  if (!item?.path) return
  closeCompact()
  await router.push(item.path)
  searchQuery.value = ''
  clear()
}

const goFirstResult = () => {
  if (!results.value.length) {
    const q = searchQuery.value.trim()
    if (q) {
      router.push({ path: '/events', query: { search: q } })
      closeCompact()
    }
    return
  }
  const idx = Math.min(Math.max(highlightIndex.value, 0), results.value.length - 1)
  goToResult(results.value[idx])
}

const moveHighlight = (delta) => {
  if (!results.value.length) return
  open.value = true
  const len = results.value.length
  highlightIndex.value = (highlightIndex.value + delta + len) % len
}

watch(searchQuery, (value) => {
  clearTimeout(searchTimeout)
  highlightIndex.value = 0

  const q = value.trim()
  if (q.length < 2) {
    clear()
    open.value = false
    return
  }

  open.value = true
  searchTimeout = setTimeout(async () => {
    await search(q)
  }, 250)
})

const onDocClick = (e) => {
  if (rootEl.value && !rootEl.value.contains(e.target)) {
    closeCompact()
  }
}

onMounted(() => {
  document.addEventListener('click', onDocClick)
})

onUnmounted(() => {
  document.removeEventListener('click', onDocClick)
  window.removeEventListener('scroll', placePanel, true)
  window.removeEventListener('resize', placePanel)
  clearTimeout(searchTimeout)
})
</script>

<style scoped>
.header-search {
  transition: box-shadow 0.2s ease, transform 0.2s ease, background-color 0.2s ease;
}
.header-search:hover {
  box-shadow: 0 6px 16px -8px rgba(29, 78, 216, 0.5);
  transform: translateY(-1px);
}
.header-search:focus {
  box-shadow: 0 10px 20px -10px rgba(29, 78, 216, 0.65);
  transform: translateY(-1px);
}
.mobile-search-panel {
  position: fixed;
  left: 0.5rem;
  right: 0.5rem;
  z-index: 120;
}
</style>
