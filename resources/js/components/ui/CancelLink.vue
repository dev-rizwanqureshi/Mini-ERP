<script setup lang="ts">
import { Link, router } from "@inertiajs/vue3";
import { ref } from "vue";
import ConfirmModal from "@/components/ui/ConfirmModal.vue";

const props = withDefaults(defineProps<{
  href: string;
  dirty?: boolean;
  label?: string;
}>(), {
  dirty: false,
  label: "Cancel",
});

const confirming = ref(false);

function requestCancel(event: MouseEvent) {
  if (!props.dirty) return;

  event.preventDefault();
  confirming.value = true;
}

function discardChanges() {
  confirming.value = false;
  router.visit(props.href);
}
</script>

<template>
  <Link
    :href="href"
    class="inline-flex h-10 items-center justify-center rounded-md border px-4 text-sm font-medium hover:bg-neutral-50 dark:border-neutral-700 dark:hover:bg-neutral-900"
    @click="requestCancel"
  >
    {{ label }}
  </Link>

  <ConfirmModal
    :open="confirming"
    title="Discard changes?"
    message="Any unsaved changes on this form will be lost."
    confirm-label="Discard Changes"
    variant="primary"
    @close="confirming = false"
    @confirm="discardChanges"
  />
</template>
