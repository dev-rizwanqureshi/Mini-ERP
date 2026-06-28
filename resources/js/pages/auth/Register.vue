<script setup lang="ts">
import { Form, Head } from "@inertiajs/vue3";
import InputError from "@/components/InputError.vue";
import PasswordInput from "@/components/PasswordInput.vue";
import TextLink from "@/components/TextLink.vue";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Spinner } from "@/components/ui/spinner";
import { login } from "@/routes";
import { store } from "@/routes/register";

defineProps<{
  passwordRules: string;
}>();

defineOptions({
  layout: {
    title: "Create an account",
    description: "Enter your details below to create your account",
  },
});
</script>

<template>
  <Head title="Register" />

  <Form
    v-bind="store.form()"
    :reset-on-success="['password', 'password_confirmation']"
    v-slot="{ errors, processing }"
    class="flex flex-col gap-6"
  >
    <div class="grid gap-6">
      <div class="grid gap-2">
        <Label for="name">Name</Label>
        <Input
          id="name"
          type="text"
          required
          autofocus
          :tabindex="1"
          autocomplete="name"
          name="name"
          placeholder="Full name"
        />
        <InputError :message="errors.name" />
      </div>

      <div class="grid gap-2">
        <Label for="email">Email address</Label>
        <Input
          id="email"
          type="email"
          required
          :tabindex="2"
          autocomplete="email"
          name="email"
          placeholder="email@example.com"
        />
        <InputError :message="errors.email" />
      </div>

      <div class="grid gap-2">
        <Label for="gender">Gender</Label>
        <select
          id="gender"
          name="gender"
          :tabindex="3"
          class="flex h-9 w-full min-w-0 rounded-md border border-input bg-transparent px-3 py-1 text-base shadow-xs outline-none transition-[color,box-shadow] selection:bg-primary selection:text-primary-foreground focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm dark:bg-input/30"
        >
          <option value="">Select gender</option>
          <option value="male">Male</option>
          <option value="female">Female</option>
          <option value="other">Other</option>
          <option value="prefer_not_to_say">Prefer not to say</option>
        </select>
        <InputError :message="errors.gender" />
      </div>

      <div class="grid gap-2">
        <Label for="password">Password</Label>
        <PasswordInput
          id="password"
          required
          :tabindex="4"
          autocomplete="new-password"
          name="password"
          placeholder="Password"
          :passwordrules="passwordRules"
        />
        <InputError :message="errors.password" />
      </div>

      <div class="grid gap-2">
        <Label for="password_confirmation">Confirm password</Label>
        <PasswordInput
          id="password_confirmation"
          required
          :tabindex="5"
          autocomplete="new-password"
          name="password_confirmation"
          placeholder="Confirm password"
          :passwordrules="passwordRules"
        />
        <InputError :message="errors.password_confirmation" />
      </div>

      <Button
        type="submit"
        class="mt-2 w-full"
        tabindex="6"
        :disabled="processing"
        data-test="register-user-button"
      >
        <Spinner v-if="processing" />
        Create account
      </Button>
    </div>

    <div class="text-center text-sm text-muted-foreground">
      Already have an account?
      <TextLink
        :href="login()"
        class="underline underline-offset-4"
        :tabindex="7"
        >Log in</TextLink
      >
    </div>
  </Form>
</template>
