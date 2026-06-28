<script setup lang="ts">
import { Link, router } from "@inertiajs/vue3";
import StatusBadge from "@/components/ui/StatusBadge.vue";
defineProps<{ invoice: any }>();
</script>

<template>
  <div class="space-y-6 p-4 md:p-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
      <div>
        <h1 class="text-2xl font-semibold">{{ invoice.invoice_number }}</h1>
        <p class="text-sm text-neutral-500">{{ invoice.customer.name }}</p>
      </div>
      <div class="flex gap-2">
        <Link :href="`/invoices/${invoice.id}/pdf`" class="rounded-md border px-4 py-2 text-sm">PDF</Link>
        <Link :href="`/invoices/${invoice.id}/payments/create`" class="rounded-md bg-teal-700 px-4 py-2 text-sm text-white">Payment</Link>
        <button class="rounded-md border px-4 py-2 text-sm" @click="router.post(`/invoices/${invoice.id}/send`)">Send</button>
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
</template>
