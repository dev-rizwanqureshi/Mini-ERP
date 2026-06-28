<script setup lang="ts">
defineProps<{
  type?: "button" | "submit" | "reset";
  variant?: "primary" | "secondary" | "danger" | "ghost";
  loading?: boolean;
  disabled?: boolean;
}>();
</script>

<template>
  <button
    :type="type ?? 'button'"
    :disabled="disabled || loading"
    class="inline-flex h-10 items-center justify-center gap-2 rounded-md px-4 text-sm font-medium transition disabled:cursor-not-allowed disabled:opacity-60"
    :class="{
      'bg-teal-700 text-white hover:bg-teal-800': (variant ?? 'primary') === 'primary',
      'border border-neutral-300 bg-white text-neutral-900 hover:bg-neutral-50 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-50': variant === 'secondary',
      'bg-red-600 text-white hover:bg-red-700': variant === 'danger',
      'text-neutral-700 hover:bg-neutral-100 dark:text-neutral-200 dark:hover:bg-neutral-800': variant === 'ghost',
    }"
  >
    <slot name="icon" />
    <span><slot /></span>
  </button>
</template>
