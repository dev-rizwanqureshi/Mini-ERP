<script setup lang="ts">
import { useForm } from "@inertiajs/vue3";
import BaseButton from "@/components/Base/BaseButton.vue";
import BaseInput from "@/components/Base/BaseInput.vue";
const props = defineProps<{ settings: any[] }>();
const form = useForm({ settings: Object.fromEntries(props.settings.map((setting) => [setting.key, setting.value])) });
</script>

<template>
  <form class="grid max-w-3xl gap-4 p-4 md:p-6" @submit.prevent="form.put('/erp-settings')">
    <h1 class="text-2xl font-semibold">ERP Settings</h1>
    <BaseInput v-for="setting in settings" :key="setting.key" v-model="form.settings[setting.key]" :label="setting.key.replaceAll('_', ' ')" />
    <BaseButton type="submit" :loading="form.processing">Save Settings</BaseButton>
  </form>
</template>
