<script setup lang="ts">
import { useForm } from "@inertiajs/vue3";
import BaseButton from "@/components/Base/BaseButton.vue";
import BaseInput from "@/components/Base/BaseInput.vue";
import BaseModal from "@/components/Base/BaseModal.vue";
import BaseSelect from "@/components/Base/BaseSelect.vue";
import BaseTextarea from "@/components/Base/BaseTextarea.vue";

const props = defineProps<{
  open: boolean;
  product: any | null;
}>();

const emit = defineEmits<{ close: [] }>();

const form = useForm({
  mode: "add",
  quantity: 0,
  notes: "",
});

const modeOptions = [
  { value: "add", label: "Add stock" },
  { value: "remove", label: "Remove stock" },
  { value: "set", label: "Set exact stock" },
];

function close() {
  form.clearErrors();
  emit("close");
}

function submit() {
  if (!props.product) return;

  form.post(`/products/${props.product.id}/stock-adjustments`, {
    preserveScroll: true,
    onSuccess: () => {
      form.reset();
      close();
    },
  });
}
</script>

<template>
  <BaseModal :open="open" title="Adjust Stock" @close="close">
    <form v-if="product" class="space-y-4" @submit.prevent="submit">
      <div>
        <div class="font-medium">{{ product.name }}</div>
        <div class="text-sm text-neutral-500">{{ product.sku }}</div>
      </div>
      <BaseSelect v-model="form.mode" label="Action" :options="modeOptions" :error="form.errors.mode" />
      <BaseInput
        v-model="form.quantity"
        :label="form.mode === 'set' ? 'New stock quantity' : 'Quantity'"
        type="number"
        :error="form.errors.quantity"
      />
      <BaseTextarea v-model="form.notes" label="Notes" :error="form.errors.notes" />
      <div class="rounded-md border p-3 text-sm text-neutral-500 dark:border-neutral-800">
        Current stock is <strong>{{ product.stock_quantity }} {{ product.unit }}</strong>. Every change will be saved as a stock transaction.
      </div>
    </form>
    <template #footer>
      <button type="button" class="h-10 rounded-md border px-4 text-sm font-medium" @click="close">Cancel</button>
      <BaseButton type="button" :loading="form.processing" @click="submit">Save Stock</BaseButton>
    </template>
  </BaseModal>
</template>
