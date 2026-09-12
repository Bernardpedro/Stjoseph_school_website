<template>
    <div class="project-details-page">
      <div class="container">
        <!-- Loading State -->
        <div v-if="loading" class="loading-container">
          <div class="spinner"></div>
          <p>Loading project details...</p>
        </div>
        
        <!-- Error State -->
        <div v-if="error" class="error-banner">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="8" x2="12" y2="12"></line>
            <line x1="12" y1="16" x2="12.01" y2="16"></line>
          </svg>
          <span>{{ error }}</span>
        </div>
        
        <!-- Project Content -->
        <div v-if="!loading && !error && project" class="project-content">
          <!-- Back Button -->
          <NuxtLink to="/project" class="back-button">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
            {{ $t('partners.backToProjects') }}
          </NuxtLink>
          
          <!-- Media Gallery - Featured First -->
          <div v-if="project.media && project.media.length > 0" class="media-section featured">
            <div class="media-grid">
              <div 
                v-for="(media, index) in project.media" 
                :key="index" 
                class="media-card"
                @click="openMediaViewer(media, index)"
              >
                <!-- Image Thumbnail -->
                <div v-if="media.type === 'image'" class="media-thumbnail">
                  <img
                    :src="cldOptimize(media.url, 300)"
                    :alt="media.caption || project.title"
                    loading="lazy"
                  />
                  <div class="media-overlay">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <circle cx="11" cy="11" r="8"></circle>
                      <path d="m21 21-4.35-4.35"></path>
                      <line x1="11" y1="8" x2="11" y2="14"></line>
                      <line x1="8" y1="11" x2="14" y2="11"></line>
                    </svg>
                    <span>Click to zoom</span>
                  </div>
                </div>
                
                <!-- Video Thumbnail -->
                <div v-else-if="media.type === 'video'" class="media-thumbnail video-thumb">
                  <img 
                    v-if="media.thumbnail"
                    :src="cldOptimize(media.thumbnail, 300)"
                    :alt="media.caption || 'Video thumbnail'"
                    loading="lazy"
                  />
                  <div v-else class="video-placeholder">
                    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="currentColor">
                      <path d="M8 5v14l11-7z"/>
                    </svg>
                  </div>
                  <div class="media-overlay">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="currentColor">
                      <path d="M8 5v14l11-7z"/>
                    </svg>
                    <span>Click to play</span>
                  </div>
                </div>
                
                <p v-if="media.caption" class="media-caption">{{ media.caption }}</p>
              </div>
            </div>
          </div>
          
          <!-- Project Header -->
          <div class="project-header">
            <div class="header-top">
              <div class="header-badges">
                <span :class="`status-badge status-${project.status.toLowerCase()}`">
                  {{ project.status_label || project.status }}
                </span>
                <span class="category-badge">{{ project.category }}</span>
              </div>
              <div class="header-meta">
                <div class="meta-chip">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                  </svg>
                  <span>{{ project.partner }}</span>
                </div>
                <div class="meta-chip">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                  </svg>
                  <span>{{ project.year }}</span>
                </div>
              </div>
            </div>
            <h1 class="project-title">{{ project.title }}</h1>
            <p class="project-description">{{ project.description }}</p>
          </div>
        </div>
      </div>
      
      <!-- Media Viewer Modal -->
      <Teleport to="body">
        <div v-if="showMediaViewer" class="media-viewer-modal" @click.self="closeMediaViewer">
          <div class="modal-content">
            <!-- Close Button -->
            <button class="close-button" @click="closeMediaViewer">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
              </svg>
            </button>
            
            <!-- Navigation Buttons -->
            <button 
              v-if="project.media.length > 1"
              class="nav-button prev-button" 
              @click="previousMedia"
              :disabled="currentMediaIndex === 0"
            >
              <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M15 18l-6-6 6-6"/>
              </svg>
            </button>
            
            <button 
              v-if="project.media.length > 1"
              class="nav-button next-button" 
              @click="nextMedia"
              :disabled="currentMediaIndex === project.media.length - 1"
            >
              <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 18l6-6-6-6"/>
              </svg>
            </button>
            
            <!-- Media Display -->
            <div class="media-display">
              <!-- Image Viewer with Zoom -->
              <div v-if="currentMedia?.type === 'image'" class="image-viewer">
                <div 
                  class="zoomable-image" 
                  :style="{ transform: `scale(${zoomLevel}) translate(${panX}px, ${panY}px)` }"
                >
                  <img
                    :src="cldOptimize(currentMedia.url, 1400)"
                    :alt="currentMedia.caption || 'Project image'"
                    draggable="false"
                    @mousedown="startPan"
                  />
                </div>
                
                <!-- Zoom Controls -->
                <div class="zoom-controls">
                  <button @click="zoomOut" :disabled="zoomLevel <= 1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <circle cx="11" cy="11" r="8"></circle>
                      <path d="m21 21-4.35-4.35"></path>
                      <line x1="8" y1="11" x2="14" y2="11"></line>
                    </svg>
                  </button>
                  <span class="zoom-level">{{ Math.round(zoomLevel * 100) }}%</span>
                  <button @click="zoomIn" :disabled="zoomLevel >= 3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <circle cx="11" cy="11" r="8"></circle>
                      <path d="m21 21-4.35-4.35"></path>
                      <line x1="11" y1="8" x2="11" y2="14"></line>
                      <line x1="8" y1="11" x2="14" y2="11"></line>
                    </svg>
                  </button>
                  <button @click="resetZoom">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M21 2v6h-6M3 12a9 9 0 0 1 15-6.7L21 8M3 22v-6h6M21 12a9 9 0 0 1-15 6.7L3 16"/>
                    </svg>
                  </button>
                </div>
              </div>
              
              <!-- Video Player -->
              <div v-else-if="currentMedia?.type === 'video'" class="video-viewer">
                <video 
                  controls 
                  autoplay
                  :poster="currentMedia.thumbnail"
                  preload="auto"
                  ref="videoPlayer"
                >
                  <source :src="currentMedia.url" type="video/mp4">
                  Your browser does not support the video tag.
                </video>
              </div>
              
              <!-- Caption -->
              <p v-if="currentMedia?.caption" class="viewer-caption">
                {{ currentMedia.caption }}
              </p>
              
              <!-- Media Counter -->
              <div v-if="project.media.length > 1" class="media-counter">
                {{ currentMediaIndex + 1 }} / {{ project.media.length }}
              </div>
            </div>
          </div>
        </div>
      </Teleport>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted, computed, watch } from 'vue';
  import { useRoute } from 'vue-router';

  const { apiFetch, mediaUrl, applyStoredI18n } = useApi()
  
  definePageMeta({
    layout: 'default'
  });
  
  const route = useRoute();
  const project = ref(null);
  const loading = ref(true);
  const error = ref(null);
  
  // Media viewer state
  const showMediaViewer = ref(false);
  const currentMediaIndex = ref(0);
  const zoomLevel = ref(1);
  const panX = ref(0);
  const panY = ref(0);
  const isPanning = ref(false);
  const startPanX = ref(0);
  const startPanY = ref(0);
  const videoPlayer = ref(null);
  
  const currentMedia = computed(() => {
    if (!project.value?.media) return null;
    return project.value.media[currentMediaIndex.value];
  });
  
  const loadProject = async (silent = false) => {
    try {
      if (!silent) loading.value = true;
      const projectId = route.params.id;
      const data = await apiFetch('/api/projects', { query: { id: projectId } })
      const row = applyStoredI18n(data?.data || {}, ['title', 'description'])
      if (!row?.id) {
        throw new Error('Project not found')
      }
      project.value = {
        ...row,
        media: (row.media || []).map((m) => ({
          ...m,
          url: mediaUrl(m.url),
          thumbnail: m.thumbnail ? mediaUrl(m.thumbnail) : '',
        })),
      }
      error.value = null
    } catch (err) {
      console.error('Error loading project:', err);
      if (!silent) {
        error.value = err?.data?.message || err?.message || 'Failed to load project';
      }
    } finally {
      if (!silent) loading.value = false;
    }
  }

  onContentChange(['projects'], () => {
    loadProject(true)
  })

  onMounted(() => {
    loadProject()
  });
  
  // Media viewer functions
  const openMediaViewer = (media, index) => {
    currentMediaIndex.value = index;
    showMediaViewer.value = true;
    resetZoom();
    document.body.style.overflow = 'hidden';
  };
  
  const closeMediaViewer = () => {
    showMediaViewer.value = false;
    if (videoPlayer.value) {
      videoPlayer.value.pause();
    }
    document.body.style.overflow = '';
  };
  
  const nextMedia = () => {
    if (currentMediaIndex.value < project.value.media.length - 1) {
      currentMediaIndex.value++;
      resetZoom();
    }
  };
  
  const previousMedia = () => {
    if (currentMediaIndex.value > 0) {
      currentMediaIndex.value--;
      resetZoom();
    }
  };
  
  // Zoom functions
  const zoomIn = () => {
    if (zoomLevel.value < 3) {
      zoomLevel.value = Math.min(zoomLevel.value + 0.25, 3);
    }
  };
  
  const zoomOut = () => {
    if (zoomLevel.value > 1) {
      zoomLevel.value = Math.max(zoomLevel.value - 0.25, 1);
      if (zoomLevel.value === 1) {
        panX.value = 0;
        panY.value = 0;
      }
    }
  };
  
  const resetZoom = () => {
    zoomLevel.value = 1;
    panX.value = 0;
    panY.value = 0;
  };
  
  // Pan functions
  const startPan = (e) => {
    if (zoomLevel.value > 1) {
      isPanning.value = true;
      startPanX.value = e.clientX - panX.value;
      startPanY.value = e.clientY - panY.value;
      
      const onMouseMove = (e) => {
        if (isPanning.value) {
          panX.value = e.clientX - startPanX.value;
          panY.value = e.clientY - startPanY.value;
        }
      };
      
      const onMouseUp = () => {
        isPanning.value = false;
        document.removeEventListener('mousemove', onMouseMove);
        document.removeEventListener('mouseup', onMouseUp);
      };
      
      document.addEventListener('mousemove', onMouseMove);
      document.addEventListener('mouseup', onMouseUp);
    }
  };
  
  // Keyboard navigation
  onMounted(() => {
    const handleKeydown = (e) => {
      if (!showMediaViewer.value) return;
      
      if (e.key === 'Escape') closeMediaViewer();
      if (e.key === 'ArrowRight') nextMedia();
      if (e.key === 'ArrowLeft') previousMedia();
      if (e.key === '+' || e.key === '=') zoomIn();
      if (e.key === '-') zoomOut();
      if (e.key === '0') resetZoom();
    };
    
    document.addEventListener('keydown', handleKeydown);
    
    return () => {
      document.removeEventListener('keydown', handleKeydown);
    };
  });
  </script>
  
  <style scoped>
  .project-details-page {
    min-height: 100vh;
    background: linear-gradient(to bottom, #f9fafb, #ffffff);
    padding: 3rem 0;
  }
  
  .container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1.5rem;
  }
  
  /* Loading & Error States */
  .loading-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 4rem 0;
    gap: 1rem;
  }
  
  .spinner {
    width: 48px;
    height: 48px;
    border: 4px solid #e5e7eb;
    border-top-color: #3b82f6;
    border-radius: 50%;
    animation: spin 1s linear infinite;
  }
  
  @keyframes spin {
    to { transform: rotate(360deg); }
  }
  
  .error-banner {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 1.5rem;
    background-color: #fef2f2;
    border: 1px solid #fecaca;
    border-radius: 8px;
    color: #991b1b;
  }
  
  /* Back Button */
  .back-button {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    color: #374151;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s;
    margin-bottom: 2rem;
  }
  
  .back-button:hover {
    background: #f9fafb;
    border-color: #d1d5db;
  }
  
  /* Project Header */
  .project-header {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    margin-bottom: 2rem;
  }
  
  .header-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    gap: 1rem;
    flex-wrap: wrap;
  }
  
  .header-badges {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
  }
  
  .header-meta {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
  }
  
  .meta-chip {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: #f3f4f6;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 600;
    color: #374151;
  }
  
  .meta-chip svg {
    color: #6b7280;
    flex-shrink: 0;
  }
  
  .status-badge, .category-badge {
    padding: 0.4rem 1rem;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
  }
  
  .status-badge {
    background-color: #dbeafe;
    color: #1e40af;
  }
  
  .status-completed {
    background-color: #d1fae5;
    color: #065f46;
  }
  
  .status-ongoing {
    background-color: #fef3c7;
    color: #92400e;
  }
  
  .category-badge {
    background-color: #f3f4f6;
    color: #4b5563;
  }
  
  .project-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #111827;
    margin-bottom: 1rem;
  }
  
  .project-description {
    font-size: 1.125rem;
    color: #6b7280;
    line-height: 1.6;
  }
  
  /* Metadata */
  .project-metadata {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 3rem;
  }
  
  .metadata-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.5rem;
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  }
  
  .metadata-item svg {
    color: #3b82f6;
    flex-shrink: 0;
  }
  
  .metadata-item > div {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
  }
  
  .metadata-label {
    font-size: 0.875rem;
    color: #6b7280;
    font-weight: 500;
  }
  
  .metadata-value {
    font-size: 1.125rem;
    color: #111827;
    font-weight: 600;
  }
  
  /* Media Section */
  .media-section {
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 2rem;
  }
  
  .media-section.featured {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
  }
  
  .section-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: #111827;
    margin-bottom: 1.5rem;
    padding: 0 2rem;
    padding-top: 2rem;
  }
  
  .media-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 0;
  }
  
  .media-card {
    cursor: pointer;
    overflow: hidden;
    transition: transform 0.2s;
    border: 2px solid transparent;
  }
  
  .media-card:hover {
    transform: scale(1.05);
    z-index: 1;
    border-color: #3b82f6;
  }
  
  .media-thumbnail {
    position: relative;
    width: 100%;
    height: 300px;
    overflow: hidden;
    background: #f3f4f6;
  }
  
  .media-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
  
  .video-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #e5e7eb;
    color: #9ca3af;
  }
  
  .media-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    opacity: 0;
    transition: opacity 0.3s;
    color: white;
  }
  
  .media-card:hover .media-overlay {
    opacity: 1;
  }
  
  .media-caption {
    padding: 0.75rem;
    font-size: 0.875rem;
    color: #6b7280;
    background: #f9fafb;
  }
  
  /* Media Viewer Modal */
  .media-viewer-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.95);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  
  .modal-content {
    position: relative;
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  
  .close-button {
    position: absolute;
    top: 1.5rem;
    right: 1.5rem;
    width: 48px;
    height: 48px;
    background: rgba(255, 255, 255, 0.1);
    border: none;
    border-radius: 50%;
    color: white;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s;
    z-index: 10;
  }
  
  .close-button:hover {
    background: rgba(255, 255, 255, 0.2);
  }
  
  .nav-button {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 48px;
    height: 48px;
    background: rgba(255, 255, 255, 0.1);
    border: none;
    border-radius: 50%;
    color: white;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s;
    z-index: 10;
  }
  
  .nav-button:hover:not(:disabled) {
    background: rgba(255, 255, 255, 0.2);
  }
  
  .nav-button:disabled {
    opacity: 0.3;
    cursor: not-allowed;
  }
  
  .prev-button {
    left: 1.5rem;
  }
  
  .next-button {
    right: 1.5rem;
  }
  
  .media-display {
    max-width: 90%;
    max-height: 90%;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
  }
  
  /* Image Viewer */
  .image-viewer {
    position: relative;
    max-width: 100%;
    max-height: 80vh;
    overflow: hidden;
  }
  
  .zoomable-image {
    transition: transform 0.3s ease;
    cursor: grab;
  }
  
  .zoomable-image:active {
    cursor: grabbing;
  }
  
  .zoomable-image img {
    max-width: 90vw;
    max-height: 80vh;
    object-fit: contain;
    user-select: none;
  }
  
  /* Zoom Controls */
  .zoom-controls {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 24px;
    backdrop-filter: blur(10px);
  }
  
  .zoom-controls button {
    width: 36px;
    height: 36px;
    background: rgba(255, 255, 255, 0.2);
    border: none;
    border-radius: 50%;
    color: white;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s;
  }
  
  .zoom-controls button:hover:not(:disabled) {
    background: rgba(255, 255, 255, 0.3);
  }
  
  .zoom-controls button:disabled {
    opacity: 0.3;
    cursor: not-allowed;
  }
  
  .zoom-level {
    color: white;
    font-weight: 600;
    min-width: 60px;
    text-align: center;
  }
  
  /* Video Viewer */
  .video-viewer {
    max-width: 90vw;
    max-height: 80vh;
  }
  
  .video-viewer video {
    width: 100%;
    height: 100%;
    max-height: 80vh;
    object-fit: contain;
  }
  
  /* Viewer Caption */
  .viewer-caption {
    color: white;
    font-size: 1rem;
    text-align: center;
    max-width: 600px;
    padding: 0 1rem;
  }
  
  /* Media Counter */
  .media-counter {
    position: absolute;
    bottom: 1.5rem;
    left: 50%;
    transform: translateX(-50%);
    padding: 0.5rem 1rem;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 20px;
    color: white;
    font-weight: 600;
    backdrop-filter: blur(10px);
  }
  
  /* Responsive */
  @media (max-width: 768px) {
    .project-title {
      font-size: 2rem;
    }
    
    .media-grid {
      grid-template-columns: 1fr;
    }
    
    .nav-button, .close-button {
      width: 40px;
      height: 40px;
    }
    
    .prev-button {
      left: 0.5rem;
    }
    
    .next-button {
      right: 0.5rem;
    }
    
    .close-button {
      top: 0.5rem;
      right: 0.5rem;
    }
  }
  </style>