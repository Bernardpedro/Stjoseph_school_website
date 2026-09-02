<template>
  <div class="bg-white dark:bg-gray-900 min-h-screen">  
    <!-- OUR VISION AND MISSION -->
    <div class="max-w-6xl mx-auto px-4 mb-8">
      <VisionMissionCards />
    </div>

    <!-- ORGANISATIONAL STRUCTURE OF SAINT JOSEPH TSS NZUKI -->
    <div class="max-w-6xl mx-auto px-4">
      <div class="grid lg:grid-cols-1">
        <div class="relative group">
          <div class="bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-3xl p-8 h-full shadow-lg hover:shadow-2xl transition-all duration-500">
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 text-center">
              {{ $t('school.orgStructure') }}
            </h3>
            <!-- SCHOOL STRUCTURE Chart -->
            <OrganizationalChart />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import OrganizationalChart from './OrganizationalChart.vue';
import VisionMissionCards from './VisionMissionCards.vue';
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