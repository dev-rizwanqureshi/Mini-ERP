<script setup lang="ts">defineProps<{ users: any }>();</script>
<template><div class="space-y-4 p-6"><h1 class="text-2xl font-semibold">Users</h1><p>{{ users.data.length }} users</p></div></template>
