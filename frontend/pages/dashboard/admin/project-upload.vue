<script setup>
import { ref, onMounted } from 'vue';

const { apiFetch, mediaUrl } = useApi()
const userStore = useUserStore()

definePageMeta({
  layout: 'default',
  middleware: ['admin']
})

// Form state
const showUploadModal = ref(false);
const uploadedProjects = ref([]); // Store uploaded projects
const editingProject = ref(null); // Track which project is being edited
const isLoading = ref(false); // Loading state for API calls
const settingsSaving = ref(false);
const settingsMessage = ref('');
const partnerSettings = reactive({
  title: '',
  description: '',
});
const newProject = ref({
  title: '',
  description: '',
  partner: 'Partnerschaftsverein Rheinland-Pfalz',
  year: '',
  status: 'Ongoing',
  category: 'education'
});

const imagePreviewUrls = ref([]);
const imageFiles = ref([]);
const imageCaptions = ref([]);
const existingMedia = ref([]);

const videoPreviewUrls = ref([]);
const videoFiles = ref([]);
const videoCaptions = ref([]);
const videoThumbnails = ref([]);
const videoThumbnailPreviewUrls = ref([]);

// Project categories
const categories = [
  { value: 'education', label: 'Education' },
  { value: 'technology', label: 'Technology' },
  { value: 'infrastructure', label: 'Infrastructure' },
  { value: 'sanitation', label: 'Sanitation' },
  { value: 'scholarships', label: 'Scholarships' },
  { value: 'sports', label: 'Sports' },
  { value: 'health', label: 'Health' }
];

// Project statuses
const statuses = [
  { value: 'Completed', label: 'Completed' },
  { value: 'Ongoing', label: 'Ongoing' },
  { value: 'Upcoming', label: 'Upcoming' }
];

// Fetch projects from API when component mounts
onContentChange(['projects', 'settings'], () => {
  fetchProjects()
  fetchSettings()
})

onMounted(async () => {
  userStore.hydrate()
  await Promise.all([fetchProjects(), fetchSettings()]);
});

const fetchSettings = async () => {
  try {
    const res = await apiFetch('/api/projects/settings')
    Object.assign(partnerSettings, res?.data || {})
  } catch (error) {
    console.error('Error fetching partner settings:', error)
  }
}

const saveSettings = async () => {
  if (!partnerSettings.title.trim()) {
    alert('Section title is required')
    return
  }
  settingsSaving.value = true
  settingsMessage.value = ''
  try {
    await apiFetch('/api/projects/settings', {
      method: 'POST',
      body: {
        title: partnerSettings.title.trim(),
        description: partnerSettings.description.trim(),
      },
    })
    settingsMessage.value = 'Homepage section title saved'
    notifyContentChanged(['settings', 'projects'])
  } catch (e) {
    alert(e?.data?.message || e?.message || 'Failed to save section settings')
  } finally {
    settingsSaving.value = false
  }
}

const fetchProjects = async () => {
  try {
    isLoading.value = true;
    const data = await apiFetch('/api/projects')
    uploadedProjects.value = data?.data || [];
  } catch (error) {
    console.error('Error fetching projects:', error);
    alert('Failed to fetch projects. Please try again later.');
  } finally {
    isLoading.value = false;
  }
};

// Handle image file upload
const handleImageUpload = (e) => {
  const files = Array.from(e.target.files);
  
  if (files.length > 0) {
    files.forEach(file => {
      imageFiles.value.push(file);
      const url = URL.createObjectURL(file);
      imagePreviewUrls.value.push(url);
      imageCaptions.value.push(''); // Add empty caption for each image
    });
  }
};

// Handle video file upload
const handleVideoUpload = (e) => {
  const files = Array.from(e.target.files);
  
  if (files.length > 0) {
    files.forEach(file => {
      videoFiles.value.push(file);
      const url = URL.createObjectURL(file);
      videoPreviewUrls.value.push(url);
      videoCaptions.value.push(''); // Add empty caption for each video
      videoThumbnails.value.push(null); // Add empty thumbnail slot
      videoThumbnailPreviewUrls.value.push(null);
    });
  }
};

// Handle video thumbnail upload
const handleVideoThumbnailUpload = (e, index) => {
  const file = e.target.files[0];
  
  if (file) {
    videoThumbnails.value[index] = file;
    const url = URL.createObjectURL(file);
    videoThumbnailPreviewUrls.value[index] = url;
  }
};

