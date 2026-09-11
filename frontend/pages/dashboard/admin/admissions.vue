<template>
  <div class="min-h-screen bg-slate-50 dark:bg-gray-900 p-4 sm:p-6">
    <div class="max-w-7xl mx-auto space-y-6">
      <p v-if="error" class="text-sm text-red-600 bg-red-50 border border-red-100 rounded-lg px-3 py-2">{{ error }}</p>
      <p v-if="success" class="text-sm text-green-700 bg-green-50 border border-green-100 rounded-lg px-3 py-2">{{ success }}</p>

      <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-3 sm:p-5 space-y-4">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
          <h2 class="font-semibold text-gray-900 dark:text-white">Applications</h2>
          <div class="flex flex-col sm:flex-row flex-wrap gap-2 w-full lg:w-auto">
            <input
              v-model="search"
              type="search"
              class="field w-full sm:w-64"
              placeholder="Search name, phone, email..."
            />
            <button type="button" class="px-3 py-2 text-sm border rounded-lg" @click="exportCsv">Export CSV</button>
            <button type="button" class="px-3 py-2 text-sm border rounded-lg disabled:opacity-60" :disabled="pdfWorking || !visibleApplications.length" @click="openPdf({ print: false })">
              {{ pdfWorking ? 'Preparing PDF...' : 'Download all PDF' }}
            </button>
            <button type="button" class="px-3 py-2 text-sm border rounded-lg disabled:opacity-60" :disabled="pdfWorking || !visibleApplications.length" @click="openPdf({ print: true })">
              Print all
            </button>
            <button
              type="button"
              class="px-3 py-2 text-sm rounded-lg bg-blue-600 hover:bg-blue-500 text-white font-medium"
              @click="openCreate"
            >
              Add application
            </button>
          </div>
        </div>

        <div class="flex flex-wrap gap-2">
          <button
            v-for="s in statusFilters"
            :key="s.value"
            type="button"
            class="px-3 py-1.5 rounded-lg text-sm border"
            :class="statusFilter === s.value ? 'bg-blue-600 text-white border-blue-600' : 'bg-white dark:bg-gray-900 border-gray-300 dark:border-gray-600'"
            @click="setStatusFilter(s.value)"
          >
            {{ s.label }}
          </button>
        </div>

        <div class="flex flex-wrap gap-2">
          <button
            type="button"
            class="px-3 py-1.5 rounded-lg text-sm border"
            :class="levelFilter === '' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white dark:bg-gray-900 border-gray-300 dark:border-gray-600'"
            @click="setLevelFilter('')"
          >
            All levels
          </button>
          <button
            v-for="lv in ADMISSION_LEVELS"
            :key="lv.value"
            type="button"
            class="px-3 py-1.5 rounded-lg text-sm border"
            :class="levelFilter === lv.value ? 'bg-blue-600 text-white border-blue-600' : 'bg-white dark:bg-gray-900 border-gray-300 dark:border-gray-600'"
            @click="setLevelFilter(lv.value)"
          >
            {{ lv.value }}
          </button>
        </div>

        <div v-if="loading" class="text-gray-500">Loading...</div>
        <div v-else>
          <div class="lg:hidden space-y-3">
            <article
              v-for="app in visibleApplications"
              :key="app.id"
              class="rounded-xl border border-gray-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-900 p-4 space-y-3"
            >
              <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                  <h3 class="font-semibold text-gray-900 dark:text-white break-words">{{ app.student_name }}</h3>
                  <p v-if="app.registration_number" class="mt-1 font-mono text-xs font-semibold text-[#1D4ED8] break-all">
                    {{ app.registration_number }}
                  </p>
                </div>
                <span class="shrink-0 text-xs px-2 py-0.5 rounded-full font-medium capitalize" :class="statusClass(app.status)">
                  {{ app.status_label || app.status }}
                </span>
              </div>
              <dl class="grid grid-cols-1 gap-2 text-sm">
                <div>
                  <dt class="text-gray-400">Parent</dt>
                  <dd class="text-gray-800 dark:text-gray-200 break-words">{{ app.parent_name || '—' }}</dd>
                </div>
                <div>
                  <dt class="text-gray-400">Phone</dt>
                  <dd class="text-gray-800 dark:text-gray-200 break-words">{{ app.parent_phone || '—' }}</dd>
                </div>
                <div>
                  <dt class="text-gray-400">Level</dt>
                  <dd class="text-gray-800 dark:text-gray-200 break-words">{{ app.level_label || choice(app).level || '—' }}</dd>
                </div>
                <div>
                  <dt class="text-gray-400">Program</dt>
                  <dd class="text-gray-800 dark:text-gray-200 break-words">{{ app.program_label || choice(app).program || '—' }}</dd>
                </div>
                <div>
                  <dt class="text-gray-400">Submitted</dt>
                  <dd class="text-gray-800 dark:text-gray-200">{{ formatShortDate(app.created_at) }}</dd>
                </div>
              </dl>
              <div class="flex flex-wrap gap-2">
                <button type="button" class="px-2.5 py-1.5 text-xs bg-slate-100 dark:bg-gray-700 rounded-md" @click="openView(app)">View</button>
                <button type="button" class="px-2.5 py-1.5 text-xs bg-blue-50 text-blue-700 rounded-md" @click="openEdit(app)">Edit</button>
                <button type="button" class="px-2.5 py-1.5 text-xs bg-white border border-gray-200 dark:border-gray-600 rounded-md" :disabled="pdfWorking" @click="openPdf({ id: app.id, name: app.registration_number })">PDF</button>
                <button type="button" class="px-2.5 py-1.5 text-xs bg-white border border-gray-200 dark:border-gray-600 rounded-md" :disabled="pdfWorking" @click="openPdf({ id: app.id, print: true })">Print</button>
                <select v-model="app.status" class="field !w-auto !py-1.5 !px-2 !text-xs" @change="updateStatus(app)">
                  <option value="pending">pending</option>
                  <option value="reviewed">reviewed</option>
                  <option value="accepted">accepted</option>
                  <option value="rejected">rejected</option>
                </select>
                <button type="button" class="px-2.5 py-1.5 text-xs bg-red-50 text-red-700 rounded-md" @click="remove(app)">Delete</button>
              </div>
            </article>
          </div>

          <div class="hidden lg:block overflow-x-auto">
            <table class="applicant-table w-full">
              <thead>
                <tr class="applicant-head">
                  <th>Registration</th>
                  <th>Student</th>
                  <th>Parent</th>
                  <th>Phone</th>
                  <th>Level</th>
                  <th>Program</th>
                  <th>Status</th>
                  <th>Submitted</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="app in visibleApplications"
                  :key="app.id"
                  class="applicant-line"
                >
                  <td class="font-mono text-xs font-semibold text-[#1D4ED8]">{{ app.registration_number || '—' }}</td>
                  <td class="font-medium text-gray-900 dark:text-white">{{ app.student_name }}</td>
                  <td class="text-gray-600 dark:text-gray-300">{{ app.parent_name || '—' }}</td>
                  <td class="text-gray-600 dark:text-gray-300 whitespace-nowrap">{{ app.parent_phone || '—' }}</td>
                  <td class="text-gray-600 dark:text-gray-300">{{ app.level_label || choice(app).level || '—' }}</td>
                  <td class="text-gray-600 dark:text-gray-300">{{ app.program_label || choice(app).program || '—' }}</td>
                  <td>
                    <span class="text-xs px-2 py-0.5 rounded-full font-medium capitalize" :class="statusClass(app.status)">
                      {{ app.status_label || app.status }}
                    </span>
                  </td>
                  <td class="text-gray-500 whitespace-nowrap">{{ formatShortDate(app.created_at) }}</td>
                  <td>
                    <div class="flex flex-wrap items-center gap-1.5">
                      <button type="button" class="px-2 py-1 text-xs bg-slate-100 dark:bg-gray-700 rounded-md" @click="openView(app)">View</button>
                      <button type="button" class="px-2 py-1 text-xs bg-blue-50 text-blue-700 rounded-md" @click="openEdit(app)">Edit</button>
                      <button type="button" class="px-2 py-1 text-xs bg-white border border-gray-200 dark:border-gray-600 rounded-md" :disabled="pdfWorking" @click="openPdf({ id: app.id, name: app.registration_number })">PDF</button>
                      <button type="button" class="px-2 py-1 text-xs bg-white border border-gray-200 dark:border-gray-600 rounded-md" :disabled="pdfWorking" @click="openPdf({ id: app.id, print: true })">Print</button>
                      <select v-model="app.status" class="field !w-auto !py-1 !px-2 !text-xs" @change="updateStatus(app)">
                        <option value="pending">pending</option>
                        <option value="reviewed">reviewed</option>
                        <option value="accepted">accepted</option>
                        <option value="rejected">rejected</option>
                      </select>
                      <button type="button" class="px-2 py-1 text-xs bg-red-50 text-red-700 rounded-md" @click="remove(app)">Delete</button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <p v-if="!applications.length" class="text-sm text-gray-500 pt-3">No applications yet. Add one or wait for a website submission.</p>
          <p v-else-if="!visibleApplications.length" class="text-sm text-gray-500 pt-3">No applications match these filters.</p>
        </div>
      </div>
    </div>

    <div v-if="showForm" class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
      <div class="bg-white dark:bg-gray-800 rounded-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto p-5 space-y-4">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">
          {{ editingId ? 'Edit application' : 'Add application' }}
        </h2>

        <div class="grid sm:grid-cols-2 gap-3">
          <input v-model="form.student_name" class="field" placeholder="Student name *" />
          <input v-model="form.date_of_birth" type="date" class="field" />
          <select v-model="form.gender" class="field">
            <option value="">Gender</option>
            <option value="male">Male</option>
            <option value="female">Female</option>
          </select>
          <input v-model="form.previous_school" class="field" placeholder="Previous school" />
          <select v-model="form.level" class="field" @change="onFormLevelChange">
            <option value="">Level *</option>
            <option v-for="lv in ADMISSION_LEVELS" :key="lv.value" :value="lv.value">{{ lv.value }}</option>
          </select>
          <select v-model="form.program" class="field" :disabled="!form.level">
            <option value="">Program *</option>
            <option v-for="p in programsForLevel(form.level)" :key="p" :value="p">{{ p }}</option>
          </select>
          <input v-model="form.parent_name" class="field" placeholder="Parent / guardian *" />
          <input v-model="form.parent_phone" class="field" placeholder="Phone *" />
          <input v-model="form.parent_email" type="email" class="field" placeholder="Email" />
          <select v-model="form.province" class="field" @change="onFormProvinceChange">
            <option value="">Province</option>
            <option v-for="p in rwandaLocations" :key="p.name" :value="p.name">{{ p.name }}</option>
          </select>
          <select v-model="form.district" class="field" :disabled="!form.province">
            <option value="">District</option>
            <option v-for="d in districtsFor(form.province)" :key="d" :value="d">{{ d }}</option>
          </select>
          <select v-model="form.status" class="field">
            <option value="pending">pending</option>
            <option value="reviewed">reviewed</option>
            <option value="accepted">accepted</option>
            <option value="rejected">rejected</option>
          </select>
        </div>
        <textarea v-model="form.message" rows="3" class="field" placeholder="Message / notes" />

        <div class="flex justify-end gap-2">
          <button type="button" class="px-4 py-2 rounded-lg border" @click="showForm = false">Cancel</button>
          <button
            type="button"
            class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-500 text-white"
            :disabled="saving"
            @click="saveApplication"
          >
            {{ saving ? 'Saving...' : (editingId ? 'Update application' : 'Add application') }}
          </button>
        </div>
      </div>
    </div>

    <div v-if="viewing" class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
      <div class="bg-white dark:bg-gray-800 rounded-xl w-full max-w-xl max-h-[90vh] overflow-y-auto p-5 space-y-3">
        <div class="flex items-start justify-between gap-3">
          <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white break-words">{{ viewing.student_name }}</h2>
            <p v-if="viewing.registration_number" class="mt-1 font-mono text-sm font-semibold text-[#1D4ED8] break-all">
              {{ viewing.registration_number }}
            </p>
            <span class="text-xs px-2 py-0.5 rounded-full font-medium" :class="statusClass(viewing.status)">{{ viewing.status_label || viewing.status }}</span>
          </div>
          <button type="button" class="text-gray-500" @click="viewing = null">Close</button>
        </div>
        <dl class="grid sm:grid-cols-2 gap-x-4 gap-y-2 text-sm">
          <div><dt class="text-gray-400">Date of birth</dt><dd class="break-words">{{ viewing.date_of_birth || '—' }}</dd></div>
          <div><dt class="text-gray-400">Gender</dt><dd class="break-words">{{ viewing.gender_label || viewing.gender || '—' }}</dd></div>
          <div><dt class="text-gray-400">Level</dt><dd class="break-words">{{ viewing.level_label || choice(viewing).level || '—' }}</dd></div>
          <div><dt class="text-gray-400">Program</dt><dd class="break-words">{{ viewing.program_label || choice(viewing).program || '—' }}</dd></div>
          <div><dt class="text-gray-400">Previous school</dt><dd class="break-words">{{ viewing.previous_school || '—' }}</dd></div>
          <div><dt class="text-gray-400">Parent</dt><dd class="break-words">{{ viewing.parent_name }}</dd></div>
          <div><dt class="text-gray-400">Phone</dt><dd class="break-words">{{ viewing.parent_phone }}</dd></div>
          <div><dt class="text-gray-400">Email</dt><dd class="break-words">{{ viewing.parent_email || '—' }}</dd></div>
          <div class="sm:col-span-2"><dt class="text-gray-400">Address</dt><dd class="break-words">{{ viewing.address || [viewing.district, viewing.province].filter(Boolean).join(', ') || '—' }}</dd></div>
          <div class="sm:col-span-2"><dt class="text-gray-400">Message</dt><dd class="break-words">{{ viewing.message || '—' }}</dd></div>
          <div class="sm:col-span-2"><dt class="text-gray-400">Submitted</dt><dd class="break-words">{{ formatDate(viewing.created_at) }}</dd></div>
          <div
            v-if="documentGroups(viewing).length"
            class="sm:col-span-2 border-t border-gray-200 dark:border-gray-700 pt-3"
          >
            <dt class="text-gray-400 mb-2">Attached documents</dt>
            <dd class="space-y-3">
              <div v-for="group in documentGroups(viewing)" :key="group.label">
                <p class="text-sm font-medium text-gray-800 dark:text-gray-200 mb-1">{{ group.label }}</p>
                <div class="flex flex-wrap gap-2">
                  <a
                    v-for="(file, index) in group.files"
                    :key="file"
                    :href="mediaUrl(file)"
                    target="_blank"
                    rel="noopener noreferrer"
                    :download="documentName(file, index)"
                    class="inline-flex items-center px-3 py-1.5 text-sm font-medium rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 dark:bg-blue-950/40 dark:text-blue-300"
                  >
                    Open / download: {{ documentName(file, index) }}
                  </a>
                </div>
              </div>
            </dd>
          </div>
        </dl>
        <div class="flex justify-end gap-2 pt-2">
          <button type="button" class="px-3 py-1.5 text-sm bg-blue-50 text-blue-700 rounded-lg" @click="openEdit(viewing); viewing = null">Edit</button>
          <button type="button" class="px-3 py-1.5 text-sm border rounded-lg" :disabled="pdfWorking" @click="openPdf({ id: viewing.id, name: viewing.registration_number })">PDF</button>
          <button type="button" class="px-3 py-1.5 text-sm border rounded-lg" :disabled="pdfWorking" @click="openPdf({ id: viewing.id, print: true })">Print</button>
          <button type="button" class="px-3 py-1.5 text-sm border rounded-lg" @click="viewing = null">Close</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import {
  ADMISSION_LEVELS,
  programsForLevel,
  parseAdmissionChoice,
  encodeAdmissionChoice,
} from '~/composables/useAdmissionPrograms'
import { formatRwandaAddress, useRwandaLocations } from '~/composables/useRwandaLocations'

