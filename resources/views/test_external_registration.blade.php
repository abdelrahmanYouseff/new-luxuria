@extends('layouts.blade_app')

@section('title', 'Test External Customer Registration')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3>🧪 Test External Customer Registration</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <strong>API Status:</strong>
                        <span id="apiStatus">Ready to test external customer registration</span>
                    </div>

                    <form method="POST" action="{{ route('register') }}" id="registrationForm">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control" id="name" name="name" value="Test External User" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" value="testexternal@example.com" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone</label>
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

                        <div class="mb-3">
                            <label for="emirate" class="form-label">Emirate</label>
                            <select class="form-control" id="emirate" name="emirate" required>
                                <option value="Dubai" selected>Dubai</option>
                                <option value="Abu Dhabi">Abu Dhabi</option>
                                <option value="Sharjah">Sharjah</option>
                                <option value="Ajman">Ajman</option>
                                <option value="Umm Al Quwain">Umm Al Quwain</option>
                                <option value="Ras Al Khaimah">Ras Al Khaimah</option>
                                <option value="Fujairah">Fujairah</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="3" required>Test Address, Dubai, UAE</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-person-plus"></i> Register User (External API)
                        </button>
                    </form>

                    <hr>

                    <div class="mt-4">
                        <h4>Registration Log:</h4>
                        <div id="logContainer" style="max-height: 300px; overflow-y: auto; background: #f8f9fa; padding: 10px; border-radius: 5px;">
                            <div class="text-muted">No logs yet...</div>
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

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registrationForm');
    const apiStatus = document.getElementById('apiStatus');

    // Test API connection first
    addLog('Testing external customer API connection...', 'info');

    fetch('/test-external-customer')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                apiStatus.textContent = '✅ External API is working';
                apiStatus.className = 'text-success';
                addLog('External API test successful', 'success');
                addLog(`Created test customer with ID: ${data.external_customer_id}`, 'success');
            } else {
                apiStatus.textContent = '❌ External API error';
                apiStatus.className = 'text-danger';
                addLog('External API test failed: ' + data.message, 'error');
            }
        })
        .catch(error => {
            apiStatus.textContent = '❌ External API connection failed';
            apiStatus.className = 'text-danger';
            addLog('External API connection error: ' + error.message, 'error');
        });

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        addLog('Starting user registration process...', 'info');

        const formData = new FormData(this);
        const submitButton = form.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;

        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="bi bi-hourglass-split"></i> Registering...';

        fetch('/register', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            addLog(`Registration response status: ${response.status}`, 'info');
            return response.text();
        })
        .then(data => {
            if (data.includes('redirect')) {
                addLog('✅ User registration successful!', 'success');
                addLog('User should be redirected to home page', 'success');
                addLog('Check logs for external API registration details', 'info');

                // Redirect after a short delay
                setTimeout(() => {
                    window.location.href = '/';
                }, 2000);
            } else {
                addLog('❌ Registration failed', 'error');
                addLog('Response: ' + data.substring(0, 200) + '...', 'error');
            }
        })
        .catch(error => {
            addLog('❌ Registration error: ' + error.message, 'error');
        })
        .finally(() => {
            submitButton.disabled = false;
            submitButton.innerHTML = originalText;
        });
    });
});
</script>
@endsection
