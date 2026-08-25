<template>
  <UsersManagement />
</template>

<script setup>
definePageMeta({
  layout: 'default',
  middleware: ['admin'],
})
</script>
