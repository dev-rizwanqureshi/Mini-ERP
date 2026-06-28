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
      <h1 class="text-2xl font-semibold">Outstanding Invoices</h1>
      <p class="text-sm text-neutral-500">{{ report.range.label }}</p>
    </div>

    <ReportFilters action="/reports/outstanding" :filters="filters" />

    <div class="grid gap-4 sm:grid-cols-2">
      <ReportMetric label="Open Balance" :value="money(report.total)" />
      <ReportMetric label="Invoices" :value="report.count" />
    </div>

    <ReportBarChart :data="report.series" currency />

    <div class="overflow-x-auto rounded-md border dark:border-neutral-800">
      <table class="min-w-full text-sm">
        <thead class="bg-neutral-50 dark:bg-neutral-900">
          <tr>
            <th class="px-4 py-3 text-left"><SortLink action="/reports/outstanding" sort="invoice_number" label="Invoice" :filters="filters" /></th>
            <th class="px-4 py-3 text-left"><SortLink action="/reports/outstanding" sort="customer" label="Customer" :filters="filters" /></th>
            <th class="px-4 py-3 text-left"><SortLink action="/reports/outstanding" sort="invoice_date" label="Invoice Date" :filters="filters" /></th>
            <th class="px-4 py-3 text-left"><SortLink action="/reports/outstanding" sort="due_date" label="Due Date" :filters="filters" /></th>
            <th class="px-4 py-3 text-right"><SortLink action="/reports/outstanding" sort="balance_amount" label="Balance" :filters="filters" align="right" /></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="invoice in report.rows" :key="invoice.id" class="border-t dark:border-neutral-800">
            <td class="px-4 py-3 font-medium">{{ invoice.invoice_number }}</td>
            <td class="px-4 py-3">{{ invoice.customer?.name }}</td>
            <td class="px-4 py-3">{{ invoice.invoice_date }}</td>
            <td class="px-4 py-3">{{ invoice.due_date }}</td>
            <td class="px-4 py-3 text-right">{{ invoice.currency_symbol }}{{ Number(invoice.balance_amount).toFixed(2) }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