definePageMeta({
  layout: 'admin',
  middleware: ['admin'],
  title: 'adminDash.modAdmissions',
  subtitle: 'adminDash.modAdmissionsDesc',
})

const { apiFetch, apiUrl, mediaUrl } = useApi()
const userStore = useUserStore()
const {
  locations: rwandaLocations,
  loadLocations,
  districtsFor,
} = useRwandaLocations()

const applications = ref([])
const loading = ref(true)
const saving = ref(false)
const error = ref('')
const success = ref('')
const statusFilter = ref('')
const levelFilter = ref('')
const search = ref('')
const showForm = ref(false)
const editingId = ref(null)
const viewing = ref(null)
const pdfWorking = ref(false)

const documentGroups = (application) => {
  const documents = application?.documents || {}
  const files = (value) => Array.isArray(value)
    ? value.filter((file) => typeof file === 'string' && file.trim() !== '')
    : []

  return [
    { label: 'Report or result slip', files: files(documents.bulletin) },
    { label: 'Other uploaded documents', files: files(documents.other) },
  ].filter((group) => group.files.length)
}

const documentName = (file, index) => {
  const name = String(file).split('/').pop()?.split('?')[0]
  return name || `Document ${index + 1}`
}

const emptyForm = () => ({
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
  message: '',
  status: 'pending',
})

