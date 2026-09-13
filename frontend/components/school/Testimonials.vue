<template>
  <section class="bg-slate-50 dark:bg-gray-900 py-14 sm:py-20">
    <div class="max-w-4xl mx-auto px-4">
      <div class="text-center mb-10">
        <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">What People Say</h2>
        <p class="mt-2 text-gray-500 dark:text-gray-400">Voices from our school community.</p>
      </div>

      <div class="relative bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm p-6 sm:p-10">
        <div v-if="current" class="text-center">
          <div class="flex justify-center gap-1 mb-4">
            <svg
              v-for="n in 5"
              :key="n"
              class="w-5 h-5"
              :class="n <= current.rating ? 'text-amber-400' : 'text-gray-200 dark:text-gray-600'"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.958a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.37 2.448a1 1 0 00-.363 1.118l1.287 3.957c.3.922-.755 1.688-1.538 1.118l-3.37-2.447a1 1 0 00-1.175 0l-3.37 2.447c-.783.57-1.838-.196-1.539-1.118l1.287-3.957a1 1 0 00-.363-1.118L2.05 9.385c-.783-.57-.38-1.81.588-1.81h4.163a1 1 0 00.95-.69l1.287-3.958z" />
            </svg>
          </div>
          <p class="text-lg text-gray-700 dark:text-gray-200 italic max-w-2xl mx-auto">"{{ current.quote }}"</p>
          <p class="mt-5 font-semibold text-gray-900 dark:text-white">{{ current.name }}</p>
          <p class="text-sm text-gray-500 dark:text-gray-400 capitalize">{{ current.role }}</p>
        </div>

        <div v-if="testimonials.length > 1" class="flex justify-center gap-2 mt-8">
          <button
            v-for="(t, index) in testimonials"
            :key="index"
            type="button"
            class="h-2 rounded-full transition-all"
            :class="index === activeIndex ? 'w-6 bg-blue-600' : 'w-2 bg-gray-300 dark:bg-gray-600'"
            :aria-label="`Show testimonial ${index + 1}`"
            @click="goTo(index)"
          />
        </div>
      </div>

      <div class="text-center mt-8">
        <button
          type="button"
          class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 text-sm font-semibold text-gray-700 dark:text-gray-200 hover:bg-white dark:hover:bg-gray-800"
          @click="openModal"
        >
          Add Your Testimonial
        </button>
      </div>
    </div>

    <!-- Submission modal -->
    <div
      v-if="showModal"
      class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4"
      @click.self="closeModal"
    >
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between sticky top-0 bg-white dark:bg-gray-800 z-10">
          <h3 class="text-xl font-bold text-gray-900 dark:text-white">Add Your Testimonial</h3>
          <button type="button" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg" @click="closeModal">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <div class="p-6 space-y-4">
          <p class="text-xs text-gray-500 dark:text-gray-400 bg-blue-50 dark:bg-blue-950/40 border border-blue-100 dark:border-blue-900 rounded-lg px-3 py-2">
            Your testimonial will be reviewed before publishing.
          </p>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Full name *</label>
            <input v-model="form.name" type="text" class="field" placeholder="Your name" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Email</label>
            <input v-model="form.email" type="email" class="field" placeholder="you@example.com" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Role</label>
            <input v-model="form.role" type="text" class="field" placeholder="e.g. Parent, Alumni" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Photo (optional)</label>
            <input type="file" accept="image/png,image/jpeg,image/webp,image/gif" class="text-sm" @change="onPhotoChange" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Rating</label>
            <div class="flex gap-1">
              <button
                v-for="n in 5"
                :key="n"
                type="button"
                @click="form.rating = n"
              >
                <svg
                  class="w-7 h-7"
                  :class="n <= form.rating ? 'text-amber-400' : 'text-gray-200 dark:text-gray-600'"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.958a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.37 2.448a1 1 0 00-.363 1.118l1.287 3.957c.3.922-.755 1.688-1.538 1.118l-3.37-2.447a1 1 0 00-1.175 0l-3.37 2.447c-.783.57-1.838-.196-1.539-1.118l1.287-3.957a1 1 0 00-.363-1.118L2.05 9.385c-.783-.57-.38-1.81.588-1.81h4.163a1 1 0 00.95-.69l1.287-3.958z" />
                </svg>
              </button>
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Your testimonial</label>
            <textarea v-model="form.message" rows="4" class="field resize-none" placeholder="Share your experience..." />
          </div>
        </div>

        <div class="p-6 border-t border-gray-200 dark:border-gray-700 flex items-center justify-end gap-3 sticky bottom-0 bg-white dark:bg-gray-800">
          <button type="button" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200" @click="closeModal">
            Cancel
          </button>
          <button
            type="button"
            :disabled="submitting"
            class="px-5 py-2 rounded-lg bg-blue-600 hover:bg-blue-500 text-white font-medium disabled:opacity-60"
            @click="submitTestimonial"
          >
            {{ submitting ? 'Submitting...' : 'Submit' }}
          </button>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, reactive, computed, onMounted, onUnmounted } from 'vue'

