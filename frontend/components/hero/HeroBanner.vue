<template>
<!-- Announcement Bar -->
  <div class="bg-black text-white py-2 relative overflow-hidden">
    <div class="animate-marquee whitespace-nowrap">
      <!-- Duplicated content for seamless looping -->
      <template v-for="i in 2" :key="i">
        <a href="/admission" class="font-semibold hover:text-yellow-400 transition">
          {{ $t('hero.applicationsOpen') }}
        </a>
        <a href="/card-overview?type=hero&id=motto" class="font-semibold hover:text-yellow-400 transition">
          {{ $t('hero.motto') }}
        </a>
        <a href="#nationWide" class="font-semibold hover:text-yellow-400 transition">
          {{ $t('hero.rankedFirst') }}
        </a>
        <a href="#nationWide" class="font-semibold hover:text-yellow-400 transition">
          {{ $t('hero.topNationwide') }}
        </a>
      </template>
    </div>
  </div>

</template>

<script setup>
</script>

<style scoped>
/* Marquee Animation */
@keyframes marquee {
  0% { transform: translateX(0); }
  100% { transform: translateX(-50%); }
}

.animate-marquee {
  display: inline-block;
  white-space: nowrap;
  padding-right: 0;
  animation: marquee 20s linear infinite;
}

.animate-marquee a {
  display: inline-flex;
  align-items: center;
  margin-right: 3rem;
}
</style>
