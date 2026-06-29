<script setup lang="ts">
import { Link, router } from "@inertiajs/vue3";
import { ref } from "vue";
import ConfirmModal from "@/components/ui/ConfirmModal.vue";
import StatusBadge from "@/components/ui/StatusBadge.vue";
defineProps<{ invoice: any }>();

const confirmingDelete = ref(false);
const confirmingCancel = ref(false);
const sending = ref(false);

function sendInvoice(invoice: any) {
  sending.value = true;
  router.post(`/invoices/${invoice.id}/send`, {}, {
    onFinish: () => sending.value = false,
  });
}

function cancelInvoice(invoice: any) {
  router.post(`/invoices/${invoice.id}/cancel`, {}, {
    onFinish: () => confirmingCancel.value = false,
  });
}

function deleteInvoice(invoice: any) {
  router.delete(`/invoices/${invoice.id}`, {
    onFinish: () => confirmingDelete.value = false,
  });
}
</script>

<template>
  <div class="space-y-6 p-4 md:p-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
      <div>
        <h1 class="text-2xl font-semibold">{{ invoice.invoice_number }}</h1>
        <p class="text-sm text-neutral-500">{{ invoice.customer.name }}</p>
      </div>
      <div class="flex flex-wrap gap-2">
        <Link href="/invoices" class="rounded-md border px-4 py-2 text-sm">Back</Link>
        <a :href="`/invoices/${invoice.id}/pdf`" target="_blank" rel="noopener" class="rounded-md border px-4 py-2 text-sm">PDF</a>
        <Link :href="`/invoices/${invoice.id}/edit`" class="rounded-md border px-4 py-2 text-sm">Edit</Link>
        <Link :href="`/invoices/${invoice.id}/payments/create`" class="rounded-md bg-teal-700 px-4 py-2 text-sm text-white">Payment</Link>
        <button type="button" class="rounded-md border px-4 py-2 text-sm disabled:opacity-60" :disabled="sending" @click="sendInvoice(invoice)">Send</button>
        <button type="button" class="rounded-md border px-4 py-2 text-sm text-amber-700" @click="confirmingCancel = true">Cancel Invoice</button>
        <button type="button" class="rounded-md bg-red-600 px-4 py-2 text-sm text-white" @click="confirmingDelete = true">Delete</button>
      </div>
    </div>
    <StatusBadge :status="invoice.status" />
    <div class="rounded-md border dark:border-neutral-800">
      <div v-for="item in invoice.items" :key="item.id" class="grid grid-cols-[1fr_80px_100px_100px] gap-3 border-b p-3 text-sm dark:border-neutral-800">
        <span>{{ item.description }}</span><span>{{ item.quantity }}</span><span>${{ item.unit_price }}</span><strong>${{ item.total }}</strong>
      </div>
    </div>
    <div class="ml-auto max-w-sm space-y-2 text-sm">
      <div class="flex justify-between"><span>Subtotal</span><strong>${{ invoice.subtotal }}</strong></div>
      <div class="flex justify-between"><span>Total</span><strong>${{ invoice.total }}</strong></div>
      <div class="flex justify-between"><span>Paid</span><strong>${{ invoice.paid_amount }}</strong></div>
      <div class="flex justify-between"><span>Balance</span><strong>${{ invoice.balance_amount }}</strong></div>
    </div>
  </div>

  <ConfirmModal
    :open="confirmingCancel"
    title="Cancel invoice?"
    :message="`This will cancel invoice ${invoice.invoice_number} and restore stock where applicable.`"
    confirm-label="Cancel Invoice"
    variant="primary"
    @close="confirmingCancel = false"
    @confirm="cancelInvoice(invoice)"
  />

  <ConfirmModal
    :open="confirmingDelete"
    title="Delete invoice?"
    :message="`This will permanently remove invoice ${invoice.invoice_number}.`"
    confirm-label="Delete Invoice"
    @close="confirmingDelete = false"
    @confirm="deleteInvoice(invoice)"
  />
</template>