const form = reactive(emptyForm())

const statusFilters = [
  { value: '', label: 'All' },
  { value: 'pending', label: 'Pending' },
  { value: 'reviewed', label: 'Reviewed' },
  { value: 'accepted', label: 'Accepted' },
  { value: 'rejected', label: 'Rejected' },
]

const choice = (app) => parseAdmissionChoice(app)

const appStatus = (app) => String(app?.status || '').trim().toLowerCase()

const setStatusFilter = (value) => {
  statusFilter.value = value
}

const setLevelFilter = (value) => {
  levelFilter.value = value
}

const visibleApplications = computed(() => {
  return applications.value.filter((app) => {
    if (statusFilter.value && appStatus(app) !== statusFilter.value) return false
    if (levelFilter.value && choice(app).level !== levelFilter.value) return false
    const q = search.value.trim().toLowerCase()
    if (!q) return true
    const hay = [
      app.registration_number,
      app.student_name,
      app.parent_name,
      app.parent_phone,
      app.parent_email,
      choice(app).level,
      choice(app).program,
      app.previous_school,
    ].join(' ').toLowerCase()
    return hay.includes(q)
  })
})

const statusClass = (status) => {
  const map = {
    pending: 'bg-amber-100 text-amber-800',
    reviewed: 'bg-blue-100 text-blue-800',
    accepted: 'bg-green-100 text-green-800',
    rejected: 'bg-red-100 text-red-800',
  }
  return map[String(status || '').trim().toLowerCase()] || 'bg-gray-100 text-gray-700'
}

