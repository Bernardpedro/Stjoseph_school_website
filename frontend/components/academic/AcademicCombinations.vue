<template>
  <div class="bg-white dark:bg-gray-900 min-h-screen">
 
    <!-- Search Results Summary -->
    <div v-if="isSearchActive" class="py-6 bg-blue-50 dark:bg-blue-900/30">
      <div class="max-w-7xl mx-auto px-4">
        <div v-if="searchResults && (searchResults.automobileTech || searchResults.buildingConstruction || searchResults.carpentryTailoring || searchResults.computerLab)"
             class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4">
          <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
            Search Results for "{{ props.searchQuery }}"
          </h3>
          <p class="text-gray-600 dark:text-gray-300 mb-4">
            We found matches in the following sections:
          </p>
          <ul class="space-y-1">
            <li v-if="searchResults.automobileTech" class="flex items-center text-blue-600 dark:text-blue-400">
              <span class="w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
              Automobile Technology
            </li>
            <li v-if="searchResults.buildingConstruction" class="flex items-center text-pink-600 dark:text-pink-400">
              <span class="w-2 h-2 bg-pink-500 rounded-full mr-2"></span>
              Building Construction
            </li>
            <li v-if="searchResults.carpentryTailoring" class="flex items-center text-purple-600 dark:text-purple-400">
              <span class="w-2 h-2 bg-purple-500 rounded-full mr-2"></span>
              Carpentry & Tailoring
            </li>
            <li v-if="searchResults.computerLab" class="flex items-center text-green-600 dark:text-green-400">
              <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
              Computer Lab
            </li>
          </ul>
        </div>
        <div v-else class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4">
          <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
            No results found for "{{ props.searchQuery }}"
          </h3>
          <p class="text-gray-600 dark:text-gray-300">
            Try a different search term or browse our academic programs below.
          </p>
        </div>
      </div>
    </div>

    <!-- Academic Tracks Section -->
    <div class="py-20 bg-gray-50 dark:bg-gray-800">
      <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-16">
          <h2 class="text-4xl lg:text-5xl font-bold text-gray-900 dark:text-white mb-6">
            Academic Tracks & Programs
          </h2>
          <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
            Discover our comprehensive curriculum designed to nurture academic excellence and personal growth
          </p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
          <!-- Science Track -->
          <div class="group relative bg-white dark:bg-gray-900 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-green-400/20 to-blue-500/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="relative p-8">

              <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">🔧 Automobile Technology</h3>
              <p class="text-gray-600 dark:text-gray-300 mb-6">Program grounded in science and innovation, offering hands-on experiences in mechanics, diagnostics, and engineering to prepare students for the future of mobility and smart transportation</p>
              
              <!--  AUTO MOBILE TECHNOLOGY -->
              <div class="relative w-full h-48 rounded-2xl mb-6 overflow-hidden bg-gradient-to-br from-blue-200/50 to-indigo-200/50 dark:from-blue-800/30 dark:to-indigo-800/30">
                <div v-for="(image, index) in AutomobileTechImages" :key="index"
                      class="absolute w-full h-full transition-opacity duration-1000 ease-in-out"
                      :class="{'opacity-100': index === currentAutomobileTechImageIndex, 'opacity-0': index !== currentAutomobileTechImageIndex}">
                  <img :src="image" alt="Automobile Technology Image" class="w-full h-full object-cover object-center">
                </div>
              </div>
                <!-- Automobile Technology List   -->
              <ul class="space-y-2" v-for="feature in autoMobiles[0].features1" :key="feature.id">
                <li class="flex items-center text-sm text-gray-600 dark:text-gray-300"
                    :class="{'search-highlight bg-yellow-100 dark:bg-yellow-900 font-bold': isSearchActive && props.searchQuery && feature.toLowerCase().includes(props.searchQuery.toLowerCase())}">
                  <span class="w-2 h-2 bg-green-500 rounded-full mr-3"></span>
                  {{ feature }}
                </li>
              </ul>
              <!-- Search indicator -->
              <div v-if="isSearchActive && searchResults && searchResults.automobileTech"
                   class="mt-4 p-2 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded-lg text-sm">
                Search results found in Automobile Technology
              </div>
            </div>
          </div>

          <!-- Humanities Track -->
          <div class="group relative bg-white dark:bg-gray-900 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-orange-400/20 to-red-500/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="relative p-8">

              <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">🧱 Building Construction</h3>
              <p class="text-gray-600 dark:text-gray-300 mb-6"> Program that blends technical training with creativity, problem-solving, and structural design, equipping students to shape modern infrastructure with purpose and precision</p>
              
              <!-- Building constraction images placeholder -->
              <div class="relative w-full h-48 rounded-2xl mb-6 overflow-hidden bg-gradient-to-br from-blue-200/50 to-indigo-200/50 dark:from-blue-800/30 dark:to-indigo-800/30">
                <div v-for="(image, index) in buildingConstractionImages" :key="index"
                      class="absolute w-full h-full transition-opacity duration-1000 ease-in-out"
                      :class="{'opacity-100': index === currentBuildingConstractionImageIndex, 'opacity-0': index !== currentBuildingConstractionImageIndex}">
                  <img :src="image" alt="Building Construction Image" class="w-full h-full object-cover object-center">
                </div>
              </div>
                  <!-- Building Construction list -->
              <ul class="space-y-2" v-for="feature in autoMobiles[0].features2" :key="feature.id">
                <li class="flex items-center text-sm text-gray-600 dark:text-gray-300"
                    :class="{'search-highlight bg-yellow-100 dark:bg-yellow-900 font-bold': isSearchActive && props.searchQuery && feature.toLowerCase().includes(props.searchQuery.toLowerCase())}">
                  <span class="w-2 h-2 bg-pink-500 rounded-full mr-3"></span>
                  {{ feature }}
                </li>
              </ul>
              <!-- Search indicator -->
              <div v-if="isSearchActive && searchResults && searchResults.buildingConstruction"
                   class="mt-4 p-2 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded-lg text-sm">
                Search results found in Building Construction
              </div>
            </div>
          </div>

          <!-- Carpentry & Tailoring -->
          <div class="group relative bg-white dark:bg-gray-900 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-purple-400/20 to-indigo-500/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="relative p-8">
              <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">🏗️ Carpentry & Tailoring</h3>
              <p class="text-gray-600 dark:text-gray-300 mb-6">Program grounded innovation, offering hands-on experiences in Carpentry and Tailoring to prepare students for the future innovations</p>
              
              <!-- Carpentry & Tailoring images placeholder -->
              <div class="relative w-full h-48 rounded-2xl mb-6 overflow-hidden bg-gradient-to-br from-blue-200/50 to-indigo-200/50 dark:from-blue-800/30 dark:to-indigo-800/30">
                <div v-for="(image, index) in carpentryTailoringImages" :key="index"
                      class="absolute w-full h-full transition-opacity duration-1000 ease-in-out"
                      :class="{'opacity-100': index === currentCarpentryTailoringImageIndex, 'opacity-0': index !== currentCarpentryTailoringImageIndex}">
                  <img :src="image" alt="Carpentry & Tailoring Image" class="w-full h-full object-cover object-center">
                </div>
              </div>
                                <!-- Carpentry and Tailoring  List -->
              <ul class="space-y-2" v-for="feature in autoMobiles[0].features3" :key="feature.id">
                <li class="flex items-center text-sm text-gray-600 dark:text-gray-300"
                    :class="{'search-highlight bg-yellow-100 dark:bg-yellow-900 font-bold': isSearchActive && props.searchQuery && feature.toLowerCase().includes(props.searchQuery.toLowerCase())}">
                  <span class="w-2 h-2 bg-indigo-500 rounded-full mr-3"></span>
                  {{ feature }}
                </li>
              </ul>
              <!-- Search indicator -->
              <div v-if="isSearchActive && searchResults && searchResults.carpentryTailoring"
                   class="mt-4 p-2 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded-lg text-sm">
                Search results found in Carpentry & Tailoring
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- What Makes Us Unique Section -->
    <div class="py-20 bg-white dark:bg-gray-900">
      <div class="max-w-7xl mx-auto px-4">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
          <div>
            <h2 class="text-4xl lg:text-5xl font-bold text-gray-900 dark:text-white mb-8">
              What Makes Our Academics
              <span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">Exceptional</span>
            </h2>
            
            <div class="space-y-8">
              <div class="flex items-start space-x-4">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center flex-shrink-0">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Innovative Teaching Methods</h3>
                  <p class="text-gray-600 dark:text-gray-300">Interactive learning experiences combining traditional wisdom with cutting-edge technology and pedagogical approaches.</p>
                </div>
              </div>

              <div class="flex items-start space-x-4">
                <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-blue-600 rounded-xl flex items-center justify-center flex-shrink-0">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Small Class Sizes</h3>
                  <p class="text-gray-600 dark:text-gray-300">Personalized attention with student-to-teacher ratios that ensure every student receives individual support and guidance.</p>
                </div>
              </div>

              <div class="flex items-start space-x-4">
                <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-red-600 rounded-xl flex items-center justify-center flex-shrink-0">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Global Perspective</h3>
                  <p class="text-gray-600 dark:text-gray-300">International curriculum standards with cultural exchange programs and global awareness initiatives.</p>
                </div>
              </div>
            </div>
          </div>

          <div class="relative">
            <!-- Campus Excellence Image Slider -->
            <div class="relative w-full h-96 rounded-3xl shadow-2xl overflow-hidden bg-gradient-to-br from-blue-100 via-purple-50 to-indigo-100 dark:from-blue-900/20 dark:via-purple-900/20 dark:to-indigo-900/20">
              <div v-for="(image, index) in campusExcellenceImages" :key="index"
                    class="absolute w-full h-full transition-opacity duration-1000 ease-in-out"
                    :class="{'opacity-100': index === currentCampusExcellenceImageIndex, 'opacity-0': index !== currentCampusExcellenceImageIndex}">
                <img :src="image" alt="Campus Excellence Image" class="w-full h-full object-cover object-center">
              </div>
              <!-- Decorative elements -->
              <div class="absolute top-4 right-4 w-16 h-16 bg-blue-500/20 rounded-full blur-xl"></div>
              <div class="absolute bottom-4 left-4 w-20 h-20 bg-purple-500/20 rounded-full blur-xl"></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    
    <!-- Academic Resources Section -->
    <div class="py-20 bg-white dark:bg-gray-900">
      <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-16">
          <h2 class="text-4xl lg:text-5xl font-bold text-gray-900 dark:text-white mb-6">
            World-Class Academic Resources
          </h2>
          <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
            State-of-the-art facilities and resources designed to enhance learning and foster academic achievement
          </p>
        </div>

        <!-- Modern Computer Lab Section with 3-column layout -->
        <div class="grid lg:grid-cols-1 mb-0">
          <div class="relative group">
            <div class="bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-3xl p-8 h-full shadow-lg hover:shadow-2xl transition-all duration-500">

              <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4 text-center">Modern Computer Lab</h3>

              <!-- Computer Lab Image Slider -->
              <div class="relative w-full h-[400px] rounded-2xl mb-8 overflow-hidden bg-gradient-to-br from-blue-200/50 to-indigo-200/50 dark:from-blue-800/30 dark:to-indigo-800/30">
                <div v-for="(image, index) in computerLabImages" :key="index"
                      class="absolute w-full h-full transition-opacity duration-1000 ease-in-out"
                      :class="{'opacity-100': index === currentComputerLabImageIndex, 'opacity-0': index !== currentComputerLabImageIndex}">
                  <img :src="image" alt="Computer Lab Image" class="w-full h-full object-cover object-center">
                </div>
              </div>
              
              <!-- Features in 3 columns -->
              <div class="grid md:grid-cols-3 gap-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md">
                  <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <span class="w-3 h-3 bg-blue-500 rounded-full mr-2"></span>
                    Hardware & Equipment
                  </h4>
                  <ul class="space-y-2 text-gray-600 dark:text-gray-300">
                    <li class="flex items-center text-sm">
                      <span class="w-2 h-2 bg-blue-400 rounded-full mr-3 flex-shrink-0"></span>
                      Latest software and hardware
                    </li>
                    <li class="flex items-center text-sm">
                      <span class="w-2 h-2 bg-blue-400 rounded-full mr-3 flex-shrink-0"></span>
                      High-performance computers
                    </li>
                    <li class="flex items-center text-sm">
                      <span class="w-2 h-2 bg-blue-400 rounded-full mr-3 flex-shrink-0"></span>
                      Modern projectors & screens
                    </li>
                  </ul>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md">
                  <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <span class="w-3 h-3 bg-green-500 rounded-full mr-2"></span>
                    Connectivity & Access
                  </h4>
                  <ul class="space-y-2 text-gray-600 dark:text-gray-300">
                    <li class="flex items-center text-sm">
                      <span class="w-2 h-2 bg-green-400 rounded-full mr-3 flex-shrink-0"></span>
                      High-speed internet access
                    </li>
                    <li class="flex items-center text-sm">
                      <span class="w-2 h-2 bg-green-400 rounded-full mr-3 flex-shrink-0"></span>
                      24/7 online portal access
                    </li>
                    <li class="flex items-center text-sm">
                      <span class="w-2 h-2 bg-green-400 rounded-full mr-3 flex-shrink-0"></span>
                      Wireless network coverage
                    </li>
                  </ul>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md">
                  <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <span class="w-3 h-3 bg-purple-500 rounded-full mr-2"></span>
                    Learning Environment
                  </h4>
                  <ul class="space-y-2 text-gray-600 dark:text-gray-300">
                    <li class="flex items-center text-sm">
                      <span class="w-2 h-2 bg-purple-400 rounded-full mr-3 flex-shrink-0"></span>
                      Quiet study zones
                    </li>
                    <li class="flex items-center text-sm">
                      <span class="w-2 h-2 bg-purple-400 rounded-full mr-3 flex-shrink-0"></span>
                      Digital resources and databases
                    </li>
                    <li class="flex items-center text-sm">
                      <span class="w-2 h-2 bg-purple-400 rounded-full mr-3 flex-shrink-0"></span>
                      Collaborative workspaces
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Achievement Stats Section -->
    <div class="py-20 bg-gradient-to-br text-black relative overflow-hidden">
      <div class="absolute inset-0 bg-black/20"></div>
      <div class="absolute inset-0"></div>

      <!-- Decorative elements -->
      <div class="absolute top-10 left-10 w-40 h-40 rounded-full blur-2xl"></div>
      <div class="absolute bottom-10 right-10 w-60 h-60 rounded-full blur-3xl"></div>
      
      <div class="relative max-w-7xl mx-auto px-4">
        <div class="text-center mb-16">
          <h2 class="text-4xl lg:text-5xl font-bold mb-6">
            Academic Excellence in Numbers
          </h2>
          <p class="text-xl text-black-100 max-w-3xl mx-auto">
            Our commitment to educational excellence reflected in outstanding achievements
          </p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
          <div class="text-center group">
            <div class="w-24 h-24 bg-gradient-to-br from-blue-400 to-purple-500 rounded-full mx-auto mb-6 flex items-center justify-center shadow-2xl group-hover:scale-110 transition-transform duration-300">
              <span class="text-3xl font-bold text-white">98%</span>
            </div>
            <h3 class="text-2xl font-bold mb-2">University Admission</h3>
            <p class="text-black-100">Students accepted to universities</p>
          </div>

          <div class="text-center group">
            <div class="w-24 h-24 bg-gradient-to-br from-green-400 to-emerald-500 rounded-full mx-auto mb-6 flex items-center justify-center shadow-2xl group-hover:scale-110 transition-transform duration-300">
              <span class="text-3xl font-bold text-white">19+</span>
            </div>
            <h3 class="text-2xl font-bold mb-2">Expert Teachers</h3>
            <p class="text-black-100">Qualified and experienced educators</p>
          </div>

          <div class="text-center group">
            <div class="w-24 h-24 bg-gradient-to-br from-orange-400 to-red-500 rounded-full mx-auto mb-6 flex items-center justify-center shadow-2xl group-hover:scale-110 transition-transform duration-300">
              <span class="text-3xl font-bold text-white">37:1</span>
            </div>
            <h3 class="text-2xl font-bold mb-2">Student-Teacher Ratio</h3>
            <p class="text-black-100">Personalized attention for every student</p>
          </div>

          <div class="text-center group">
            <div class="w-24 h-24 bg-gradient-to-br from-pink-400 to-purple-500 rounded-full mx-auto mb-6 flex items-center justify-center shadow-2xl group-hover:scale-110 transition-transform duration-300">
              <span class="text-3xl font-bold text-white">5+</span>
            </div>
            <h3 class="text-2xl font-bold mb-2">School Clubs</h3>
            <p class="text-black-100">Extracurricular learning opportunities</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed, watch } from 'vue';

