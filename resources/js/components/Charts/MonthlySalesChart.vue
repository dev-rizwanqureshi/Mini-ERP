<script setup lang="ts">
defineProps<{ data: { label: string; value: number }[] }>();
</script>

<template>
  <div class="space-y-2">
    <div v-for="row in data" :key="row.label" class="grid grid-cols-[90px_1fr_80px] items-center gap-3 text-sm">
      <span>{{ row.label }}</span>
      <div class="h-2 rounded bg-neutral-100"><div class="h-2 rounded bg-teal-700" :style="{ width: `${Math.min(100, row.value / 200)}%` }" /></div>
      <span class="text-right">${{ Number(row.value).toFixed(0) }}</span>
    </div>
  </div>
</template>
