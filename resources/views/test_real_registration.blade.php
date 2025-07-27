@extends('layouts.blade_app')

@section('title', 'Test Real Registration')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3>🧪 Test Real Registration with PointSys API</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <strong>API Status:</strong>
                        <span id="apiStatus">Checking...</span>
                    </div>

                    <form method="POST" action="{{ route('register') }}" id="registrationForm">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control" id="name" name="name" value="Test User" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" value="test@example.com" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone (Optional)</label>
                                    <input type="text" class="form-control" id="phone" name="phone" value="0501234567">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="password" name="password" value="password123" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" value="password123" required>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-person-plus"></i> Register User
                        </button>
                    </form>
                </div>
            </div>

            <div class="mt-4">
                <div class="card">
                    <div class="card-header">
                        <h4>Registration Logs</h4>
                    </div>
                    <div class="card-body">
                        <div id="logs" class="bg-dark text-light p-3 rounded" style="height: 300px; overflow-y: auto; font-family: monospace; font-size: 12px;">
                            Ready to test registration...
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registrationForm');
    const logs = document.getElementById('logs');
    const apiStatus = document.getElementById('apiStatus');

    // Check API status
    fetch('/api/pointsys/customers/register', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: JSON.stringify({
            name: 'API Test',
            email: 'api@test.com',
            phone: '0501234567'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            apiStatus.innerHTML = '<span class="text-success">✅ Connected</span>';
        } else {
            apiStatus.innerHTML = '<span class="text-warning">⚠️ Limited</span>';
        }
    })
    .catch(error => {
        apiStatus.innerHTML = '<span class="text-danger">❌ Error</span>';
        addLog('API Check Error: ' + error.message);
    });

    function addLog(message) {
        const timestamp = new Date().toLocaleTimeString();
        logs.innerHTML += `[${timestamp}] ${message}\n`;
        logs.scrollTop = logs.scrollHeight;
    }

    form.addEventListener('submit', function(e) {
        addLog('Starting registration process...');
        addLog('Form data submitted');
    });
});
</script>
@endsection
