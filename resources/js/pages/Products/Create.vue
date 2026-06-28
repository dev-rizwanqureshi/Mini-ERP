<script setup lang="ts">
import { useForm } from "@inertiajs/vue3";
import BaseButton from "@/components/Base/BaseButton.vue";
import BaseInput from "@/components/Base/BaseInput.vue";

const form = useForm({ name: "", sku: "", unit_price: 0, cost_price: 0, stock_quantity: 0, low_stock_threshold: 5, unit: "pcs", tax_rate: 0, status: "active" });
</script>

<template>
  <form class="grid max-w-2xl gap-4 p-4 md:p-6" @submit.prevent="form.post('/products')">
    <h1 class="text-2xl font-semibold">New Product</h1>
    <BaseInput v-model="form.name" label="Name" :error="form.errors.name" />
    <BaseInput v-model="form.sku" label="SKU" :error="form.errors.sku" />
    <BaseInput v-model="form.unit_price" label="Unit Price" type="number" :error="form.errors.unit_price" />
    <BaseInput v-model="form.cost_price" label="Cost Price" type="number" :error="form.errors.cost_price" />
    <BaseInput v-model="form.stock_quantity" label="Stock" type="number" :error="form.errors.stock_quantity" />
    <BaseButton type="submit" :loading="form.processing">Save Product</BaseButton>
  </form>
</template>
