<script setup lang="ts">
import { Link, router } from "@inertiajs/vue3";
import { ref } from "vue";
import StockAdjustmentModal from "@/components/Stock/StockAdjustmentModal.vue";
import ConfirmModal from "@/components/ui/ConfirmModal.vue";

defineProps<{ product: any }>();
const confirmingDelete = ref(false);
const adjustingStock = ref(false);

function deleteProduct(product: any) {
  router.delete(`/products/${product.id}`, {
    onFinish: () => confirmingDelete.value = false,
  });
}
</script>

<template>
  <div class="space-y-6 p-4 md:p-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
      <div>
        <h1 class="text-2xl font-semibold">{{ product.name }}</h1>
        <p class="text-sm text-neutral-500">{{ product.sku }}</p>
      </div>
      <div class="flex flex-wrap gap-2">
        <Link href="/products" class="rounded-md border px-4 py-2 text-sm">Back</Link>
        <Link :href="`/products/${product.id}/edit`" class="rounded-md border px-4 py-2 text-sm">Edit</Link>
        <button type="button" class="rounded-md bg-teal-700 px-4 py-2 text-sm text-white" @click="adjustingStock = true">Adjust Stock</button>
        <button type="button" class="rounded-md bg-red-600 px-4 py-2 text-sm text-white" @click="confirmingDelete = true">Delete</button>
      </div>
    </div>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
      <div class="rounded-md border p-4 dark:border-neutral-800">
        <div class="text-sm text-neutral-500">Current Stock</div>
        <div class="mt-2 text-2xl font-semibold">{{ product.stock_quantity }} {{ product.unit }}</div>
        <div class="mt-1 text-xs" :class="Number(product.stock_quantity) <= 0 ? 'text-red-600' : Number(product.stock_quantity) <= Number(product.low_stock_threshold) ? 'text-amber-600' : 'text-neutral-500'">
          {{ Number(product.stock_quantity) <= 0 ? "Out of stock" : Number(product.stock_quantity) <= Number(product.low_stock_threshold) ? "Low stock" : "In stock" }}
        </div>
      </div>
      <div class="rounded-md border p-4 dark:border-neutral-800">
        <div class="text-sm text-neutral-500">Low Stock Alert</div>
        <div class="mt-2 text-2xl font-semibold">{{ product.low_stock_threshold }} {{ product.unit }}</div>
      </div>
      <div class="rounded-md border p-4 dark:border-neutral-800">
        <div class="text-sm text-neutral-500">Unit Price</div>
        <div class="mt-2 text-2xl font-semibold">${{ product.unit_price }}</div>
      </div>
      <div class="rounded-md border p-4 dark:border-neutral-800">
        <div class="text-sm text-neutral-500">Cost Price</div>
        <div class="mt-2 text-2xl font-semibold">${{ product.cost_price ?? "0.00" }}</div>
      </div>
    </section>

    <section class="rounded-md border dark:border-neutral-800">
      <div class="flex items-center justify-between border-b p-4 dark:border-neutral-800">
        <h2 class="font-semibold">Stock Transactions</h2>
        <span class="text-sm text-neutral-500">{{ product.stock_movements?.length ?? 0 }} records</span>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-neutral-50 dark:bg-neutral-900">
            <tr>
              <th class="px-4 py-3 text-left font-medium">Date</th>
              <th class="px-4 py-3 text-left font-medium">Type</th>
              <th class="px-4 py-3 text-right font-medium">Change</th>
              <th class="px-4 py-3 text-right font-medium">Before</th>
              <th class="px-4 py-3 text-right font-medium">After</th>
              <th class="px-4 py-3 text-left font-medium">Notes</th>
              <th class="px-4 py-3 text-left font-medium">User</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="movement in product.stock_movements" :key="movement.id" class="border-t dark:border-neutral-800">
              <td class="px-4 py-3">{{ movement.created_at }}</td>
              <td class="px-4 py-3 capitalize">{{ String(movement.type).replaceAll("_", " ") }}</td>
              <td class="px-4 py-3 text-right" :class="Number(movement.quantity) < 0 ? 'text-red-600' : 'text-emerald-600'">
                {{ Number(movement.quantity) > 0 ? "+" : "" }}{{ movement.quantity }}
              </td>
              <td class="px-4 py-3 text-right">{{ movement.quantity_before }}</td>
              <td class="px-4 py-3 text-right">{{ movement.quantity_after }}</td>
              <td class="px-4 py-3">{{ movement.notes }}</td>
              <td class="px-4 py-3">{{ movement.user?.name ?? "System" }}</td>
            </tr>
            <tr v-if="!product.stock_movements?.length">
              <td class="px-4 py-6 text-center text-neutral-500" colspan="7">No stock transactions yet.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>
  </div>

  <StockAdjustmentModal :open="adjustingStock" :product="product" @close="adjustingStock = false" />

  <ConfirmModal
    :open="confirmingDelete"
    title="Delete product?"
    :message="`This will permanently remove ${product.name}.`"
    confirm-label="Delete Product"
    @close="confirmingDelete = false"
    @confirm="deleteProduct(product)"
  />
</template>
