@extends('layouts.blade_app')

@section('title', 'My Invoices')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3><i class="bi bi-receipt"></i> My Coupon Invoices</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h5>Total Invoices</h5>
                                    <h3 id="totalInvoices">-</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h5>Total Amount</h5>
                                    <h3 id="totalAmount">-</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h5>Completed</h5>
                                    <h3 id="completedInvoices">-</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body text-center">
                                    <h5>Failed</h5>
                                    <h3 id="failedInvoices">-</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Coupon</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="invoicesTable">
                                <tr>
                                    <td colspan="6" class="text-center">
                                        <div class="spinner-border" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadInvoices();

    function loadInvoices() {
        fetch('/stripe/user-invoices', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayInvoices(data.invoices);
                updateStats(data.invoices);
            } else {
                showError('Failed to load invoices');
            }
        })
        .catch(error => {
            console.error('Error loading invoices:', error);
            showError('Network error occurred');
        });
    }

    function displayInvoices(invoices) {
        const tbody = document.getElementById('invoicesTable');

        if (invoices.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center text-muted">
                        <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                        <p class="mt-2">No invoices found</p>
                        <a href="/coupons" class="btn btn-primary btn-sm">
                            <i class="bi bi-cart-plus"></i> Buy Coupons
                        </a>
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = invoices.map(invoice => `
            <tr>
                <td>
                    <strong>${invoice.invoice_number}</strong>
                </td>
                <td>
                    <div>
                        <strong>${invoice.coupon_name}</strong>
                        ${invoice.coupon_code ? `<br><small class="text-muted">Code: ${invoice.coupon_code}</small>` : ''}
                    </div>
                </td>
                <td>
                    <strong>${parseFloat(invoice.amount).toFixed(2)} ${invoice.currency}</strong>
                </td>
                <td>
                    <span class="badge ${getStatusBadgeClass(invoice.payment_status)}">
                        ${invoice.payment_status.toUpperCase()}
                    </span>
                </td>
                <td>
                    <div>
                        <small class="text-muted">Created</small><br>
                        ${new Date(invoice.created_at).toLocaleDateString()}
                        ${invoice.paid_at ? `
                            <br><small class="text-muted">Paid</small><br>
                            ${new Date(invoice.paid_at).toLocaleDateString()}
                        ` : ''}
                    </div>
                </td>
                <td>
                    <button class="btn btn-sm btn-outline-primary" onclick="viewInvoiceDetails('${invoice.id}')">
                        <i class="bi bi-eye"></i> View
                    </button>
                </td>
            </tr>
        `).join('');
    }

    function updateStats(invoices) {
        const totalInvoices = invoices.length;
        const totalAmount = invoices.reduce((sum, invoice) => sum + parseFloat(invoice.amount), 0);
        const completedInvoices = invoices.filter(invoice => invoice.payment_status === 'completed').length;
        const failedInvoices = invoices.filter(invoice => invoice.payment_status === 'failed').length;

        document.getElementById('totalInvoices').textContent = totalInvoices;
        document.getElementById('totalAmount').textContent = totalAmount.toFixed(2) + ' AED';
        document.getElementById('completedInvoices').textContent = completedInvoices;
        document.getElementById('failedInvoices').textContent = failedInvoices;
    }

    function getStatusBadgeClass(status) {
        switch(status) {
            case 'completed': return 'bg-success';
            case 'pending': return 'bg-warning';
            case 'failed': return 'bg-danger';
            case 'refunded': return 'bg-info';
            default: return 'bg-secondary';
        }
    }

    function showError(message) {
        const tbody = document.getElementById('invoicesTable');
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center text-danger">
                    <i class="bi bi-exclamation-triangle"></i>
                    <p class="mt-2">${message}</p>
                    <button class="btn btn-primary btn-sm" onclick="loadInvoices()">
                        <i class="bi bi-arrow-clockwise"></i> Retry
                    </button>
                </td>
            </tr>
        `;
    }
});

function viewInvoiceDetails(invoiceId) {
    // You can implement a modal or redirect to a detailed view
    alert('Invoice details feature coming soon!');
}
</script>

<style>
.card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    border-radius: 10px;
}

.table th {
    background-color: #f8f9fa;
    border-top: none;
    font-weight: 600;
}

.badge {
    font-size: 0.8rem;
    padding: 0.5rem 0.8rem;
}
</style>
@endsection
