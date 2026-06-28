<script setup lang="ts">
defineProps<{ data: { label: string; value: number }[] }>();
</script>

<template>
  <div class="grid gap-2 text-sm">
    <div v-for="row in data" :key="row.label" class="flex justify-between">
      <span>{{ row.label }}</span><strong>{{ row.value }}</strong>
    </div>
  </div>
</template>
