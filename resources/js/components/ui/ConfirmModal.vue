<script setup lang="ts">
withDefaults(defineProps<{
  open: boolean;
  title: string;
  message?: string;
  confirmLabel?: string;
  cancelLabel?: string;
  variant?: "danger" | "primary";
  processing?: boolean;
}>(), {
  message: "",
  confirmLabel: "Confirm",
  cancelLabel: "Cancel",
  variant: "danger",
  processing: false,
});

defineEmits<{ close: []; confirm: [] }>();
</script>

<template>
  <div v-if="open" class="fixed inset-0 z-50 grid place-items-center bg-black/50 p-4">
    <section class="w-full max-w-md rounded-md border bg-white p-5 shadow-xl dark:border-neutral-800 dark:bg-neutral-950">
      <div class="space-y-2">
        <h2 class="text-lg font-semibold">{{ title }}</h2>
        <p v-if="message" class="text-sm text-neutral-500">{{ message }}</p>
        <slot />
      </div>

      <footer class="mt-6 flex justify-end gap-2">
        <button
          type="button"
          class="h-10 rounded-md border px-4 text-sm font-medium hover:bg-neutral-50 dark:border-neutral-700 dark:hover:bg-neutral-900"
          :disabled="processing"
          @click="$emit('close')"
        >
          {{ cancelLabel }}
        </button>
        <button
          type="button"
          class="h-10 rounded-md px-4 text-sm font-medium text-white disabled:opacity-60"
          :class="variant === 'danger' ? 'bg-red-600 hover:bg-red-700' : 'bg-teal-700 hover:bg-teal-800'"
          :disabled="processing"
          @click="$emit('confirm')"
        >
          {{ confirmLabel }}
        </button>
      </footer>
    </section>
  </div>
</template>
