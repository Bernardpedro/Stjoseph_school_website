<template>
  <div>
    <Register />
  </div>
</template>

<script setup>
import Register from '~/components/auth/Register.vue';



definePageMeta({
  layout: 'default',
  middleware: ['admin']
});
</script>
