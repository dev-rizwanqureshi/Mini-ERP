<script setup lang="ts">
defineProps<{
  id?: string;
  label?: string;
  type?: string;
  modelValue?: string | number;
  error?: string;
  placeholder?: string;
  required?: boolean;
  disabled?: boolean;
}>();
defineEmits<{ "update:modelValue": [value: string] }>();
</script>

<template>
  <label class="grid gap-1.5 text-sm">
    <span v-if="label" class="font-medium">{{ label }}</span>
    <input
      :id="id"
      :type="type ?? 'text'"
      :value="modelValue"
      :placeholder="placeholder"
      :required="required"
      :disabled="disabled"
      class="h-10 rounded-md border border-neutral-300 bg-white px-3 text-sm outline-none focus:border-teal-700 dark:border-neutral-700 dark:bg-neutral-900"
      @input="$emit('update:modelValue', ($event.target as HTMLInputElement).value)"
    />
    <span v-if="error" class="text-xs text-red-600">{{ error }}</span>
  </label>
</template>
