@extends('layouts.blade_app')

@section('title', 'Test Booking Payment Confirmation')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3>🧪 Test Booking Payment Confirmation</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <strong>Status:</strong>
                        <span id="status">Ready to test booking payment confirmation</span>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <h4>Test Local Booking Payment</h4>
                            <form id="localPaymentForm">
                                <div class="mb-3">
                                    <label for="bookingId" class="form-label">Booking ID</label>
                                    <input type="number" class="form-control" id="bookingId" name="bookingId" value="1" required>
                                </div>
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-check-circle"></i> Simulate Payment Success
                                </button>
                            </form>
                        </div>

                        <div class="col-md-6">
                            <h4>Test External Booking Update</h4>
                            <form id="externalUpdateForm">
                                <div class="mb-3">
                                    <label for="externalBookingId" class="form-label">External Booking ID</label>
                                    <input type="text" class="form-control" id="externalBookingId" name="externalBookingId" value="0198466e-b2cc-7153-a74a-a42b12b5cc52" required>
                                </div>
                                <div class="mb-3">
                                    <label for="status" class="form-label">New Status</label>
                                    <select class="form-control" id="status" name="status" required>
                                        <option value="confirmed" selected>Confirmed</option>
                                        <option value="cancelled">Cancelled</option>
                                        <option value="completed">Completed</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-warning">
                                    <i class="bi bi-arrow-clockwise"></i> Update External Status
                                </button>
                            </form>
                        </div>
                    </div>

                    <hr>

                    <div class="mt-4">
                        <h4>Test Log:</h4>
                        <div id="logContainer" style="max-height: 400px; overflow-y: auto; background: #f8f9fa; padding: 10px; border-radius: 5px;">
                            <div class="text-muted">No logs yet...</div>
                        </div>
                    </div>

                    <hr>

                    <div class="mt-4">
                        <h4>Current Bookings Status:</h4>
                        <div id="bookingsStatus" class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Vehicle</th>
                                        <th>User</th>
                                        <th>Status</th>
                                        <th>External ID</th>
                                        <th>Created</th>
                                    </tr>
                                </thead>
                                <tbody id="bookingsTableBody">
                                    <tr>
                                        <td colspan="6" class="text-center">Loading...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.log-entry {
    margin-bottom: 5px;
    padding: 5px;
    border-radius: 3px;
}

.log-success {
    background-color: #d4edda;
    color: #155724;
    border-left: 4px solid #28a745;
}

.log-error {
    background-color: #f8d7da;
    color: #721c24;
    border-left: 4px solid #dc3545;
}

.log-warning {
    background-color: #fff3cd;
    color: #856404;
    border-left: 4px solid #ffc107;
}

.log-info {
    background-color: #d1ecf1;
    color: #0c5460;
    border-left: 4px solid #17a2b8;
}

.status-pending {
    color: #856404;
    background-color: #fff3cd;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.8rem;
}

.status-confirmed {
    color: #155724;
    background-color: #d4edda;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.8rem;
}

.status-cancelled {
    color: #721c24;
    background-color: #f8d7da;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.8rem;
}
</style>

<script>
function addLog(message, type = 'info') {
    const logContainer = document.getElementById('logContainer');
    const timestamp = new Date().toLocaleTimeString();
    const logEntry = document.createElement('div');
    logEntry.className = `log-entry log-${type}`;
    logEntry.innerHTML = `<strong>[${timestamp}]</strong> ${message}`;

    // Remove "No logs yet..." message if it exists
    const noLogsMessage = logContainer.querySelector('.text-muted');
    if (noLogsMessage) {
        noLogsMessage.remove();
    }

    logContainer.appendChild(logEntry);
    logContainer.scrollTop = logContainer.scrollHeight;
}

function loadBookings() {
    fetch('/api/bookings/status')
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('bookingsTableBody');
            if (data.success && data.bookings.length > 0) {
                tbody.innerHTML = data.bookings.map(booking => `
                    <tr>
                        <td>${booking.id}</td>
                        <td>${booking.vehicle.make} ${booking.vehicle.model}</td>
                        <td>${booking.user.name}</td>
                        <td><span class="status-${booking.status.toLowerCase()}">${booking.status}</span></td>
                        <td>${booking.external_reservation_id || 'N/A'}</td>
                        <td>${new Date(booking.created_at).toLocaleDateString()}</td>
                    </tr>
                `).join('');
            } else {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center">No bookings found</td></tr>';
            }
        })
        .catch(error => {
            addLog('Error loading bookings: ' + error.message, 'error');
        });
}

document.addEventListener('DOMContentLoaded', function() {
    const status = document.getElementById('status');
    const localPaymentForm = document.getElementById('localPaymentForm');
    const externalUpdateForm = document.getElementById('externalUpdateForm');

    // Load initial bookings
    loadBookings();

    // Test local payment success
    localPaymentForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const bookingId = document.getElementById('bookingId').value;
        addLog(`Simulating payment success for booking ID: ${bookingId}`, 'info');

        fetch('/test-booking-payment-success', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify({
                booking_id: bookingId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                addLog('✅ Local booking payment simulated successfully', 'success');
                addLog(`Booking ${bookingId} status updated to: ${data.booking.status}`, 'success');
                if (data.external_update) {
                    addLog(`External booking updated: ${data.external_update.message}`, 'success');
                }
            } else {
                addLog('❌ Local booking payment simulation failed: ' + data.message, 'error');
            }
            loadBookings(); // Refresh table
        })
        .catch(error => {
            addLog('❌ Error: ' + error.message, 'error');
        });
    });

    // Test external booking status update
    externalUpdateForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const externalBookingId = document.getElementById('externalBookingId').value;
        const newStatus = document.getElementById('status').value;

        addLog(`Updating external booking ${externalBookingId} to status: ${newStatus}`, 'info');

        fetch('/test-external-booking-update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify({
                external_booking_id: externalBookingId,
                status: newStatus
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                addLog('✅ External booking status updated successfully', 'success');
                addLog(`External booking ${externalBookingId} is now: ${newStatus}`, 'success');
            } else {
                addLog('❌ External booking status update failed: ' + data.message, 'error');
            }
        })
        .catch(error => {
            addLog('❌ Error: ' + error.message, 'error');
        });
    });
});
</script>
@endsection
