<script setup lang="ts">
defineProps<{
  label?: string;
  modelValue?: string | number | null;
  options: { value: string | number; label: string }[];
  error?: string;
  placeholder?: string;
}>();
defineEmits<{ "update:modelValue": [value: string] }>();
</script>

<template>
  <label class="grid gap-1.5 text-sm">
    <span v-if="label" class="font-medium">{{ label }}</span>
    <select
      :value="modelValue ?? ''"
      class="h-10 rounded-md border border-neutral-300 bg-white px-3 text-sm outline-none focus:border-teal-700 dark:border-neutral-700 dark:bg-neutral-900"
      @change="$emit('update:modelValue', ($event.target as HTMLSelectElement).value)"
    >
      <option value="">{{ placeholder ?? "Select" }}</option>
      <option v-for="option in options" :key="option.value" :value="option.value">
        {{ option.label }}
      </option>
    </select>
    <span v-if="error" class="text-xs text-red-600">{{ error }}</span>
  </label>
</template>
