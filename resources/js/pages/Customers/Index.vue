<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
import BasePagination from "@/components/Base/BasePagination.vue";
import SearchFilter from "@/components/ui/SearchFilter.vue";

defineProps<{ customers: any; filters: { search?: string } }>();
</script>

<template>
  <div class="space-y-5 p-4 md:p-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
      <h1 class="text-2xl font-semibold">Customers</h1>
      <Link href="/customers/create" class="rounded-md bg-teal-700 px-4 py-2 text-sm font-medium text-white">New Customer</Link>
    </div>
    <SearchFilter action="/customers" :search="filters.search" />
    <div class="overflow-x-auto rounded-md border dark:border-neutral-800">
      <table class="min-w-full text-sm">
        <tbody>
          <tr v-for="customer in customers.data" :key="customer.id" class="border-b dark:border-neutral-800">
            <td class="px-4 py-3 font-medium"><Link :href="`/customers/${customer.id}`">{{ customer.name }}</Link></td>
            <td class="px-4 py-3">{{ customer.email }}</td>
            <td class="px-4 py-3">{{ customer.company_name }}</td>
            <td class="px-4 py-3 text-right">{{ customer.invoices_count }} invoices</td>
          </tr>
        </tbody>
      </table>
    </div>
    <BasePagination :links="customers.links" />
  </div>
</template>
