@extends('layouts.blade_app')

@section('title', 'Test Booking Form')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3>Test Booking Form Submission</h3>
                </div>
                <div class="card-body">
                    <form id="testForm" action="{{ route('booking.confirm') }}" method="POST">
                        @csrf
                        <input type="hidden" name="booking_data" value='{"vehicle_id":57,"emirate":"Dubai","start_date":"2025-07-27","end_date":"2025-08-07","daily_rate":599.00,"pricing_type":"weekly","applied_rate":499.85714285714,"total_days":12,"total_amount":5998.29,"notes":""}'>
                        <input type="hidden" name="payment_method" value="stripe">

                        <button type="submit" class="btn btn-primary">Test Submit to Booking Confirm</button>
                    </form>

                    <hr>

                    <button type="button" id="debugAuth" class="btn btn-secondary">Debug Authentication</button>
                    <button type="button" id="testStripe" class="btn btn-info">Test Stripe Session</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const testForm = document.getElementById('testForm');
    const debugAuth = document.getElementById('debugAuth');
    const testStripe = document.getElementById('testStripe');

    testForm.addEventListener('submit', function(e) {
        console.log('Test form submitted');
        console.log('Form action:', this.action);
        console.log('Form method:', this.method);

        const formData = new FormData(this);
        for (let [key, value] of formData.entries()) {
            console.log(key + ':', value);
        }
    });

    debugAuth.addEventListener('click', function() {
        fetch('/debug-booking-confirm')
            .then(response => response.json())
            .then(data => {
                console.log('Auth debug:', data);
                alert('Auth: ' + (data.authenticated ? 'Yes' : 'No') + '\nUser: ' + (data.user ? data.user.name : 'None'));
            })
            .catch(error => {
                console.error('Debug error:', error);
                alert('Error: ' + error.message);
            });
    });

    testStripe.addEventListener('click', function() {
        const bookingData = {
            booking_id: 1 // Assuming booking ID 1 exists
        };

        fetch('/booking/payment', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            },
            body: JSON.stringify(bookingData)
        })
        .then(response => response.json())
        .then(data => {
            console.log('Stripe test:', data);
            if (data.success) {
                alert('Stripe session created! URL: ' + data.payment_url);
            } else {
                alert('Stripe error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Stripe error:', error);
            alert('Error: ' + error.message);
        });
    });
});
</script>
@endsection
