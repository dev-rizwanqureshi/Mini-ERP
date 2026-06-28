<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
import BasePagination from "@/components/Base/BasePagination.vue";
import SearchFilter from "@/components/ui/SearchFilter.vue";
import SortLink from "@/components/ui/SortLink.vue";

defineProps<{ users: any; filters: Record<string, unknown> & { search?: string } }>();
</script>

<template>
  <div class="space-y-5 p-4 md:p-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
      <div>
        <h1 class="text-2xl font-semibold">Users</h1>
        <p class="text-sm text-neutral-500">Assign roles, approve pending accounts, and manage profile details.</p>
      </div>
      <Link href="/users/create" class="rounded-md bg-teal-700 px-4 py-2 text-sm font-medium text-white">New User</Link>
    </div>

    <SearchFilter action="/users" :search="filters.search" :filters="filters" placeholder="Search users" />

    <div class="overflow-x-auto rounded-md border dark:border-neutral-800">
      <table class="min-w-full text-sm">
        <thead class="bg-neutral-50 dark:bg-neutral-900">
          <tr>
            <th class="px-4 py-3 text-left"><SortLink action="/users" sort="name" label="Name" :filters="filters" /></th>
            <th class="px-4 py-3 text-left"><SortLink action="/users" sort="role" label="Role" :filters="filters" /></th>
            <th class="px-4 py-3 text-left"><SortLink action="/users" sort="gender" label="Gender" :filters="filters" /></th>
            <th class="px-4 py-3 text-left"><SortLink action="/users" sort="is_active" label="Status" :filters="filters" /></th>
            <th class="px-4 py-3 text-right font-medium">Action</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="user in users.data" :key="user.id" class="border-t dark:border-neutral-800">
            <td class="px-4 py-3">
              <div class="font-medium">{{ user.name }}</div>
              <div class="text-xs text-neutral-500">{{ user.email }}</div>
            </td>
            <td class="px-4 py-3">{{ user.role?.display_name ?? "Unassigned" }}</td>
            <td class="px-4 py-3">{{ user.gender ? String(user.gender).replaceAll("_", " ") : "Not set" }}</td>
            <td class="px-4 py-3">
              <span
                class="rounded px-2 py-1 text-xs font-medium"
                :class="user.is_active && user.role_id ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'"
              >
                {{ user.is_active && user.role_id ? "Active" : "Pending" }}
              </span>
            </td>
            <td class="px-4 py-3 text-right">
              <Link :href="`/users/${user.id}/edit`" class="rounded-md border px-3 py-1.5 text-xs">Manage</Link>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <BasePagination :links="users.links" />
  </div>
</template>