const formatDate = (value) => {
  if (!value) return ''
  try {
    return new Date(value).toLocaleString()
  } catch {
    return value
  }
}

const formatShortDate = (value) => {
  if (!value) return '—'
  try {
    return new Date(value).toLocaleDateString()
  } catch {
    return value
  }
}

const assignForm = (data) => {
  Object.assign(form, emptyForm(), data)
}

const loadApps = async (silent = false) => {
  if (!silent) loading.value = true
  error.value = ''
  try {
    const res = await apiFetch('/api/admissions')
    applications.value = res?.data || []
  } catch (e) {
    if (!silent) {
      error.value = e?.data?.message || e?.message || 'Failed to load applications'
    }
  } finally {
    if (!silent) loading.value = false
  }
}

const openCreate = () => {
  editingId.value = null
  assignForm(emptyForm())
  showForm.value = true
}

const openView = (app) => {
  viewing.value = app
}

const openEdit = (app) => {
  const parsed = choice(app)
  editingId.value = app.id
  assignForm({
    student_name: app.student_name || '',
    date_of_birth: app.date_of_birth || '',
    gender: app.gender || '',
    level: parsed.level || '',
    program: parsed.program || '',
    previous_school: app.previous_school || '',
    parent_name: app.parent_name || '',
    parent_phone: app.parent_phone || '',
    parent_email: app.parent_email || '',
    province: app.province || '',
    district: app.district || '',
    message: app.message || '',
    status: app.status || 'pending',
  })
  showForm.value = true
}

