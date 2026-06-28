<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
defineProps<{ product: any }>();
</script>

<template>
  <div class="space-y-4 p-4 md:p-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-semibold">{{ product.name }}</h1>
      <Link :href="`/products/${product.id}/edit`" class="rounded-md border px-4 py-2 text-sm">Edit</Link>
    </div>
    <p>{{ product.sku }}</p>
    <p>${{ product.unit_price }} · {{ product.stock_quantity }} {{ product.unit }}</p>
  </div>
</template>
