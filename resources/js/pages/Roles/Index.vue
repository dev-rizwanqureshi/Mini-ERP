<script setup lang="ts">
import { Link, router } from "@inertiajs/vue3";
import { Plus, ShieldCheck, Trash2 } from "@lucide/vue";
import SearchFilter from "@/components/ui/SearchFilter.vue";
import SortLink from "@/components/ui/SortLink.vue";

defineProps<{ roles: any[]; permissionModules: Record<string, any>; filters: Record<string, unknown> & { search?: string } }>();

function deleteRole(role: any) {
  if (!confirm(`Delete ${role.display_name}?`)) return;

  router.delete(`/roles/${role.id}`);
}
</script>

<template>
  <div class="space-y-5 p-4 md:p-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
      <div>
        <h1 class="text-2xl font-semibold">Role Management</h1>
        <p class="text-sm text-neutral-500">Control which ERP services each role can access.</p>
      </div>
      <Link href="/roles/create" class="inline-flex h-10 items-center gap-2 rounded-md bg-teal-700 px-4 text-sm font-medium text-white hover:bg-teal-800">
        <Plus class="size-4" />
        New role
      </Link>
    </div>

    <div class="flex flex-wrap items-center justify-between gap-3">
      <SearchFilter action="/roles" :search="filters.search" :filters="filters" placeholder="Search roles" />
      <div class="flex flex-wrap gap-3 text-sm">
        <SortLink action="/roles" sort="display_name" label="Role" :filters="filters" />
        <SortLink action="/roles" sort="users_count" label="Users" :filters="filters" />
        <SortLink action="/roles" sort="is_system" label="Type" :filters="filters" />
      </div>
    </div>

    <div class="grid gap-4 lg:grid-cols-2">
      <section
        v-for="role in roles"
        :key="role.id"
        class="rounded-md border border-neutral-200 bg-white p-5 dark:border-neutral-800 dark:bg-neutral-900"
      >
        <div class="flex items-start justify-between gap-4">
          <div>
            <div class="flex items-center gap-2">
              <ShieldCheck class="size-5 text-teal-700" />
              <h2 class="font-semibold">{{ role.display_name }}</h2>
            </div>
            <p class="mt-1 text-sm text-neutral-500">{{ role.description }}</p>
          </div>
          <div class="flex flex-col items-end gap-2">
            <span class="rounded bg-neutral-100 px-2 py-1 text-xs text-neutral-700">{{ role.users_count }} users</span>
            <span class="rounded px-2 py-1 text-xs" :class="role.is_system ? 'bg-amber-50 text-amber-700' : 'bg-teal-50 text-teal-700'">
              {{ role.is_system ? "System" : "Custom" }}
            </span>
          </div>
        </div>

        <div class="mt-4 flex items-center justify-between gap-3 text-sm">
          <span class="text-neutral-500">{{ role.name === "super_admin" ? "Protected full-access role" : `${role.permissions?.length ?? 0} permissions enabled` }}</span>
          <div class="flex items-center gap-2">
            <button
              v-if="!role.is_system && role.users_count === 0"
              type="button"
              class="inline-flex size-8 items-center justify-center rounded-md border text-red-600 hover:bg-red-50"
              :aria-label="`Delete ${role.display_name}`"
              :title="`Delete ${role.display_name}`"
              @click="deleteRole(role)"
            >
              <Trash2 class="size-4" />
            </button>
            <Link :href="`/roles/${role.id}/edit`" class="rounded-md border px-3 py-1.5 text-xs">Manage rights</Link>
          </div>
        </div>
      </section>
    </div>
  </div>
</template>
