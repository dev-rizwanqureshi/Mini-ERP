<script setup lang="ts">
import { Link, router } from "@inertiajs/vue3";
import { ref } from "vue";
import ConfirmModal from "@/components/ui/ConfirmModal.vue";

defineProps<{ payment: any }>();
const confirmingDelete = ref(false);

function deletePayment(payment: any) {
  router.delete(`/payments/${payment.id}`, {
    onFinish: () => confirmingDelete.value = false,
  });
}
</script>

<template>
  <div class="space-y-5 p-4 md:p-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
      <div>
        <h1 class="text-2xl font-semibold">Payment #{{ payment.id }}</h1>
        <p class="text-sm text-neutral-500">{{ payment.invoice?.invoice_number }} · {{ payment.customer?.name }}</p>
      </div>
      <div class="flex gap-2">
        <Link href="/payments" class="rounded-md border px-4 py-2 text-sm">Back</Link>
        <button type="button" class="rounded-md bg-red-600 px-4 py-2 text-sm text-white" @click="confirmingDelete = true">Delete</button>
      </div>
    </div>

    <section class="grid gap-3 rounded-md border p-5 text-sm dark:border-neutral-800 md:grid-cols-2">
      <div><span class="text-neutral-500">Amount</span><div class="font-medium">${{ payment.amount }}</div></div>
      <div><span class="text-neutral-500">Date</span><div class="font-medium">{{ payment.payment_date }}</div></div>
      <div><span class="text-neutral-500">Method</span><div class="font-medium">{{ payment.payment_method }}</div></div>
      <div><span class="text-neutral-500">Reference</span><div class="font-medium">{{ payment.reference_number ?? "Not set" }}</div></div>
    </section>
  </div>

  <ConfirmModal
    :open="confirmingDelete"
    title="Delete payment?"
    message="This will remove the payment and update the invoice balance."
    confirm-label="Delete Payment"
    @close="confirmingDelete = false"
    @confirm="deletePayment(payment)"
  />
</template>