const onFormLevelChange = () => {
  if (form.program && !programsForLevel(form.level).includes(form.program)) {
    form.program = ''
  }
}

const onFormProvinceChange = () => {
  if (form.district && !districtsFor(form.province).includes(form.district)) {
    form.district = ''
  }
}

const saveApplication = async () => {
  if (!form.student_name.trim() || !form.parent_name.trim() || !form.parent_phone.trim()) {
    error.value = 'Student name, parent name and phone are required'
    return
  }

  saving.value = true
  error.value = ''
  success.value = ''
  const payload = {
    ...form,
    program: encodeAdmissionChoice(form.level, form.program),
    address: formatRwandaAddress(form.district, form.province),
  }

  try {
    if (editingId.value) {
      await apiFetch(`/api/admissions/update?id=${editingId.value}`, {
        method: 'POST',
        body: payload,
      })
      success.value = 'Application updated'
    } else {
      const created = await apiFetch('/api/admissions', {
        method: 'POST',
        body: payload,
      })
      const id = created?.data?.id
      if (id && form.status && form.status !== 'pending') {
        await apiFetch(`/api/admissions/update?id=${id}`, {
          method: 'POST',
          body: { status: form.status, level: form.level, program: payload.program },
        })
      }
      success.value = 'Application added'
    }
    showForm.value = false
    notifyContentChanged(['admissions', 'notifications'])
    await loadApps(true)
  } catch (e) {
    error.value = e?.data?.message || e?.message || 'Failed to save application'
  } finally {
    saving.value = false
  }
}

