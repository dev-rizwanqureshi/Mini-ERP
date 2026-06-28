<script setup lang="ts">
defineProps<{ label: string; value: string | number }>();
</script>

<template>
  <div class="rounded-md border p-4 dark:border-neutral-800">
    <div class="text-sm text-neutral-500">{{ label }}</div>
    <div class="mt-2 text-2xl font-semibold">{{ value }}</div>
  </div>
</template>
