<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50 to-gray-50 flex flex-col lg:flex-row">
    <!-- Left Side - Features -->
    <div class="flex w-full lg:w-1/2 flex-col justify-between p-6 lg:p-12 text-gray-900">
      <!-- Campus Image -->
      <div class="relative">
        <div class="relative w-full h-96 rounded-3xl shadow-2xl overflow-hidden bg-gradient-to-br from-blue-100 via-purple-50 to-indigo-100">
          <div class="absolute w-full h-full transition-opacity duration-1000 ease-in-out">
            <img src="https://res.cloudinary.com/dck2vzccq/image/upload/v1752140933/5._efpl5u.jpg" alt="Campus Excellence" class="w-full h-full object-cover object-center">
          </div>
          <div class="absolute top-4 right-4 w-16 h-16 bg-blue-500/20 rounded-full blur-xl"></div>
          <div class="absolute bottom-4 left-4 w-20 h-20 bg-purple-500/20 rounded-full blur-xl"></div>
        </div>
      </div>

      <!-- Features -->
      <div class="space-y-6 lg:space-y-8 mt-6 flex-grow hidden lg:block">
        <div class="flex items-start gap-4">
          <div class="mt-1">
            <div class="w-6 h-6 bg-blue-600 rounded-full flex items-center justify-center">
              <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
              </svg>
            </div>
          </div>
          <div>
            <h3 class="text-xl font-semibold mb-2 text-gray-900">Innovative Teaching Methods</h3>
            <p class="text-gray-600">Interactive learning experiences combining traditional wisdom with cutting-edge technology.</p>
          </div>
        </div>

        <div class="flex items-start gap-4">
          <div class="mt-1">
            <div class="w-6 h-6 bg-blue-600 rounded-full flex items-center justify-center">
              <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
              </svg>
            </div>
          </div>
          <div>
            <h3 class="text-xl font-semibold mb-2 text-gray-900">Small Class Sizes</h3>
            <p class="text-gray-600">Personalized attention ensuring every student receives individual support and guidance.</p>
          </div>
        </div>

        <div class="flex items-start gap-4">
          <div class="mt-1">
            <div class="w-6 h-6 bg-blue-600 rounded-full flex items-center justify-center">
              <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
              </svg>
            </div>
          </div>
          <div>
            <h3 class="text-xl font-semibold mb-2 text-gray-900">Global Perspective</h3>
            <p class="text-gray-600">International curriculum standards with cultural exchange programs and global awareness.</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Right Side - Registration Form -->
    <div class="flex-1 flex items-center justify-center p-6 lg:p-8">
      <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-2xl p-6 lg:p-8 border border-gray-200">
          <h2 class="text-3xl font-bold text-gray-900 mb-8">Create an account</h2>

          <!-- Error Message -->
          <div v-if="errorMessage" class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-sm text-red-600">{{ errorMessage }}</p>
          </div>

          <!-- Success Message -->
          <div v-if="successMessage" class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
            <p class="text-sm text-green-600">{{ successMessage }}</p>
          </div>

          <!-- Registration Form -->
          <form @submit.prevent="handleSubmit" class="space-y-4">
            <!-- Username -->
            <!-- <div>
              <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                Username
              </label>
              <input
                type="text"
                id="username"
                v-model="formData.username"
                placeholder="Choose a username"
                required
                :disabled="loading"
                class="w-full bg-white border border-gray-300 text-gray-900 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder-gray-400 disabled:opacity-50"
              />
            </div> -->

            <!-- Name -->
            <div>
              <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                Name
              </label>
              <input
                type="text"
                id="name"
                v-model="formData.name"
                placeholder="Write your name here"
                required
                :disabled="loading"
                class="w-full bg-white border border-gray-300 text-gray-900 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder-gray-400 disabled:opacity-50"
              />
            </div>

            <!-- Last Name -->
            <!-- <div>
              <label for="lastName" class="block text-sm font-medium text-gray-700 mb-2">
                Last name
              </label>
              <input
                type="text"
                id="lastName"
                v-model="formData.lastName"
                placeholder="Write your last name here"
                required
                :disabled="loading"
                class="w-full bg-white border border-gray-300 text-gray-900 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder-gray-400 disabled:opacity-50"
              />
            </div> -->

            <!-- Email -->
            <div>
              <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                Your email
              </label>
              <input
                type="email"
                id="email"
                v-model="formData.email"
                placeholder="name@example.com"
                required
                :disabled="loading"
                class="w-full bg-white border border-gray-300 text-gray-900 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder-gray-400 disabled:opacity-50"
              />
            </div>

            <!-- Phone Number (Optional) -->
            <!-- <div>
              <label for="phoneNumber" class="block text-sm font-medium text-gray-700 mb-2">
                Phone number (optional)
              </label>
              <input
                type="tel"
                id="phoneNumber"
                v-model="formData.phoneNumber"
                placeholder="+250 XXX XXX XXX"
                :disabled="loading"
                class="w-full bg-white border border-gray-300 text-gray-900 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder-gray-400 disabled:opacity-50"
              />
            </div> -->

            <!-- Password -->
            <div>
              <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                Your password
              </label>
              <div class="relative">
                <input
                  :type="showPassword ? 'text' : 'password'"
                  id="password"
                  v-model="formData.password"
                  placeholder="••••••••"
                  required
                  minlength="6"
                  :disabled="loading"
                  class="w-full bg-white border border-gray-300 text-gray-900 rounded-lg px-4 py-3 pr-12 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder-gray-400 disabled:opacity-50"
                />
                <button
                  type="button"
                  @click="showPassword = !showPassword"
                  :disabled="loading"
                  class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 disabled:opacity-50"
                >
                  <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                  </svg>
                  <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd" />
                    <path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z" />
                  </svg>
                </button>
              </div>
              <p class="mt-1 text-xs text-gray-500">Password must be at least 6 characters</p>
            </div>

            <!-- Confirm Password -->
            <div>
              <label for="confirmPassword" class="block text-sm font-medium text-gray-700 mb-2">
                Confirm password
              </label>
              <div class="relative">
                <input
                  :type="showConfirmPassword ? 'text' : 'password'"
                  id="confirmPassword"
                  v-model="confirmPassword"
                  placeholder="••••••••"
                  required
                  :disabled="loading"
                  class="w-full bg-white border border-gray-300 text-gray-900 rounded-lg px-4 py-3 pr-12 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder-gray-400 disabled:opacity-50"
                />
                <button
                  type="button"
                  @click="showConfirmPassword = !showConfirmPassword"
                  :disabled="loading"
                  class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 disabled:opacity-50"
                >
                  <svg v-if="!showConfirmPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                  </svg>
                  <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd" />
                    <path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z" />
                  </svg>
                </button>
              </div>
            </div>

            <!-- Terms and Conditions -->
            <div class="flex items-start gap-2">
              <input
                type="checkbox"
                id="terms"
                v-model="acceptTerms"
                required
                :disabled="loading"
                class="mt-1 w-4 h-4 rounded border-gray-300 bg-white text-blue-600 focus:ring-2 focus:ring-blue-500 cursor-pointer disabled:opacity-50"
              />
              <label for="terms" class="text-sm text-gray-700">
                I accept the <NuxtLink to="/terms" class="text-blue-600 hover:text-blue-700">Terms and Conditions</NuxtLink>
              </label>
            </div>

            <!-- Create Account Button -->
            <button
              type="submit"
              :disabled="loading"
              class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
            >
              <svg v-if="loading" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span>{{ loading ? 'Creating account...' : 'Create account' }}</span>
            </button>
          </form>

          <!-- Login Link -->
          <p class="mt-6 text-center text-sm text-gray-600">
            Already have an account?
            <NuxtLink to="/login" class="text-blue-600 hover:text-blue-700 font-medium">
              Login here
            </NuxtLink>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
