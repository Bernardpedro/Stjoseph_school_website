<template>
 <!-- School compound -->
 <div class="py-0 sm:py-12 md:py-16 lg:py-20 bg-gray-50 dark:bg-gray-800 mb-8 sm:mb-12 lg:mb-16">
   <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
     <div class="text-center mb-8 sm:mb-12 lg:mb-16">
       <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 dark:text-white mb-4 sm:mb-6 px-2">
         {{ $t('school.compoundTitle') }}
       </h2>
     </div>

     <!-- First Row: Classrooms -->
     <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8 mb-6 sm:mb-8">
       <!-- Classrooms -->
       <div
         class="group relative bg-white dark:bg-gray-900 rounded-xl sm:rounded-2xl shadow-lg hover:shadow-xl sm:hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-1 sm:hover:-translate-y-2 overflow-hidden"
         :class="{ 'search-highlight ring-2 ring-blue-400': isSearchActive && searchResults?.classrooms }"
         :data-search-hit="isSearchActive && searchResults?.classrooms ? 'true' : undefined"
       >
         <div class="absolute inset-0 bg-gradient-to-br from-green-400/20 to-blue-500/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
         <div class="relative p-4 sm:p-6 lg:p-8">
           <h3 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white mb-3 sm:mb-4 text-center">
             {{ $t('school.classrooms') }}
           </h3>

           <!-- Classrooms Images -->
           <NuxtLink :to="`/card-overview?type=academic&id=classrooms`" class="block">
             <div class="relative w-full h-48 sm:h-60 md:h-72 lg:h-80 rounded-lg sm:rounded-2xl mb-4 sm:mb-6 overflow-hidden bg-gradient-to-br from-blue-200/50 to-indigo-200/50 dark:from-blue-800/30 dark:to-indigo-800/30">
               <div v-for="(image, index) in classroomsImages" :key="index"
                    class="absolute w-full h-full transition-opacity duration-1000 ease-in-out"
                    :class="{'opacity-100': index === currentClassroomsImageIndex, 'opacity-0': index !== currentClassroomsImageIndex}">
                 <img :src="cldOptimize(image, 800)" alt="Classrooms Image" class="w-full h-full object-cover object-center" loading="lazy">
               </div>
             </div>

             <!-- Classrooms Description -->
              <p class="text-sm sm:text-base text-gray-600 dark:text-gray-300 mb-4 sm:mb-6 leading-relaxed">
                {{ $t('school.classroomsDesc') }}
              </p>
           </NuxtLink>

           <!-- Search indicator -->
           <div v-if="isSearchActive && searchResults && searchResults.classrooms"
                class="mt-3 sm:mt-4 p-2 sm:p-3 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded-lg text-xs sm:text-sm">
             Search results found in Classrooms
           </div>
         </div>
       </div>

       <!-- Assembly Hall -->
       <div
         class="group relative bg-white dark:bg-gray-900 rounded-xl sm:rounded-2xl shadow-lg hover:shadow-xl sm:hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-1 sm:hover:-translate-y-2 overflow-hidden"
         :class="{ 'search-highlight ring-2 ring-blue-400': isSearchActive && searchResults?.assemblyHall }"
         :data-search-hit="isSearchActive && searchResults?.assemblyHall ? 'true' : undefined"
       >
         <div class="absolute inset-0 bg-gradient-to-br from-orange-400/20 to-red-500/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
         <div class="relative p-4 sm:p-6 lg:p-8">
           <h3 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white mb-3 sm:mb-4 text-center">
              {{ $t('school.assemblyHall') }}
           </h3>

           <!-- Assembly Hall Images -->
           <NuxtLink :to="`/card-overview?type=academic&id=assembly`" class="block">
             <div class="relative w-full h-48 sm:h-60 md:h-72 lg:h-80 rounded-lg sm:rounded-2xl mb-4 sm:mb-6 overflow-hidden bg-gradient-to-br from-blue-200/50 to-indigo-200/50 dark:from-blue-800/30 dark:to-indigo-800/30">
               <div v-for="(image, index) in assemblyHallImages" :key="index"
                    class="absolute w-full h-full transition-opacity duration-1000 ease-in-out"
                    :class="{'opacity-100': index === currentAssemblyHallImageIndex, 'opacity-0': index !== currentAssemblyHallImageIndex}">
                 <img :src="cldOptimize(image, 800)" alt="Assembly Hall Image" class="w-full h-full object-cover object-center" loading="lazy">
               </div>
             </div>

             <!-- Assembly Hall Description -->
             <p class="text-sm sm:text-base text-gray-600 dark:text-gray-300 mb-4 sm:mb-6 leading-relaxed">
               {{ $t('school.assemblyHallDesc') }}
             </p> 
           </NuxtLink>

           <!-- Search indicator -->
           <div v-if="isSearchActive && searchResults && searchResults.assemblyHall"
                class="mt-3 sm:mt-4 p-2 sm:p-3 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded-lg text-xs sm:text-sm">
             Search results found in Assembly Hall
           </div>
         </div>
       </div>
     </div>

     <!-- Second Row: Girls Dormitory -->
     <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8">
       <!-- Girls Dormitory -->
       <div
         class="group relative bg-white dark:bg-gray-900 rounded-xl sm:rounded-2xl shadow-lg hover:shadow-xl sm:hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-1 sm:hover:-translate-y-2 overflow-hidden"
         :class="{ 'search-highlight ring-2 ring-blue-400': isSearchActive && searchResults?.girlsDormitory }"
         :data-search-hit="isSearchActive && searchResults?.girlsDormitory ? 'true' : undefined"
       >
         <div class="absolute inset-0 bg-gradient-to-br from-purple-400/20 to-indigo-500/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
         <div class="relative p-4 sm:p-6 lg:p-8">
           <h3 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white mb-3 sm:mb-4 text-center">
             {{ $t('school.girlsDormitory') }}
           </h3>


           <!-- Girls Dormitory Images -->
           <NuxtLink :to="`/card-overview?type=academic&id=girls-dormitory`" class="block">
             <div class="relative w-full h-48 sm:h-60 md:h-72 lg:h-80 rounded-lg sm:rounded-2xl mb-4 sm:mb-6 overflow-hidden bg-gradient-to-br from-blue-200/50 to-indigo-200/50 dark:from-blue-800/30 dark:to-indigo-800/30">
               <div v-for="(image, index) in girlsDormitoryImages" :key="index"
                    class="absolute w-full h-full transition-opacity duration-1000 ease-in-out"
                    :class="{'opacity-100': index === currentGirlsDormitoryImageIndex, 'opacity-0': index !== currentGirlsDormitoryImageIndex}">
                 <img :src="cldOptimize(image, 800)" alt="Girls Dormitory Image" class="w-full h-full object-cover object-center" loading="lazy">
               </div>
             </div>
             <!-- Girls Dormitory Description -->
              <p class="text-sm sm:text-base text-gray-600 dark:text-gray-300 mb-4 sm:mb-6 leading-relaxed">
              {{ $t('school.girlsDormitoryDesc') }}
              </p>
           </NuxtLink>

           <!-- Search indicator -->
           <div v-if="isSearchActive && searchResults && searchResults.girlsDormitory"
                class="mt-3 sm:mt-4 p-2 sm:p-3 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded-lg text-xs sm:text-sm">
             Search results found in Girls Dormitory
           </div>
         </div>
       </div>

       <!-- Boys Dormitory -->
       <div
         class="group relative bg-white dark:bg-gray-900 rounded-xl sm:rounded-2xl shadow-lg hover:shadow-xl sm:hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-1 sm:hover:-translate-y-2 overflow-hidden"
         :class="{ 'search-highlight ring-2 ring-blue-400': isSearchActive && searchResults?.boysDormitory }"
         :data-search-hit="isSearchActive && searchResults?.boysDormitory ? 'true' : undefined"
       >
         <div class="absolute inset-0 bg-gradient-to-br from-purple-400/20 to-indigo-500/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
         <div class="relative p-4 sm:p-6 lg:p-8">
           <h3 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white mb-3 sm:mb-4 text-center">
             {{ $t('school.boysDormitory') }}
           </h3>

           <!-- Boys Dormitory Images -->
           <NuxtLink :to="`/card-overview?type=academic&id=boysDormitory`" class="block">
             <div class="relative w-full h-48 sm:h-60 md:h-72 lg:h-80 rounded-lg sm:rounded-2xl mb-4 sm:mb-6 overflow-hidden bg-gradient-to-br from-blue-200/50 to-indigo-200/50 dark:from-blue-800/30 dark:to-indigo-800/30">
               <div v-for="(image, index) in boysDormitoryImages" :key="index"
                    class="absolute w-full h-full transition-opacity duration-1000 ease-in-out"
                    :class="{'opacity-100': index === currentBoysDormitoryImageIndex, 'opacity-0': index !== currentBoysDormitoryImageIndex}">
                 <img :src="cldOptimize(image, 800)" alt="Boys Dormitory Image" class="w-full h-full object-cover object-center" loading="lazy">
               </div>
             </div>

             <!-- Boys Dormitory Description -->
              <p class="text-sm sm:text-base text-gray-600 dark:text-gray-300 mb-4 sm:mb-6 leading-relaxed">
                {{ $t('school.boysDormitoryDesc') }}
              </p>
           </NuxtLink>

           <div v-if="isSearchActive && searchResults && searchResults.boysDormitory"
                class="mt-3 sm:mt-4 p-2 sm:p-3 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded-lg text-xs sm:text-sm">
             Search results found in Boys Dormitory
           </div>
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

