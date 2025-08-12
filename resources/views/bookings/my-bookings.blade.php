@extends('layouts.blade_app')

@section('title', 'My Bookings')

@section('content')
<div class="container mt-5 pt-5">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">
                    <i class="bi bi-calendar-check me-2"></i>My Bookings
                </h2>
                <a href="/" class="btn btn-outline-primary">
                    <i class="bi bi-house me-1"></i>Browse Cars
                </a>
            </div>

            <div id="bookingsContainer">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadBookings();

    function loadBookings() {
        fetch('/my-bookings')
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('bookingsContainer');

                if (data.success && data.bookings.length > 0) {
                    container.innerHTML = data.bookings.map(booking => `
                        <div class="card mb-3 shadow-sm">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h5 class="card-title mb-2">
                                            ${booking.vehicle.make} ${booking.vehicle.model}
                                            <span class="badge ${getStatusBadgeClass(booking.status)} ms-2">${booking.status}</span>
                                        </h5>
                                        <div class="row g-2 mb-2">
                                            <div class="col-sm-6">
                                                <small class="text-muted">
                                                    <i class="bi bi-geo-alt me-1"></i>Emirate: ${booking.emirate}
                                                </small>
                                            </div>
                                            <div class="col-sm-6">
                                                <small class="text-muted">
                                                    <i class="bi bi-calendar-range me-1"></i>
                                                    ${formatDate(booking.start_date)} - ${formatDate(booking.end_date)}
                                                </small>
                                            </div>
                                        </div>
                                        <div class="row g-2">
                                            <div class="col-sm-6">
                                                <small class="text-muted">
                                                    <i class="bi bi-clock me-1"></i>Duration: ${booking.total_days} days
                                                </small>
                                            </div>
                                            <div class="col-sm-6">
                                                <small class="text-success fw-bold">
                                                    <i class="bi bi-currency-exchange me-1"></i>Total: AED ${numberFormat(booking.total_amount)}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-md-end">
                                        ${booking.status === 'pending' || booking.status === 'confirmed' ? `
                                            <button class="btn btn-outline-danger btn-sm" onclick="cancelBooking(${booking.id})">
                                                <i class="bi bi-x-circle me-1"></i>Cancel
                                            </button>
                                        ` : ''}
                                        <a href="/cars/${booking.vehicle.id}" class="btn btn-outline-primary btn-sm ms-1">
                                            <i class="bi bi-eye me-1"></i>View Car
                                        </a>
                                    </div>
                                </div>
                                ${booking.notes ? `
                                    <hr class="my-2">
                                    <small class="text-muted">
                                        <i class="bi bi-chat-text me-1"></i>Notes: ${booking.notes}
                                    </small>
                                ` : ''}
                            </div>
                        </div>
                    `).join('');
                } else {
                    container.innerHTML = `
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="bi bi-calendar-x" style="font-size: 4rem; color: #6c757d;"></i>
                            </div>
                            <h4>No Bookings Yet</h4>
                            <p class="text-muted">You haven't made any bookings yet. Browse our cars to make your first booking!</p>
                            <a href="/" class="btn btn-primary">
                                <i class="bi bi-car-front me-1"></i>Browse Cars
                            </a>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error loading bookings:', error);
                document.getElementById('bookingsContainer').innerHTML = `
                    <div class="alert alert-danger text-center">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Error loading bookings. Please refresh the page.
                    </div>
                `;
            });
    }

    // Cancel booking function
    window.cancelBooking = function(bookingId) {
        if (confirm('Are you sure you want to cancel this booking?')) {
            fetch(`/bookings/${bookingId}/cancel`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    loadBookings(); // Reload bookings
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error cancelling booking. Please try again.');
            });
        }
    }

    function getStatusBadgeClass(status) {
        const classes = {
            'pending': 'bg-warning text-dark',
            'confirmed': 'bg-success',
            'cancelled': 'bg-danger',
            'completed': 'bg-info'
        };
        return classes[status] || 'bg-secondary';
    }

    function formatDate(dateString) {
        return new Date(dateString).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    }

    function numberFormat(num) {
        return new Intl.NumberFormat().format(num);
    }
});
</script>
@endsection
