<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
import BasePagination from "@/components/Base/BasePagination.vue";
import StatusBadge from "@/components/ui/StatusBadge.vue";
defineProps<{ invoices: any }>();
</script>

<template>
  <div class="space-y-5 p-4 md:p-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-semibold">Invoices</h1>
      <Link href="/invoices/create" class="rounded-md bg-teal-700 px-4 py-2 text-sm font-medium text-white">New Invoice</Link>
    </div>
    <div class="overflow-x-auto rounded-md border dark:border-neutral-800">
      <table class="min-w-full text-sm">
        <tbody>
          <tr v-for="invoice in invoices.data" :key="invoice.id" class="border-b dark:border-neutral-800">
            <td class="px-4 py-3 font-medium"><Link :href="`/invoices/${invoice.id}`">{{ invoice.invoice_number }}</Link></td>
            <td class="px-4 py-3">{{ invoice.customer?.name }}</td>
            <td class="px-4 py-3"><StatusBadge :status="invoice.status" /></td>
            <td class="px-4 py-3 text-right">{{ invoice.currency_symbol }}{{ invoice.total }}</td>
          </tr>
        </tbody>
      </table>
    </div>
    <BasePagination :links="invoices.links" />
  </div>
</template>
