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
      <h1 class="text-2xl font-semibold">Payment Report</h1>
      <p class="text-sm text-neutral-500">{{ report.range.label }}</p>
    </div>

    <ReportFilters action="/reports/payments" :filters="filters" export-type="payments" />

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
      <ReportMetric label="Payments" :value="money(report.total)" />
      <ReportMetric label="Transactions" :value="report.count" />
      <ReportMetric label="Average Payment" :value="money(report.count ? report.total / report.count : 0)" />
    </div>

    <ReportBarChart :data="report.series" currency />

    <div class="overflow-x-auto rounded-md border dark:border-neutral-800">
      <table class="min-w-full text-sm">
        <thead class="bg-neutral-50 dark:bg-neutral-900">
          <tr>
            <th class="px-4 py-3 text-left"><SortLink action="/reports/payments" sort="invoice" label="Invoice" :filters="filters" /></th>
            <th class="px-4 py-3 text-left"><SortLink action="/reports/payments" sort="customer" label="Customer" :filters="filters" /></th>
            <th class="px-4 py-3 text-left"><SortLink action="/reports/payments" sort="payment_date" label="Date" :filters="filters" /></th>
            <th class="px-4 py-3 text-left"><SortLink action="/reports/payments" sort="payment_method" label="Method" :filters="filters" /></th>
            <th class="px-4 py-3 text-right"><SortLink action="/reports/payments" sort="amount" label="Amount" :filters="filters" align="right" /></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="payment in report.rows" :key="payment.id" class="border-t dark:border-neutral-800">
            <td class="px-4 py-3 font-medium">{{ payment.invoice?.invoice_number }}</td>
            <td class="px-4 py-3">{{ payment.customer?.name }}</td>
            <td class="px-4 py-3">{{ payment.payment_date }}</td>
            <td class="px-4 py-3">{{ payment.payment_method }}</td>
            <td class="px-4 py-3 text-right">{{ money(payment.amount) }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
