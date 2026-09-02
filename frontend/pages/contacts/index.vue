<template>
  <div class="bg-white dark:bg-gray-900">
    <div class="bg-white dark:bg-gray-900">
      <div class="mb-[0px]">
        <Contact />
      </div>
    </div>
  </div>
</template>

<script setup>
import Contact from '~/components/contact/Contact.vue';

definePageMeta({
  layout:'default'
});

usePageSeo({
  title: 'Contact Us',
  description: 'Get in touch with Saint Joseph Technical Secondary School Nzuki. Find our contact information, location, and office hours.',
})

</script>
