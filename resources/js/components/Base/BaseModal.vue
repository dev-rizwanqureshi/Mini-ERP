<script setup lang="ts">
defineProps<{ open?: boolean; title?: string }>();
defineEmits<{ close: [] }>();
</script>

<template>
  <div v-if="open" class="fixed inset-0 z-50 grid place-items-center bg-black/40 p-4">
    <section class="w-full max-w-lg rounded-md bg-white p-5 shadow-xl dark:bg-neutral-900">
      <div class="mb-4 flex items-center justify-between">
        <h2 class="font-semibold">{{ title }}</h2>
        <button type="button" class="rounded-md border px-2" @click="$emit('close')">×</button>
      </div>
      <slot />
      <footer class="mt-5 flex justify-end gap-2"><slot name="footer" /></footer>
    </section>
  </div>
</template>
