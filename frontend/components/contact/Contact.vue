<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-blue-800 to-blue-600 text-white py-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
          <h1 class="text-4xl md:text-5xl font-bold mb-4">{{ $t('contact.title') }}</h1>
          <p class="text-xl md:text-2xl text-blue-100">
            {{ $t('contact.subtitle') }}
          </p>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
        
        <!-- Contact Information Section -->
        <div class="space-y-8">
          <!-- School Info Card -->
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center mb-6">
              <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center mr-4">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
              </div>
              <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $t('contact.schoolInfo') }}</h2>
            </div>
            
            <div class="space-y-4">
              <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ $t('common.schoolName') }}</h3>
                <p class="text-gray-600 dark:text-gray-300">{{ $t('contact.technicalSchool') }}</p>
              </div>
              
              <div class="flex items-start">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-1 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <div>
                  <p class="text-gray-900 dark:text-white font-medium">{{ $t('contact.physicalAddress') }}</p>
                  <p class="text-gray-600 dark:text-gray-300">{{ $t('contact.addressLine1') }}</p>
                  <p class="text-gray-600 dark:text-gray-300">{{ $t('contact.addressLine2') }}</p>
                  <p class="text-gray-600 dark:text-gray-300">{{ $t('contact.addressLine3') }}</p>
                  <p class="text-gray-600 dark:text-gray-300">{{ $t('contact.addressLine4') }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Contact Details Card -->
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center mb-6">
              <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center mr-4">
                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
              </div>
              <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $t('contact.contactDetails') }}</h2>
            </div>
            
            <div class="space-y-6">
              <!-- Phone Numbers -->
              <div class="flex items-start">
                <svg class="w-5 h-5 text-green-600 dark:text-green-400 mt-1 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                </svg>
                <div>
                  <p class="text-gray-900 dark:text-white font-medium">{{ $t('contact.phoneNumbers') }}</p>
                  <a href="tel:+250783138446" class="text-blue-600 dark:text-blue-400 hover:underline">

                    +250 783 138 446

                  </a>
                  <br>
                </div>
              </div>

              <!-- Email -->
              <div class="flex items-start">
                <svg class="w-5 h-5 text-green-600 dark:text-green-400 mt-1 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                </svg>
                <div>
                  <p class="text-gray-900 dark:text-white font-medium">{{ $t('contact.emailAddress') }}</p>
                  <a href="mailto:tssnzuki@gmail.com" class="text-blue-600 dark:text-blue-400 hover:underline">tssnzuki@gmail.com</a>
                  <br>
                </div>
              </div>

              <!-- Office Hours -->
              <div class="flex items-start">
                <svg class="w-5 h-5 text-green-600 dark:text-green-400 mt-1 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                  <p class="text-gray-900 dark:text-white font-medium">{{ $t('contact.officeHours') }}</p>
                  <p class="text-gray-600 dark:text-gray-300">{{ $t('contact.weekdays') }}</p>
                  <p class="text-gray-600 dark:text-gray-300">{{ $t('contact.saturday') }}</p>
                  <p class="text-gray-600 dark:text-gray-300">{{ $t('contact.sunday') }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Social Media Card -->
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center mb-6">
              <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center mr-4">
                <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h4a1 1 0 011 1v2h5a1 1 0 011 1v14a1 1 0 01-1 1H6a1 1 0 01-1-1V5a1 1 0 011-1h1zM8 6v2m8-2v2M9 14h6"></path>
                </svg>
              </div>
              <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $t('contact.connectWithUs') }}</h2>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
              <a href="https://wa.me/250783138446" target="_blank" class="flex items-center p-3 bg-green-50 dark:bg-green-900/20 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/40 transition-colors duration-200">
                <img :src="cldOptimize('https://res.cloudinary.com/dck2vzccq/image/upload/v1752774382/whatsApp_m6nmnz.png', 100)" alt="WhatsApp" class="w-8 h-8 mr-3 rounded-full" loading="lazy" />
                <span class="text-green-700 dark:text-green-300 font-medium">WhatsApp</span>
              </a>
              
              <a href="https://threema.id/UNHZY9DX" target="_blank" class="flex items-center p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/40 transition-colors duration-200">
                <img :src="cldOptimize('https://res.cloudinary.com/dck2vzccq/image/upload/v1752079103/Threema_iwilfe.png', 100)" alt="Threema" class="w-8 h-8 mr-3 rounded-full" loading="lazy" />
                <span class="text-blue-700 dark:text-blue-300 font-medium">Threema</span>
              </a>
              
              <a href="https://signal.me/#p/+250783138446" target="_blank" class="flex items-center p-3 bg-pink-50 dark:bg-pink-900/20 rounded-lg hover:bg-pink-100 dark:hover:bg-blue-900/40 transition-colors duration-200">
                <img :src="cldOptimize('https://res.cloudinary.com/dck2vzccq/image/upload/v1752079087/signal_g9aabs.jpg', 100)" alt="Instagram" class="w-8 h-8 mr-3 rounded-full" loading="lazy" />
                <span class="text-blue-700 dark:text-pink-300 font-medium">Signal</span>
              </a>
              
              <a href="https://x.com/@tssnzuki" target="_blank" class="flex items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-200">
                <img :src="cldOptimize('https://res.cloudinary.com/dck2vzccq/image/upload/v1752774382/xIcon_gzkqse.png', 100)" alt="X (Twitter)" class="w-8 h-8 mr-3 rounded-full" loading="lazy" />
                <span class="text-gray-700 dark:text-gray-300 font-medium">X (Twitter)</span>
              </a>
            </div>
          </div>
        </div>

        <!-- Map Section -->
        <div class="space-y-8">
          <!-- Map Card -->
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden border border-gray-200 dark:border-gray-700">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
              <div class="flex items-center">
                <div class="w-12 h-12 bg-red-100 dark:bg-red-900 rounded-lg flex items-center justify-center mr-4">
                  <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                  </svg>
                </div>
                <div>
                  <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $t('contact.findUs') }}</h2>
                  <p class="text-gray-600 dark:text-gray-300">{{ $t('contact.locationLabel') }}</p>
                </div>
              </div>
            </div>
            
            <div class="relative">
              <iframe
                :src="embedMapUrl"
                width="100%"
                height="500"
                style="border:0;"
                allowfullscreen=""
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
                class="w-full"
              ></iframe>
              
              <!-- Map Controls -->
              <div class="absolute top-4 right-4 space-y-2">
                <button 
                  @click="openGoogleMaps" 
                  class="bg-white dark:bg-gray-800 text-gray-800 dark:text-white px-4 py-2 rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-200 text-sm font-medium border border-gray-200 dark:border-gray-600"
                >
                  {{ $t('contact.openInGoogleMaps') }}
                </button>
                <button
                  @click="openOpenStreetMap"
                  class="bg-white dark:bg-gray-800 text-gray-800 dark:text-white px-4 py-2 rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-200 text-sm font-medium border border-gray-200 dark:border-gray-600 block w-full"
                >
                  {{ $t('contact.openInOsm') }}
                </button>
                <button
                  @click="getDirections"
                  class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 text-sm font-medium block w-full"
                >
                  {{ $t('contact.getDirections') }}
                </button>
              </div>
            </div>
          </div>

          <!-- Quick Contact Form -->
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center mb-6">
              <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900 rounded-lg flex items-center justify-center mr-4">
                <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                </svg>
              </div>
              <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $t('contact.sendMessage') }}</h2>
            </div>
            
            <form @submit.prevent="submitForm" class="space-y-6">
              <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ $t('contact.name') }}</label>
                <input 
                  type="text" 
                  id="name" 
                  v-model="form.name" 
                  required 
                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                  :placeholder="$t('contact.namePlaceholder')"
                />
              </div>
              
              <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ $t('contact.email') }}</label>
                <input 
                  type="email" 
                  id="email" 
                  v-model="form.email" 
                  required 
                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                  :placeholder="$t('contact.emailPlaceholder')"
                />
              </div>
              
              <div>
                <label for="subject" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ $t('contact.subject') }}</label>
                <select 
                  id="subject" 
                  v-model="form.subject" 
                  required 
                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                >
                  <option value="">{{ $t('contact.selectSubject') }}</option>
                  <option value="admissions">{{ $t('contact.subjectAdmissions') }}</option>
                  <option value="academics">{{ $t('contact.subjectAcademics') }}</option>
                  <option value="general">{{ $t('contact.subjectGeneral') }}</option>
                  <option value="support">{{ $t('contact.subjectSupport') }}</option>
                  <option value="other">{{ $t('contact.subjectOther') }}</option>
                </select>
              </div>
              
              <div>
                <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ $t('contact.message') }}</label>
                <textarea 
                  id="message" 
                  v-model="form.message" 
                  required 
                  rows="4" 
                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                  :placeholder="$t('contact.messagePlaceholder')"
                ></textarea>
              </div>
              
              <button 
                type="submit" 
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-lg transition-colors duration-200 flex items-center justify-center space-x-2"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                </svg>
                <span>{{ $t('contact.submit') }}</span>
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'

