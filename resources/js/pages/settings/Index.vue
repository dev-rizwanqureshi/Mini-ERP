<script setup lang="ts">
import { useForm } from "@inertiajs/vue3";
import BaseButton from "@/components/Base/BaseButton.vue";
import BaseInput from "@/components/Base/BaseInput.vue";
import SearchFilter from "@/components/ui/SearchFilter.vue";
import SortLink from "@/components/ui/SortLink.vue";
const props = defineProps<{ settings: any[]; filters: Record<string, unknown> & { search?: string } }>();
const form = useForm({ settings: Object.fromEntries(props.settings.map((setting) => [setting.key, setting.value])) });
</script>

<template>
  <div class="space-y-5 p-4 md:p-6">
    <div>
      <h1 class="text-2xl font-semibold">ERP Settings</h1>
      <p class="text-sm text-neutral-500">Company, invoice, and system defaults.</p>
    </div>

    <SearchFilter action="/erp-settings" :search="filters.search" :filters="filters" placeholder="Search settings" />

    <form class="space-y-4" @submit.prevent="form.put('/erp-settings')">
      <div class="overflow-x-auto rounded-md border dark:border-neutral-800">
        <table class="min-w-full text-sm">
          <thead class="bg-neutral-50 dark:bg-neutral-900">
            <tr>
              <th class="px-4 py-3 text-left"><SortLink action="/erp-settings" sort="group" label="Group" :filters="filters" /></th>
              <th class="px-4 py-3 text-left"><SortLink action="/erp-settings" sort="key" label="Setting" :filters="filters" /></th>
              <th class="px-4 py-3 text-left"><SortLink action="/erp-settings" sort="value" label="Value" :filters="filters" /></th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="setting in settings" :key="setting.key" class="border-t dark:border-neutral-800">
              <td class="px-4 py-3 capitalize">{{ setting.group }}</td>
              <td class="px-4 py-3 font-medium">{{ setting.key.replaceAll("_", " ") }}</td>
              <td class="px-4 py-3"><BaseInput v-model="form.settings[setting.key]" /></td>
            </tr>
          </tbody>
        </table>
      </div>
      <BaseButton type="submit" :loading="form.processing">Save Settings</BaseButton>
    </form>
  </div>
</template>
