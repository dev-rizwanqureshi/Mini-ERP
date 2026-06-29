<script setup lang="ts">
withDefaults(defineProps<{ maxWidthClass?: string }>(), {
  maxWidthClass: "max-w-6xl",
});
</script>

<template>
  <div class="fixed inset-0 z-40 overflow-y-auto bg-black/50 p-4">
    <div class="flex min-h-full items-start justify-center py-6">
      <section :class="['w-full rounded-md border bg-white shadow-xl dark:border-neutral-800 dark:bg-neutral-950', maxWidthClass]">
        <slot />
      </section>
    </div>
  </div>
</template>