const toast = useAppToast()
const { t } = useI18n()

// Map configuration
const latitude = -2.223788
const longitude = 29.613161
const embedMapUrl = 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3967.123456789!2d29.613161!3d-2.223788!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMsKwMTMnMjUuNiJTIDI5wrAzNic0Ny40IkU!5e0!3m2!1sen!2srw!4v1234567890123!5m2!1sen!2srw'

// Form data
const form = reactive({
  name: '',
  email: '',
  subject: '',
  message: ''
})

// Methods
const openGoogleMaps = () => {
  const googleMapsUrl = `https://www.google.com/maps/place/Nzuki+Vocational+Training+Centre/@${latitude},${longitude},17z/data=!3m1!4b1!4m6!3m5!1s0x0:0x0!8m2!3d${latitude}!4d${longitude}!16s%2Fg%2F11p0866ed7`
  window.open(googleMapsUrl, '_blank')
}

const openOpenStreetMap = () => {
  const osmUrl = `https://www.openstreetmap.org/?mlat=${latitude}&mlon=${longitude}&zoom=17`
  window.open(osmUrl, '_blank')
}

const getDirections = () => {
  const directionsUrl = `https://www.google.com/maps/dir/?api=1&destination=${latitude},${longitude}`
  window.open(directionsUrl, '_blank')
}

const submitForm = async () => {
  try {
    const response = await fetch('https://formspree.io/f/xblybngl', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json'
      },
      body: JSON.stringify({
        name: form.name,
        email: form.email,
        subject: form.subject,
        message: form.message
      })
    })

    if (!response.ok) throw new Error('Form submission failed')

    toast.success(t('contact.messageSentSuccess'))

    // Reset form
    form.name = ''
    form.email = ''
    form.subject = ''
    form.message = ''
  } catch (error) {
    toast.error(t('contact.messageSentError'))
    console.error(error)
  }
}

</script>

<style scoped>
.contact-card {
  transition: transform 0.2s ease-in-out;
}

.contact-card:hover {
  transform: translateY(-2px);
}
</style>