const includesAny = (query, keywords) =>
  keywords.some((k) => k.includes(query) || query.includes(k));

const searchResults = computed(() => {
  if (!isSearchActive.value) return null;

  const query = props.searchQuery.toLowerCase().trim();
  return {
    classrooms: includesAny(query, ['classroom', 'classrooms', 'learning', 'class']),
    assemblyHall: includesAny(query, ['assembly', 'hall', 'ceremony', 'gathering']),
    girlsDormitory: includesAny(query, ['girls', 'dormitory', 'boarding', 'dorm']),
    boysDormitory: includesAny(query, ['boys', 'dormitory', 'boarding', 'dorm']),
  };
});

watch(() => props.searchQuery, (newQuery) => {
  if (newQuery && newQuery.trim() !== '') {
    setTimeout(() => {
      const match =
        document.querySelector('[data-search-hit="true"]') ||
        document.querySelector('.search-highlight');
      if (match) match.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }, 120);
  }
}, { immediate: true });


// Images of automobile for sliders
const AutomobileTechImages = ref([
  new URL('@/assets/img/AutoMobile/compressor2.jpg', import.meta.url),
  'https://res.cloudinary.com/dck2vzccq/image/upload/v1752078021/capeau1_vv6np8.jpg',
  'https://res.cloudinary.com/dck2vzccq/image/upload/v1752078037/engine4_vyhrvi.jpg',
  new URL('@/assets/img/AutoMobile/moteur1.jpg', import.meta.url),
]);

