<template>
    <div class="projects-page">
      <div class="container">
        <h1 class="page-title">{{ $t('partners.ourProjects') }}</h1>
        
        <!-- Loading State -->
        <div v-if="loading" class="loading-container">
          <div class="spinner"></div>
          <p>{{ $t('partners.loadingProjects') }}</p>
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
        
        <!-- Projects Grid -->
        <div v-if="!loading && !error && projects.length > 0" class="projects-grid">
          <NuxtLink 
            v-for="project in projects" 
            :key="project.id" 
            :to="`/projects/${project.id}`"
            class="project-card-link"
          >
            <div class="project-card">
              <!-- Project Header -->
              <div class="project-header">
                <span :class="`status-badge status-${project.status.toLowerCase()}`">
                  {{ project.status_label || project.status }}
                </span>
                <span class="category-badge">{{ project.category }}</span>
              </div>
              
              <!-- Project Title & Description -->
              <h2 class="project-title">{{ project.title }}</h2>
              <p class="project-description">{{ project.description }}</p>
              
              <!-- Media Gallery -->
              <div v-if="project.media && project.media.length > 0" class="media-gallery">
                <div v-for="(media, index) in project.media" :key="index" class="media-item">
                  <!-- Image -->
                  <div v-if="media.type === 'image'" class="image-wrapper">
                    <img
                      :src="cldOptimize(media.url, 500)"
                      :alt="media.caption || project.title"
                      loading="lazy"
                      @error="handleImageError($event, media.url)"
                    />
                    <p v-if="media.caption" class="media-caption">{{ media.caption }}</p>
                  </div>
                  
                  <!-- Video -->
                  <div v-else-if="media.type === 'video'" class="video-wrapper">
                    <video 
                      controls 
                      :poster="media.thumbnail"
                      preload="metadata"
                    >
                      <source :src="media.url" type="video/mp4">
                      Your browser does not support the video tag.
                    </video>
                    <p v-if="media.caption" class="media-caption">{{ media.caption }}</p>
                  </div>
                </div>
              </div>
              
              <!-- Project Details -->
              <div class="project-details">
                <div class="detail-item">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                  </svg>
                  <span class="detail-label">{{ $t('common.partner') }}:</span>
                  <span class="detail-value">{{ project.partner }}</span>
                </div>
                
                <div class="detail-item">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                  </svg>
                  <span class="detail-label">{{ $t('common.year') }}:</span>
                  <span class="detail-value">{{ project.year }}</span>
                </div>
              </div>
            </div>
          </NuxtLink>
        </div>
        
        <!-- Empty State -->
        <div v-if="!loading && !error && projects.length === 0" class="empty-state">
          <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
            <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
            <line x1="12" y1="22.08" x2="12" y2="12"></line>
          </svg>
          <p>{{ $t('partners.noProjects') }}</p>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue';

  const { apiFetch, mediaUrl, applyStoredI18n } = useApi()
  
  definePageMeta({
    layout: 'default'
  });
  
  const projects = ref([]);
  const loading = ref(true);
  const error = ref(null);
  
  // Handle image loading errors
  const handleImageError = (event, url) => {
    console.error('Failed to load image:', url);
    event.target.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="400" height="300" viewBox="0 0 400 300"%3E%3Crect fill="%23f0f0f0" width="400" height="300"/%3E%3Ctext fill="%23999" font-family="sans-serif" font-size="16" dy="10.5" font-weight="bold" x="50%25" y="50%25" text-anchor="middle"%3EImage not available%3C/text%3E%3C/svg%3E';
  };

  const resolveMedia = (project) => {
    const localized = applyStoredI18n(project, ['title', 'description'])
    return {
      ...localized,
      media: (localized.media || []).map((m) => ({
        ...m,
        url: mediaUrl(m.url),
        thumbnail: m.thumbnail ? mediaUrl(m.thumbnail) : '',
      })),
    }
  }
  
  const loadProjects = async (silent = false) => {
    try {
      if (!silent) loading.value = true;
      const data = await apiFetch('/api/projects')
      projects.value = (data?.data || []).map(resolveMedia)
      error.value = null
    } catch (err) {
      console.error('Error loading projects:', err);
      if (!silent) {
        error.value = err?.data?.message || err?.message || 'Failed to load projects';
      }
    } finally {
      if (!silent) loading.value = false;
    }
  }

  onContentChange(['projects'], () => {
    loadProjects(true)
  })

  onMounted(async () => {
    await loadProjects()
  });
  </script>
  
  <style scoped>
  .projects-page {
    min-height: 100vh;
    background: linear-gradient(to bottom, #f9fafb, #ffffff);
    padding: 3rem 0;
  }
  
  .container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1.5rem;
  }
  
  .page-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #111827;
    margin-bottom: 2.5rem;
    text-align: center;
  }
  
  /* Loading State */
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
  
  /* Error Banner */
  .error-banner {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 1.5rem;
    background-color: #fef2f2;
    border: 1px solid #fecaca;
    border-radius: 8px;
    color: #991b1b;
    margin-bottom: 2rem;
  }
  
  /* Projects Grid */
  .projects-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 2rem;
  }
  
  .project-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: transform 0.2s, box-shadow 0.2s;
  }
  
  .project-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
  }
  
  /* Project Header */
  .project-header {
    display: flex;
    gap: 0.5rem;
    padding: 1rem 1.5rem 0;
  }
  
  .status-badge, .category-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.75rem;
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
  
  .status-upcoming {
    background-color: #e0e7ff;
    color: #3730a3;
  }
  
  .category-badge {
    background-color: #f3f4f6;
    color: #4b5563;
  }
  
  /* Project Content */
  .project-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #111827;
    margin: 1rem 1.5rem 0.75rem;
  }
  
  .project-description {
    font-size: 0.95rem;
    color: #6b7280;
    line-height: 1.6;
    margin: 0 1.5rem 1.5rem;
  }
  
  /* Media Gallery */
  .media-gallery {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    padding: 0 1.5rem 1.5rem;
  }
  
  .media-item {
    position: relative;
  }
  
  .image-wrapper, .video-wrapper {
    width: 100%;
  }
  
  .image-wrapper img, .video-wrapper video {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-radius: 8px;
    background-color: #f3f4f6;
  }
  
  .media-caption {
    font-size: 0.8rem;
    color: #6b7280;
    margin-top: 0.5rem;
    line-height: 1.4;
  }
  
  /* Project Details */
  .project-details {
    border-top: 1px solid #e5e7eb;
    padding: 1rem 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
  }
  
  .detail-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
  }
  
  .detail-item svg {
    color: #9ca3af;
    flex-shrink: 0;
  }
  
  .detail-label {
    color: #6b7280;
    font-weight: 500;
  }
  
  .detail-value {
    color: #111827;
  }
  
  /* Empty State */
  .empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 4rem 0;
    color: #9ca3af;
  }
  
  .empty-state svg {
    margin-bottom: 1rem;
  }
  
  .empty-state p {
    font-size: 1.125rem;
  }
  
  /* Responsive Design */
  @media (max-width: 768px) {
    .page-title {
      font-size: 2rem;
    }
    
    .projects-grid {
      grid-template-columns: 1fr;
    }
    
    .media-gallery {
      grid-template-columns: 1fr;
    }
    
    .project-details {
      font-size: 0.85rem;
    }
  }
  </style>