<script setup lang="ts">
type PermissionModules = Record<string, { label: string; description: string; abilities: Record<string, string> }>;

const permissions = defineModel<string[]>({ required: true });

const props = defineProps<{
  permissionModules: PermissionModules;
  disabled?: boolean;
}>();

function permissionKey(module: string, ability: string) {
  return `${module}.${ability}`;
}

function hasPermission(permission: string) {
  return permissions.value.includes(permission);
}

function togglePermission(permission: string) {
  if (props.disabled) return;

  if (hasPermission(permission)) {
    permissions.value = permissions.value.filter((item) => item !== permission);
    return;
  }

  permissions.value = [...permissions.value, permission];
}

function toggleModule(module: string) {
  if (props.disabled) return;

  const modulePermissions = Object.keys(props.permissionModules[module].abilities).map((ability) => permissionKey(module, ability));
  const allSelected = modulePermissions.every((permission) => hasPermission(permission));

  permissions.value = allSelected
    ? permissions.value.filter((permission) => !modulePermissions.includes(permission))
    : Array.from(new Set([...permissions.value, ...modulePermissions]));
}
</script>

<template>
  <section class="grid gap-4 xl:grid-cols-2">
    <div
      v-for="(module, key) in permissionModules"
      :key="key"
      class="rounded-md border border-neutral-200 bg-white p-5 dark:border-neutral-800 dark:bg-neutral-900"
    >
      <div class="mb-4 flex items-start justify-between gap-4">
        <div>
          <h2 class="font-semibold">{{ module.label }}</h2>
          <p class="mt-1 text-sm text-neutral-500">{{ module.description }}</p>
        </div>
        <button
          type="button"
          class="rounded-md border px-3 py-1.5 text-xs disabled:opacity-50"
          :disabled="disabled"
          @click="toggleModule(String(key))"
        >
          Toggle all
        </button>
      </div>

      <div class="grid gap-2 sm:grid-cols-2">
        <label
          v-for="(label, ability) in module.abilities"
          :key="ability"
          class="flex items-center gap-2 rounded-md border px-3 py-2 text-sm dark:border-neutral-800"
        >
          <input
            type="checkbox"
            class="size-4 rounded border-neutral-300"
            :checked="hasPermission(permissionKey(String(key), String(ability)))"
            :disabled="disabled"
            @change="togglePermission(permissionKey(String(key), String(ability)))"
          />
          <span>{{ label }}</span>
        </label>
      </div>
    </div>
  </section>
</template>
