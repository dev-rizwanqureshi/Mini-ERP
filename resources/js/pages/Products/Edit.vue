<script setup lang="ts">
import { useForm } from "@inertiajs/vue3";
import BaseButton from "@/components/Base/BaseButton.vue";
import BaseInput from "@/components/Base/BaseInput.vue";
import BaseSelect from "@/components/Base/BaseSelect.vue";
import BaseTextarea from "@/components/Base/BaseTextarea.vue";
import CancelLink from "@/components/ui/CancelLink.vue";
import CrudDialogPage from "@/components/ui/CrudDialogPage.vue";
const props = defineProps<{ product: any }>();
const form = useForm({ ...props.product });
const statusOptions = [
  { value: "active", label: "Active" },
  { value: "inactive", label: "Inactive" },
];
</script>

<template>
  <CrudDialogPage max-width-class="max-w-3xl">
    <form class="space-y-5 p-4 md:p-6" @submit.prevent="form.put(`/products/${product.id}`)">
      <header class="border-b pb-4 dark:border-neutral-800">
        <h1 class="text-2xl font-semibold">Edit Product</h1>
        <p class="mt-1 text-sm text-neutral-500">Update product details, pricing, and stock settings.</p>
      </header>

      <section class="grid gap-4 md:grid-cols-2">
        <BaseInput v-model="form.name" label="Product Name" :error="form.errors.name" />
        <BaseInput v-model="form.sku" label="SKU" :error="form.errors.sku" />
        <div class="md:col-span-2">
          <BaseTextarea v-model="form.description" label="Description" :error="form.errors.description" />
        </div>
      </section>

      <section class="grid gap-4 rounded-md border p-4 dark:border-neutral-800 md:grid-cols-3">
        <BaseInput v-model="form.unit_price" label="Selling Price" type="number" :error="form.errors.unit_price" />
        <BaseInput v-model="form.cost_price" label="Purchase / Cost Price" type="number" :error="form.errors.cost_price" />
        <BaseInput v-model="form.tax_rate" label="Tax Rate %" type="number" :error="form.errors.tax_rate" />
      </section>

      <section class="grid gap-4 rounded-md border p-4 dark:border-neutral-800 md:grid-cols-4">
        <BaseInput v-model="form.stock_quantity" label="Current Stock" type="number" :error="form.errors.stock_quantity" />
        <BaseInput v-model="form.low_stock_threshold" label="Low Stock Alert" type="number" :error="form.errors.low_stock_threshold" />
        <BaseInput v-model="form.unit" label="Unit" placeholder="pcs, kg, box" :error="form.errors.unit" />
        <BaseSelect v-model="form.status" label="Status" :options="statusOptions" :error="form.errors.status" />
      </section>

      <footer class="flex flex-wrap justify-end gap-2 border-t pt-4 dark:border-neutral-800">
        <CancelLink :href="`/products/${product.id}`" :dirty="form.isDirty" />
        <BaseButton type="submit" :loading="form.processing">Update Product</BaseButton>
      </footer>
    </form>
  </CrudDialogPage>
</template>
