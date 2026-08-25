<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900 p-6">
    <div class="max-w-7xl mx-auto">
      <!-- Header -->
      <div class="flex justify-between items-center mb-8">
        <div>
          <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Documents Management</h1>
          <p class="text-gray-500 dark:text-gray-400 mt-2">Upload and manage school documents</p>
        </div>
        <button 
          @click="showUploadModal = true"
          class="flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors shadow-lg hover:shadow-xl"
        >
          <span v-html="icons.plus" class="w-5 h-5"></span>
          Upload Document
        </button>
      </div>

      <!-- Documents Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div 
          v-for="doc in documents" 
          :key="doc.id"
          class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-2xl transition-all group"
        >
          <div class="p-6">
            <div class="flex items-start justify-between mb-4">
              <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                <span v-html="icons.fileText" class="w-8 h-8 text-blue-600 dark:text-blue-400"></span>
              </div>
              <span class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs rounded-full font-medium">
                {{ doc.type }}
              </span>
            </div>
            
            <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-2 truncate">{{ doc.name }}</h4>
            <p class="text-gray-500 dark:text-gray-400 text-sm mb-4 line-clamp-2">{{ doc.description }}</p>
            
            <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400 mb-4">
              <span>{{ doc.date }}</span>
              <span>{{ doc.size }}</span>
            </div>

            <div class="flex items-center gap-2">
              <button 
                @click="viewDocument(doc)"
                class="flex-1 flex items-center justify-center gap-2 px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition-colors"
              >
                <span v-html="icons.eye" class="w-4 h-4"></span>
                View
              </button>
              <button 
                @click="editDocument(doc)"
                class="px-4 py-2 bg-blue-50 dark:bg-blue-900/30 hover:bg-blue-100 dark:hover:bg-blue-900/50 text-blue-600 dark:text-blue-400 rounded-lg transition-colors"
              >
                <span v-html="icons.edit" class="w-4 h-4"></span>
              </button>
              <button 
                @click="deleteDocument(doc.id)"
                class="px-4 py-2 bg-red-50 dark:bg-red-900/30 hover:bg-red-100 dark:hover:bg-red-900/50 text-red-600 dark:text-red-400 rounded-lg transition-colors"
              >
                <span v-html="icons.trash" class="w-4 h-4"></span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Upload Modal -->
    <div 
      v-if="showUploadModal"
      class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
      @click.self="showUploadModal = false"
    >
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
          <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Upload New Document</h3>
          <button 
            @click="showUploadModal = false"
            class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg"
          >
            <span v-html="icons.x" class="w-6 h-6 text-gray-500"></span>
          </button>
        </div>

        <div class="p-6 space-y-6">
          <!-- File Upload Area -->
          <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-12 text-center hover:border-blue-500 transition-colors cursor-pointer">
            <span v-html="icons.upload" class="w-16 h-16 text-gray-400 mx-auto mb-4"></span>
            <p class="text-gray-900 dark:text-white font-medium mb-2">Click to upload or drag and drop</p>
            <p class="text-gray-500 dark:text-gray-400 text-sm">PDF, DOC, DOCX up to 10MB</p>
          </div>

          <!-- Form Fields -->
          <div class="space-y-4">
            <div>
              <label class="block text-gray-700 dark:text-gray-300 font-medium mb-2">Document Name</label>
              <input 
                v-model="newDoc.name"
                type="text" 
                placeholder="Enter document name"
                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
              />
            </div>

            <div>
              <label class="block text-gray-700 dark:text-gray-300 font-medium mb-2">Description</label>
              <textarea 
                v-model="newDoc.description"
                rows="4"
                placeholder="Enter document description"
                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
              ></textarea>
            </div>

            <div>
              <label class="block text-gray-700 dark:text-gray-300 font-medium mb-2">Document Type</label>
              <select 
                v-model="newDoc.type"
                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
              >
                <option value="PDF">PDF</option>
                <option value="DOC">DOC</option>
                <option value="DOCX">DOCX</option>
                <option value="XLS">XLS</option>
                <option value="XLSX">XLSX</option>
              </select>
            </div>
          </div>
        </div>

        <div class="p-6 border-t border-gray-200 dark:border-gray-700 flex items-center justify-end gap-3">
          <button 
            @click="showUploadModal = false"
            class="px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 font-medium transition-colors"
          >
            Cancel
          </button>
          <button 
            @click="handleUpload"
            class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors flex items-center gap-2"
          >
            <span v-html="icons.save" class="w-5 h-5"></span>
            Upload Document
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

// Icon definitions
const icons = {
  plus: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>',
  fileText: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>',
  eye: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>',
  edit: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>',
  trash: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>',
  x: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>',
  save: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>',
  upload: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>'
};

const showUploadModal = ref(false);
const documents = ref([
  { id: 1, name: 'School Policy 2025', description: 'Updated school policies and regulations for the academic year 2025', type: 'PDF', date: '2025-01-15', size: '2.5 MB' },
  { id: 2, name: 'Event Guidelines', description: 'Comprehensive guidelines for organizing school events', type: 'PDF', date: '2025-01-10', size: '1.8 MB' },
  { id: 3, name: 'Admission Form', description: 'Student admission application form', type: 'DOCX', date: '2025-01-08', size: '856 KB' },
  { id: 4, name: 'Academic Calendar', description: '2025 academic year calendar with important dates', type: 'PDF', date: '2025-01-05', size: '1.2 MB' },
  { id: 5, name: 'Teacher Handbook', description: 'Guidelines and policies for teaching staff', type: 'PDF', date: '2025-01-03', size: '3.4 MB' },
  { id: 6, name: 'Student Code of Conduct', description: 'Rules and expectations for student behavior', type: 'PDF', date: '2025-01-01', size: '1.5 MB' },
]);

const newDoc = ref({
  name: '',
  description: '',
  type: 'PDF'
});

const handleUpload = () => {
  if (newDoc.value.name && newDoc.value.description) {
    const doc = {
      id: documents.value.length + 1,
      ...newDoc.value,
      date: new Date().toISOString().split('T')[0],
      size: '0 MB'
    };
    documents.value = [...documents.value, doc];
    newDoc.value = { name: '', description: '', type: 'PDF' };
    showUploadModal.value = false;
  }
};

const viewDocument = (doc) => {

};

const editDocument = (doc) => {
  console.log('Editing document:', doc);
};

const deleteDocument = (id) => {
  if (confirm('Are you sure you want to delete this document?')) {
    documents.value = documents.value.filter(doc => doc.id !== id);
  }
};
</script>