// Remove an image
const removeImage = (index) => {
  const url = imagePreviewUrls.value[index]
  const existingIndex = existingMedia.value.findIndex((item) =>
    item.type === 'image' && (item.url === url || mediaUrl(item.url) === url)
  )
  if (existingIndex !== -1) {
    existingMedia.value.splice(existingIndex, 1)
  } else {
    const newIndex = imagePreviewUrls.value.slice(0, index).filter((preview) => String(preview).startsWith('blob:')).length
    imageFiles.value.splice(newIndex, 1)
  }
  imagePreviewUrls.value.splice(index, 1);
  imageCaptions.value.splice(index, 1);
};

// Remove a video
const removeVideo = (index) => {
  const url = videoPreviewUrls.value[index]
  const existingIndex = existingMedia.value.findIndex((item) =>
    item.type === 'video' && (item.url === url || mediaUrl(item.url) === url)
  )
  if (existingIndex !== -1) {
    existingMedia.value.splice(existingIndex, 1)
  } else {
    const newIndex = videoPreviewUrls.value.slice(0, index).filter((preview) => String(preview).startsWith('blob:')).length
    videoFiles.value.splice(newIndex, 1)
    videoThumbnails.value.splice(newIndex, 1)
  }
  videoPreviewUrls.value.splice(index, 1);
  videoCaptions.value.splice(index, 1);
  videoThumbnailPreviewUrls.value.splice(index, 1);
};

// Create FormData for API submission
const createFormData = () => {
  const formData = new FormData();
  
  // Append all text fields
  formData.append('title', newProject.value.title);
  formData.append('description', newProject.value.description);
  formData.append('partner', newProject.value.partner);
  formData.append('year', newProject.value.year);
  formData.append('status', newProject.value.status);
  formData.append('category', newProject.value.category);
  formData.append('existingMedia', JSON.stringify(existingMedia.value));
  
  // Append images
  if (imageFiles.value.length > 0) {
    imageFiles.value.forEach((file) => {
      formData.append('images', file);
    });
    
    // Append image captions
    imageCaptions.value.forEach((caption) => {
      formData.append('captions', caption || '');
    });
  }
  
  // Append videos
  if (videoFiles.value.length > 0) {
    videoFiles.value.forEach((file) => {
      formData.append('videos', file);
    });
    
    // Append video captions
    videoCaptions.value.forEach((caption) => {
      formData.append('videoCaptions', caption || '');
    });
    
    // Append video thumbnails
    videoThumbnails.value.forEach((thumbnail) => {
      if (thumbnail) {
        formData.append('videoThumbnails', thumbnail);
      } else {
        // Send empty file if no thumbnail
        formData.append('videoThumbnails', new File([], ''));
      }
    });
  }
  
  return formData;
};

// Handle form submission
const handleUpload = async () => {
  // Validate form
  if (!newProject.value.title || !newProject.value.description || !newProject.value.partner || !newProject.value.year) {
    alert('Please fill in all required fields');
    return;
  }
  
  if (imagePreviewUrls.value.length === 0 && videoPreviewUrls.value.length === 0 && !editingProject.value) {
    alert('Please upload at least one image or video');
    return;
  }
  
  try {
    isLoading.value = true;
    
    // Create FormData for submission
    const formData = createFormData();
    
    if (editingProject.value) {
      await apiFetch(`/api/projects/update?id=${editingProject.value.id}`, {
        method: 'POST',
        body: formData,
      })
    } else {
      await apiFetch('/api/projects', {
        method: 'POST',
        body: formData,
      })
    }
    
    notifyContentChanged(['projects'])
    // Refresh the projects list
    await fetchProjects();
    
    // Show success message BEFORE resetting form
    alert(editingProject.value ? 'Project updated successfully!' : 'Project uploaded successfully!');
    
    // Reset form and close modal
    resetForm();
    showUploadModal.value = false;
    editingProject.value = null;
    
  } catch (error) {
    console.error('Error saving project:', error);
    alert(`Failed to save project: ${error?.data?.message || error?.message || 'Unknown error'}`);
  } finally {
    isLoading.value = false;
  }
};

// Delete a project
const deleteProject = async (projectId) => {
  if (!confirm('Are you sure you want to delete this project?')) {
    return;
  }
  
  try {
    isLoading.value = true;
    
    await apiFetch(`/api/projects?id=${projectId}`, { method: 'DELETE' })
    
    // Remove from local state
    uploadedProjects.value = uploadedProjects.value.filter(project => project.id !== projectId);
    notifyContentChanged(['projects'])
    
    alert('Project deleted successfully!');
  } catch (error) {
    console.error('Error deleting project:', error);
    alert(`Failed to delete project: ${error?.data?.message || error?.message || 'Unknown error'}`);
  } finally {
    isLoading.value = false;
  }
};

