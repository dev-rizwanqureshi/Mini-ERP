<script setup lang="ts">
import { useForm } from "@inertiajs/vue3";
import BaseButton from "@/components/Base/BaseButton.vue";
import BaseInput from "@/components/Base/BaseInput.vue";
import BaseSelect from "@/components/Base/BaseSelect.vue";

const props = defineProps<{ roles: any[]; genders: { value: string; label: string }[] }>();
const form = useForm({
  name: "",
  email: "",
  password: "",
  role_id: "",
  gender: "",
  is_active: false,
});

const roleOptions = props.roles.map((role) => ({ value: role.id, label: role.display_name }));
</script>

<template>
  <form class="grid max-w-2xl gap-4 p-4 md:p-6" @submit.prevent="form.post('/users')">
    <h1 class="text-2xl font-semibold">New User</h1>
    <BaseInput v-model="form.name" label="Name" :error="form.errors.name" />
    <BaseInput v-model="form.email" label="Email" type="email" :error="form.errors.email" />
    <BaseInput v-model="form.password" label="Password" type="password" :error="form.errors.password" />
    <BaseSelect v-model="form.gender" label="Gender" :options="genders" placeholder="Not set" :error="form.errors.gender" />
    <BaseSelect v-model="form.role_id" label="Role" :options="roleOptions" placeholder="Keep pending" :error="form.errors.role_id" />
    <label class="flex items-center gap-2 text-sm">
      <input v-model="form.is_active" type="checkbox" class="size-4 rounded border-neutral-300" />
      Account is active
    </label>
    <p class="text-sm text-neutral-500">Leave role empty or active unchecked to keep the account on hold.</p>
    <BaseButton type="submit" :loading="form.processing">Create User</BaseButton>
  </form>
</template>
