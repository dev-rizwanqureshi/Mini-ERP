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
  topCustomers: any[];
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
    <div class="grid gap-4 xl:grid-cols-2">
      <section class="rounded-md border p-5 dark:border-neutral-800">
        <h2 class="mb-4 font-semibold">Top Customers</h2>
        <div class="space-y-3">
          <div v-for="customer in topCustomers" :key="customer.id" class="grid grid-cols-[1fr_90px] items-center gap-3 text-sm">
            <div>
              <div class="font-medium">{{ customer.name }}</div>
              <div class="mt-1 h-2 rounded bg-neutral-100 dark:bg-neutral-800">
                <div
                  class="h-2 rounded bg-teal-700"
                  :style="{ width: `${Math.min(100, Number(customer.invoices_sum_total ?? 0) / Math.max(1, Number(topCustomers[0]?.invoices_sum_total ?? 1)) * 100)}%` }"
                />
              </div>
            </div>
            <div class="text-right font-medium">${{ Number(customer.invoices_sum_total ?? 0).toFixed(0) }}</div>
          </div>
        </div>
      </section>
      <section class="rounded-md border p-5 dark:border-neutral-800">
        <h2 class="mb-4 font-semibold">Low Stock</h2>
        <div class="space-y-3 text-sm">
          <div v-for="product in lowStockProducts" :key="product.id" class="flex justify-between gap-4">
            <span>{{ product.name }}</span>
            <strong>{{ product.stock_quantity }} {{ product.unit }}</strong>
          </div>
        </div>
      </section>
    </div>
  </div>
</template>