// Edit a project
const editProject = (project) => {
  resetForm()
  editingProject.value = project;
  
  // Populate form with project data
  newProject.value = {
    title: project.title,
    description: project.description,
    partner: project.partner,
    year: project.year,
    status: project.status,
    category: project.category
  };
  
  // Set preview URLs and captions if available
  existingMedia.value = (project.media || []).map((item) => ({
    type: item.type,
    url: item.url,
    caption: item.caption || '',
    thumbnail: item.thumbnail || '',
  }))

  if (project.media && project.media.length > 0) {
    project.media.forEach(media => {
      if (media.type === 'image') {
        imagePreviewUrls.value.push(mediaUrl(media.url));
        imageCaptions.value.push(media.caption || '');
      } else if (media.type === 'video') {
        videoPreviewUrls.value.push(mediaUrl(media.url));
        videoCaptions.value.push(media.caption || '');
        videoThumbnailPreviewUrls.value.push(media.thumbnail ? mediaUrl(media.thumbnail) : null);
      }
    });
  }
  
  // Show the modal
  showUploadModal.value = true;
};

// Reset form
const resetForm = () => {
  newProject.value = {
    title: '',
    description: '',
    partner: 'Partnerschaftsverein Rheinland-Pfalz',
    year: '',
    status: 'Ongoing',
    category: 'education'
  };
  imagePreviewUrls.value = [];
  imageFiles.value = [];
  imageCaptions.value = [];
  videoPreviewUrls.value = [];
  videoFiles.value = [];
  videoCaptions.value = [];
  videoThumbnails.value = [];
  videoThumbnailPreviewUrls.value = [];
  existingMedia.value = [];
};

// Handle image error
const handleImageError = (event) => {
  console.error('Image failed to load:', event.target.src);
  event.target.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="400" height="300"%3E%3Crect fill="%23ddd" width="400" height="300"/%3E%3Ctext fill="%23999" x="50%25" y="50%25" text-anchor="middle" dy=".3em"%3EImage not available%3C/text%3E%3C/svg%3E';
  event.target.onerror = null;
};
</script>

