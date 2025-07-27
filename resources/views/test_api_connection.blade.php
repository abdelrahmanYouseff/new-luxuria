@extends('layouts.blade_app')

@section('title', 'Test API Connection')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3>🔗 Test PointSys API Connection</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <strong>Current API Key:</strong> {{ config('services.pointsys.api_key') ? '✅ Set' : '❌ Not Set' }}<br>
                        <strong>Base URL:</strong> {{ config('services.pointsys.base_url') ?: 'Not Set' }}
                    </div>

                    <button id="testApi" class="btn btn-success btn-lg">
                        <i class="bi bi-play-circle"></i> Test API Connection
                    </button>

                    <div class="mt-4">
                        <h5>Test Results:</h5>
                        <div id="results" class="bg-dark text-light p-3 rounded" style="height: 400px; overflow-y: auto; font-family: monospace; font-size: 12px;">
                            Click "Test API Connection" to start...
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const testButton = document.getElementById('testApi');
    const results = document.getElementById('results');

    function addResult(message, type = 'info') {
        const timestamp = new Date().toLocaleTimeString();
        const color = type === 'error' ? '#ff6b6b' : type === 'success' ? '#51cf66' : '#74c0fc';
        results.innerHTML += `<span style="color: ${color}">[${timestamp}] ${message}</span>\n`;
        results.scrollTop = results.scrollHeight;
    }

    testButton.addEventListener('click', async function() {
        testButton.disabled = true;
        testButton.innerHTML = '<i class="bi bi-hourglass-split"></i> Testing...';
        results.innerHTML = '';

        addResult('Starting API connection test...');

        try {
            // Test 1: Check if API key is valid
            addResult('Testing API key validity...');

            const response = await fetch('/api/pointsys/customers/register', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({
                    name: 'API Test User',
                    email: 'apitest@example.com',
                    phone: '0501234567'
                })
            });

            const data = await response.json();

            if (response.ok && data.status === 'success') {
                addResult('✅ API connection successful!', 'success');
                addResult(`Customer ID: ${data.data?.customer_id}`, 'success');
            } else {
                addResult(`❌ API Error: ${data.message || 'Unknown error'}`, 'error');
                addResult(`Status Code: ${response.status}`, 'error');
            }

        } catch (error) {
            addResult(`❌ Network Error: ${error.message}`, 'error');
        }

        testButton.disabled = false;
        testButton.innerHTML = '<i class="bi bi-play-circle"></i> Test API Connection';
    });
});
</script>
@endsection
