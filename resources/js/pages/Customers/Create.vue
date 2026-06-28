<script setup lang="ts">
import { useForm } from "@inertiajs/vue3";
import BaseButton from "@/components/Base/BaseButton.vue";
import BaseInput from "@/components/Base/BaseInput.vue";
import BaseSelect from "@/components/Base/BaseSelect.vue";

const form = useForm({ name: "", email: "", phone: "", company_name: "", status: "active", credit_limit: 0 });
const statuses = ["active", "inactive", "blocked"].map((status) => ({ value: status, label: status }));
</script>

<template>
  <form class="grid max-w-2xl gap-4 p-4 md:p-6" @submit.prevent="form.post('/customers')">
    <h1 class="text-2xl font-semibold">New Customer</h1>
    <BaseInput v-model="form.name" label="Name" :error="form.errors.name" />
    <BaseInput v-model="form.email" label="Email" type="email" :error="form.errors.email" />
    <BaseInput v-model="form.phone" label="Phone" :error="form.errors.phone" />
    <BaseInput v-model="form.company_name" label="Company" :error="form.errors.company_name" />
    <BaseSelect v-model="form.status" label="Status" :options="statuses" :error="form.errors.status" />
    <BaseInput v-model="form.credit_limit" label="Credit Limit" type="number" :error="form.errors.credit_limit" />
    <BaseButton type="submit" :loading="form.processing">Save Customer</BaseButton>
  </form>
</template>
