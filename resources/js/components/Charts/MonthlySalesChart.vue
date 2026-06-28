<script setup lang="ts">
import { computed } from "vue";

const props = defineProps<{ data: { label: string; value: number }[] }>();
const max = computed(() => Math.max(...props.data.map((row) => Number(row.value) || 0), 1));
</script>

<template>
  <div class="h-72 overflow-x-auto">
    <div class="flex h-full min-w-[680px] items-end gap-3">
      <div v-for="row in data" :key="row.label" class="flex h-full flex-1 flex-col justify-end gap-2">
        <div class="flex flex-1 items-end rounded-t bg-neutral-100 dark:bg-neutral-800">
          <div
            class="w-full rounded-t bg-teal-700"
            :style="{ height: `${Math.max(3, (Number(row.value) / max) * 100)}%` }"
            :title="`${row.label}: $${Number(row.value).toFixed(2)}`"
          />
        </div>
        <div class="truncate text-center text-xs text-neutral-500">{{ row.label }}</div>
        <div class="text-center text-xs font-medium">${{ Number(row.value).toFixed(0) }}</div>
      </div>
    </div>
  </div>
</template>