const updateStatus = async (app) => {
  error.value = ''
  success.value = ''
  try {
    await apiFetch(`/api/admissions/update?id=${app.id}`, {
      method: 'POST',
      body: {
        status: app.status,
        level: choice(app).level,
        program: encodeAdmissionChoice(choice(app).level, choice(app).program) || app.program,
      },
    })
    success.value = `Updated ${app.student_name}`
    notifyContentChanged(['admissions'])
  } catch (e) {
    error.value = e?.data?.message || e?.message || 'Failed to update'
    await loadApps()
  }
}

const { confirmDialog } = useConfirmDialog()

const remove = async (app) => {
  const confirmed = await confirmDialog(`Delete application for ${app.student_name}?`, {
    title: 'Delete application',
    confirmText: 'Delete',
    danger: true,
  })
  if (!confirmed) return
  try {
    await apiFetch(`/api/admissions?id=${app.id}`, { method: 'DELETE' })
    success.value = 'Application deleted'
    notifyContentChanged(['admissions'])
    await loadApps(true)
  } catch (e) {
    error.value = e?.data?.message || e?.message || 'Failed to delete'
  }
}

const openPdf = async ({ id, name, print = false } = {}) => {
  pdfWorking.value = true
  error.value = ''
  try {
    userStore.hydrate()
    const params = new URLSearchParams()
    if (id) {
      params.set('id', String(id))
    } else {
      if (statusFilter.value) params.set('status', statusFilter.value)
      if (levelFilter.value) params.set('level', levelFilter.value)
      const q = search.value.trim()
      if (q) params.set('q', q)
    }
    if (print) params.set('inline', '1')

    const url = apiUrl(`/api/admissions/pdf${params.toString() ? `?${params}` : ''}`)
    const res = await fetch(url, {
      headers: {
        Authorization: `Bearer ${userStore.token || ''}`,
      },
    })

    if (!res.ok) {
      let message = 'Could not generate PDF'
      try {
        const data = await res.json()
        if (data?.message) message = data.message
      } catch {
        // binary error body
      }
      throw new Error(message)
    }

    const blob = await res.blob()
    const objectUrl = URL.createObjectURL(blob)
    if (print) {
      window.open(objectUrl, '_blank', 'noopener')
    } else {
      const disposition = res.headers.get('Content-Disposition') || ''
      const match = disposition.match(/filename="?([^"]+)"?/i)
      const fallback = name ? `${name}.pdf` : 'admission-applications.pdf'
      const link = document.createElement('a')
      link.href = objectUrl
      link.download = match?.[1] || fallback
      document.body.appendChild(link)
      link.click()
      link.remove()
    }
    setTimeout(() => URL.revokeObjectURL(objectUrl), 120000)
    success.value = print ? 'PDF opened for printing' : 'PDF downloaded'
  } catch (e) {
    error.value = e?.message || 'Could not generate PDF'
  } finally {
    pdfWorking.value = false
  }
}

