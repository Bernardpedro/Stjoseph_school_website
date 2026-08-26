<template>
  <div class="relative" ref="dropdownRef">
    <NuxtLink
      v-if="!role"
      to="/auth/login"
      class="login-btn"
      :class="{ 'is-active': $route.path.startsWith('/auth/login') }"
    >
      <svg class="w-6 h-6 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
      </svg>
      <span class="hidden sm:inline">{{ $t('auth.login') }}</span>
    </NuxtLink>

    <button
      v-else
      type="button"
      class="login-btn is-account"
      :class="{ 'is-open': isDropdownOpen }"
      @click="toggleDropdown"
    >
      <span
        class="w-7 h-7 sm:w-7 sm:h-7 rounded-full flex items-center justify-center text-sm sm:text-xs font-bold"
        :class="isDropdownOpen ? 'bg-white/20 text-white' : 'bg-blue-100 text-[#1D4ED8] dark:bg-blue-900/50 dark:text-blue-300'"
      >
        {{ computedAvatarInitial || '•' }}
      </span>
      <svg
        class="hidden sm:block w-3.5 h-3.5 transition-transform"
        :class="{ 'rotate-180': isDropdownOpen }"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
        aria-hidden="true"
      >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
      </svg>
    </button>

    <!-- Dropdown Menu -->
    <transition
      enter-active-class="transition ease-out duration-200"
      enter-from-class="opacity-0 translate-y-1"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition ease-in duration-150"
      leave-from-class="opacity-100 translate-y-0"
      leave-to-class="opacity-0 translate-y-1"
    >
      <div
        v-if="role && isDropdownOpen"
        class="absolute right-0 mt-2 w-64 bg-white dark:bg-gray-800 rounded-xl shadow-2xl shadow-blue-900/10 border border-blue-100 dark:border-blue-900 z-50 overflow-hidden"
      >
        <!-- User Info Header -->
        <div class="px-4 py-3 bg-gradient-to-r from-blue-600 to-blue-800 border-b border-blue-700">
          <p class="text-sm font-semibold text-white">{{ name }}</p>
        </div>

        <!-- Logout -->
        <div class="border-t border-blue-100 dark:border-blue-900">
          <!-- Logout Confirmation -->
          <div v-if="showLogoutConfirm" class="px-4 py-3 bg-red-50">
            <p class="text-sm text-gray-800 mb-3">{{ $t('auth.logoutConfirm') }}</p>
            <div class="flex justify-end gap-2">
              <button 
                @click.stop="cancelLogout"
                class="px-3 py-1.5 text-sm text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded transition-colors"
              >
                {{ $t('common.cancel') }}
              </button>
              <button 
                @click.stop="handleLogout"
                :disabled="isLoading"
                class="px-3 py-1.5 text-sm bg-red-500 hover:bg-red-600 text-white rounded transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
              >
                {{ isLoading ? $t('common.loading') : $t('common.confirm') }}
              </button>
            </div>
          </div>
          
          <!-- Logout Button -->
          <button
            v-else
            @click.stop="openLogoutConfirm"
            class="flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors w-full text-left"
          >
            <svg 
              class="w-5 h-5" 
              fill="none" 
              stroke="currentColor" 
              viewBox="0 0 24 24"
            >
              <path 
                stroke-linecap="round" 
                stroke-linejoin="round" 
                stroke-width="2" 
                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" 
              />
            </svg>
            {{ $t('auth.logout') }}
          </button>
        </div>

      </div>
    </transition>
  </div>
</template>

<script setup lang="ts">

import { ref, onMounted } from 'vue'
import { storeToRefs } from 'pinia'
import { useUserStore } from '~/stores/user'

const userStore = useUserStore()
const { name, role } = storeToRefs(userStore)


const showLogoutConfirm = ref(false)
const isLoading = ref(false)


onMounted(() => {
  userStore.hydrate()
})

// Open confirmation box
const openLogoutConfirm = () => {
  showLogoutConfirm.value = true
}

// Cancel logout
const cancelLogout = () => {
  showLogoutConfirm.value = false
}

// Confirm logout
const handleLogout = async () => {
  isLoading.value = true
  try {
     userStore.logout()

      // Redirect to login
    navigateTo('/auth/login');
    
  } finally {
    isLoading.value = false
    showLogoutConfirm.value = false
  }
}

const isDropdownOpen = ref(false)


// Computed properties for avatar 
const computedAvatarInitial = computed(() => {
  if (!name.value) return null
  return name.value.charAt(0).toUpperCase()
});

const toggleDropdown = () => {
  isDropdownOpen.value = !isDropdownOpen.value
}

const closeDropdown = () => {
  isDropdownOpen.value = false
}

// Close dropdown when clicking outside
const dropdownRef = ref<HTMLElement | null>(null)

onMounted(() => {
  const handleClickOutside = (e: MouseEvent) => {
    const target = e.target as HTMLElement
    if (dropdownRef.value && !dropdownRef.value.contains(target)) {
      closeDropdown()
    }
  }
  
  document.addEventListener('click', handleClickOutside)
  
  onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside)
  })
})
</script>

<style scoped>
.login-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  padding: 0.4rem 0.9rem;
  border-radius: 9999px;
  font-size: 0.875rem;
  font-weight: 600;
  letter-spacing: 0.02em;
  line-height: 1.25;
  color: #1d4ed8;
  background: rgba(239, 246, 255, 0.9);
  box-shadow: inset 0 0 0 1px #dbeafe;
  white-space: nowrap;
  transition: color 0.2s ease, background 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
}
.login-btn:hover,
.login-btn.is-active,
.login-btn.is-open {
  background: linear-gradient(180deg, #3b82f6 0%, #1d4ed8 100%);
  color: #fff;
  box-shadow: 0 10px 20px -10px rgba(29, 78, 216, 0.7);
  transform: translateY(-1px);
}
.login-btn.is-account:hover,
.login-btn.is-account.is-open {
  background: linear-gradient(180deg, #3b82f6 0%, #1d4ed8 100%);
  color: #fff;
}
.dark .login-btn {
  color: #60a5fa;
  background: rgba(23, 37, 84, 0.45);
  box-shadow: inset 0 0 0 1px rgba(30, 58, 138, 0.7);
}
.dark .login-btn:hover,
.dark .login-btn.is-active,
.dark .login-btn.is-open {
  color: #fff;
}
@media (max-width: 639px) {
  .login-btn {
    min-width: 2rem;
    min-height: 2rem;
    justify-content: center;
    padding: 0;
    gap: 0;
    background: transparent;
    box-shadow: none;
    transform: none;
  }
  .login-btn:hover,
  .login-btn.is-active,
  .login-btn.is-open,
  .login-btn.is-account:hover,
  .login-btn.is-account.is-open {
    background: transparent;
    color: #1d4ed8;
    box-shadow: none;
    transform: none;
  }
  .dark .login-btn,
  .dark .login-btn:hover,
  .dark .login-btn.is-active,
  .dark .login-btn.is-open {
    background: transparent;
    box-shadow: none;
    color: #60a5fa;
  }
}
</style>
