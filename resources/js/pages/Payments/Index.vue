<script setup lang="ts">
import BasePagination from "@/components/Base/BasePagination.vue";
import SearchFilter from "@/components/ui/SearchFilter.vue";
import SortLink from "@/components/ui/SortLink.vue";
defineProps<{ payments: any; filters: Record<string, unknown> & { search?: string } }>();
</script>

<template>
  <div class="space-y-5 p-4 md:p-6">
    <h1 class="text-2xl font-semibold">Payments</h1>
    <SearchFilter action="/payments" :search="filters.search" :filters="filters" placeholder="Search payments" />
    <div class="overflow-x-auto rounded-md border dark:border-neutral-800">
      <table class="min-w-full text-sm">
        <thead class="bg-neutral-50 dark:bg-neutral-900">
          <tr>
            <th class="px-4 py-3 text-left"><SortLink action="/payments" sort="invoice" label="Invoice" :filters="filters" /></th>
            <th class="px-4 py-3 text-left"><SortLink action="/payments" sort="customer" label="Customer" :filters="filters" /></th>
            <th class="px-4 py-3 text-left"><SortLink action="/payments" sort="payment_method" label="Method" :filters="filters" /></th>
            <th class="px-4 py-3 text-left"><SortLink action="/payments" sort="payment_date" label="Date" :filters="filters" /></th>
            <th class="px-4 py-3 text-right"><SortLink action="/payments" sort="amount" label="Amount" :filters="filters" align="right" /></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="payment in payments.data" :key="payment.id" class="border-b dark:border-neutral-800">
            <td class="px-4 py-3">{{ payment.invoice?.invoice_number }}</td>
            <td class="px-4 py-3">{{ payment.customer?.name }}</td>
            <td class="px-4 py-3">{{ payment.payment_method }}</td>
            <td class="px-4 py-3">{{ payment.payment_date }}</td>
            <td class="px-4 py-3 text-right">${{ payment.amount }}</td>
          </tr>
        </tbody>
      </table>
    </div>
    <BasePagination :links="payments.links" />
  </div>
</template>