const exportCsv = () => {
  const rows = [
    ['Registration', 'Student', 'Status', 'Level', 'Program', 'Parent', 'Phone', 'Email', 'District', 'Province', 'Submitted'],
    ...visibleApplications.value.map((app) => [
      app.registration_number,
      app.student_name,
      app.status,
      choice(app).level,
      choice(app).program,
      app.parent_name,
      app.parent_phone,
      app.parent_email,
      app.district,
      app.province,
      app.created_at,
    ]),
  ]
  const csv = rows.map((r) => r.map((v) => `"${String(v || '').replace(/"/g, '""')}"`).join(',')).join('\n')
  const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' })
  const url = URL.createObjectURL(blob)
  const link = document.createElement('a')
  link.href = url
  link.download = 'admission-applications.csv'
  link.click()
  URL.revokeObjectURL(url)
}

onContentChange(['admissions'], () => {
  loadApps(true)
})

onMounted(async () => {
  await Promise.all([loadApps(), loadLocations()])
})
</script>

<style scoped>
.field {
  @apply w-full px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-white;
}
.applicant-table {
  border-collapse: separate;
  border-spacing: 0;
  font-size: 0.8125rem;
}
.applicant-table th,
.applicant-table td {
  padding: 0.7rem 0.75rem;
  text-align: left;
  vertical-align: top;
  overflow-wrap: break-word;
  word-break: normal;
  white-space: normal;
}
.applicant-table th:last-child,
.applicant-table td:last-child {
  min-width: 13rem;
}
.applicant-head {
  background: #2563eb;
  color: #ffffff;
}
.applicant-head th {
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  font-size: 0.72rem;
}
.applicant-head th:first-child {
  border-radius: 0.6rem 0 0 0;
}
.applicant-head th:last-child {
  border-radius: 0 0.6rem 0 0;
}
.applicant-line {
  border-bottom: 1px solid #eef2f7;
}
.applicant-line:hover {
  background: #f8fafc;
}
:global(.dark) .applicant-head {
  background: #1d4ed8;
  color: #ffffff;
}
:global(.dark) .applicant-line {
  border-bottom-color: #1f2937;
}
:global(.dark) .applicant-line:hover {
  background: #111827;
}
</style>
