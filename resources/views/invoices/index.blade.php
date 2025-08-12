@extends('layouts.app')

@section('title', 'Invoices')

@section('content')
<div class="flex h-screen bg-gray-50">
    <!-- Sidebar -->
    <div class="w-64 bg-white shadow-lg">
        <!-- Sidebar Header -->
        <div class="flex items-center justify-between p-4 border-b">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-black rounded flex items-center justify-center">
                    <span class="text-white font-bold text-sm">LB</span>
                </div>
                <span class="font-semibold text-gray-900">abdelrahman</span>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="mt-6">
            <div class="px-4 mb-2">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Platform</h3>
            </div>

            <div class="space-y-1">
                <a href="/dashboard" class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 hover:text-gray-900">
                    <svg class="mr-3 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                    Dashboard
                </a>

                <a href="/reservations" class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 hover:text-gray-900">
                    <svg class="mr-3 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Reservations
                </a>

                <a href="/invoice-coupons" class="flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-gray-100">
                    <svg class="mr-3 h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Invoices
                </a>

                <a href="/coupons" class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 hover:text-gray-900">
                    <svg class="mr-3 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                    </svg>
                    Coupons
                </a>

                <a href="/my-points" class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 hover:text-gray-900">
                    <svg class="mr-3 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                    </svg>
                    My Points
                </a>
            </div>
        </nav>

        <!-- User Account Section -->
        <div class="absolute bottom-0 w-64 p-4 border-t bg-white">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-gray-200 rounded flex items-center justify-center">
                    <span class="text-gray-600 font-semibold text-sm">A</span>
                </div>
                <span class="text-sm font-medium text-gray-900">abdelrahman</span>
                <svg class="ml-auto h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b">
            <div class="flex items-center justify-between px-6 py-4">
                <div class="flex items-center space-x-4">
                    <h1 class="text-2xl font-semibold text-gray-900">Invoices</h1>
                    <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 overflow-y-auto bg-gray-50 p-6">
            <div class="max-w-7xl mx-auto">
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8" id="statsCards" style="display: none;">
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-blue-500 rounded flex items-center justify-center">
                                        <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Total Invoices</dt>
                                        <dd class="text-lg font-medium text-gray-900" id="totalInvoices">-</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-green-500 rounded flex items-center justify-center">
                                        <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Total Amount</dt>
                                        <dd class="text-lg font-medium text-gray-900" id="totalAmount">-</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-yellow-500 rounded flex items-center justify-center">
                                        <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Completed</dt>
                                        <dd class="text-lg font-medium text-gray-900" id="completedInvoices">-</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Invoices Table -->
                <div class="bg-white shadow overflow-hidden sm:rounded-lg" id="invoicesTable" style="display: none;">
                    <div class="px-4 py-5 sm:px-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Recent Invoices</h3>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">Your latest coupon purchase invoices.</p>
                    </div>
                    <div class="border-t border-gray-200">
                        <ul class="divide-y divide-gray-200" id="invoicesList">
                            <!-- Invoices will be loaded here -->
                        </ul>
                    </div>
                </div>

                <!-- Loading State -->
                <div id="loadingState" class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Loading invoices...</h3>
                </div>

                <!-- Empty State -->
                <div id="emptyState" class="text-center py-12" style="display: none;">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No invoices</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by purchasing a coupon.</p>
                    <div class="mt-6">
                        <a href="/coupons" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                            </svg>
                            Browse Coupons
                        </a>
                    </div>
                </div>

                <!-- Placeholder Content -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8" id="placeholderContent" style="display: none;">
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="animate-pulse">
                                <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                                <div class="h-8 bg-gray-200 rounded w-1/2"></div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="animate-pulse">
                                <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                                <div class="h-8 bg-gray-200 rounded w-1/2"></div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="animate-pulse">
                                <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                                <div class="h-8 bg-gray-200 rounded w-1/2"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Large Placeholder -->
                <div class="bg-white overflow-hidden shadow rounded-lg mt-8" id="largePlaceholder" style="display: none;">
                    <div class="p-6">
                        <div class="animate-pulse">
                            <div class="h-4 bg-gray-200 rounded w-1/4 mb-4"></div>
                            <div class="space-y-3">
                                <div class="h-4 bg-gray-200 rounded"></div>
                                <div class="h-4 bg-gray-200 rounded w-5/6"></div>
                                <div class="h-4 bg-gray-200 rounded w-4/6"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadInvoices();

    function loadInvoices() {
        const loadingState = document.getElementById('loadingState');
        const emptyState = document.getElementById('emptyState');
        const statsCards = document.getElementById('statsCards');
        const invoicesTable = document.getElementById('invoicesTable');
        const placeholderContent = document.getElementById('placeholderContent');
        const largePlaceholder = document.getElementById('largePlaceholder');

        // Show loading state
        loadingState.style.display = 'block';
        emptyState.style.display = 'none';
        statsCards.style.display = 'none';
        invoicesTable.style.display = 'none';
        placeholderContent.style.display = 'none';
        largePlaceholder.style.display = 'none';

        fetch('/invoice-coupons/api/user-invoices', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            loadingState.style.display = 'none';

            if (data.success) {
                if (data.invoices.length === 0) {
                    emptyState.style.display = 'block';
                    placeholderContent.style.display = 'grid';
                    largePlaceholder.style.display = 'block';
                } else {
                    displayInvoices(data.invoices);
                    updateStats(data.stats);
                    statsCards.style.display = 'grid';
                    invoicesTable.style.display = 'block';
                }
            } else {
                showError('Failed to load invoices: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error loading invoices:', error);
            loadingState.style.display = 'none';
            showError('Network error occurred while loading invoices');
        });
    }

    function displayInvoices(invoices) {
        const invoicesList = document.getElementById('invoicesList');

        invoicesList.innerHTML = invoices.map(invoice => `
            <li>
                <div class="px-4 py-4 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                    <svg class="h-6 w-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="flex items-center">
                                    <p class="text-sm font-medium text-gray-900">${invoice.invoice_number}</p>
                                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getStatusBadgeClass(invoice.payment_status)}">
                                        ${invoice.payment_status.toUpperCase()}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-500">${invoice.coupon_name}</p>
                                <p class="text-sm text-gray-500">Created: ${new Date(invoice.created_at).toLocaleDateString()}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900">${parseFloat(invoice.amount).toFixed(2)} ${invoice.currency}</p>
                                ${invoice.paid_at ? `<p class="text-xs text-gray-500">Paid: ${new Date(invoice.paid_at).toLocaleDateString()}</p>` : ''}
                            </div>
                            <div class="flex space-x-2">
                                <button onclick="viewInvoice(${invoice.id})" class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                    View PDF
                                </button>
                                <button onclick="downloadInvoice(${invoice.id})" class="text-gray-600 hover:text-gray-900 text-sm font-medium">
                                    Download
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        `).join('');
    }

    function updateStats(stats) {
        document.getElementById('totalInvoices').textContent = stats.total_invoices;
        document.getElementById('totalAmount').textContent = parseFloat(stats.total_amount).toFixed(2) + ' AED';
        document.getElementById('completedInvoices').textContent = stats.completed_invoices;
    }

    function getStatusBadgeClass(status) {
        switch(status) {
            case 'completed': return 'bg-green-100 text-green-800';
            case 'pending': return 'bg-yellow-100 text-yellow-800';
            case 'failed': return 'bg-red-100 text-red-800';
            case 'refunded': return 'bg-blue-100 text-blue-800';
            default: return 'bg-gray-100 text-gray-800';
        }
    }

    function showError(message) {
        const emptyState = document.getElementById('emptyState');
        emptyState.innerHTML = `
            <svg class="mx-auto h-12 w-12 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Error</h3>
            <p class="mt-1 text-sm text-gray-500">${message}</p>
            <div class="mt-6">
                <button onclick="loadInvoices()" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Retry
                </button>
            </div>
        `;
        emptyState.style.display = 'block';
    }
});

function viewInvoice(invoiceId) {
    window.open(`/invoice-coupons/${invoiceId}/pdf`, '_blank');
}

function downloadInvoice(invoiceId) {
    window.open(`/invoice-coupons/${invoiceId}/download`, '_blank');
}
</script>

<style>
/* Custom styles for better appearance */
body {
    margin: 0;
    padding: 0;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

/* Ensure the sidebar takes full height */
.flex.h-screen {
    height: 100vh;
}

/* Make sure the user account section stays at bottom */
.absolute.bottom-0 {
    position: absolute;
    bottom: 0;
}

/* Smooth transitions */
.transition-all {
    transition: all 0.2s ease-in-out;
}

/* Hover effects */
.hover\:bg-gray-100:hover {
    background-color: #f3f4f6;
}

.hover\:text-gray-900:hover {
    color: #111827;
}

.hover\:bg-blue-700:hover {
    background-color: #1d4ed8;
}

/* Animation for placeholders */
@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: .5;
    }
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
@endsection
