<script setup lang="ts">
import { router } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import BaseInput from "@/components/Base/BaseInput.vue";
import BasePagination from "@/components/Base/BasePagination.vue";
import BaseSelect from "@/components/Base/BaseSelect.vue";
import StockAdjustmentModal from "@/components/Stock/StockAdjustmentModal.vue";
import SortLink from "@/components/ui/SortLink.vue";

const props = defineProps<{
  products: any;
  filters: Record<string, unknown> & {
    search?: string;
    unit?: string;
    stock_filter?: string;
    threshold?: string | number;
    sort?: string;
    direction?: string;
  };
  units: string[];
  stats: { total: number; out: number; low: number; soon: number };
}>();

const search = ref(props.filters.search ?? "");
const unit = ref(props.filters.unit ?? "");
const stockFilter = ref(props.filters.stock_filter ?? "");
const threshold = ref(props.filters.threshold ?? 5);
const productPendingAdjustment = ref<any | null>(null);

const unitOptions = computed(() => props.units.map((item) => ({ value: item, label: item })));
const stockFilterOptions = [
  { value: "out", label: "Out of stock" },
  { value: "low", label: "Low stock threshold" },
  { value: "soon", label: "Out of stock soon" },
  { value: "below_5", label: "Less than 5" },
  { value: "below_10", label: "Less than 10" },
  { value: "below_custom", label: "Less than custom" },
];

function applyFilters() {
  router.get("/stock", {
    search: search.value,
    unit: unit.value,
    stock_filter: stockFilter.value,
    threshold: stockFilter.value === "below_custom" ? threshold.value : undefined,
    sort: props.filters.sort,
    direction: props.filters.direction,
  }, { preserveState: true, preserveScroll: true });
}

function clearFilters() {
  router.get("/stock", {}, { preserveState: true, preserveScroll: true });
}

function stockStatus(product: any) {
  const stock = Number(product.stock_quantity);
  const low = Number(product.low_stock_threshold);

  if (stock <= 0) return { label: "Out of stock", class: "bg-red-100 text-red-800" };
  if (stock <= low) return { label: "Low stock", class: "bg-amber-100 text-amber-800" };
  if (stock <= low * 2) return { label: "Out soon", class: "bg-yellow-100 text-yellow-800" };

  return { label: "In stock", class: "bg-emerald-100 text-emerald-800" };
}
</script>

<template>
  <div class="space-y-5 p-4 md:p-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
      <div>
        <h1 class="text-2xl font-semibold">Stock</h1>
        <p class="text-sm text-neutral-500">Monitor inventory risk and record stock transactions.</p>
      </div>
    </div>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
      <div class="rounded-md border p-4 dark:border-neutral-800">
        <div class="text-sm text-neutral-500">Products</div>
        <div class="mt-2 text-2xl font-semibold">{{ stats.total }}</div>
      </div>
      <div class="rounded-md border p-4 dark:border-neutral-800">
        <div class="text-sm text-neutral-500">Out of Stock</div>
        <div class="mt-2 text-2xl font-semibold text-red-600">{{ stats.out }}</div>
      </div>
      <div class="rounded-md border p-4 dark:border-neutral-800">
        <div class="text-sm text-neutral-500">Low Stock</div>
        <div class="mt-2 text-2xl font-semibold text-amber-600">{{ stats.low }}</div>
      </div>
      <div class="rounded-md border p-4 dark:border-neutral-800">
        <div class="text-sm text-neutral-500">Out Soon</div>
        <div class="mt-2 text-2xl font-semibold text-yellow-600">{{ stats.soon }}</div>
      </div>
    </section>

    <form class="flex flex-wrap items-end gap-3 rounded-md border p-4 dark:border-neutral-800" @submit.prevent="applyFilters">
      <BaseInput v-model="search" label="Search" placeholder="Product name or SKU" />
      <BaseSelect v-model="stockFilter" label="Stock filter" :options="stockFilterOptions" placeholder="All stock" />
      <BaseInput v-if="stockFilter === 'below_custom'" v-model="threshold" label="Less than" type="number" />
      <BaseSelect v-model="unit" label="Unit" :options="unitOptions" placeholder="All units" />
      <button class="h-10 rounded-md bg-teal-700 px-4 text-sm font-medium text-white">Apply</button>
      <button type="button" class="h-10 rounded-md border px-4 text-sm font-medium" @click="clearFilters">Clear</button>
    </form>

    <div class="overflow-x-auto rounded-md border dark:border-neutral-800">
      <table class="min-w-full text-sm">
        <thead class="bg-neutral-50 dark:bg-neutral-900">
          <tr>
            <th class="px-4 py-3 text-left"><SortLink action="/stock" sort="name" label="Product" :filters="filters" /></th>
            <th class="px-4 py-3 text-left"><SortLink action="/stock" sort="sku" label="SKU" :filters="filters" /></th>
            <th class="px-4 py-3 text-right"><SortLink action="/stock" sort="stock_quantity" label="Stock" :filters="filters" align="right" /></th>
            <th class="px-4 py-3 text-right"><SortLink action="/stock" sort="low_stock_threshold" label="Alert At" :filters="filters" align="right" /></th>
            <th class="px-4 py-3 text-left">Warning</th>
            <th class="px-4 py-3 text-right">Action</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="product in products.data" :key="product.id" class="border-t dark:border-neutral-800">
            <td class="px-4 py-3 font-medium">{{ product.name }}</td>
            <td class="px-4 py-3">{{ product.sku }}</td>
            <td class="px-4 py-3 text-right">{{ product.stock_quantity }} {{ product.unit }}</td>
            <td class="px-4 py-3 text-right">{{ product.low_stock_threshold }} {{ product.unit }}</td>
            <td class="px-4 py-3">
              <span class="rounded px-2 py-1 text-xs font-medium" :class="stockStatus(product).class">{{ stockStatus(product).label }}</span>
            </td>
            <td class="px-4 py-3 text-right">
              <button type="button" class="rounded-md border px-3 py-1.5 text-xs" @click="productPendingAdjustment = product">Adjust Stock</button>
            </td>
          </tr>
          <tr v-if="!products.data.length">
            <td colspan="6" class="px-4 py-8 text-center text-neutral-500">No stock records match these filters.</td>
          </tr>
        </tbody>
      </table>
    </div>

    <BasePagination :links="products.links" />
  </div>

  <StockAdjustmentModal
    :open="Boolean(productPendingAdjustment)"
    :product="productPendingAdjustment"
    @close="productPendingAdjustment = null"
  />
</template>
