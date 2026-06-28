<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
import BasePagination from "@/components/Base/BasePagination.vue";
import SearchFilter from "@/components/ui/SearchFilter.vue";
defineProps<{ products: any; filters: { search?: string } }>();
</script>

<template>
  <div class="space-y-5 p-4 md:p-6">
    <div class="flex items-center justify-between gap-3">
      <h1 class="text-2xl font-semibold">Products</h1>
      <Link href="/products/create" class="rounded-md bg-teal-700 px-4 py-2 text-sm font-medium text-white">New Product</Link>
    </div>
    <SearchFilter action="/products" :search="filters.search" />
    <div class="overflow-x-auto rounded-md border dark:border-neutral-800">
      <table class="min-w-full text-sm">
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
