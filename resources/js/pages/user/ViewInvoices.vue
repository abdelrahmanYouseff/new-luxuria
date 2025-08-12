<template>
    <Head title="My Invoices" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6">
            <!-- Page Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-foreground">My Invoices</h1>
                    <p class="text-muted-foreground">View and manage your purchase invoices</p>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
                <!-- Total Invoices -->
                <Card>
                    <CardContent class="p-4">
                        <div class="flex items-center space-x-2">
                            <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
                                <FileText class="h-4 w-4 text-blue-600 dark:text-blue-300" />
                            </div>
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Total Invoices</p>
                                <p class="text-xl font-bold">{{ stats.total_invoices }}</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Total Amount -->
                <Card>
                    <CardContent class="p-4">
                        <div class="flex items-center space-x-2">
                            <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg">
                                <DollarSign class="h-4 w-4 text-green-600 dark:text-green-300" />
                            </div>
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Total Spent</p>
                                <p class="text-xl font-bold">{{ formatAmount(stats.total_amount) }} AED</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Completed -->
                <Card>
                    <CardContent class="p-4">
                        <div class="flex items-center space-x-2">
                            <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg">
                                <CheckCircle class="h-4 w-4 text-green-600 dark:text-green-300" />
                            </div>
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Completed</p>
                                <p class="text-xl font-bold">{{ stats.completed_invoices }}</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Pending -->
                <Card>
                    <CardContent class="p-4">
                        <div class="flex items-center space-x-2">
                            <div class="p-2 bg-yellow-100 dark:bg-yellow-900 rounded-lg">
                                <Clock class="h-4 w-4 text-yellow-600 dark:text-yellow-300" />
                            </div>
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Pending</p>
                                <p class="text-xl font-bold">{{ stats.pending_invoices }}</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Failed -->
                <Card>
                    <CardContent class="p-4">
                        <div class="flex items-center space-x-2">
                            <div class="p-2 bg-red-100 dark:bg-red-900 rounded-lg">
                                <XCircle class="h-4 w-4 text-red-600 dark:text-red-300" />
                            </div>
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Failed</p>
                                <p class="text-xl font-bold">{{ stats.failed_invoices }}</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Invoices Table -->
            <div class="bg-white dark:bg-gray-900 rounded-xl shadow border border-sidebar-border/70 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Invoice History</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Track all your purchases and payments</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Invoice
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Coupon
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Amount
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Date
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="invoice in invoices.data" :key="invoice.id" class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ invoice.invoice_number }}
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        ID: {{ invoice.id }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        {{ invoice.coupon_name }}
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400" v-if="invoice.coupon_code">
                                        Code: {{ invoice.coupon_code }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ formatAmount(invoice.amount) }} {{ invoice.currency }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="getStatusBadgeClass(invoice.payment_status)" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full">
                                        {{ formatStatus(invoice.payment_status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ formatDate(invoice.created_at) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                    <Button
                                        variant="outline"
                                        size="sm"
                                        @click="viewInvoicePdf(invoice)"
                                        class="text-blue-600 hover:text-blue-800"
                                    >
                                        <Eye class="h-4 w-4 mr-1" />
                                        View PDF
                                    </Button>

                                    <Button
                                        variant="outline"
                                        size="sm"
                                        @click="downloadInvoice(invoice)"
                                        class="text-green-600 hover:text-green-800"
                                        v-if="invoice.payment_status === 'completed'"
                                    >
                                        <Download class="h-4 w-4 mr-1" />
                                        Download
                                    </Button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="invoices.links && invoices.links.length > 3" class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            Showing {{ invoices.from }} to {{ invoices.to }} of {{ invoices.total }} results
                        </div>
                        <div class="flex space-x-1">
                            <Button
                                v-for="link in invoices.links"
                                :key="link.label"
                                variant="outline"
                                size="sm"
                                @click="navigateToPage(link.url)"
                                :disabled="!link.url"
                                :class="{
                                    'bg-primary text-primary-foreground': link.active,
                                    'opacity-50 cursor-not-allowed': !link.url
                                }"
                                v-html="link.label"
                            />
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
                    <DialogDescription>Invoice #{{ selectedInvoice?.invoice_number }}</DialogDescription>
                </DialogHeader>

                <div v-if="selectedInvoice" class="space-y-6">
                    <!-- Basic Info -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Invoice Number</label>
                            <p class="text-sm font-semibold">{{ selectedInvoice.invoice_number }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Date</label>
                            <p class="text-sm">{{ formatDate(selectedInvoice.created_at) }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Status</label>
                            <span :class="getStatusBadgeClass(selectedInvoice.payment_status)" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full">
                                {{ formatStatus(selectedInvoice.payment_status) }}
                            </span>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Amount</label>
                            <p class="text-sm font-semibold">{{ formatAmount(selectedInvoice.amount) }} {{ selectedInvoice.currency }}</p>
                        </div>
                    </div>

                    <!-- Coupon Details -->
                    <div class="border-t pt-4">
                        <h4 class="font-medium mb-2">Coupon Details</h4>
                        <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded-lg">
                            <p class="font-medium">{{ selectedInvoice.coupon_name }}</p>
                            <p v-if="selectedInvoice.coupon_code" class="text-sm text-gray-600 dark:text-gray-400">
                                Code: {{ selectedInvoice.coupon_code }}
                            </p>
                        </div>
                    </div>

                    <!-- Payment Details -->
                    <div class="border-t pt-4" v-if="selectedInvoice.payment_method">
                        <h4 class="font-medium mb-2">Payment Details</h4>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <label class="text-gray-600 dark:text-gray-400">Payment Method</label>
                                <p class="capitalize">{{ selectedInvoice.payment_method }}</p>
                            </div>
                            <div v-if="selectedInvoice.paid_at">
                                <label class="text-gray-600 dark:text-gray-400">Paid At</label>
                                <p>{{ formatDate(selectedInvoice.paid_at) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <DialogFooter>
                    <Button variant="outline" @click="showInvoiceModal = false">Close</Button>
                    <Button variant="outline" @click="selectedInvoice && viewInvoicePdf(selectedInvoice)" class="mr-2" v-if="selectedInvoice?.payment_status === 'completed'">
                        <Eye class="h-4 w-4 mr-2" />
                        View PDF
                    </Button>
                    <Button @click="selectedInvoice && downloadInvoice(selectedInvoice)" v-if="selectedInvoice?.payment_status === 'completed'">
                        <Download class="h-4 w-4 mr-2" />
                        Download PDF
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>

<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { Button } from '@/components/ui/button'
import { Card, CardContent } from '@/components/ui/card'
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog'
import {
    FileText,
    DollarSign,
    CheckCircle,
    Clock,
    XCircle,
    Eye,
    Download
} from 'lucide-vue-next'

// Props
interface Invoice {
    id: number
    invoice_number: string
    coupon_name: string
    coupon_code?: string
    amount: number
    currency: string
    payment_status: string
    payment_method?: string
    created_at: string
    paid_at?: string
}

interface Stats {
    total_invoices: number
    total_amount: number
    completed_invoices: number
    pending_invoices: number
    failed_invoices: number
}

interface Props {
    invoices: {
        data: Invoice[]
        links: any[]
        from: number
        to: number
        total: number
    }
    stats: Stats
    user: any
}

const props = defineProps<Props>()

// Reactive state
const showInvoiceModal = ref(false)
const selectedInvoice = ref<Invoice | null>(null)

// Breadcrumbs
const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'My Invoices', href: '/view-invoices', current: true }
]

// Methods
const formatAmount = (amount: number): string => {
    return new Intl.NumberFormat('en-AE', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(amount)
}

const formatDate = (date: string): string => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}

const formatStatus = (status: string): string => {
    const statusMap: Record<string, string> = {
        'completed': 'Completed',
        'pending': 'Pending',
        'failed': 'Failed',
        'cancelled': 'Cancelled'
    }
    return statusMap[status] || status
}

const getStatusBadgeClass = (status: string): string => {
    const classMap: Record<string, string> = {
        'completed': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
        'pending': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
        'failed': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
        'cancelled': 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300'
    }
    return classMap[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300'
}

const viewInvoiceDetails = (invoice: Invoice) => {
    selectedInvoice.value = invoice
    showInvoiceModal.value = true
}

const downloadInvoice = (invoice: Invoice) => {
    // Download PDF invoice
    window.open(`/invoice-coupons/${invoice.id}/download`, '_blank')
}

const viewInvoicePdf = (invoice: Invoice) => {
    // View PDF invoice in browser
    window.open(`/invoice-coupons/${invoice.id}/pdf`, '_blank')
}

const navigateToPage = (url: string | null) => {
    if (url) {
        router.visit(url)
    }
}
</script>
