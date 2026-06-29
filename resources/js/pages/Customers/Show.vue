<script setup lang="ts">
import { Link, router } from "@inertiajs/vue3";
import { ref } from "vue";
import ConfirmModal from "@/components/ui/ConfirmModal.vue";

defineProps<{ customer: any }>();
const confirmingDelete = ref(false);

function deleteCustomer(customer: any) {
  router.delete(`/customers/${customer.id}`, {
    onFinish: () => confirmingDelete.value = false,
  });
}
</script>

<template>
  <div class="space-y-4 p-4 md:p-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-semibold">{{ customer.name }}</h1>
      <div class="flex gap-2">
        <Link href="/customers" class="rounded-md border px-4 py-2 text-sm">Back</Link>
        <Link :href="`/customers/${customer.id}/edit`" class="rounded-md border px-4 py-2 text-sm">Edit</Link>
        <button type="button" class="rounded-md bg-red-600 px-4 py-2 text-sm text-white" @click="confirmingDelete = true">Delete</button>
      </div>
    </div>
    <p>{{ customer.email }}</p>
    <p class="text-neutral-500">{{ customer.company_name }}</p>
  </div>

  <ConfirmModal
    :open="confirmingDelete"
    title="Delete customer?"
    :message="`This will permanently remove ${customer.name}.`"
    confirm-label="Delete Customer"
    @close="confirmingDelete = false"
    @confirm="deleteCustomer(customer)"
  />
</template>
