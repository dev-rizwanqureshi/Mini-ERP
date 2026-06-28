<script setup lang="ts">
import { computed } from "vue";

const props = defineProps<{ data: { label: string; value: number; count?: number }[]; currency?: boolean }>();
const max = computed(() => Math.max(...props.data.map((item) => Number(item.value) || 0), 1));
</script>

<template>
  <div class="rounded-md border p-5 dark:border-neutral-800">
    <div class="flex h-64 items-end gap-2 overflow-x-auto">
      <div v-for="item in data" :key="item.label" class="flex h-full min-w-12 flex-1 flex-col justify-end gap-2">
        <div class="flex flex-1 items-end">
          <div
            class="w-full rounded-t bg-teal-700"
            :style="{ height: `${Math.max(4, (Number(item.value) / max) * 100)}%` }"
            :title="`${item.label}: ${currency ? '$' : ''}${Number(item.value).toFixed(currency ? 2 : 0)}`"
          />
        </div>
        <div class="truncate text-center text-xs text-neutral-500">{{ item.label }}</div>
      </div>
    </div>
  </div>
</template>
