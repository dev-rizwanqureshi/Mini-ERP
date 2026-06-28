<script setup lang="ts">
import { Head, usePage } from "@inertiajs/vue3";
import { Clock3 } from "@lucide/vue";
import { computed } from "vue";

const page = usePage();
const user = computed(() => page.props.auth.user as any);
</script>

<template>
  <Head title="Account Pending" />

  <div class="grid min-h-[70vh] place-items-center p-6">
    <section class="max-w-xl rounded-md border border-neutral-200 bg-white p-8 text-center shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
      <Clock3 class="mx-auto mb-4 size-10 text-teal-700" />
      <h1 class="text-2xl font-semibold">Your account is on hold</h1>
      <p class="mt-3 text-sm leading-6 text-neutral-600 dark:text-neutral-300">
        Thanks for registering, {{ user?.name }}. A super admin or admin needs to assign your role before you can access the ERP workspace.
      </p>
      <p class="mt-4 text-xs text-neutral-500">
        You can still update your profile from the user menu while your access is being reviewed.
      </p>
    </section>
  </div>
</template>
