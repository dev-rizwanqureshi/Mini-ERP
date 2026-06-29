<script setup lang="ts">
import { useForm } from "@inertiajs/vue3";
import BaseButton from "@/components/Base/BaseButton.vue";
import BaseInput from "@/components/Base/BaseInput.vue";
import BaseSelect from "@/components/Base/BaseSelect.vue";
import CancelLink from "@/components/ui/CancelLink.vue";
import CrudDialogPage from "@/components/ui/CrudDialogPage.vue";

const props = defineProps<{ managedUser: any; roles: any[]; genders: { value: string; label: string }[] }>();
const form = useForm({
  name: props.managedUser.name,
  email: props.managedUser.email,
  role_id: props.managedUser.role_id ?? "",
  gender: props.managedUser.gender ?? "",
  is_active: Boolean(props.managedUser.is_active),
});

const roleOptions = props.roles.map((role) => ({ value: role.id, label: role.display_name }));
</script>

<template>
  <CrudDialogPage>
    <form class="grid max-w-2xl gap-4 p-4 md:p-6" @submit.prevent="form.put(`/users/${managedUser.id}`)">
      <div>
        <h1 class="text-2xl font-semibold">Manage User</h1>
        <p class="text-sm text-neutral-500">Assigning a role and activating the account grants ERP access.</p>
      </div>
      <BaseInput v-model="form.name" label="Name" :error="form.errors.name" />
      <BaseInput v-model="form.email" label="Email" type="email" :error="form.errors.email" />
      <BaseSelect v-model="form.gender" label="Gender" :options="genders" placeholder="Not set" :error="form.errors.gender" />
      <BaseSelect v-model="form.role_id" label="Role" :options="roleOptions" placeholder="Keep pending" :error="form.errors.role_id" />
      <label class="flex items-center gap-2 text-sm">
        <input v-model="form.is_active" type="checkbox" class="size-4 rounded border-neutral-300" />
        Account is active
      </label>
      <div class="flex flex-wrap justify-end gap-2 pt-2">
        <CancelLink href="/users" :dirty="form.isDirty" />
        <BaseButton type="submit" :loading="form.processing">Save User</BaseButton>
      </div>
    </form>
  </CrudDialogPage>
</template>
