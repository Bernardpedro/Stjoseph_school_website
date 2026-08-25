<template>
  <section class="scroll-mt-24">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
      <div class="bg-gradient-to-r from-blue-800 to-blue-600 px-6 py-8 text-white text-center">
        <h2 class="text-2xl sm:text-3xl font-bold">School Admission Requirements</h2>
        <p class="mt-2 text-blue-100 text-sm sm:text-base">Prepare these documents, then submit your application below.</p>
      </div>

      <div class="grid lg:grid-cols-2 gap-0">
        <!-- Requirements checklist -->
        <div class="p-6 sm:p-8 border-b lg:border-b-0 lg:border-r border-gray-200 dark:border-gray-700">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Required documents</h3>
          <div v-if="loadingReqs" class="text-sm text-gray-500">Loading...</div>
          <ol v-else class="space-y-3">
            <li
              v-for="(item, idx) in requirements"
              :key="item.id || idx"
              class="flex gap-3 text-gray-700 dark:text-gray-300"
            >
              <span class="shrink-0 w-7 h-7 rounded-full bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 text-sm font-semibold flex items-center justify-center">
                {{ idx + 1 }}
              </span>
              <span class="pt-0.5">{{ item.item_text }}</span>
            </li>
          </ol>
          <p v-if="!loadingReqs && !requirements.length" class="text-sm text-gray-500">Requirements will be published soon.</p>
        </div>

        <!-- Application form -->
        <div class="p-6 sm:p-8">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Application form</h3>

          <form class="space-y-3" @submit.prevent="submit">
            <div class="grid sm:grid-cols-2 gap-3">
              <input v-model="form.student_name" required type="text" placeholder="Student full name *" class="field" />
              <input v-model="form.date_of_birth" type="date" class="field" title="Date of birth" />
            </div>

            <div class="grid sm:grid-cols-2 gap-3">
              <select v-model="form.gender" class="field">
                <option value="">Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
              </select>
              <select v-model="form.province" class="field" @change="onProvinceChange">
                <option value="">Select a province</option>
                <option v-for="p in rwandaLocations" :key="p.code" :value="p.name">{{ p.name }}</option>
              </select>
            </div>
            <select v-model="form.district" class="field" :disabled="!form.province">
              <option value="">{{ form.province ? 'Select a district' : 'Select a province first' }}</option>
              <option v-for="d in availableDistricts" :key="d" :value="d">{{ d }}</option>
            </select>

            <div class="grid sm:grid-cols-2 gap-3">
              <select v-model="form.level" class="field" @change="onLevelChange">
                <option value="">Select a level</option>
                <option v-for="opt in ADMISSION_LEVELS" :key="opt.value" :value="opt.value">{{ opt.value }}</option>
              </select>
              <select v-model="form.program" class="field" :disabled="!form.level">
                <option value="">{{ form.level ? 'Select a program' : 'Select a level first' }}</option>
                <option v-for="p in availablePrograms" :key="p" :value="p">{{ p }}</option>
              </select>
            </div>

            <div class="rounded-lg border border-gray-200 dark:border-gray-600 p-3 space-y-2 bg-gray-50 dark:bg-gray-900/40">
              <label class="block text-sm font-medium text-gray-800 dark:text-gray-200">Previous school</label>
              <input
                v-model="form.previous_school"
                type="text"
                placeholder="School name (if transferring)"
                class="field"
              />
              <div>
                <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">
                  Upload bulletin / school report
                </label>
                <input
                  type="file"
                  multiple
                  accept="image/*,application/pdf"
                  class="block w-full text-sm text-gray-600 dark:text-gray-300"
                  @change="onBulletinFiles"
                />
                <p v-if="bulletinFiles.length" class="text-xs text-blue-600 mt-1">
                  {{ bulletinFiles.length }} file(s) selected
                </p>
                <p class="text-xs text-gray-400 mt-1">
                  Upload the school bulletin, report card, or transfer letter (PDF or image).
                </p>
              </div>
            </div>

            <div class="grid sm:grid-cols-2 gap-3">
              <input v-model="form.parent_name" required type="text" placeholder="Parent / guardian name *" class="field" />
              <input v-model="form.parent_phone" required type="tel" placeholder="Phone number *" class="field" />
            </div>

            <input v-model="form.parent_email" type="email" placeholder="Email (optional)" class="field" />
            <textarea v-model="form.message" rows="3" placeholder="Additional message (optional)" class="field" />

            <div>
              <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Other documents (optional)</label>
              <input type="file" multiple accept="image/*,application/pdf" class="block w-full text-sm text-gray-600" @change="onFiles" />
              <p class="text-xs text-gray-400 mt-1">Birth certificate, photos, national ID…</p>
            </div>

            <p v-if="error" class="text-sm text-red-600">{{ error }}</p>
            <p v-if="success" class="text-sm text-green-600">{{ success }}</p>

            <button
              type="submit"
              class="w-full sm:w-auto px-6 py-2.5 bg-blue-700 hover:bg-blue-800 text-white rounded-lg font-medium disabled:opacity-60"
              :disabled="submitting"
            >
              {{ submitting ? 'Sending...' : 'Submit application' }}
            </button>
          </form>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ADMISSION_LEVELS, programsForLevel, encodeAdmissionChoice } from '~/composables/useAdmissionPrograms'