const { apiFetch } = useApi()
const toast = useAppToast()

const testimonials = ref([
  { name: 'Marie Uwase', role: 'parent', rating: 5, quote: 'The teachers genuinely care about every student. My child has grown so much academically and personally.' },
  { name: 'Claudine Mukamana', role: 'parent', rating: 5, quote: 'A school that balances discipline, faith, and academic excellence. We are proud to be part of this community.' },
])

const activeIndex = ref(0)
const current = computed(() => testimonials.value[activeIndex.value] || null)
let timer = null

const goTo = (index) => {
  activeIndex.value = index
  resetTimer()
}

const next = () => {
  if (!testimonials.value.length) return
  activeIndex.value = (activeIndex.value + 1) % testimonials.value.length
}

const resetTimer = () => {
  if (timer) clearInterval(timer)
  timer = setInterval(next, 7000)
}

const loadTestimonials = async () => {
  try {
    const res = await apiFetch('/api/testimonials')
    const rows = res?.data || []
    if (rows.length) {
      testimonials.value = rows.map((row) => ({
        name: row.name,
        role: row.role || 'parent',
        rating: row.rating || 5,
        quote: row.message,
      }))
      activeIndex.value = 0
      resetTimer()
    }
  } catch {
    // keep fallback testimonials
  }
}

onMounted(() => {
  resetTimer()
  loadTestimonials()
})

onUnmounted(() => {
  if (timer) clearInterval(timer)
})

const showModal = ref(false)
const submitting = ref(false)
const emptyForm = () => ({ name: '', email: '', role: '', message: '', rating: 5 })
const form = reactive(emptyForm())
const photoFile = ref(null)

const openModal = () => {
  Object.assign(form, emptyForm())
  photoFile.value = null
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
}

const onPhotoChange = (event) => {
  photoFile.value = event.target.files?.[0] || null
}

const submitTestimonial = async () => {
  if (!form.name.trim()) {
    toast.error('Your name is required.')
    return
  }

  submitting.value = true
  try {
    const body = new FormData()
    body.append('name', form.name.trim())
    body.append('email', form.email.trim())
    body.append('role', form.role.trim())
    body.append('rating', String(form.rating))
    body.append('message', form.message.trim())
    if (photoFile.value) body.append('photo', photoFile.value)

    const res = await apiFetch('/api/testimonials', { method: 'POST', body })
    toast.success(res?.message || 'Thank you! Your testimonial has been submitted for review.')
    closeModal()
  } catch (err) {
    toast.error(err?.data?.message || err?.message || 'Something went wrong. Please check the form and try again.')
  } finally {
    submitting.value = false
  }
}
</script>

<style scoped>
.field {
  @apply w-full px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-white;
}
</style>