import { ref } from 'vue'

const { apiFetch } = useApi()

// Form data
const formData = ref({
  name: '',
  email: '',
  password: ''
})

const confirmPassword = ref('')
const showPassword = ref(false)
const showConfirmPassword = ref(false)
const acceptTerms = ref(false)
const loading = ref(false)
const errorMessage = ref('')
const successMessage = ref('')

const handleSubmit = async () => {
  // Clear previous messages
  errorMessage.value = ''
  successMessage.value = ''

  // Validate passwords match
  if (formData.value.password !== confirmPassword.value) {
    errorMessage.value = 'Passwords do not match!'
    return
  }

  // Validate password length
  if (formData.value.password.length < 6) {
    errorMessage.value = 'Password must be at least 6 characters long'
    return
  }

  // Validate terms accepted
  if (!acceptTerms.value) {
    errorMessage.value = 'Please accept the Terms and Conditions'
    return
  }

  // Validate username
  if (formData.value.name.length < 4) {
    errorMessage.value = 'Username must be at least 4 characters long'
    return
  }

  loading.value = true

  try {
    // Prepare registration data
    const registrationData = {
      name: formData.value.name,
      email: formData.value.email,
      password: formData.value.password,
    }


    const res = await apiFetch('/api/auth/register', {
      method: 'POST',
      body: registrationData
    })

    if (res.success) {

      alert(res.message);

      navigateTo('/auth/login');

    } else {
      // Registration failed
      errorMessage.value = data.error || 'Registration failed. Please try again.'
    }
  } catch (error) {
    console.error('Registration error:', error)

  } finally {
    loading.value = false
  }
}
</script>