import { formatRwandaAddress, useRwandaLocations } from '~/composables/useRwandaLocations'

const { apiFetch } = useApi()
const { locations: rwandaLocations, loadLocations, districtsFor } = useRwandaLocations()

const requirements = ref([])
const loadingReqs = ref(true)
const submitting = ref(false)
const error = ref('')
const success = ref('')
const files = ref([])
const bulletinFiles = ref([])

const form = reactive({
  student_name: '',
  date_of_birth: '',
  gender: '',
  level: '',
  program: '',
  previous_school: '',
  parent_name: '',
  parent_phone: '',
  parent_email: '',
  province: '',
  district: '',
  address: '',
  message: '',
})

const availablePrograms = computed(() => programsForLevel(form.level))
const availableDistricts = computed(() => districtsFor(form.province))

const onLevelChange = () => {
  if (form.program && !programsForLevel(form.level).includes(form.program)) {
    form.program = ''
  }
}

const onProvinceChange = () => {
  if (form.district && !districtsFor(form.province).includes(form.district)) {
    form.district = ''
  }
}

const loadRequirements = async (silent = false) => {
  if (!silent) loadingReqs.value = true
  try {
    const res = await apiFetch('/api/admissions/requirements')
    requirements.value = res?.data || []
  } catch {
    requirements.value = [
      { id: 1, item_text: 'Completed application form.' },
      { id: 2, item_text: "Result Slip" },
      { id: 3, item_text: 'Previous school report or transfer letter (if transferring from another school).' },
      { id: 4, item_text: 'Two passport-size photographs.' },
      { id: 5, item_text: "Copy of the parent or guardian's national ID." },
      { id: 6, item_text: 'Payment of the registration or application fee (if applicable).' },
    ]
  } finally {
    if (!silent) loadingReqs.value = false
  }
}

const onFiles = (e) => {
  files.value = Array.from(e.target.files || [])
}

const onBulletinFiles = (e) => {
  bulletinFiles.value = Array.from(e.target.files || [])
}

const resetForm = () => {
  Object.keys(form).forEach((k) => { form[k] = '' })
  files.value = []
  bulletinFiles.value = []
}

const submit = async () => {
  error.value = ''
  success.value = ''
  submitting.value = true

  try {
    const fd = new FormData()
    Object.entries(form).forEach(([key, value]) => {
      if (key === 'program' || key === 'address') return
      fd.append(key, value || '')
    })
    fd.append('program', encodeAdmissionChoice(form.level, form.program))
    fd.append('address', formatRwandaAddress(form.district, form.province))
    bulletinFiles.value.forEach((f) => fd.append('bulletin[]', f))
    files.value.forEach((f) => fd.append('documents[]', f))

    const res = await apiFetch('/api/admissions', {
      method: 'POST',
      body: fd,
    })

    success.value = res?.message || 'Application submitted successfully.'
    notifyContentChanged(['admissions', 'notifications'])
    resetForm()
  } catch (e) {
    error.value = e?.data?.message || e?.message || 'Failed to submit application'
  } finally {
    submitting.value = false
  }
}

onContentChange(['admissions'], () => {
  loadRequirements(true)
})

onMounted(() => {
  loadRequirements()
  loadLocations()
})
</script>

<style scoped>
.field {
  @apply w-full px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white;
}
</style>
