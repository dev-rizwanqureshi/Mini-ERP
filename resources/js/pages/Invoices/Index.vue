<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
import BasePagination from "@/components/Base/BasePagination.vue";
import SearchFilter from "@/components/ui/SearchFilter.vue";
import SortLink from "@/components/ui/SortLink.vue";
import StatusBadge from "@/components/ui/StatusBadge.vue";
defineProps<{ invoices: any; filters: Record<string, unknown> & { search?: string }; statuses: any[] }>();
</script>

<template>
  <div class="space-y-5 p-4 md:p-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-semibold">Invoices</h1>
      <Link href="/invoices/create" class="rounded-md bg-teal-700 px-4 py-2 text-sm font-medium text-white">New Invoice</Link>
    </div>
    <SearchFilter action="/invoices" :search="filters.search" :filters="filters" placeholder="Search invoices" />
    <div class="overflow-x-auto rounded-md border dark:border-neutral-800">
      <table class="min-w-full text-sm">
        <thead class="bg-neutral-50 dark:bg-neutral-900">
          <tr>
            <th class="px-4 py-3 text-left"><SortLink action="/invoices" sort="invoice_number" label="Invoice" :filters="filters" /></th>
            <th class="px-4 py-3 text-left"><SortLink action="/invoices" sort="customer" label="Customer" :filters="filters" /></th>
            <th class="px-4 py-3 text-left"><SortLink action="/invoices" sort="status" label="Status" :filters="filters" /></th>
            <th class="px-4 py-3 text-left"><SortLink action="/invoices" sort="invoice_date" label="Date" :filters="filters" /></th>
            <th class="px-4 py-3 text-right"><SortLink action="/invoices" sort="total" label="Total" :filters="filters" align="right" /></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="invoice in invoices.data" :key="invoice.id" class="border-b dark:border-neutral-800">
            <td class="px-4 py-3 font-medium"><Link :href="`/invoices/${invoice.id}`">{{ invoice.invoice_number }}</Link></td>
            <td class="px-4 py-3">{{ invoice.customer?.name }}</td>
            <td class="px-4 py-3"><StatusBadge :status="invoice.status" /></td>
            <td class="px-4 py-3">{{ invoice.invoice_date }}</td>
            <td class="px-4 py-3 text-right">{{ invoice.currency_symbol }}{{ invoice.total }}</td>
          </tr>
        </tbody>
      </table>
    </div>
    <BasePagination :links="invoices.links" />
  </div>
</template>
