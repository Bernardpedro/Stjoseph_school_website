<template>
  <div class="max-w-3xl mx-auto space-y-6">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
      <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between flex-wrap gap-2">
        <h2 class="font-semibold text-gray-900">Send SMS</h2>
        <span class="text-xs font-semibold text-blue-700 bg-blue-50 px-2.5 py-1 rounded-lg">SMS Gateway</span>
      </div>

      <div class="p-5 space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">
            Recipient phone number(s)
            <span class="text-gray-400 font-normal">(one per line, or comma-separated, to send to multiple numbers)</span>
          </label>
          <textarea
            v-model="recipient"
            rows="3"
            placeholder="e.g. +250788254443&#10;+250788254444"
            :disabled="sending"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-y disabled:opacity-70"
          ></textarea>
          <p v-if="recipientList.length" class="mt-1 text-xs text-gray-500">
            {{ recipientList.length }} recipient{{ recipientList.length > 1 ? 's' : '' }} detected
          </p>
          <p v-if="invalidRecipients.length" class="mt-1 text-xs text-amber-600">
            These don't look like full phone numbers: {{ invalidRecipients.join(', ') }}
          </p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">
            Sender ID <span class="text-gray-400 font-normal">(leave blank to use your provider's default sender ID — some providers require one)</span>
          </label>
          <input
            v-model="senderId"
            type="text"
            placeholder="Leave blank for default"
            :disabled="sending"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent disabled:opacity-70"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Message</label>
          <textarea
            v-model="message"
            rows="5"
            placeholder="Type the message to send..."
            :disabled="sending"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-y disabled:opacity-70"
          ></textarea>
          <p class="mt-1 text-xs text-right" :class="isOverLimit ? 'text-red-600' : 'text-gray-400'">
            {{ charCount }} / {{ MAX_LEN }} characters · ~{{ segmentCount }} SMS segment{{ segmentCount > 1 ? 's' : '' }}
          </p>
        </div>

        <div v-if="message.trim()" class="rounded-lg border border-dashed border-blue-200 bg-blue-50/40 p-3">
          <p class="text-xs font-semibold text-blue-700 mb-1">Preview</p>
          <p class="text-xs font-medium text-gray-500 mb-1">
            To: {{ recipientList.length ? recipientList.join(', ') : '—' }}
          </p>
          <p class="text-sm text-gray-800 whitespace-pre-wrap break-words">{{ message }}</p>
        </div>

        <button
          type="button"
          :disabled="!canSend"
          class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold shadow-md shadow-blue-600/30 disabled:opacity-50 disabled:cursor-not-allowed"
          @click="sendMessage"
        >
          <span
            v-if="sending"
            class="inline-block h-4 w-4 rounded-full border-2 border-white border-t-transparent animate-spin"
          />
          <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
            <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
          </svg>
          {{ sending ? 'Sending…' : 'Send Message' }}
        </button>
      </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
      <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between flex-wrap gap-2">
        <h2 class="font-semibold text-gray-900">Bulk Upload (Excel / CSV)</h2>
        <button
          type="button"
          class="text-xs font-semibold text-blue-700 hover:text-blue-800"
          @click="showFormatGuide = !showFormatGuide"
        >
          {{ showFormatGuide ? 'Hide format guide' : 'Check required format' }}
        </button>
      </div>

      <div v-if="showFormatGuide" class="px-5 py-4 border-b border-gray-200 bg-blue-50/50 text-sm text-gray-700 space-y-3">
        <p>
          Upload a <strong>.xlsx</strong>, <strong>.xls</strong>, or <strong>.csv</strong> file. The first row must be
          column headers — the order of columns doesn't matter.
        </p>
        <ul class="list-disc pl-5 space-y-1">
          <li>
            A phone number column is always required. Accepted header names:
            <code class="text-xs bg-white border border-gray-200 rounded px-1 py-0.5">phone</code>,
            <code class="text-xs bg-white border border-gray-200 rounded px-1 py-0.5">phone number</code>,
            <code class="text-xs bg-white border border-gray-200 rounded px-1 py-0.5">number</code>,
            <code class="text-xs bg-white border border-gray-200 rounded px-1 py-0.5">recipient</code>, or
            <code class="text-xs bg-white border border-gray-200 rounded px-1 py-0.5">msisdn</code>.
          </li>
          <li>
            <strong>Same message for everyone:</strong> only the phone number column is used — type the shared
            message below the upload button. A message column, if present, is ignored.
          </li>
          <li>
            <strong>Different message per recipient:</strong> a message column is required too. Accepted header
            names:
            <code class="text-xs bg-white border border-gray-200 rounded px-1 py-0.5">message</code>,
            <code class="text-xs bg-white border border-gray-200 rounded px-1 py-0.5">text</code>,
            <code class="text-xs bg-white border border-gray-200 rounded px-1 py-0.5">sms</code>, or
            <code class="text-xs bg-white border border-gray-200 rounded px-1 py-0.5">sms message</code>.
          </li>
          <li>Rows with a blank phone number, or a blank message (in "different message" mode), are skipped.</li>
          <li>Up to 500 rows are processed per upload.</li>
        </ul>
        <div class="rounded-lg border border-gray-200 bg-white overflow-hidden">
          <table class="w-full text-xs">
            <thead>
              <tr class="bg-gray-50">
                <th class="text-left px-3 py-1.5 font-semibold text-gray-600">phone</th>
                <th class="text-left px-3 py-1.5 font-semibold text-gray-600">message</th>
              </tr>
            </thead>
            <tbody>
              <tr class="border-t border-gray-100">
                <td class="px-3 py-1.5">+250788254443</td>
                <td class="px-3 py-1.5 text-gray-400">(only needed for "different message" mode)</td>
              </tr>
              <tr class="border-t border-gray-100">
                <td class="px-3 py-1.5">+250788254444</td>
                <td class="px-3 py-1.5 text-gray-400">(only needed for "different message" mode)</td>
              </tr>
            </tbody>
          </table>
        </div>
        <button
          type="button"
          class="text-xs font-semibold text-blue-700 hover:text-blue-800 underline"
          @click="downloadSampleTemplate"
        >
          Download a sample template ({{ importMode === 'different' ? 'different message' : 'same message' }} mode)
        </button>
      </div>

      <div class="p-5 space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Message mode</label>
          <div class="flex items-center gap-1 bg-gray-100 rounded-lg p-1 w-fit">
            <button
              type="button"
              class="px-3 py-1.5 rounded-md text-xs font-semibold transition-colors"
              :class="importMode === 'same' ? 'bg-white text-blue-700 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
              @click="importMode = 'same'"
            >
              Same message for everyone
            </button>
            <button
              type="button"
              class="px-3 py-1.5 rounded-md text-xs font-semibold transition-colors"
              :class="importMode === 'different' ? 'bg-white text-blue-700 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
              @click="importMode = 'different'"
            >
              Different message per recipient
            </button>
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">File (.xlsx, .xls, or .csv)</label>
          <input
            ref="importFileInput"
            type="file"
            accept=".xlsx,.xls,.csv"
            :disabled="importing"
            class="w-full text-sm text-gray-700 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 disabled:opacity-70"
            @change="onImportFileChange"
          />
          <p v-if="importFile" class="mt-1 text-xs text-gray-500">Selected: {{ importFile.name }}</p>
        </div>

        <div v-if="importMode === 'same'">
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Message to send to everyone in the file</label>
          <textarea
            v-model="importMessage"
            rows="4"
            placeholder="Type the shared message to send to every phone number in the file..."
            :disabled="importing"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-y disabled:opacity-70"
          ></textarea>
          <p class="mt-1 text-xs text-right" :class="importMessage.length > MAX_LEN ? 'text-red-600' : 'text-gray-400'">
            {{ importMessage.length }} / {{ MAX_LEN }} characters
          </p>
        </div>
        <p v-else class="text-xs text-gray-500">
          Each row's own "message" column will be sent to that row's phone number.
        </p>

        <button
          type="button"
          :disabled="!canImport"
          class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold shadow-md shadow-blue-600/30 disabled:opacity-50 disabled:cursor-not-allowed"
          @click="uploadImport"
        >
          <span
            v-if="importing"
            class="inline-block h-4 w-4 rounded-full border-2 border-white border-t-transparent animate-spin"
          />
          {{ importing ? 'Uploading…' : 'Upload & Send' }}
        </button>
      </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
      <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between flex-wrap gap-2">
        <h2 class="font-semibold text-gray-900">Recent Messages</h2>
        <div class="flex items-center gap-1 bg-gray-100 rounded-lg p-1">
          <button
            v-for="option in statusFilterOptions"
            :key="option.value"
            type="button"
            class="px-3 py-1 rounded-md text-xs font-semibold transition-colors"
            :class="statusFilter === option.value ? 'bg-white text-blue-700 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
            @click="statusFilter = option.value"
          >
            {{ option.label }}
          </button>
        </div>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b border-gray-200">
              <th class="text-left py-2.5 px-4 text-xs font-semibold uppercase tracking-wide text-gray-500">Recipient</th>
              <th class="text-left py-2.5 px-4 text-xs font-semibold uppercase tracking-wide text-gray-500">Message</th>
              <th class="text-left py-2.5 px-4 text-xs font-semibold uppercase tracking-wide text-gray-500">Status</th>
              <th class="text-left py-2.5 px-4 text-xs font-semibold uppercase tracking-wide text-gray-500">Sent</th>
              <th class="text-right py-2.5 px-4 text-xs font-semibold uppercase tracking-wide text-gray-500"></th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="loadingHistory">
              <td colspan="5" class="text-center text-gray-400 py-10">Loading…</td>
            </tr>
            <tr v-else-if="history.length === 0">
              <td colspan="5" class="text-center text-gray-400 py-10">
                {{ statusFilter === 'all' ? 'No messages sent yet.' : `No ${statusFilter} messages.` }}
              </td>
            </tr>
            <tr v-for="row in history" :key="row.id" class="border-b border-gray-100 hover:bg-gray-50">
              <td class="py-2.5 px-4 whitespace-nowrap text-gray-700">{{ row.recipient }}</td>
              <td class="py-2.5 px-4 text-gray-700 max-w-xs">
                <span class="line-clamp-2">{{ row.message }}</span>
              </td>
              <td class="py-2.5 px-4">
                <span class="px-2 py-0.5 rounded text-xs font-semibold capitalize" :class="statusClass(row.status)">
                  {{ row.status }}
                </span>
                <p v-if="row.status === 'failed' && row.error_message" class="mt-1 text-xs text-red-600 max-w-[220px]">
                  {{ row.error_message }}
                </p>
              </td>
              <td class="py-2.5 px-4 whitespace-nowrap text-gray-400">{{ formatDate(row.created_at) }}</td>
              <td class="py-2.5 px-4 text-right">
                <button
                  type="button"
                  :disabled="deletingId === row.id"
                  class="text-xs font-semibold text-red-600 hover:text-red-700 disabled:opacity-50"
                  @click="deleteMessage(row)"
                >
                  {{ deletingId === row.id ? 'Deleting…' : 'Delete' }}
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'

const { apiFetch } = useApi()
const toast = useAppToast()

definePageMeta({
  layout: 'admin',
  middleware: ['admin'],
  title: 'adminDash.modSms',
  subtitle: 'adminDash.modSmsDesc',
})

const MAX_LEN = 918 // matches the backend ceiling (~6 GSM-7 segments)

const recipient = ref('')
const message = ref('')
const senderId = ref('')
const sending = ref(false)

const showFormatGuide = ref(false)
const importMode = ref('same')
const importMessage = ref('')
const importFile = ref(null)
const importFileInput = ref(null)
const importing = ref(false)

const history = ref([])
const loadingHistory = ref(true)
const deletingId = ref(null)
const statusFilter = ref('all')
const statusFilterOptions = [
  { value: 'all', label: 'All' },
  { value: 'sent', label: 'Sent' },
  { value: 'failed', label: 'Failed' },
]

const charCount = computed(() => message.value.length)
const segmentCount = computed(() => Math.max(1, Math.ceil(charCount.value / 153) || 1))
const isOverLimit = computed(() => charCount.value > MAX_LEN)

const recipientList = computed(() => {
  const seen = new Set()
  return recipient.value
    .split(/[,;\n\r]+/)
    .map((part) => part.trim())
    .filter((part) => part !== '')
    .filter((part) => {
      if (seen.has(part)) return false
      seen.add(part)
      return true
    })
})

const invalidRecipients = computed(() =>
  recipientList.value.filter((r) => r.replace(/[^0-9+]/g, '').replace('+', '').length < 9)
)

const canSend = computed(() =>
  recipientList.value.length > 0 &&
  invalidRecipients.value.length === 0 &&
  message.value.trim() !== '' &&
  !isOverLimit.value &&
  !sending.value
)

const canImport = computed(() =>
  !!importFile.value &&
  !importing.value &&
  (importMode.value === 'different' || (importMessage.value.trim() !== '' && importMessage.value.length <= MAX_LEN))
)

const onImportFileChange = (e) => {
  importFile.value = e.target.files?.[0] || null
}

const downloadSampleTemplate = () => {
  if (!import.meta.client) return

  const rows = importMode.value === 'different'
    ? [
        ['phone', 'message'],
        ['+250788254443', 'Hello! Your fees balance is due on 30th.'],
        ['+250788254444', 'Hello! Please collect your report card.'],
      ]
    : [
        ['phone'],
        ['+250788254443'],
        ['+250788254444'],
      ]

  const csv = rows.map((row) => row.map((cell) => `"${String(cell).replace(/"/g, '""')}"`).join(',')).join('\r\n')
  const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' })
  const url = URL.createObjectURL(blob)
  const link = document.createElement('a')
  link.href = url
  link.download = `sms-${importMode.value}-message-template.csv`
  link.style.display = 'none'
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
  URL.revokeObjectURL(url)
}

const loadHistory = async () => {
  loadingHistory.value = true
  try {
    const query = { limit: 50 }
    if (statusFilter.value !== 'all') {
      query.status = statusFilter.value
    }
    const res = await apiFetch('/api/sms', { query })
    history.value = res?.data || []
  } catch (err) {
    console.error('Failed to load SMS history:', err)
  } finally {
    loadingHistory.value = false
  }
}

watch(statusFilter, () => {
  loadHistory()
})

onMounted(() => {
  loadHistory()
})

const uploadImport = async () => {
  if (!importFile.value) {
    toast.error('Choose a file to upload first.')
    return
  }
  if (importMode.value === 'same' && !importMessage.value.trim()) {
    toast.error('Message text is required.')
    return
  }
  if (importMode.value === 'same' && importMessage.value.length > MAX_LEN) {
    toast.error(`Message is too long (${importMessage.value.length}/${MAX_LEN} characters).`)
    return
  }

  importing.value = true
  try {
    const form = new FormData()
    form.append('file', importFile.value)
    form.append('mode', importMode.value)
    if (importMode.value === 'same') {
      form.append('message', importMessage.value.trim())
    }
    if (senderId.value.trim()) {
      form.append('sender_id', senderId.value.trim())
    }

    const res = await apiFetch('/api/sms/import', {
      method: 'POST',
      body: form,
    })
    toast.success(res?.message || 'Messages sent.')
    importFile.value = null
    if (importFileInput.value) importFileInput.value.value = ''
    importMessage.value = ''
  } catch (err) {
    toast.error(err?.data?.message || err?.message || 'Could not process the file.')
  } finally {
    importing.value = false
    await loadHistory()
  }
}

const deleteMessage = async (row) => {
  if (!import.meta.client || !window.confirm(`Delete this message to ${row.recipient}?`)) {
    return
  }

  deletingId.value = row.id
  try {
    await apiFetch(`/api/sms?id=${row.id}`, { method: 'DELETE' })
    history.value = history.value.filter((r) => r.id !== row.id)
    toast.success('Message deleted.')
  } catch (err) {
    toast.error(err?.data?.message || err?.message || 'Could not delete the message.')
  } finally {
    deletingId.value = null
  }
}

const statusClass = (status) => {
  if (status === 'failed') return 'bg-red-100 text-red-700'
  if (['sent', 'queued', 'delivered', 'success'].includes(status)) return 'bg-green-100 text-green-700'
  return 'bg-amber-100 text-amber-700'
}

const formatDate = (value) => {
  if (!value) return '—'
  const date = new Date(value)
  if (Number.isNaN(date.getTime())) return value
  return date.toLocaleString()
}

const sendMessage = async () => {
  if (recipientList.value.length === 0) {
    toast.error('At least one recipient phone number is required.')
    return
  }
  if (invalidRecipients.value.length > 0) {
    toast.error('One or more phone numbers look too short — double-check them.')
    return
  }
  if (!message.value.trim()) {
    toast.error('Message text is required.')
    return
  }
  if (isOverLimit.value) {
    toast.error(`Message is too long (${charCount.value}/${MAX_LEN} characters).`)
    return
  }

  sending.value = true
  try {
    const res = await apiFetch('/api/sms', {
      method: 'POST',
      body: {
        recipients: recipientList.value,
        message: message.value.trim(),
        sender_id: senderId.value.trim() || undefined,
      },
    })
    toast.success(res?.message || 'Message sent.')
    message.value = ''
  } catch (err) {
    toast.error(err?.data?.message || err?.message || 'Could not send the message.')
  } finally {
    sending.value = false
    await loadHistory()
  }
}
</script>
