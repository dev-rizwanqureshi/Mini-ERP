<script setup lang="ts">
defineProps<{ title: string; value: string | number }>();
</script>

<template>
  <section class="rounded-md border border-neutral-200 bg-white p-5 dark:border-neutral-800 dark:bg-neutral-900">
    <p class="text-sm text-neutral-500">{{ title }}</p>
    <p class="mt-2 text-2xl font-semibold">{{ value }}</p>
  </section>
</template>
