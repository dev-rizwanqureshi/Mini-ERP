<script setup lang="ts">
import { Link, usePage } from "@inertiajs/vue3";
import {
  BarChart3,
  Boxes,
  CreditCard,
  FileText,
  LayoutGrid,
  Settings,
  ShieldCheck,
  Users,
  UserCog,
} from "@lucide/vue";
import { computed } from "vue";
import AppLogo from "@/components/AppLogo.vue";
import NavFooter from "@/components/NavFooter.vue";
import NavMain from "@/components/NavMain.vue";
import NavUser from "@/components/NavUser.vue";
import {
  Sidebar,
  SidebarContent,
  SidebarFooter,
  SidebarHeader,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
} from "@/components/ui/sidebar";
import type { NavItem } from "@/types";

const page = usePage();
const user = computed(() => {
  const authUser = page.props.auth.user as any;

  return authUser?.data ?? authUser;
});
const roleName = computed(() => user.value?.role?.name ?? user.value?.role_name);
const isSuperAdmin = computed(() => roleName.value === "super_admin");
const isApproved = computed(() => Boolean(user.value?.is_approved ?? (user.value?.is_active && roleName.value)));
const permissions = computed<string[]>(() => Array.isArray(user.value?.permissions) ? user.value.permissions : []);
const can = (permission: string) => isSuperAdmin.value || permissions.value.includes(permission);

const mainNavItems = computed<NavItem[]>(() => [
  {
    title: "Dashboard",
    href: "/dashboard",
    icon: LayoutGrid,
  },
  ...(isApproved.value
    ? [
        ...(can("customers.viewAny") ? [{
          title: "Customers",
          href: "/customers",
          icon: Users,
        }] : []),
        ...(can("products.viewAny") ? [{
          title: "Products",
          href: "/products",
          icon: Boxes,
        }] : []),
        ...(can("invoices.viewAny") ? [{
          title: "Invoices",
          href: "/invoices",
          icon: FileText,
        }] : []),
        ...(can("payments.viewAny") ? [{
          title: "Payments",
          href: "/payments",
          icon: CreditCard,
        }] : []),
        ...(can("reports.viewAny") || can("reports.view") ? [{
          title: "Reports",
          href: "/reports/sales",
          icon: BarChart3,
        }] : []),
      ]
    : []),
  ...(isApproved.value
    ? [
        ...(can("settings.view") ? [{
          title: "Settings",
          href: "/erp-settings",
          icon: Settings,
        }] : []),
        ...(can("users.viewAny") ? [{
          title: "Users",
          href: "/users",
          icon: UserCog,
        }] : []),
        ...(isSuperAdmin.value ? [{
          title: "Roles",
          href: "/roles",
          icon: ShieldCheck,
        }] : []),
      ]
    : []),
]);

const footerNavItems: NavItem[] = [
];
</script>

<template>
  <Sidebar collapsible="icon" variant="inset">
    <SidebarHeader>
      <SidebarMenu>
        <SidebarMenuItem>
          <SidebarMenuButton size="lg" as-child>
            <Link href="/dashboard">
              <AppLogo />
            </Link>
          </SidebarMenuButton>
        </SidebarMenuItem>
      </SidebarMenu>
    </SidebarHeader>

    <SidebarContent>
      <NavMain :items="mainNavItems" />
    </SidebarContent>

    <SidebarFooter>
      <NavFooter :items="footerNavItems" />
      <NavUser />
    </SidebarFooter>
  </Sidebar>
  <slot />
</template>
