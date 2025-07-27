@extends('layouts.blade_app')

@section('title', 'PointSys Integration Test')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <h1 class="text-center mb-4">PointSys Integration Test</h1>
            <p class="text-center text-muted">This page demonstrates the PointSys loyalty system integration using mock responses.</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Test Customer Registration</h5>
                </div>
                <div class="card-body">
                    <form id="registerForm">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="Test User" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="test@example.com" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="tel" class="form-control" id="phone" name="phone" value="0501234567" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Register Customer</button>
                    </form>
                    <div id="registerResult" class="mt-3"></div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Test Add Points</h5>
                </div>
                <div class="card-body">
                    <form id="addPointsForm">
                        <div class="mb-3">
                            <label for="customerId" class="form-label">Customer ID</label>
                            <input type="number" class="form-control" id="customerId" name="customerId" value="1" required>
                        </div>
                        <div class="mb-3">
                            <label for="points" class="form-label">Points</label>
                            <input type="number" class="form-control" id="points" name="points" value="100" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <input type="text" class="form-control" id="description" name="description" value="Test points">
                        </div>
                        <button type="submit" class="btn btn-success">Add Points</button>
                    </form>
                    <div id="addPointsResult" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Test Get Rewards</h5>
                </div>
                <div class="card-body">
                    <button id="getRewardsBtn" class="btn btn-info">Get Available Rewards</button>
                    <div id="rewardsResult" class="mt-3"></div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Test Redeem Reward</h5>
                </div>
                <div class="card-body">
                    <form id="redeemForm">
                        <div class="mb-3">
                            <label for="redeemCustomerId" class="form-label">Customer ID</label>
                            <input type="number" class="form-control" id="redeemCustomerId" name="customerId" value="1" required>
                        </div>
                        <div class="mb-3">
                            <label for="rewardId" class="form-label">Reward ID</label>
                            <input type="number" class="form-control" id="rewardId" name="rewardId" value="1" required>
                        </div>
                        <button type="submit" class="btn btn-warning">Redeem Reward</button>
                    </form>
                    <div id="redeemResult" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>API Logs</h5>
                </div>
                <div class="card-body">
                    <div id="apiLogs" class="bg-light p-3" style="max-height: 300px; overflow-y: auto; font-family: monospace; font-size: 12px;">
                        <div class="text-muted">API logs will appear here...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const apiLogs = document.getElementById('apiLogs');

    function addLog(message, type = 'info') {
        const timestamp = new Date().toLocaleTimeString();
        const logEntry = document.createElement('div');
        logEntry.className = `log-entry text-${type === 'error' ? 'danger' : type === 'success' ? 'success' : 'info'}`;
        logEntry.innerHTML = `[${timestamp}] ${message}`;
        apiLogs.appendChild(logEntry);
        apiLogs.scrollTop = apiLogs.scrollHeight;
    }

    // Register Customer
    document.getElementById('registerForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        addLog('Registering customer...');

        fetch('/api/pointsys/customers/register', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-API-Key': 'lux_M01oRXyMzqM3tPDtN4ELFNQ50lRLY25a'
            },
            body: JSON.stringify({
                name: formData.get('name'),
                email: formData.get('email'),
                phone: formData.get('phone')
            })
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('registerResult').innerHTML = `<pre class="text-success">${JSON.stringify(data, null, 2)}</pre>`;
            addLog('Customer registered successfully', 'success');
        })
        .catch(error => {
            document.getElementById('registerResult').innerHTML = `<pre class="text-danger">${error.message}</pre>`;
            addLog('Registration failed: ' + error.message, 'error');
        });
    });

    // Add Points
    document.getElementById('addPointsForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        addLog('Adding points...');

        fetch('/api/pointsys/customers/points/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-API-Key': 'lux_M01oRXyMzqM3tPDtN4ELFNQ50lRLY25a'
            },
            body: JSON.stringify({
                customer_id: parseInt(formData.get('customerId')),
                points: parseInt(formData.get('points')),
                description: formData.get('description'),
                reference_id: 'TEST_' + Date.now()
            })
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('addPointsResult').innerHTML = `<pre class="text-success">${JSON.stringify(data, null, 2)}</pre>`;
            addLog('Points added successfully', 'success');
        })
        .catch(error => {
            document.getElementById('addPointsResult').innerHTML = `<pre class="text-danger">${error.message}</pre>`;
            addLog('Add points failed: ' + error.message, 'error');
        });
    });

    // Get Rewards
    document.getElementById('getRewardsBtn').addEventListener('click', function() {
        addLog('Fetching rewards...');

        fetch('/api/pointsys/rewards', {
            method: 'GET',
            headers: {
                'X-API-Key': 'lux_M01oRXyMzqM3tPDtN4ELFNQ50lRLY25a'
            }
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('rewardsResult').innerHTML = `<pre class="text-success">${JSON.stringify(data, null, 2)}</pre>`;
            addLog('Rewards fetched successfully', 'success');
        })
        .catch(error => {
            document.getElementById('rewardsResult').innerHTML = `<pre class="text-danger">${error.message}</pre>`;
            addLog('Get rewards failed: ' + error.message, 'error');
        });
    });

    // Redeem Reward
    document.getElementById('redeemForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        addLog('Redeeming reward...');

        fetch('/api/pointsys/rewards/redeem', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-API-Key': 'lux_M01oRXyMzqM3tPDtN4ELFNQ50lRLY25a'
            },
            body: JSON.stringify({
                customer_id: parseInt(formData.get('customerId')),
                reward_id: parseInt(formData.get('rewardId'))
            })
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('redeemResult').innerHTML = `<pre class="text-success">${JSON.stringify(data, null, 2)}</pre>`;
            addLog('Reward redeemed successfully', 'success');
        })
        .catch(error => {
            document.getElementById('redeemResult').innerHTML = `<pre class="text-danger">${error.message}</pre>`;
            addLog('Redeem reward failed: ' + error.message, 'error');
        });
    });
});
</script>

<style>
.log-entry {
    margin-bottom: 5px;
    padding: 2px 0;
}
</style>
@endsection
