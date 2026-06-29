<script setup lang="ts">
import { Link, useForm } from "@inertiajs/vue3";
import BaseButton from "@/components/Base/BaseButton.vue";
import BaseInput from "@/components/Base/BaseInput.vue";
import BaseTextarea from "@/components/Base/BaseTextarea.vue";
import CancelLink from "@/components/ui/CancelLink.vue";
import CrudDialogPage from "@/components/ui/CrudDialogPage.vue";
import PermissionGrid from "./Partials/PermissionGrid.vue";

const props = defineProps<{
  role: any;
  permissionModules: Record<string, { label: string; description: string; abilities: Record<string, string> }>;
  allPermissions: string[];
  selectedPermissions: string[];
  isLocked: boolean;
}>();

const form = useForm({
  display_name: props.role.display_name,
  description: props.role.description ?? "",
  permissions: [...props.selectedPermissions],
});

</script>

<template>
  <CrudDialogPage>
    <form class="space-y-6 p-4 md:p-6" @submit.prevent="form.put(`/roles/${role.id}`)">
      <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
          <h1 class="text-2xl font-semibold">Manage {{ role.display_name }}</h1>
          <p class="text-sm text-neutral-500">Choose exactly which services this role can use.</p>
        </div>
        <Link href="/roles" class="rounded-md border px-4 py-2 text-sm">Back to roles</Link>
      </div>

      <div v-if="isLocked" class="rounded-md border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
        Super Admin is protected and always keeps full access. This prevents accidental lockout.
      </div>

      <section class="grid gap-4 rounded-md border p-5 dark:border-neutral-800 md:grid-cols-2">
        <BaseInput v-model="form.display_name" label="Role name" :disabled="isLocked" :error="form.errors.display_name" />
        <BaseTextarea v-model="form.description" label="Description" :disabled="isLocked" :error="form.errors.description" />
      </section>

      <PermissionGrid v-model="form.permissions" :permission-modules="permissionModules" :disabled="isLocked" />

      <div class="flex flex-wrap justify-end gap-2">
        <CancelLink href="/roles" :dirty="form.isDirty" />
        <BaseButton type="submit" :disabled="isLocked" :loading="form.processing">Save role rights</BaseButton>
      </div>
    </form>
  </CrudDialogPage>
</template>
