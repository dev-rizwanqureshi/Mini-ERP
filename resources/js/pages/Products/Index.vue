<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
import BasePagination from "@/components/Base/BasePagination.vue";
import SearchFilter from "@/components/ui/SearchFilter.vue";
import SortLink from "@/components/ui/SortLink.vue";
defineProps<{ products: any; filters: Record<string, unknown> & { search?: string } }>();
</script>

<template>
  <div class="space-y-5 p-4 md:p-6">
    <div class="flex items-center justify-between gap-3">
      <h1 class="text-2xl font-semibold">Products</h1>
      <Link href="/products/create" class="rounded-md bg-teal-700 px-4 py-2 text-sm font-medium text-white">New Product</Link>
    </div>
    <SearchFilter action="/products" :search="filters.search" :filters="filters" placeholder="Search products" />
    <div class="overflow-x-auto rounded-md border dark:border-neutral-800">
      <table class="min-w-full text-sm">
        <thead class="bg-neutral-50 dark:bg-neutral-900">
          <tr>
            <th class="px-4 py-3 text-left"><SortLink action="/products" sort="name" label="Name" :filters="filters" /></th>
            <th class="px-4 py-3 text-left"><SortLink action="/products" sort="sku" label="SKU" :filters="filters" /></th>
            <th class="px-4 py-3 text-left"><SortLink action="/products" sort="unit_price" label="Price" :filters="filters" /></th>
            <th class="px-4 py-3 text-right"><SortLink action="/products" sort="stock_quantity" label="Stock" :filters="filters" align="right" /></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="product in products.data" :key="product.id" class="border-b dark:border-neutral-800">
            <td class="px-4 py-3 font-medium"><Link :href="`/products/${product.id}`">{{ product.name }}</Link></td>
            <td class="px-4 py-3">{{ product.sku }}</td>
            <td class="px-4 py-3">${{ product.unit_price }}</td>
            <td class="px-4 py-3 text-right">{{ product.stock_quantity }} {{ product.unit }}</td>
          </tr>
        </tbody>
      </table>
    </div>
    <BasePagination :links="products.links" />
  </div>
</template>
