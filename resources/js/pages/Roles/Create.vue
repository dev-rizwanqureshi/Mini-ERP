<script setup lang="ts">
import { Link, useForm } from "@inertiajs/vue3";
import BaseButton from "@/components/Base/BaseButton.vue";
import BaseInput from "@/components/Base/BaseInput.vue";
import BaseTextarea from "@/components/Base/BaseTextarea.vue";
import PermissionGrid from "./Partials/PermissionGrid.vue";

defineProps<{
  permissionModules: Record<string, { label: string; description: string; abilities: Record<string, string> }>;
}>();

const form = useForm({
  display_name: "",
  description: "",
  permissions: [] as string[],
});
</script>

<template>
  <form class="space-y-6 p-4 md:p-6" @submit.prevent="form.post('/roles')">
    <div class="flex flex-wrap items-center justify-between gap-3">
      <div>
        <h1 class="text-2xl font-semibold">Create Role</h1>
        <p class="text-sm text-neutral-500">Add a custom role and choose the services it can manage.</p>
      </div>
      <Link href="/roles" class="rounded-md border px-4 py-2 text-sm">Back to roles</Link>
    </div>

    <section class="grid gap-4 rounded-md border p-5 dark:border-neutral-800 md:grid-cols-2">
      <BaseInput v-model="form.display_name" label="Role name" :error="form.errors.display_name" required />
      <BaseTextarea v-model="form.description" label="Description" :error="form.errors.description" />
    </section>

    <PermissionGrid v-model="form.permissions" :permission-modules="permissionModules" />

    <BaseButton type="submit" :loading="form.processing">Create role</BaseButton>
  </form>
</template>
