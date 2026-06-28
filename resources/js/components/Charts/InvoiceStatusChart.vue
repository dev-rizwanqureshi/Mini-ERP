<script setup lang="ts">
import { computed } from "vue";

const props = defineProps<{ data: { label: string; value: number }[] }>();
const total = computed(() => props.data.reduce((sum, row) => sum + Number(row.value || 0), 0));
</script>

<template>
  <div class="space-y-4">
    <div v-for="row in data" :key="row.label" class="space-y-1.5 text-sm">
      <div class="flex justify-between gap-3">
        <span>{{ row.label }}</span>
        <strong>{{ row.value }}</strong>
      </div>
      <div class="h-2 rounded bg-neutral-100 dark:bg-neutral-800">
        <div
          class="h-2 rounded bg-teal-700"
          :style="{ width: `${total ? (Number(row.value) / total) * 100 : 0}%` }"
        />
      </div>
    </div>
  </div>
</template>
