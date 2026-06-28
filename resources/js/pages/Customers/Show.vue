<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
defineProps<{ customer: any }>();
</script>

<template>
  <div class="space-y-4 p-4 md:p-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-semibold">{{ customer.name }}</h1>
      <Link :href="`/customers/${customer.id}/edit`" class="rounded-md border px-4 py-2 text-sm">Edit</Link>
    </div>
    <p>{{ customer.email }}</p>
    <p class="text-neutral-500">{{ customer.company_name }}</p>
  </div>
</template>
