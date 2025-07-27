@extends('layouts.blade_app')

@section('title', 'Test User Points')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3>⭐ Test User Points Display</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <strong>Current User:</strong> {{ Auth::user()->name }} ({{ Auth::user()->email }})<br>
                        <strong>PointSys Customer ID:</strong> {{ Auth::user()->pointsys_customer_id ?: 'Not registered' }}
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>API Test</h5>
                                </div>
                                <div class="card-body">
                                    <button id="testPoints" class="btn btn-success">
                                        <i class="bi bi-star"></i> Test Points API
                                    </button>
                                    <div id="apiResult" class="mt-3 p-3 bg-light rounded"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Dropdown Test</h5>
                                </div>
                                <div class="card-body">
                                    <p>Click on your profile icon in the navbar to test the dropdown points display.</p>
                                    <div class="alert alert-warning">
                                        <i class="bi bi-info-circle"></i>
                                        The dropdown should show your current points from PointSys API.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <h5>Manual Points Test:</h5>
                        <div class="row">
                            <div class="col-md-4">
                                <button id="addPoints" class="btn btn-warning">
                                    <i class="bi bi-plus-circle"></i> Add 100 Points
                                </button>
                            </div>
                            <div class="col-md-4">
                                <button id="getRewards" class="btn btn-info">
                                    <i class="bi bi-gift"></i> Get Rewards
                                </button>
                            </div>
                            <div class="col-md-4">
                                <button id="refreshPoints" class="btn btn-secondary">
                                    <i class="bi bi-arrow-clockwise"></i> Refresh
                                </button>
                            </div>
                        </div>
                        <div id="manualResult" class="mt-3 p-3 bg-light rounded"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const testPointsBtn = document.getElementById('testPoints');
    const addPointsBtn = document.getElementById('addPoints');
    const getRewardsBtn = document.getElementById('getRewards');
    const refreshPointsBtn = document.getElementById('refreshPoints');
    const apiResult = document.getElementById('apiResult');
    const manualResult = document.getElementById('manualResult');

    function showResult(element, message, type = 'info') {
        const color = type === 'success' ? '#d4edda' : type === 'error' ? '#f8d7da' : '#d1ecf1';
        element.style.backgroundColor = color;
        element.innerHTML = message;
    }

    testPointsBtn.addEventListener('click', function() {
        showResult(apiResult, 'Testing points API...');

        fetch('/user-points', {
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
                showResult(apiResult, `
                    <strong>✅ Success!</strong><br>
                    Points: ${data.points}<br>
                    Total Earned: ${data.total_earned}<br>
                    Total Redeemed: ${data.total_redeemed}
                `, 'success');
            } else {
                showResult(apiResult, `<strong>❌ Error:</strong> ${data.error}`, 'error');
            }
        })
        .catch(error => {
            showResult(apiResult, `<strong>❌ Network Error:</strong> ${error.message}`, 'error');
        });
    });

    addPointsBtn.addEventListener('click', function() {
        showResult(manualResult, 'Adding points...');

        fetch('/add-points', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify({
                points: 100,
                description: 'Test points addition',
                reference_id: 'TEST_' + Date.now()
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showResult(manualResult, `
                    <strong>✅ Points Added!</strong><br>
                    Points Added: ${data.points_added}<br>
                    New Balance: ${data.new_balance}
                `, 'success');
            } else {
                showResult(manualResult, `<strong>❌ Error:</strong> ${data.error}`, 'error');
            }
        })
        .catch(error => {
            showResult(manualResult, `<strong>❌ Network Error:</strong> ${error.message}`, 'error');
        });
    });

    getRewardsBtn.addEventListener('click', function() {
        showResult(manualResult, 'Fetching rewards...');

        fetch('/rewards', {
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
                const rewardsList = data.rewards.map(reward =>
                    `• ${reward.title} (${reward.points_required} points)`
                ).join('<br>');
                showResult(manualResult, `
                    <strong>✅ Rewards Available:</strong><br>
                    ${rewardsList}
                `, 'success');
            } else {
                showResult(manualResult, `<strong>❌ Error:</strong> ${data.error}`, 'error');
            }
        })
        .catch(error => {
            showResult(manualResult, `<strong>❌ Network Error:</strong> ${error.message}`, 'error');
        });
    });

    refreshPointsBtn.addEventListener('click', function() {
        testPointsBtn.click();
    });

    // Auto-test on page load
    setTimeout(() => {
        testPointsBtn.click();
    }, 1000);
});
</script>
@endsection
