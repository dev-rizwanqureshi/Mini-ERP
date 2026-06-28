<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
import { computed } from "vue";

const props = defineProps<{
  action: string;
  sort: string;
  label: string;
  filters?: Record<string, unknown>;
  align?: "left" | "right";
}>();

const isActive = computed(() => props.filters?.sort === props.sort);
const nextDirection = computed(() => (isActive.value && props.filters?.direction === "asc" ? "desc" : "asc"));
const indicator = computed(() => (isActive.value ? (props.filters?.direction === "asc" ? "↑" : "↓") : "↕"));
const href = computed(() => {
  const params = new URLSearchParams();

  Object.entries(props.filters ?? {}).forEach(([key, value]) => {
    if (value !== undefined && value !== null && value !== "" && key !== "page") {
      params.set(key, String(value));
    }
  });

  params.set("sort", props.sort);
  params.set("direction", nextDirection.value);

  return `${props.action}?${params.toString()}`;
});
</script>

<template>
  <Link
    :href="href"
    class="inline-flex items-center gap-1 font-medium text-neutral-700 hover:text-teal-700 dark:text-neutral-200"
    :class="align === 'right' ? 'justify-end' : 'justify-start'"
    preserve-state
  >
    <span>{{ label }}</span>
    <span class="text-xs text-neutral-400">{{ indicator }}</span>
  </Link>
</template>