// Images of building construction for sliders

const buildingConstructionImages = ref([
  'https://res.cloudinary.com/dck2vzccq/image/upload/v1752078179/constr1_nfdcod.jpg',
  new URL('@/assets/img/Construction/constr2.jpg', import.meta.url),
  new URL('@/assets/img/Construction/constr3.jpg', import.meta.url),
]);

// Images of Carpentry for sliders

const carpentryImages = ref([
'https://res.cloudinary.com/dck2vzccq/image/upload/v1752078673/Carpentry1_zn8ktl.jpg',
'https://res.cloudinary.com/dck2vzccq/image/upload/v1752078674/Carpentry2_sdzt0d.jpg',
'https://res.cloudinary.com/dck2vzccq/image/upload/v1752741665/carpentryWorkShop005_r3gocb.jpg',
'https://res.cloudinary.com/dck2vzccq/image/upload/v1752741661/carpentryWorkShop003_it0lmr.jpg',
'https://res.cloudinary.com/dck2vzccq/image/upload/v1752741657/carpentryWorkShop002_kzytdw.jpg',
])

// Images of Tailoring for sliders

const tailoringImages = ref([
  'https://res.cloudinary.com/dck2vzccq/image/upload/v1752775223/tailoringResized2_f1gkyl.jpg',
  'https://res.cloudinary.com/dck2vzccq/image/upload/v1752078742/tailoring5_elkcvz.jpg'

]);

