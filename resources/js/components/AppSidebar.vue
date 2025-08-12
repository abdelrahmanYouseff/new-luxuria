<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid, CalendarCheck, FileText, Ticket, Car, Star } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const page = usePage();
const userRole = page.props.userRole as string | null;
const user = page.props.auth?.user;

let mainNavItems: NavItem[] = [];

if (userRole === 'admin') {
    mainNavItems = [
        {
            title: 'Dashboard',
            href: '/dashboard',
            icon: LayoutGrid,
        },
        {
            title: 'Vehicles',
            href: '/vehicles',
            icon: Car,
        },
        {
            title: 'Invoices',
            href: '/view-invoices',
            icon: FileText,
        },
    ];
} else {
    mainNavItems = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
    },
        {
            title: 'Reservations',
            href: '/reservations',
            icon: CalendarCheck,
        },
        {
            title: 'Invoices',
            href: '/view-invoices',
            icon: FileText,
        },
        {
            title: 'Coupons',
            href: '/coupons',
            icon: Ticket,
        },
        {
            title: 'My Points',
            href: '/my-points',
            icon: Star,
        },
];
}

const footerNavItems: NavItem[] = [
    // Removed Github Repo and Documentation
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                            <AppLogo :userName="user?.name" />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
