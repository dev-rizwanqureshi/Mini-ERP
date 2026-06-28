<script setup lang="ts">
import BasePagination from "@/components/Base/BasePagination.vue";
defineProps<{ payments: any }>();
</script>

<template>
  <div class="space-y-5 p-4 md:p-6">
    <h1 class="text-2xl font-semibold">Payments</h1>
    <div class="overflow-x-auto rounded-md border dark:border-neutral-800">
      <table class="min-w-full text-sm">
        <tbody>
          <tr v-for="payment in payments.data" :key="payment.id" class="border-b dark:border-neutral-800">
            <td class="px-4 py-3">{{ payment.invoice?.invoice_number }}</td>
            <td class="px-4 py-3">{{ payment.customer?.name }}</td>
            <td class="px-4 py-3">{{ payment.payment_method }}</td>
            <td class="px-4 py-3 text-right">${{ payment.amount }}</td>
          </tr>
        </tbody>
      </table>
    </div>
    <BasePagination :links="payments.links" />
  </div>
</template>
