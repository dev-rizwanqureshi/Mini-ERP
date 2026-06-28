<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
defineProps<{ links?: { url: string | null; label: string; active: boolean }[] }>();
</script>

<template>
  <nav v-if="links?.length" class="flex flex-wrap gap-2">
    <Link
      v-for="link in links"
      :key="link.label"
      :href="link.url ?? '#'"
      class="rounded-md border px-3 py-1 text-sm"
      :class="{ 'bg-teal-700 text-white': link.active, 'pointer-events-none opacity-40': !link.url }"
      v-html="link.label"
    />
  </nav>
</template>
