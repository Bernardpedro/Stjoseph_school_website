<script setup>
const route = useRoute()
await navigateTo({ path: '/card-overview', query: route.query }, { redirectCode: 301 })
</script>
