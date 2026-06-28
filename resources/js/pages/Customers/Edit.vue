<script setup lang="ts">
import { useForm } from "@inertiajs/vue3";
import BaseButton from "@/components/Base/BaseButton.vue";
import BaseInput from "@/components/Base/BaseInput.vue";
import BaseSelect from "@/components/Base/BaseSelect.vue";

const props = defineProps<{ customer: any }>();
const form = useForm({ ...props.customer });
const statuses = ["active", "inactive", "blocked"].map((status) => ({ value: status, label: status }));
</script>

<template>
  <form class="grid max-w-2xl gap-4 p-4 md:p-6" @submit.prevent="form.put(`/customers/${customer.id}`)">
    <h1 class="text-2xl font-semibold">Edit Customer</h1>
    <BaseInput v-model="form.name" label="Name" :error="form.errors.name" />
    <BaseInput v-model="form.email" label="Email" type="email" :error="form.errors.email" />
    <BaseInput v-model="form.phone" label="Phone" :error="form.errors.phone" />
    <BaseInput v-model="form.company_name" label="Company" :error="form.errors.company_name" />
    <BaseSelect v-model="form.status" label="Status" :options="statuses" :error="form.errors.status" />
    <BaseButton type="submit" :loading="form.processing">Update Customer</BaseButton>
  </form>
</template>
