<template>
  <div class="bg-white dark:bg-gray-900 min-h-screen">  
    <!-- OUR VISION AND MISSION -->
    <div class="max-w-6xl mx-auto px-4 mb-8">
      <div class="grid lg:grid-cols-2 gap-8">
        <!-- OUR VISION -->
        <div class="relative group">
          <div class="ml-4 bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-3xl p-0 h-full shadow-lg hover:shadow-2xl transition-all duration-500">
            <!-- OUR VISION Image Slider -->
            <div class="relative w-full h-[400px] rounded-2xl mb-0 overflow-hidden bg-gradient-to-br from-blue-200/50 to-indigo-200/50 dark:from-blue-800/30 dark:to-indigo-800/30">
              <img src="https://res.cloudinary.com/dck2vzccq/image/upload/v1752126687/OURVISION_oy7ohr.png" alt="OUR VISION Image" class="w-full h-full object-fill object-center">
            </div>
          </div>
        </div>
        <!-- OUR MISSION -->
        <div class="relative group">
          <div class="mr-4 bg-gradient-to-br from-green-50 to-emerald-100 dark:from-green-900/20 dark:to-emerald-900/20 rounded-3xl p-0 h-full shadow-lg hover:shadow-2xl transition-all duration-500">
            <!-- OUR MISSION Image  -->
            <div class="relative w-full h-[400px] rounded-2xl mb-0 overflow-hidden bg-gradient-to-br from-green-200/50 to-emerald-200/50 dark:from-green-800/30 dark:to-emerald-800/30">
              <img src="https://res.cloudinary.com/dck2vzccq/image/upload/v1752126683/OURMISSION_r1qzd4.png" alt="OUR MISSION Image" class="w-full h-full object-fill object-center">
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ORGANISATIONAL STRUCTURE OF SAINT JOSEPH TSS NZUKI -->
    <div class="max-w-6xl mx-auto px-4">
      <div class="grid lg:grid-cols-1">
        <div class="relative group">
          <div class="bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-3xl p-8 h-full shadow-lg hover:shadow-2xl transition-all duration-500">
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 text-center">
              {{ $t('school.orgStructure') }}
            </h3>
            <!-- SCHOOL STRUCTURE Image -->
            <div class="relative w-full h-[500px] rounded-2xl overflow-hidden bg-gradient-to-br from-blue-200/50 to-indigo-200/50 dark:from-blue-800/30 dark:to-indigo-800/30">
              <img src="https://res.cloudinary.com/dck2vzccq/image/upload/v1752126767/SchoolStructure1_if3kbc.png" alt="ORGANISATIONAL STRUCTURE" class="w-full h-full object-fill object-center">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
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
</style>