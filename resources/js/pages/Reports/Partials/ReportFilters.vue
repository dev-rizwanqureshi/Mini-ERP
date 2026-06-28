<script setup lang="ts">
import { Link, router } from "@inertiajs/vue3";
import { computed, ref, watch } from "vue";

const props = defineProps<{
  action: string;
  filters: Record<string, unknown>;
  exportType?: string;
}>();

const period = ref(String(props.filters.period ?? "month"));
const periodValue = ref(String(props.filters.period_value ?? ""));
const dateFrom = ref(String(props.filters.date_from ?? ""));
const dateTo = ref(String(props.filters.date_to ?? ""));
const search = ref(String(props.filters.search ?? ""));

const periodInputType = computed(() => {
  if (period.value === "day") return "date";
  if (period.value === "week") return "week";
  if (period.value === "month") return "month";
  if (period.value === "year") return "number";
  return "text";
});

watch(period, () => {
  periodValue.value = "";
});

function params(): Record<string, string> {
  const query: Record<string, unknown> = {
    period: period.value,
    search: search.value,
    sort: props.filters.sort,
    direction: props.filters.direction,
  };

  if (period.value === "custom") {
    query.date_from = dateFrom.value;
    query.date_to = dateTo.value;
  } else {
    query.period_value = periodValue.value;
  }

  return Object.fromEntries(
    Object.entries(query)
      .filter(([, value]) => value !== undefined && value !== null && value !== "")
      .map(([key, value]) => [key, String(value)])
  );
}

function submit() {
  router.get(props.action, params(), { preserveState: true, preserveScroll: true });
}

const exportHref = computed(() => {
  if (!props.exportType) return "";

  const query = new URLSearchParams();
  Object.entries(params()).forEach(([key, value]) => query.set(key, String(value)));

  return `/reports/export/${props.exportType}?${query.toString()}`;
});
</script>

<template>
  <form class="flex flex-wrap items-end gap-3 rounded-md border p-4 dark:border-neutral-800" @submit.prevent="submit">
    <label class="grid gap-1.5 text-sm">
      <span class="font-medium">Period</span>
      <select v-model="period" class="h-10 rounded-md border bg-white px-3 text-sm dark:border-neutral-700 dark:bg-neutral-900">
        <option value="day">Day</option>
        <option value="week">Week</option>
        <option value="month">Month</option>
        <option value="year">Year</option>
        <option value="custom">Custom</option>
      </select>
    </label>

    <label v-if="period !== 'custom'" class="grid gap-1.5 text-sm">
      <span class="font-medium">Value</span>
      <input
        v-model="periodValue"
        :type="periodInputType"
        :min="period === 'year' ? '1971' : undefined"
        class="h-10 rounded-md border px-3 text-sm dark:border-neutral-700 dark:bg-neutral-900"
      />
    </label>

    <template v-else>
      <label class="grid gap-1.5 text-sm">
        <span class="font-medium">From</span>
        <input v-model="dateFrom" type="date" class="h-10 rounded-md border px-3 text-sm dark:border-neutral-700 dark:bg-neutral-900" />
      </label>
      <label class="grid gap-1.5 text-sm">
        <span class="font-medium">To</span>
        <input v-model="dateTo" type="date" class="h-10 rounded-md border px-3 text-sm dark:border-neutral-700 dark:bg-neutral-900" />
      </label>
    </template>

    <label class="grid min-w-64 flex-1 gap-1.5 text-sm">
      <span class="font-medium">Search</span>
      <input v-model="search" class="h-10 rounded-md border px-3 text-sm dark:border-neutral-700 dark:bg-neutral-900" />
    </label>

    <button class="h-10 rounded-md bg-teal-700 px-4 text-sm font-medium text-white">Apply</button>
    <Link v-if="exportType" :href="exportHref" class="inline-flex h-10 items-center rounded-md border px-4 text-sm">Export CSV</Link>
  </form>
</template>