// Images of computer lab for sliders
const computerLabImages = ref([
  'https://res.cloudinary.com/dck2vzccq/image/upload/v1752080145/DSC_0121_rsjx6k.jpg',  
  'https://res.cloudinary.com/dck2vzccq/image/upload/v1752080177/DSC_0122_revr2l.jpg',
  'https://res.cloudinary.com/dck2vzccq/image/upload/v1752080182/DSC_0123_yqqtu9.jpg',
  new URL('@/assets/img/computerLab/DSC_0124.JPG', import.meta.url),
]);

const campusExcellenceImages = ref([
  'https://res.cloudinary.com/dck2vzccq/image/upload/v1752140950/4._dz31vg.jpg',
  'https://res.cloudinary.com/dck2vzccq/image/upload/v1752078864/2._uc8kz0.jpg'
]);

// Images of Classrooms for sliders
const classroomsImages = ref([
  'https://res.cloudinary.com/dck2vzccq/image/upload/v1752079026/8._odsquj.jpg',
  'https://res.cloudinary.com/dck2vzccq/image/upload/v1752771935/SchoolCompound006_wrzj5r.jpg',
  'https://res.cloudinary.com/dck2vzccq/image/upload/v1752771577/ClassRoom001_ambcrn.jpg',
  'https://res.cloudinary.com/dck2vzccq/image/upload/v1752771937/SchoolCompound002_f7geff.jpg',
  'https://res.cloudinary.com/dck2vzccq/image/upload/v1752771577/ClassRoom002_yrl0hs.jpg',
]);

// girlsDormitoryImages
const girlsDormitoryImages = ref([
  'https://res.cloudinary.com/dck2vzccq/image/upload/v1752078935/16_oshfay.jpg',
  'https://res.cloudinary.com/dck2vzccq/image/upload/v1752141026/14_tx7qxv.jpg'
]);

// boysDormitoryImages
const boysDormitoryImages = ref([
  'https://res.cloudinary.com/dck2vzccq/image/upload/v1752078965/13_axv4kj.jpg',
  'https://res.cloudinary.com/dck2vzccq/image/upload/v1752078966/12_iczfhn.jpg'
]); 

// assemblyHallImages
const assemblyHallImages = ref([
  'https://res.cloudinary.com/dck2vzccq/image/upload/v1752078864/2._uc8kz0.jpg',
  'https://res.cloudinary.com/dck2vzccq/image/upload/v1753956667/DSC_0613.JPGResized1_zqz5go.jpg',
  'https://res.cloudinary.com/dck2vzccq/image/upload/v1753956666/DSC_0611.JPGResized2_f0xrdd.jpg'
]);

// partnersImages
const partnersImages = ref([
  'https://res.cloudinary.com/dck2vzccq/image/upload/v1752738861/rlp_ruanda_logo_jt9mcf.png',
]);



// Slider state
const currentAutomobileTechImageIndex = ref(0);
const currentBuildingConstructionImageIndex = ref(0);
const currentCarpentryImageIndex = ref(0);
const currentTailoringImageIndex = ref(0);
const currentComputerLabImageIndex = ref(0);
const currentCampusExcellenceImageIndex = ref(0);
const currentClassroomsImageIndex = ref(0);
const currentGirlsDormitoryImageIndex = ref(0);
const currentBoysDormitoryImageIndex = ref(0);
const currentAssemblyHallImageIndex = ref(0);
const currentPartnersImageIndex = ref(0);