// Props
const props = defineProps({
  searchQuery: {
    type: String,
    default: ''
  }
});

const isSearchActive = computed(() => props.searchQuery && props.searchQuery.trim() !== '');

const matchText = (query, ...parts) =>
  parts.some((p) => {
    const text = String(p || '').toLowerCase()
    return text.includes(query) || query.includes(text)
  })

const searchResults = computed(() => {
  if (!isSearchActive.value) return null;

  const query = props.searchQuery.toLowerCase().trim();
  return {
    automobileTech:
      matchText(query, 'automobile', 'technology', 'auto', 'garage', 'vehicle', 'engine') ||
      autoMobiles[0].features1.some((feature) => feature.toLowerCase().includes(query)),
    buildingConstruction:
      matchText(query, 'building', 'construction', 'masonry') ||
      autoMobiles[0].features2.some((feature) => feature.toLowerCase().includes(query)),
    carpentryTailoring:
      matchText(query, 'carpentry', 'tailoring', 'sewing', 'wood', 'fashion') ||
      autoMobiles[0].features3.some((feature) => feature.toLowerCase().includes(query)),
    computerLab:
      matchText(query, 'computer', 'lab', 'digital', 'ict') ||
      computerLabComponents[0].features.some((feature) => feature.toLowerCase().includes(query)),
  };
});

