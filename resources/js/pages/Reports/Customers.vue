<script setup lang="ts">defineProps<{ report: any }>();</script>
<template><div class="space-y-4 p-6"><h1 class="text-2xl font-semibold">Customer Report</h1><p>{{ report.rows.length }} customers</p></div></template>
