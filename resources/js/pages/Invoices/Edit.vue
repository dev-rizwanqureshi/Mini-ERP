<script setup lang="ts">
import CreateInvoice from "./Create.vue";
defineProps<{ invoice: any; customers: any[]; products: any[] }>();
</script>

<template>
  <CreateInvoice :customers="customers" :products="products" />
</template>
