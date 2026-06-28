<script setup lang="ts">
defineProps<{ text: string; color?: "success" | "warning" | "danger" | "info" | "neutral" }>();
</script>

<template>
  <span
    class="inline-flex rounded px-2 py-1 text-xs font-medium"
    :class="{
      'bg-emerald-100 text-emerald-800': color === 'success',
      'bg-amber-100 text-amber-800': color === 'warning',
      'bg-red-100 text-red-800': color === 'danger',
      'bg-sky-100 text-sky-800': color === 'info',
      'bg-neutral-100 text-neutral-700': !color || color === 'neutral',
    }"
  >
    {{ text }}
  </span>
</template>
