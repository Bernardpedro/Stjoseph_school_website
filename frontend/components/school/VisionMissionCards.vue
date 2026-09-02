<template>
  <div class="w-full flex flex-col md:flex-row gap-6 md:gap-8 justify-center items-stretch">
    <article class="vm-card-bg rounded-[1.5rem] shadow-xl p-8 md:p-12 w-full md:w-1/2 flex flex-col items-center text-center text-white border border-gray-700/50 relative z-10">
      <h2 class="font-heading text-3xl md:text-4xl lg:text-5xl font-bold mb-4 drop-shadow-[0_2px_2px_rgba(0,0,0,0.8)] tracking-wide">
        {{ $t('school.visionTitle') }}
      </h2>
      <p class="font-body text-xl md:text-2xl lg:text-[1.75rem] leading-snug drop-shadow-[0_1px_3px_rgba(0,0,0,0.9)] font-semibold">
        {{ $t('school.visionText') }}
      </p>
    </article>

    <article class="vm-card-bg rounded-[1.5rem] shadow-xl p-8 md:p-12 w-full md:w-1/2 flex flex-col items-center text-center text-white border border-gray-700/50 relative z-10">
      <h2 class="font-heading text-3xl md:text-4xl lg:text-5xl font-bold mb-4 drop-shadow-[0_2px_2px_rgba(0,0,0,0.8)] tracking-wide">
        {{ $t('school.missionTitle') }}
      </h2>
      <p class="font-body text-xl md:text-2xl lg:text-[1.75rem] leading-snug drop-shadow-[0_1px_3px_rgba(0,0,0,0.9)] font-semibold">
        {{ $t('school.missionText') }}
      </p>
    </article>
  </div>
</template>

<script setup>
useHead({
  link: [
    {
      rel: 'stylesheet',
      href: 'https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Tinos:wght@400;700&display=swap',
    },
  ],
})
</script>

<style scoped>
.font-heading {
  font-family: 'Playfair Display', serif;
}

.font-body {
  font-family: 'Tinos', serif;
}

.vm-card-bg {
  background-color: #0b1c3c;
  background-image:
    radial-gradient(circle at 10% 20%, rgba(59, 130, 246, 0.4) 0%, transparent 20%),
    radial-gradient(circle at 90% 80%, rgba(59, 130, 246, 0.4) 0%, transparent 20%),
    radial-gradient(circle at 50% 50%, rgba(30, 64, 175, 0.5) 0%, transparent 50%),
    linear-gradient(135deg, rgba(255, 255, 255, 0.05) 0%, rgba(255, 255, 255, 0) 1px, transparent 1px, transparent 100%),
    linear-gradient(45deg, rgba(255, 255, 255, 0.05) 0%, rgba(255, 255, 255, 0) 1px, transparent 1px, transparent 100%);
  background-size: 100% 100%, 100% 100%, 100% 100%, 20px 20px, 20px 20px;
  position: relative;
  overflow: hidden;
}

.vm-card-bg::before {
  content: '';
  position: absolute;
  inset: 0;
  background-image:
    radial-gradient(circle at 30% 70%, rgba(255, 255, 255, 0.8) 1px, transparent 2px),
    radial-gradient(circle at 70% 30%, rgba(255, 255, 255, 0.8) 1px, transparent 2px),
    radial-gradient(circle at 50% 10%, rgba(255, 255, 255, 0.8) 1px, transparent 2px),
    radial-gradient(circle at 80% 80%, rgba(255, 255, 255, 0.8) 1px, transparent 2px),
    linear-gradient(15deg, transparent 48%, rgba(255, 255, 255, 0.2) 49%, rgba(255, 255, 255, 0.2) 51%, transparent 52%),
    linear-gradient(-35deg, transparent 48%, rgba(255, 255, 255, 0.2) 49%, rgba(255, 255, 255, 0.2) 51%, transparent 52%);
  background-size: 150px 150px, 120px 120px, 200px 200px, 180px 180px, 100% 100%, 100% 100%;
  pointer-events: none;
  opacity: 0.7;
}
</style>
