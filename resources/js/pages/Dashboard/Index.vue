<script setup lang="ts">
import InvoiceStatusChart from "@/components/Charts/InvoiceStatusChart.vue";
import MonthlySalesChart from "@/components/Charts/MonthlySalesChart.vue";
import DashboardCard from "@/components/ui/DashboardCard.vue";

defineProps<{
  stats: Record<string, number>;
  monthlySales: { label: string; value: number }[];
  statusBreakdown: { label: string; value: number }[];
  recentInvoices: any[];
  recentPayments: any[];
  lowStockProducts: any[];
}>();
</script>

<template>
  <div class="space-y-6 p-4 md:p-6">
    <div>
      <h1 class="text-2xl font-semibold">Dashboard</h1>
      <p class="text-sm text-neutral-500">Live operating snapshot for the business.</p>
    </div>
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-6">
      <DashboardCard title="Customers" :value="stats.customers" />
      <DashboardCard title="Products" :value="stats.products" />
      <DashboardCard title="Invoices" :value="stats.invoices" />
      <DashboardCard title="Revenue" :value="`$${Number(stats.revenue).toFixed(2)}`" />
      <DashboardCard title="Pending" :value="`$${Number(stats.pending).toFixed(2)}`" />
      <DashboardCard title="Overdue" :value="stats.overdue" />
    </div>
    <div class="grid gap-4 xl:grid-cols-2">
      <section class="rounded-md border p-5 dark:border-neutral-800">
        <h2 class="mb-4 font-semibold">Monthly Revenue</h2>
        <MonthlySalesChart :data="monthlySales" />
      </section>
      <section class="rounded-md border p-5 dark:border-neutral-800">
        <h2 class="mb-4 font-semibold">Invoice Status</h2>
        <InvoiceStatusChart :data="statusBreakdown" />
      </section>
    </div>
  </div>
</template>
