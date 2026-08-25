<template>
  <div>
    <DashboardDocument />
  </div>
</template>

<script setup>
definePageMeta({
  layout: 'default',
  middleware: ['admin']
})
</script>