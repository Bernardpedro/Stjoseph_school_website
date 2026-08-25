<script setup>
import { ref } from 'vue'
import { useUserStore } from '~/stores/user'

const userStore = useUserStore()
const { apiFetch } = useApi()
const { t } = useI18n()

const formData = ref({
  email: '',
  password: ''
})

const showPassword = ref(false)
const rememberMe = ref(false)
const loading = ref(false)
const errorMessage = ref('')
const successMessage = ref('')

const handleSubmit = async () => {
  errorMessage.value = ''
  successMessage.value = ''

  if (!formData.value.email || !formData.value.password) {
    errorMessage.value = t('auth.fillEmailPassword')
    return
  }

  loading.value = true

  try {
    const res = await apiFetch('/api/auth/login', {
      method: 'POST',
      body: {
        email: formData.value.email,
        password: formData.value.password
      }
    })

    if (!res.success || !res.data?.token) {
      errorMessage.value = res.message || 'Login failed'
      return
    }

    userStore.setUser({
      name: res.data.name,
      role: res.data.role,
      token: res.data.token
    })

    if (rememberMe.value) {
      localStorage.setItem('rememberedEmail', formData.value.email)
    } else {
      localStorage.removeItem('rememberedEmail')
    }

    successMessage.value = res.message || 'Login successful'
    await nextTick()

    if (userStore.isAdmin) {
      await navigateTo('/dashboard/admin')
    } else {
      await navigateTo('/')
    }
  } catch (err) {
    console.error('Login error:', err)
    if (err?.data?.message) {
      errorMessage.value = err.data.message
    } else if (err?.message) {
      errorMessage.value = err.message
    } else {
      errorMessage.value = t('auth.loginFailed')
    }
  } finally {
    loading.value = false
  }
}

const handleGoogleLogin = () => {

  errorMessage.value = 'Google login is not yet implemented. Please use username/password login.'
  // TODO: Implement Google OAuth
}

const handleFacebookLogin = () => {

  errorMessage.value = 'Facebook login is not yet implemented. Please use username/password login.'
  // TODO: Implement Facebook OAuth
}

// Load remembered username on mount
onMounted(() => {
  const rememberedEmail = localStorage.getItem('rememberedEmail')
  if (rememberedEmail) {
    formData.value.email= rememberedEmail
    rememberMe.value = true
  }
})

definePageMeta({
  layout: 'default'
})
</script>

<template>
  <div class="min-h-screen relative flex items-center justify-center p-6">
    <!-- Background Image with Overlay -->
    <div class="absolute inset-0 z-0">
      <img 
        src="https://res.cloudinary.com/dck2vzccq/image/upload/v1752140933/5._efpl5u.jpg" 
        alt="Campus Background" 
        class="w-full h-full object-cover"
      />
      <!-- Dark overlay for better text readability -->
      <div class="absolute inset-0 bg-black/40"></div>
    </div>

    <!-- Welcome Message -->
    <div class="absolute top-8 left-0 right-0 z-10 text-center px-6">
      <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white drop-shadow-2xl">
        {{ $t('auth.welcomeSchool') }}
      </h1>
    </div>

    <!-- Login Form Container -->
    <div class="relative z-10 w-full max-w-md mt-20">
      <div class="bg-transparent backdrop-blur-sm rounded-2xl shadow-2xl p-6 lg:p-8 border border-gray-200">
        <!-- Header -->
        <div class="text-center mb-8">
          <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">{{ $t('auth.welcomeBack') }}</h2>
          <p class="text-gray-600 dark:text-gray-300 text-sm">{{ $t('auth.signInContinue') }}</p>
        </div>

        <!-- Error Message Ad Banner -->
        <div v-if="errorMessage" class="mb-6 p-4 bg-gradient-to-r from-red-50 to-pink-50 border-l-4 border-red-600 rounded-lg shadow-md">
          <div class="flex items-start gap-3">
            <div class="flex-shrink-0 mt-0.5">
              <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
              </svg>
            </div>
            <div class="flex-1">
              <h4 class="text-sm font-semibold text-red-900 mb-1">{{ $t('auth.authError') }}</h4>
              <p class="text-xs text-red-800">{{ errorMessage }}</p>
            </div>
          </div>
        </div>

        <!-- Success Message Ad Banner -->
        <div v-if="successMessage" class="mb-6 p-4 bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-600 rounded-lg shadow-md animate-pulse">
          <div class="flex items-start gap-3">
            <div class="flex-shrink-0 mt-0.5">
              <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
            </div>
            <div class="flex-1">
              <h4 class="text-sm font-semibold text-green-900 mb-1">{{ $t('auth.loginSuccess') }}</h4>
              <p class="text-xs text-green-800">{{ successMessage }}</p>
            </div>
          </div>
        </div>

        <!-- Email/Password Form -->
        <form @submit.prevent="handleSubmit" class="space-y-4">
          <!-- Username Field -->
          <div>
            <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
              {{ $t('auth.email') }}
            </label>
            <div class="relative">
              <input
                type="text"
                id="email"
                v-model="formData.email"
                :placeholder="$t('auth.enterEmail')"
                required
                :disabled="loading"
                class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder-gray-400 disabled:opacity-50"
              />
            </div>
          </div>

          <!-- Password Field -->
          <div>
            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
              {{ $t('auth.password') }}
            </label>
            <div class="relative">
              <input
                :type="showPassword ? 'text' : 'password'"
                id="password"
                v-model="formData.password"
                placeholder="••••••••"
                required
                :disabled="loading"
                class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-4 py-3 pr-12 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder-gray-400 disabled:opacity-50"
              />
              <button
                type="button"
                @click="showPassword = !showPassword"
                :disabled="loading"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 disabled:opacity-50"
              >
                <!-- Eye icon when password is hidden -->
                <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                  <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                  <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                </svg>
                <!-- EyeOff icon when password is visible -->
                <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd" />
                  <path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z" />
                </svg>
              </button>
            </div>
          </div>

          <!-- Remember Me & Forgot Password -->
          <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 cursor-pointer">
              <input
                type="checkbox"
                v-model="rememberMe"
                :disabled="loading"
                class="w-4 h-4 rounded border-gray-300 bg-white text-blue-600 focus:ring-2 focus:ring-blue-500 cursor-pointer disabled:opacity-50"
              />
              <span class="text-sm text-gray-700 dark:text-gray-200">{{ $t('auth.rememberMe') }}</span>
            </label>
            <!-- <NuxtLink to="/forgot-password" class="text-sm text-blue-600 hover:text-blue-700">
              Forgot password?
            </NuxtLink> -->
          </div>

          <!-- Submit Button -->
          <button
            type="submit"
            :disabled="loading"
            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
          >
            <svg v-if="loading" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>{{ loading ? $t('auth.signingIn') : $t('auth.signIn') }}</span>
          </button>
        </form>

        <p class="mt-6 text-center text-sm text-gray-600 dark:text-gray-300">
          Accounts are created by the school owner only.
        </p>
      </div>
    </div>
  </div>
</template>