<template>
  <div class="min-h-screen bg-gray-50 p-6">
    <!-- Loading indicator -->
    <div v-if="isLoading" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white p-6 rounded-lg shadow-lg">
        <div class="flex items-center">
          <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-blue-600"></div>
          <span class="ml-3 text-lg">Processing...</span>
        </div>
      </div>
    </div>

    <div class="max-w-7xl mx-auto">
      <!-- Header -->
      <div class="mb-8 flex justify-between items-center">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">Partner Projects</h1>
          <p class="text-gray-600 mt-1">Manage Projects Financed By Partnerschaftsverein Rheinland-Pfalz</p>
        </div>
        <button 
          @click="showUploadModal = true; resetForm();"
          class="flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-medium transition-all shadow-md shadow-blue-600/40 hover:shadow-lg hover:shadow-blue-500/50 transform hover:-translate-y-0.5"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
          </svg>
          Add New Project
        </button>
      </div>

      <!-- Homepage section settings -->
      <div class="mb-8 bg-white rounded-xl border border-gray-200 p-5 space-y-3">
        <h2 class="text-lg font-semibold text-gray-900">Homepage section</h2>
        <p class="text-sm text-gray-500">Title and description shown under “Projects Financed By…” on the home page</p>
        <input
          v-model="partnerSettings.title"
          type="text"
          class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
          placeholder="Section title"
        />
        <textarea
          v-model="partnerSettings.description"
          rows="3"
          class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
          placeholder="Section description"
        />
        <div class="flex items-center gap-3">
          <button
            type="button"
            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm"
            :disabled="settingsSaving"
            @click="saveSettings"
          >
            {{ settingsSaving ? 'Saving...' : 'Save section text' }}
          </button>
          <span v-if="settingsMessage" class="text-sm text-green-600">{{ settingsMessage }}</span>
        </div>
      </div>

      <!-- Display Uploaded Projects -->
      <div v-if="uploadedProjects.length > 0">
        <h3 class="text-xl font-bold text-gray-900 mb-4">All Projects ({{ uploadedProjects.length }})</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div v-for="project in uploadedProjects" :key="project.id" class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200 hover:shadow-lg transition-shadow">
            <!-- Project Image -->
            <div class="h-48 overflow-hidden bg-gray-100">
              <img 
                v-if="project.media && project.media.length > 0 && project.media[0].type === 'image'"
                :src="mediaUrl(project.media[0].url)" 
                :alt="project.title"
                class="w-full h-full object-cover"
                @error="handleImageError"
              />
              <div v-else class="w-full h-full flex items-center justify-center bg-gray-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
              </div>
            </div>
            
            <!-- Project Details -->
            <div class="p-4">
              <div class="flex justify-between items-start mb-2">
                <h4 class="text-lg font-semibold text-gray-900 line-clamp-1">{{ project.title }}</h4>
                <span 
                  class="px-2 py-1 text-xs font-medium rounded-full whitespace-nowrap ml-2"
                  :class="{
                    'bg-green-100 text-green-800': project.status === 'Completed',
                    'bg-yellow-100 text-yellow-800': project.status === 'Ongoing',
                    'bg-blue-100 text-blue-800': project.status === 'Upcoming'
                  }"
                >
                  {{ project.status_label || project.status }}
                </span>
              </div>
              
              <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ project.description }}</p>
              
              <div class="space-y-2 text-sm text-gray-500">
                <div class="flex items-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                  </svg>
                  <span class="line-clamp-1">{{ project.partner }}</span>
                </div>
                
                <div class="flex items-center justify-between">
                  <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    {{ project.year }}
                  </div>
                  
                  <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs font-medium rounded-full">
                    {{ categories.find(c => c.value === project.category)?.label || project.category }}
                  </span>
                </div>
                
                <div v-if="project.media && project.media.length > 0" class="flex items-center text-xs">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                  {{ project.media.length }} media file(s)
                </div>
              </div>
              
              <!-- Action Buttons -->
              <div class="mt-4 flex gap-2">
                <button 
                  @click="editProject(project)"
                  class="flex-1 flex items-center justify-center gap-1 px-3 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors text-sm font-medium"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                  </svg>
                  Edit
                </button>
                <button 
                  @click="deleteProject(project.id)"
                  class="flex-1 flex items-center justify-center gap-1 px-3 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors text-sm font-medium"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                  </svg>
                  Delete
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-16">
        <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-24 w-24 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
        </svg>
        <h3 class="mt-4 text-lg font-medium text-gray-900">No projects yet</h3>
        <p class="mt-2 text-gray-500">Get started by adding your first project.</p>
      </div>
    </div>

    <!-- Upload Modal -->
    <div 
      v-if="showUploadModal"
      class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4"
      @click.self="showUploadModal = false; editingProject = null;"
    >
      <div class="bg-white rounded-2xl shadow-2xl max-w-5xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between sticky top-0 bg-white z-10">
          <h3 class="text-2xl font-bold text-gray-900">
            {{ editingProject ? 'Edit Project' : 'Add New Project' }}
          </h3>
          <button 
            @click="showUploadModal = false; editingProject = null; resetForm();"
            class="p-2 hover:bg-gray-100 rounded-lg transition-colors"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <div class="p-6 space-y-6">
          <!-- Image Upload Section -->
          <div>
            <label class="block text-gray-700 font-medium mb-3">Project Images</label>
            
            <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-blue-500 transition-colors cursor-pointer bg-gray-50">
              <input
                type="file"
                accept="image/*"
                multiple
                @change="handleImageUpload"
                class="hidden"
                id="image-upload"
              />
              <label for="image-upload" class="cursor-pointer">
                <div v-if="imagePreviewUrls.length === 0">
                  <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                  <p class="mt-2 text-sm text-gray-600">
                    <span class="font-medium text-blue-600 hover:text-blue-500">Click to upload images</span>
                  </p>
                </div>
                <div v-else>
                  <p class="text-sm text-gray-600 mb-4">{{ imagePreviewUrls.length }} image(s) selected</p>
                  <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <div v-for="(url, index) in imagePreviewUrls" :key="index" class="relative group">
                      <img :src="url" alt="Preview" class="h-32 w-full object-cover rounded-lg" />
                      <button 
                        @click.prevent="removeImage(index)"
                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1.5 opacity-0 group-hover:opacity-100 transition-opacity shadow-lg"
                      >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                          <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                      </button>
                      <input 
                        v-model="imageCaptions[index]"
                        type="text"
                        placeholder="Add caption (optional)"
                        class="mt-2 w-full px-2 py-1 text-xs border border-gray-300 rounded focus:ring-1 focus:ring-blue-500 focus:border-transparent"
                      />
                    </div>
                  </div>
                  <p class="mt-4 text-sm text-blue-600 font-medium">Add more images</p>
                </div>
              </label>
            </div>
          </div>

          <!-- Video Upload Section -->
          <div>
            <label class="block text-gray-700 font-medium mb-3">Project Videos (Optional)</label>
            
            <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-blue-500 transition-colors cursor-pointer bg-gray-50">
              <input
                type="file"
                accept="video/*"
                multiple
                @change="handleVideoUpload"
                class="hidden"
                id="video-upload"
              />
              <label for="video-upload" class="cursor-pointer">
                <div v-if="videoPreviewUrls.length === 0">
                  <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                  </svg>
                  <p class="mt-2 text-sm text-gray-600">
                    <span class="font-medium text-blue-600 hover:text-blue-500">Click to upload videos</span>
                  </p>
                </div>
                <div v-else>
                  <p class="text-sm text-gray-600 mb-4">{{ videoPreviewUrls.length }} video(s) selected</p>
                  <div class="space-y-4">
                    <div v-for="(url, index) in videoPreviewUrls" :key="index" class="bg-white p-4 rounded-lg border border-gray-200">
                      <div class="flex gap-4">
                        <div class="flex-shrink-0">
                          <video :src="url" class="h-24 w-40 object-cover rounded" controls></video>
                        </div>
                        <div class="flex-1 space-y-2">
                          <input 
                            v-model="videoCaptions[index]"
                            type="text"
                            placeholder="Video caption (optional)"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-blue-500 focus:border-transparent"
                          />
                          <div class="flex items-center gap-2">
                            <input
                              type="file"
                              accept="image/*"
                              @change="handleVideoThumbnailUpload($event, index)"
                              class="hidden"
                              :id="`thumbnail-${index}`"
                            />
                            <label :for="`thumbnail-${index}`" class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded cursor-pointer hover:bg-gray-50 text-center">
                              {{ videoThumbnailPreviewUrls[index] ? 'Change Thumbnail' : 'Add Thumbnail' }}
                            </label>
                            <button 
                              @click.prevent="removeVideo(index)"
                              class="px-3 py-2 bg-red-100 text-red-700 rounded hover:bg-red-200 text-sm font-medium"
                            >
                              Remove
                            </button>
                          </div>
                          <img v-if="videoThumbnailPreviewUrls[index]" :src="videoThumbnailPreviewUrls[index]" class="h-16 w-28 object-cover rounded mt-2" alt="Thumbnail" />
                        </div>
                      </div>
                    </div>
                  </div>
                  <p class="mt-4 text-sm text-blue-600 font-medium">Add more videos</p>
                </div>
              </label>
            </div>
          </div>

          <!-- Form Fields -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
              <label class="block text-gray-700 font-medium mb-2">Project Title *</label>
              <input 
                v-model="newProject.title"
                type="text" 
                placeholder="Enter project title"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>

            <div class="md:col-span-2">
              <label class="block text-gray-700 font-medium mb-2">Description *</label>
              <textarea 
                v-model="newProject.description"
                rows="4"
                placeholder="Describe the project"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
              ></textarea>
            </div>

            <div>
              <label class="block text-gray-700 font-medium mb-2">Partner Organization *</label>
              <input 
                v-model="newProject.partner"
                type="text"
                placeholder="Partner name"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>

            <div>
              <label class="block text-gray-700 font-medium mb-2">Year *</label>
              <input 
                v-model="newProject.year"
                type="text"
                placeholder="2024"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>

            <div>
              <label class="block text-gray-700 font-medium mb-2">Status *</label>
              <select 
                v-model="newProject.status"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
                <option v-for="status in statuses" :key="status.value" :value="status.value">
                  {{ status.label }}
                </option>
              </select>
            </div>

            <div>
              <label class="block text-gray-700 font-medium mb-2">Category *</label>
              <select 
                v-model="newProject.category"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
                <option v-for="category in categories" :key="category.value" :value="category.value">
                  {{ category.label }}
                </option>
              </select>
            </div>
          </div>
        </div>

        <div class="p-6 border-t border-gray-200 flex items-center justify-end gap-3 sticky bottom-0 bg-white">
          <button 
            @click="showUploadModal = false; editingProject = null; resetForm();"
            class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-colors"
          >
            Cancel
          </button>
          <button 
            @click="handleUpload"
            class="px-6 py-3 bg-blue-600 hover:bg-blue-500 text-white rounded-lg font-medium transition-all flex items-center gap-2 shadow-md shadow-blue-600/40 hover:shadow-lg hover:shadow-blue-500/50"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
            </svg>
            {{ editingProject ? 'Update Project' : 'Upload Project' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.line-clamp-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>