// Scroll to search results when search query changes
watch(() => props.searchQuery, (newQuery) => {
  if (newQuery && newQuery.trim() !== '') {
    // Wait for DOM to update
    setTimeout(() => {
      const searchResultElements = document.querySelectorAll('.search-highlight');
      if (searchResultElements.length > 0) {
        searchResultElements[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
    }, 100);
  }
}, { immediate: true });


// Images of automobile for sliders
const AutomobileTechImages = ref(['https://res.cloudinary.com/dck2vzccq/image/upload/v1752745560/moteur2Resized_pt3a83.jpg',

]);

// Images of building construction for sliders

const buildingConstractionImages = ref([
'https://res.cloudinary.com/dck2vzccq/image/upload/v1752774851/constr3_j4zg1g.jpg',
'https://res.cloudinary.com/dck2vzccq/image/upload/v1752078187/ConstructionField_b2qbga.jpg'
]);

// Images of Carpentry & Tailoring for sliders

const carpentryTailoringImages = ref([
'https://res.cloudinary.com/dck2vzccq/image/upload/v1752741665/carpentryWorkShop005_r3gocb.jpg',
'https://res.cloudinary.com/dck2vzccq/image/upload/v1752775223/tailoringResized2_f1gkyl.jpg'
]);

// Images of computer lab for sliders
const computerLabImages = ref([
 'https://res.cloudinary.com/dck2vzccq/image/upload/v1752080182/DSC_0123_yqqtu9.jpg'
]);

const academicClubsImages = ref([
 'https://res.cloudinary.com/dck2vzccq/image/upload/v1752078864/2._uc8kz0.jpg',
 'https://res.cloudinary.com/dck2vzccq/image/upload/v1752140950/4._dz31vg.jpg' // Add appropriate academic club images here
]);

const campusExcellenceImages = ref([
 'https://res.cloudinary.com/dck2vzccq/image/upload/v1752140950/4._dz31vg.jpg',
 'https://res.cloudinary.com/dck2vzccq/image/upload/v1752078864/2._uc8kz0.jpg'
]);

// Slider state
const currentAutomobileTechImageIndex = ref(0);
const currentBuildingConstractionImageIndex = ref(0);
const currentCarpentryTailoringImageIndex = ref(0);
const currentComputerLabImageIndex = ref(0);
const currentAcademicClubsImageIndex = ref(0);
const currentCampusExcellenceImageIndex = ref(0);

let automobileTechInterval = null;
let buildingConstractionInterval = null;
let carpentryTailoringInterval = null;
let computerLabInterval = null;
let campusExcellenceInterval = null;

// Functions to advance sliders
const nextAutomobileTechImage = () => {
  currentAutomobileTechImageIndex.value = (currentAutomobileTechImageIndex.value + 1) % AutomobileTechImages.value.length;
};
const nextBuildingConstractionImage = () => {
  currentBuildingConstractionImageIndex.value = (currentBuildingConstractionImageIndex.value + 1) % buildingConstractionImages.value.length;
};
const nextcarpentryTailoringImage = () => {
  currentCarpentryTailoringImageIndex.value = (currentCarpentryTailoringImageIndex.value + 1) % carpentryTailoringImages.value.length;
};
const nextComputerLabImage = () => {
  currentComputerLabImageIndex.value = (currentComputerLabImageIndex.value + 1) % computerLabImages.value.length;
};


const nextCampusExcellenceImage = () => {
  currentCampusExcellenceImageIndex.value = (currentCampusExcellenceImageIndex.value + 1) % campusExcellenceImages.value.length;
};

onMounted(() => {
  automobileTechInterval = setInterval(nextAutomobileTechImage, 3000);
  buildingConstractionInterval = setInterval(nextBuildingConstractionImage, 3200); // 
  carpentryTailoringInterval = setInterval(nextcarpentryTailoringImage, 3500);
  computerLabInterval = setInterval(nextComputerLabImage, 3000);
  campusExcellenceInterval = setInterval(nextCampusExcellenceImage, 3800);
});

onUnmounted(() => {
  clearInterval(automobileTechInterval);
  clearInterval(buildingConstractionInterval);
  clearInterval(carpentryTailoringInterval);
  clearInterval(computerLabInterval);
  clearInterval(campusExcellenceInterval);
});
// automobile array for the autoMobile section
const autoMobiles = [
  {
    id: 1,
    title: 'Automobile Technology',
    description: 'Program grounded in science and innovation, offering hands-on experiences in mechanics, diagnostics, and engineering to prepare students for the future of mobility and smart transportation',
    image: 'path/to/automobile-image.jpg', // Placeholder for actual image path
    features1: [
      'Air Compressor Systems',
      'Wheel Balancer',
      'Grinder Machine',
      'Two Stroke Engine',
      'Acetylene Welding',
      'Exhaust Gas Analyser',
      'Battery Charger',
      'Compressor Ring',
      'Alternator',
      'Starter Motor',
      'Service Bay',
    ],
    features2: [
      'Theodolite & its Accessories',
      'Dumpy Level & its Accessories',
      ' Tiles Cutter',
      'Pipe Wrench Vice',
      'Grinding Disk',
      'Divider Strip',
      'Angle Grinder',
      'Chips',
      'Rubber Mallet',
      'White Cement & Tiles ',
      'Fixture (Sanitary Appliances)',
      'Spirit Level & Plumb Bob',
      'Pipes & Fittings',
      'Welding Machine',
      'Hose Pipe'
    ],
    features3: [
      'Sewing Machine',
      'Flat iron',
      'Velcro',
      'Kanete Button',
      'Fabrics',
      'Needle, Thread & Scissors',
      'Claw Hammer',
      'Cross Cut Saw',
      'Hand Saw',
      'Chisels',
      'Screwdriver',
      'Wooden Ruler',
    ]
  }
];
// computer lab array for the computer lab section
const computerLabComponents = [
  {
    id: 1,
    title: 'Modern Computer Lab',
    description: 'State-of-the-art computer lab with the latest technology and resources to support digital learning and research.',
    image: 'path/to/computer-lab-image.jpg', // Placeholder for actual image path
    features: [
      'High-speed internet access',
      'Latest software and hardware',
      'Quiet study zones',
      'Digital resources and databases',
      '24/7 online portal access'
    ]
  }
];
</script>

<style scoped>
/* Additional custom animations */
@keyframes float {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-10px); }
}

.group:hover .animate-float {
  animation: float 3s ease-in-out infinite;
}

/* Search highlight styles */
.search-highlight {
  position: relative;
  padding: 2px 4px;
  border-radius: 4px;
  transition: all 0.3s ease;
}

.search-highlight::before {
  content: '';
  position: absolute;
  left: -8px;
  top: 50%;
  transform: translateY(-50%);
  width: 4px;
  height: 80%;
  background-color: #3b82f6;
  border-radius: 2px;
}

@keyframes pulse-highlight {
  0%, 100% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.5); }
  50% { box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.5); }
}

.search-highlight {
  animation: pulse-highlight 2s infinite;
}
</style>