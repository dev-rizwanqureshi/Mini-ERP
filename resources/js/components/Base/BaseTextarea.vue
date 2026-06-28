<script setup lang="ts">
defineProps<{ label?: string; modelValue?: string | null; error?: string }>();
defineEmits<{ "update:modelValue": [value: string] }>();
</script>

<template>
  <label class="grid gap-1.5 text-sm">
    <span v-if="label" class="font-medium">{{ label }}</span>
    <textarea
      :value="modelValue ?? ''"
      rows="4"
      class="rounded-md border border-neutral-300 bg-white px-3 py-2 text-sm outline-none focus:border-teal-700 dark:border-neutral-700 dark:bg-neutral-900"
      @input="$emit('update:modelValue', ($event.target as HTMLTextAreaElement).value)"
    />
    <span v-if="error" class="text-xs text-red-600">{{ error }}</span>
  </label>
</template>
