<script setup lang="ts">
import BaseBadge from "@/components/Base/BaseBadge.vue";
defineProps<{ status: string }>();
</script>

<template>
  <BaseBadge
    :text="status"
    :color="status === 'paid' ? 'success' : status === 'overdue' ? 'danger' : status === 'draft' ? 'warning' : 'info'"
  />
</template>