let automobileTechInterval = null;
let buildingConstructionInterval = null;
let carpentryInterval = null;
let tailoringInterval = null;
let computerLabInterval = null;
let campusExcellenceInterval = null;
let classroomsInterval = null;
let girlsDormitoryInterval = null;
let boysDormitoryInterval = null;
let assemblyHallInterval = null;
let partnersInterval = null;


// Functions to advance sliders
const nextAutomobileTechImage = () => {
  currentAutomobileTechImageIndex.value = (currentAutomobileTechImageIndex.value + 1) % AutomobileTechImages.value.length;
};
const nextBuildingConstructionImage = () => {
  currentBuildingConstructionImageIndex.value = (currentBuildingConstructionImageIndex.value + 1) % buildingConstructionImages.value.length;
};
const nextCarpentryImage = () => {
  currentCarpentryImageIndex.value = (currentCarpentryImageIndex.value + 1) % carpentryImages.value.length;
};
const nextTailoringImage = () => {
  currentTailoringImageIndex.value = (currentTailoringImageIndex.value + 1) % tailoringImages.value.length;
};
const nextComputerLabImage = () => {
  currentComputerLabImageIndex.value = (currentComputerLabImageIndex.value + 1) % computerLabImages.value.length;
};

const nextCampusExcellenceImage = () => {
  currentCampusExcellenceImageIndex.value = (currentCampusExcellenceImageIndex.value + 1) % campusExcellenceImages.value.length;
};
const nextClassroomsImage = () => {
  currentClassroomsImageIndex.value = (currentClassroomsImageIndex.value + 1) % classroomsImages.value.length;
};
const nextGirlsDormitoryImage = () => {
  currentGirlsDormitoryImageIndex.value = (currentGirlsDormitoryImageIndex.value + 1) % girlsDormitoryImages.value.length;
};
const nextAssemblyHallImage = () => {
  currentAssemblyHallImageIndex.value = (currentAssemblyHallImageIndex.value + 1) % assemblyHallImages.value.length;
};
const nextBoysDormitoryImage = () => {
  currentBoysDormitoryImageIndex.value = (currentBoysDormitoryImageIndex.value + 1) % boysDormitoryImages.value.length;
};
const nextPartnersImage = () => {
  currentPartnersImageIndex.value = (currentPartnersImageIndex.value + 1) % partnersImages.value.length;
};

onMounted(() => {
  automobileTechInterval = setInterval(nextAutomobileTechImage, 3000);
  buildingConstructionInterval = setInterval(nextBuildingConstructionImage, 3200);
  carpentryInterval = setInterval(nextCarpentryImage, 3500);
  tailoringInterval = setInterval(nextTailoringImage, 3700);
  computerLabInterval = setInterval(nextComputerLabImage, 3000);
  campusExcellenceInterval = setInterval(nextCampusExcellenceImage, 3200);
  classroomsInterval = setInterval(nextClassroomsImage, 3500);
  girlsDormitoryInterval = setInterval(nextGirlsDormitoryImage, 3700);
  boysDormitoryInterval = setInterval(nextBoysDormitoryImage, 3750);
  assemblyHallInterval = setInterval(nextAssemblyHallImage, 3800);
  partnersInterval = setInterval(nextPartnersImage, 4000);
});

onUnmounted(() => {
  clearInterval(automobileTechInterval);
  clearInterval(buildingConstructionInterval);
  clearInterval(carpentryInterval);
  clearInterval(tailoringInterval);
  clearInterval(computerLabInterval);
  clearInterval(campusExcellenceInterval);
  clearInterval(classroomsInterval);
  clearInterval(girlsDormitoryInterval);
  clearInterval(boysDormitoryInterval);
  clearInterval(assemblyHallInterval);
  clearInterval(partnersInterval);
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
      ''
    ],
    features4: [
      'Sewing Machine',
      'Flat iron',
      'Velcro',
      'Kanete Button',
      'Fabrics',
      'Needle, Thread & Scissors'
    ],
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