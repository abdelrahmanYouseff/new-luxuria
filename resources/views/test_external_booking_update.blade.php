@extends('layouts.blade_app')

@section('title', 'Test External Booking Update')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3>Test External Booking Status Update</h3>
                </div>
                <div class="card-body">
                    <form id="testForm" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="external_booking_id" class="form-label">External Booking ID:</label>
                            <input type="text" class="form-control" id="external_booking_id" name="external_booking_id"
                                   value="019846c4-3338-736b-9220-d91cc09474f2" required>
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">New Status:</label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="confirmed">Confirmed</option>
                                <option value="pending">Pending</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Test Update External Booking</button>
                    </form>

                    <hr>

                    <div id="result" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const testForm = document.getElementById('testForm');
    const resultDiv = document.getElementById('result');

    testForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch('/test-external-booking-update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            },
            body: JSON.stringify({
                external_booking_id: formData.get('external_booking_id'),
                status: formData.get('status')
            })
        })
        .then(response => response.json())
        .then(data => {
            resultDiv.innerHTML = `
                <div class="alert alert-${data.success ? 'success' : 'danger'}">
                    <h5>Result:</h5>
                    <p><strong>Success:</strong> ${data.success}</p>
                    <p><strong>Message:</strong> ${data.message}</p>
                    ${data.update_url ? `<p><strong>URL:</strong> ${data.update_url}</p>` : ''}
                    ${data.response_status ? `<p><strong>Response Status:</strong> ${data.response_status}</p>` : ''}
                    ${data.response_body ? `<p><strong>Response Body:</strong> <pre>${data.response_body}</pre></p>` : ''}
                </div>
            `;
        })
        .catch(error => {
            resultDiv.innerHTML = `
                <div class="alert alert-danger">
                    <h5>Error:</h5>
                    <p>${error.message}</p>
                </div>
            `;
        });
    });
});
</script>
@endsection
