<template>
    <Head title="View All Invoices" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-900 rounded-xl shadow p-5 flex flex-col items-center justify-center border border-sidebar-border/70">
                    <div class="text-lg font-semibold mb-1 text-blue-600">Total Invoices</div>
                    <div class="text-3xl font-extrabold text-gray-900 dark:text-white">{{ stats.total_invoices }}</div>
                </div>
                <div class="bg-white dark:bg-gray-900 rounded-xl shadow p-5 flex flex-col items-center justify-center border border-sidebar-border/70">
                    <div class="text-lg font-semibold mb-1 text-green-600">Total Amount</div>
                    <div class="text-2xl font-extrabold text-gray-900 dark:text-white">${{ formatAmount(stats.total_amount) }}</div>
                </div>
                <div class="bg-white dark:bg-gray-900 rounded-xl shadow p-5 flex flex-col items-center justify-center border border-sidebar-border/70">
                    <div class="text-lg font-semibold mb-1 text-green-500">Completed</div>
                    <div class="text-3xl font-extrabold text-green-500">{{ stats.completed_invoices }}</div>
                </div>
                <div class="bg-white dark:bg-gray-900 rounded-xl shadow p-5 flex flex-col items-center justify-center border border-sidebar-border/70">
                    <div class="text-lg font-semibold mb-1 text-yellow-500">Pending</div>
                    <div class="text-3xl font-extrabold text-yellow-500">{{ stats.pending_invoices }}</div>
                </div>
                <div class="bg-white dark:bg-gray-900 rounded-xl shadow p-5 flex flex-col items-center justify-center border border-sidebar-border/70">
                    <div class="text-lg font-semibold mb-1 text-red-500">Failed</div>
                    <div class="text-3xl font-extrabold text-red-500">{{ stats.failed_invoices }}</div>
                </div>
            </div>

            <!-- Invoices Table -->
            <div class="bg-white dark:bg-gray-900 rounded-xl shadow border border-sidebar-border/70 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">All Invoices</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Invoice
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Customer
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Coupon
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Amount
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Payment Method
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Date
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="invoice in invoices.data" :key="invoice.id" class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ invoice.invoice_number }}
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        ID: {{ invoice.id }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ invoice.customer_name || invoice.user?.name || 'N/A' }}
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ invoice.customer_email || invoice.user?.email || 'N/A' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ invoice.coupon_name || 'N/A' }}
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ invoice.coupon_code || 'N/A' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        ${{ formatAmount(invoice.amount) }}
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ invoice.currency || 'USD' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="getStatusBadgeClass(invoice.payment_status)" class="inline-flex px-2 py-1 text-xs font-medium rounded-full">
                                        {{ formatStatus(invoice.payment_status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ invoice.payment_method || 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        {{ formatDate(invoice.created_at) }}
                                    </div>
                                    <div v-if="invoice.paid_at" class="text-sm text-gray-500 dark:text-gray-400">
                                        Paid: {{ formatDate(invoice.paid_at) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <Button
                                            variant="outline"
                                            size="sm"
                                            @click="viewInvoicePdf(invoice)"
                                            class="text-blue-600 hover:text-blue-900"
                                        >
                                            <Eye class="w-4 h-4 mr-1" />
                                            View PDF
                                        </Button>
                                        <Button
                                            variant="outline"
                                            size="sm"
                                            @click="downloadInvoice(invoice)"
                                            class="text-green-600 hover:text-green-900"
                                        >
                                            <Download class="w-4 h-4 mr-1" />
                                            Download
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="invoices.links && invoices.links.length > 3" class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700 dark:text-gray-300">
                            Showing {{ invoices.from || 0 }} to {{ invoices.to || 0 }} of {{ invoices.total || 0 }} results
                        </div>
                        <div class="flex space-x-1">
                            <template v-for="(link, index) in invoices.links" :key="index">
                                <Button
                                    v-if="link.url"
                                    :variant="link.active ? 'default' : 'outline'"
                                    size="sm"
                                    @click="navigateToPage(link.url)"
                                    :disabled="!link.url"
                                    v-html="link.label"
                                    class="min-w-[40px]"
                                />
                                <span v-else class="px-3 py-2 text-sm text-gray-500" v-html="link.label" />
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Invoice Details Modal -->
        <Dialog :open="showInvoiceModal" @update:open="showInvoiceModal = $event">
            <DialogContent class="max-w-2xl">
                <DialogHeader>
                    <DialogTitle>Invoice Details</DialogTitle>
                    <DialogDescription>
                        Invoice #{{ selectedInvoice?.invoice_number }}
                    </DialogDescription>
                </DialogHeader>

                <div v-if="selectedInvoice" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-600">Customer</label>
                            <p class="text-sm text-gray-900">{{ selectedInvoice.customer_name || selectedInvoice.user?.name }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600">Email</label>
                            <p class="text-sm text-gray-900">{{ selectedInvoice.customer_email || selectedInvoice.user?.email }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600">Coupon</label>
                            <p class="text-sm text-gray-900">{{ selectedInvoice.coupon_name }} ({{ selectedInvoice.coupon_code }})</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600">Amount</label>
                            <p class="text-sm text-gray-900">${{ formatAmount(selectedInvoice.amount) }} {{ selectedInvoice.currency }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600">Status</label>
                            <p>
                                <span :class="getStatusBadgeClass(selectedInvoice.payment_status)" class="inline-flex px-2 py-1 text-xs font-medium rounded-full">
                                    {{ formatStatus(selectedInvoice.payment_status) }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600">Payment Method</label>
                            <p class="text-sm text-gray-900">{{ selectedInvoice.payment_method || 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600">Created</label>
                            <p class="text-sm text-gray-900">{{ formatDate(selectedInvoice.created_at) }}</p>
                        </div>
                        <div v-if="selectedInvoice.paid_at">
                            <label class="text-sm font-medium text-gray-600">Paid At</label>
                            <p class="text-sm text-gray-900">{{ formatDate(selectedInvoice.paid_at) }}</p>
                        </div>
                    </div>

                    <div v-if="selectedInvoice.payment_details">
                        <label class="text-sm font-medium text-gray-600">Payment Details</label>
                        <pre class="mt-1 p-3 bg-gray-100 rounded text-xs text-gray-800 overflow-auto">{{ JSON.stringify(selectedInvoice.payment_details, null, 2) }}</pre>
                    </div>
                </div>

                <DialogFooter>
                    <Button variant="outline" @click="showInvoiceModal = false">Close</Button>
                    <Button variant="outline" @click="selectedInvoice && viewInvoicePdf(selectedInvoice)" class="mr-2">
                        <Eye class="w-4 h-4 mr-2" />
                        View PDF
                    </Button>
                    <Button @click="selectedInvoice && downloadInvoice(selectedInvoice)">
                        <Download class="w-4 h-4 mr-2" />
                        Download PDF
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { Eye, Download } from 'lucide-vue-next';
import { ref } from 'vue';

interface Invoice {
    id: number;
    invoice_number: string;
    customer_name?: string;
    customer_email?: string;
    coupon_name?: string;
    coupon_code?: string;
    amount: number;
    currency: string;
    payment_status: string;
    payment_method?: string;
    payment_details?: any;
    created_at: string;
    paid_at?: string;
    user?: {
        name: string;
        email: string;
    };
}

interface Stats {
    total_invoices: number;
    total_amount: number;
    completed_invoices: number;
    pending_invoices: number;
    failed_invoices: number;
}

interface PaginatedInvoices {
    data: Invoice[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from?: number;
    to?: number;
    links?: Array<{
        url?: string;
        label: string;
        active: boolean;
    }>;
}

const props = defineProps<{
    invoices: PaginatedInvoices;
    stats: Stats;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'View Invoices',
        href: '/view-invoices',
    },
];

// Modal state
const showInvoiceModal = ref(false);
const selectedInvoice = ref<Invoice | null>(null);

// Methods
const formatAmount = (amount: number | string): string => {
    const num = typeof amount === 'string' ? parseFloat(amount) : amount;
    return new Intl.NumberFormat('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(num || 0);
};

const formatDate = (dateString: string): string => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const formatStatus = (status: string): string => {
    return status.charAt(0).toUpperCase() + status.slice(1);
};

const getStatusBadgeClass = (status: string): string => {
    const classes = {
        completed: 'bg-green-100 text-green-800',
        pending: 'bg-yellow-100 text-yellow-800',
        failed: 'bg-red-100 text-red-800',
        refunded: 'bg-blue-100 text-blue-800',
    };
    return classes[status as keyof typeof classes] || 'bg-gray-100 text-gray-800';
};

const viewInvoiceDetails = (invoice: Invoice) => {
    selectedInvoice.value = invoice;
    showInvoiceModal.value = true;
};

const downloadInvoice = (invoice: Invoice) => {
    // Download PDF invoice
    window.open(`/invoice-coupons/${invoice.id}/download`, '_blank');
};

const viewInvoicePdf = (invoice: Invoice) => {
    // View PDF invoice in browser
    window.open(`/invoice-coupons/${invoice.id}/pdf`, '_blank');
};

const navigateToPage = (url: string) => {
    router.visit(url, {
        preserveState: true,
        preserveScroll: true,
    });
};
</script>
