<script setup lang="ts">
import { useForm } from "@inertiajs/vue3";
import BaseButton from "@/components/Base/BaseButton.vue";
import BaseInput from "@/components/Base/BaseInput.vue";
import BaseSelect from "@/components/Base/BaseSelect.vue";
import CancelLink from "@/components/ui/CancelLink.vue";
import CrudDialogPage from "@/components/ui/CrudDialogPage.vue";
const props = defineProps<{ invoice: any; methods: any[] }>();
const form = useForm({ invoice_id: props.invoice.id, customer_id: props.invoice.customer_id, payment_date: new Date().toISOString().slice(0, 10), amount: props.invoice.balance_amount, payment_method: "cash", reference_number: "", notes: "" });
</script>

<template>
  <CrudDialogPage>
    <form class="grid max-w-xl gap-4 p-4 md:p-6" @submit.prevent="form.post('/payments')">
      <h1 class="text-2xl font-semibold">Record Payment</h1>
      <p class="text-sm text-neutral-500">{{ invoice.invoice_number }} · Balance ${{ invoice.balance_amount }}</p>
      <BaseInput v-model="form.payment_date" label="Payment Date" type="date" :error="form.errors.payment_date" />
      <BaseInput v-model="form.amount" label="Amount" type="number" :error="form.errors.amount" />
      <BaseSelect v-model="form.payment_method" label="Method" :options="methods.map((m) => ({ value: m.value, label: m.name ?? m.value }))" :error="form.errors.payment_method" />
      <BaseInput v-model="form.reference_number" label="Reference" :error="form.errors.reference_number" />
      <div class="flex flex-wrap justify-end gap-2 pt-2">
        <CancelLink :href="`/invoices/${invoice.id}`" :dirty="form.isDirty" />
        <BaseButton type="submit" :loading="form.processing">Record Payment</BaseButton>
      </div>
    </form>
  </CrudDialogPage>
</template>
