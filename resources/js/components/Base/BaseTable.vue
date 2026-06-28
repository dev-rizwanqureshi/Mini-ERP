<script setup lang="ts">
defineProps<{ columns: string[]; empty?: string }>();
</script>

<template>
  <div class="overflow-x-auto rounded-md border border-neutral-200 dark:border-neutral-800">
    <table class="min-w-full divide-y divide-neutral-200 text-sm dark:divide-neutral-800">
      <thead class="bg-neutral-50 dark:bg-neutral-900">
        <tr>
          <th v-for="column in columns" :key="column" class="px-4 py-3 text-left font-medium">
            {{ column }}
          </th>
        </tr>
      </thead>
      <tbody class="divide-y divide-neutral-200 dark:divide-neutral-800">
        <slot />
      </tbody>
    </table>
  </div>
</template>
