<script setup lang="ts">
import { router } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps<{ action: string; search?: string; filters?: Record<string, unknown>; placeholder?: string }>();
const term = ref(props.search ?? "");

function submit() {
  const params = { ...(props.filters ?? {}), search: term.value };
  delete (params as Record<string, unknown>).page;

  router.get(props.action, params, { preserveState: true, preserveScroll: true });
}
</script>

<template>
  <form class="flex flex-wrap gap-2" @submit.prevent="submit">
    <input v-model="term" class="h-10 min-w-64 rounded-md border px-3 text-sm dark:bg-neutral-900" :placeholder="placeholder ?? 'Search'" />
    <button class="rounded-md bg-teal-700 px-4 text-sm font-medium text-white">Search</button>
  </form>
</template>
