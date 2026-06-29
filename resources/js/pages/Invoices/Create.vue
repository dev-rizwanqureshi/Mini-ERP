<script setup lang="ts">
import { computed } from "vue";
import { useForm } from "@inertiajs/vue3";
import BaseButton from "@/components/Base/BaseButton.vue";
import BaseInput from "@/components/Base/BaseInput.vue";
import BaseSelect from "@/components/Base/BaseSelect.vue";
import BaseTextarea from "@/components/Base/BaseTextarea.vue";
import CancelLink from "@/components/ui/CancelLink.vue";
import CrudDialogPage from "@/components/ui/CrudDialogPage.vue";

const props = defineProps<{ customers: any[]; products: any[]; invoice?: any }>();

const isEditing = computed(() => Boolean(props.invoice?.id));
const cancelHref = computed(() => isEditing.value ? `/invoices/${props.invoice.id}` : "/invoices");

function dateValue(value: string | null | undefined, fallback: string) {
  return String(value ?? fallback).slice(0, 10);
}

const form = useForm({
  customer_id: props.invoice?.customer_id ?? "",
  invoice_date: dateValue(props.invoice?.invoice_date, new Date().toISOString().slice(0, 10)),
  due_date: dateValue(props.invoice?.due_date, new Date(Date.now() + 30 * 86400000).toISOString().slice(0, 10)),
  discount_type: props.invoice?.discount_type ?? "fixed",
  discount_value: Number(props.invoice?.discount_value ?? 0),
  currency_code: props.invoice?.currency_code ?? "USD",
  currency_symbol: props.invoice?.currency_symbol ?? "$",
  notes: props.invoice?.notes ?? "",
  terms: props.invoice?.terms ?? "Net 30",
  footer: props.invoice?.footer ?? "",
  items: props.invoice?.items?.length
    ? props.invoice.items.map((item: any) => ({
        product_id: item.product_id ?? "",
        description: item.description ?? "",
        quantity: Number(item.quantity ?? 1),
        unit_price: Number(item.unit_price ?? 0),
        tax_rate: Number(item.tax_rate ?? 0),
        discount_type: item.discount_type ?? "fixed",
        discount_value: Number(item.discount_value ?? 0),
      }))
    : [{ product_id: "", description: "", quantity: 1, unit_price: 0, tax_rate: 0, discount_type: "fixed", discount_value: 0 }],
});

const customerOptions = computed(() => props.customers.map((customer) => ({ value: customer.id, label: customer.name })));
const productOptions = computed(() => props.products.map((product) => ({ value: product.id, label: `${product.name} (${product.sku})` })));

function selectProduct(index: number) {
  const product = props.products.find((item) => Number(item.id) === Number(form.items[index].product_id));
  if (!product) return;
  form.items[index].description = product.name;
  form.items[index].unit_price = Number(product.unit_price);
  form.items[index].tax_rate = Number(product.tax_rate);
}

function addLine() {
  form.items.push({ product_id: "", description: "", quantity: 1, unit_price: 0, tax_rate: 0, discount_type: "fixed", discount_value: 0 });
}

const totals = computed(() => {
  let subtotal = 0;
  let tax = 0;
  let lineDiscount = 0;
  for (const item of form.items) {
    const base = Number(item.quantity) * Number(item.unit_price);
    subtotal += base;
    tax += base * (Number(item.tax_rate) / 100);
    lineDiscount += item.discount_type === "percentage" ? base * (Number(item.discount_value) / 100) : Number(item.discount_value);
  }
  const invoiceDiscount = form.discount_type === "percentage" ? subtotal * (Number(form.discount_value) / 100) : Number(form.discount_value);
  return { subtotal, tax, discount: lineDiscount + invoiceDiscount, total: Math.max(0, subtotal + tax - lineDiscount - invoiceDiscount) };
});

function submit() {
  if (isEditing.value) {
    form.put(`/invoices/${props.invoice.id}`);
    return;
  }

  form.post('/invoices');
}
</script>

<template>
  <CrudDialogPage>
    <form class="grid gap-6 p-4 md:grid-cols-[1fr_320px] md:p-6" @submit.prevent="submit">
      <div class="space-y-5">
        <h1 class="text-2xl font-semibold">{{ isEditing ? "Edit Invoice" : "New Invoice" }}</h1>
        <div class="grid gap-4 md:grid-cols-3">
          <BaseSelect v-model="form.customer_id" label="Customer" :options="customerOptions" :error="form.errors.customer_id" />
          <BaseInput v-model="form.invoice_date" label="Invoice Date" type="date" :error="form.errors.invoice_date" />
          <BaseInput v-model="form.due_date" label="Due Date" type="date" :error="form.errors.due_date" />
        </div>
        <section class="space-y-3 rounded-md border p-4 dark:border-neutral-800">
          <div v-for="(item, index) in form.items" :key="index" class="grid gap-3 md:grid-cols-[1.2fr_1fr_80px_100px_80px_40px]">
            <BaseSelect v-model="item.product_id" :options="productOptions" placeholder="Product" @update:model-value="selectProduct(index)" />
            <BaseInput v-model="item.description" placeholder="Description" />
            <BaseInput v-model="item.quantity" type="number" placeholder="Qty" />
            <BaseInput v-model="item.unit_price" type="number" placeholder="Price" />
            <BaseInput v-model="item.tax_rate" type="number" placeholder="Tax" />
            <button type="button" class="rounded-md border" @click="form.items.splice(index, 1)">×</button>
          </div>
          <button type="button" class="rounded-md border px-3 py-2 text-sm" @click="addLine">Add line</button>
        </section>
        <BaseTextarea v-model="form.notes" label="Notes" />
        <BaseTextarea v-model="form.terms" label="Terms" />
      </div>
      <aside class="space-y-4 rounded-md border p-5 dark:border-neutral-800">
        <h2 class="font-semibold">Totals</h2>
        <BaseSelect v-model="form.discount_type" label="Discount Type" :options="[{ value: 'fixed', label: 'Fixed' }, { value: 'percentage', label: 'Percentage' }]" />
        <BaseInput v-model="form.discount_value" label="Discount" type="number" />
        <div class="space-y-2 text-sm">
          <div class="flex justify-between"><span>Subtotal</span><strong>${{ totals.subtotal.toFixed(2) }}</strong></div>
          <div class="flex justify-between"><span>Tax</span><strong>${{ totals.tax.toFixed(2) }}</strong></div>
          <div class="flex justify-between"><span>Discount</span><strong>${{ totals.discount.toFixed(2) }}</strong></div>
          <div class="flex justify-between text-lg"><span>Total</span><strong>${{ totals.total.toFixed(2) }}</strong></div>
        </div>
        <div class="grid gap-2">
          <BaseButton type="submit" class="w-full" :loading="form.processing">{{ isEditing ? "Update Invoice" : "Save Invoice" }}</BaseButton>
          <CancelLink :href="cancelHref" :dirty="form.isDirty" />
        </div>
      </aside>
    </form>
  </CrudDialogPage>
</template>
