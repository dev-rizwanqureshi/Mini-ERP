<script setup lang="ts">
import SortLink from "@/components/ui/SortLink.vue";
import ReportBarChart from "./Partials/ReportBarChart.vue";
import ReportFilters from "./Partials/ReportFilters.vue";
import ReportMetric from "./Partials/ReportMetric.vue";

defineProps<{ report: any; filters: Record<string, unknown> }>();

const money = (value: number | string | null | undefined) => `$${Number(value ?? 0).toFixed(2)}`;
</script>

<template>
  <div class="space-y-5 p-4 md:p-6">
    <div>
      <h1 class="text-2xl font-semibold">Customer Report</h1>
      <p class="text-sm text-neutral-500">{{ report.range.label }}</p>
    </div>

    <ReportFilters action="/reports/customers" :filters="filters" />

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
      <ReportMetric label="Customers" :value="report.count" />
      <ReportMetric label="Invoiced" :value="money(report.total)" />
      <ReportMetric label="Paid" :value="money(report.paid)" />
    </div>

    <ReportBarChart :data="report.series" currency />

    <div class="overflow-x-auto rounded-md border dark:border-neutral-800">
      <table class="min-w-full text-sm">
        <thead class="bg-neutral-50 dark:bg-neutral-900">
          <tr>
            <th class="px-4 py-3 text-left"><SortLink action="/reports/customers" sort="name" label="Customer" :filters="filters" /></th>
            <th class="px-4 py-3 text-left"><SortLink action="/reports/customers" sort="company_name" label="Company" :filters="filters" /></th>
            <th class="px-4 py-3 text-right"><SortLink action="/reports/customers" sort="invoices_count" label="Invoices" :filters="filters" align="right" /></th>
            <th class="px-4 py-3 text-right"><SortLink action="/reports/customers" sort="invoices_sum_total" label="Invoiced" :filters="filters" align="right" /></th>
            <th class="px-4 py-3 text-right"><SortLink action="/reports/customers" sort="payments_sum_amount" label="Paid" :filters="filters" align="right" /></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="customer in report.rows" :key="customer.id" class="border-t dark:border-neutral-800">
            <td class="px-4 py-3 font-medium">{{ customer.name }}</td>
            <td class="px-4 py-3">{{ customer.company_name }}</td>
            <td class="px-4 py-3 text-right">{{ customer.invoices_count }}</td>
            <td class="px-4 py-3 text-right">{{ money(customer.invoices_sum_total) }}</td>
            <td class="px-4 py-3 text-right">{{ money(customer.payments_sum_amount) }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
