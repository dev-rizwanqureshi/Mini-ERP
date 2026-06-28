<script setup lang="ts">
import { router } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps<{ action: string; search?: string }>();
const term = ref(props.search ?? "");

function submit() {
  router.get(props.action, { search: term.value }, { preserveState: true });
}
</script>

<template>
  <form class="flex gap-2" @submit.prevent="submit">
    <input v-model="term" class="h-10 rounded-md border px-3 text-sm dark:bg-neutral-900" placeholder="Search" />
    <button class="rounded-md bg-teal-700 px-4 text-sm font-medium text-white">Search</button>
  </form>
</template>
