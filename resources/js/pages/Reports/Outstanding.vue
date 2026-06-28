<script setup lang="ts">defineProps<{ report: any }>();</script>
<template><div class="space-y-4 p-6"><h1 class="text-2xl font-semibold">Outstanding Invoices</h1><p>{{ report.rows.length }} open invoices</p></div></template>
