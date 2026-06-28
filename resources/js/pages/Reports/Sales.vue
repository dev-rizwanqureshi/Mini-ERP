<script setup lang="ts">
import StatusBadge from "@/components/ui/StatusBadge.vue";
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
      <h1 class="text-2xl font-semibold">Sales Report</h1>
      <p class="text-sm text-neutral-500">{{ report.range.label }}</p>
    </div>

    <ReportFilters action="/reports/sales" :filters="filters" export-type="sales" />

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
      <ReportMetric label="Sales" :value="money(report.total)" />
      <ReportMetric label="Invoices" :value="report.count" />
      <ReportMetric label="Average Invoice" :value="money(report.average)" />
      <ReportMetric label="Open Balance" :value="money(report.balance)" />
    </div>

    <div class="grid gap-4 xl:grid-cols-[1fr_360px]">
      <ReportBarChart :data="report.series" currency />
      <section class="rounded-md border p-5 dark:border-neutral-800">
        <h2 class="mb-4 font-semibold">Status</h2>
        <div class="space-y-3">
          <div v-for="row in report.statusBreakdown" :key="row.label" class="flex items-center justify-between text-sm">
            <span>{{ row.label }}</span>
            <strong>{{ row.value }}</strong>
          </div>
        </div>
      </section>
    </div>

    <div class="overflow-x-auto rounded-md border dark:border-neutral-800">
      <table class="min-w-full text-sm">
        <thead class="bg-neutral-50 dark:bg-neutral-900">
          <tr>
            <th class="px-4 py-3 text-left"><SortLink action="/reports/sales" sort="invoice_number" label="Invoice" :filters="filters" /></th>
            <th class="px-4 py-3 text-left"><SortLink action="/reports/sales" sort="customer" label="Customer" :filters="filters" /></th>
            <th class="px-4 py-3 text-left"><SortLink action="/reports/sales" sort="invoice_date" label="Date" :filters="filters" /></th>
            <th class="px-4 py-3 text-left"><SortLink action="/reports/sales" sort="status" label="Status" :filters="filters" /></th>
            <th class="px-4 py-3 text-right"><SortLink action="/reports/sales" sort="total" label="Total" :filters="filters" align="right" /></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="invoice in report.rows" :key="invoice.id" class="border-t dark:border-neutral-800">
            <td class="px-4 py-3 font-medium">{{ invoice.invoice_number }}</td>
            <td class="px-4 py-3">{{ invoice.customer?.name }}</td>
            <td class="px-4 py-3">{{ invoice.invoice_date }}</td>
            <td class="px-4 py-3"><StatusBadge :status="invoice.status" /></td>
            <td class="px-4 py-3 text-right">{{ invoice.currency_symbol }}{{ Number(invoice.total).toFixed(2) }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
