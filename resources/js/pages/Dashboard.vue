<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

const page = usePage();
const reservationsCount = page.props.reservationsCount as number;
const invoicesCount = page.props.invoicesCount as number;
const couponsCount = page.props.couponsCount as number;
const vehicalsCount = page.props.vehicalsCount as number;
const userRole = page.props.userRole as string | null;
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <!-- Admin Stats Cards -->
            <div v-if="userRole === 'admin'" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-900 rounded-xl shadow p-5 flex flex-col items-center justify-center border border-sidebar-border/70">
                    <div class="text-2xl font-bold mb-1">Total Invoices</div>
                    <div class="text-4xl font-extrabold text-primary">{{ invoicesCount }}</div>
                </div>
                <div class="bg-white dark:bg-gray-900 rounded-xl shadow p-5 flex flex-col items-center justify-center border border-sidebar-border/70">
                    <div class="text-2xl font-bold mb-1">Total Reservations</div>
                    <div class="text-4xl font-extrabold text-primary">{{ reservationsCount }}</div>
                </div>
                <div class="bg-white dark:bg-gray-900 rounded-xl shadow p-5 flex flex-col items-center justify-center border border-sidebar-border/70">
                    <div class="text-2xl font-bold mb-1">Total Vehicals</div>
                    <div class="text-4xl font-extrabold text-primary">{{ vehicalsCount }}</div>
                </div>
                <div class="bg-white dark:bg-gray-900 rounded-xl shadow p-5 flex flex-col items-center justify-center border border-sidebar-border/70">
                    <div class="text-2xl font-bold mb-1">Total Coupons</div>
                    <div class="text-4xl font-extrabold text-primary">{{ couponsCount }}</div>
                </div>
            </div>
            <!-- End Admin Stats Cards -->
            <!-- Stats Cards (user only) -->
            <div v-if="userRole === 'user'" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-900 rounded-xl shadow p-5 flex flex-col items-center justify-center border border-sidebar-border/70">
                    <div class="text-3xl mb-2">📅</div>
                    <div class="text-2xl font-bold">{{ reservationsCount }}</div>
                    <div class="text-gray-500 mt-1">Reservations</div>
                </div>
                <div class="bg-white dark:bg-gray-900 rounded-xl shadow p-5 flex flex-col items-center justify-center border border-sidebar-border/70">
                    <div class="text-3xl mb-2">🧾</div>
                    <div class="text-2xl font-bold">{{ invoicesCount }}</div>
                    <div class="text-gray-500 mt-1">Invoices</div>
                </div>
                <div class="bg-white dark:bg-gray-900 rounded-xl shadow p-5 flex flex-col items-center justify-center border border-sidebar-border/70">
                    <div class="text-3xl mb-2">🎟️</div>
                    <div class="text-2xl font-bold">{{ couponsCount }}</div>
                    <div class="text-gray-500 mt-1">Coupons</div>
                </div>
            </div>
            <!-- End Stats Cards -->
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                </div>
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                </div>
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                </div>
            </div>
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                <PlaceholderPattern />
            </div>
        </div>
    </AppLayout>
</template>
