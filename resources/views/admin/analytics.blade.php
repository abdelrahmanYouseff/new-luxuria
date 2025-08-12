@extends('layouts.blade_app')

@section('title', 'Analytics')

@section('content')
<div class="container-fluid" style="font-family: Arial, sans-serif;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-gray-900">Analytics & Reports</h2>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <div class="row">
        <!-- Monthly Registrations Chart -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Monthly User Registrations ({{ date('Y') }})</h5>
                </div>
                <div class="card-body">
                    @if($monthlyRegistrations->count() > 0)
                        <canvas id="registrationsChart" width="400" height="200"></canvas>
                    @else
                        <div class="text-center py-4">
                            <p class="text-muted">No registration data available for this year.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Vehicles by Category Chart -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Vehicles by Category</h5>
                </div>
                <div class="card-body">
                    @if($vehiclesByCategory->count() > 0)
                        <canvas id="vehiclesCategoryChart" width="400" height="200"></canvas>
                    @else
                        <div class="text-center py-4">
                            <p class="text-muted">No vehicle data available.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Data Tables -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Registration Data</h5>
                </div>
                <div class="card-body">
                    @if($monthlyRegistrations->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Month</th>
                                        <th>Registrations</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($monthlyRegistrations as $data)
                                        <tr>
                                            <td>{{ DateTime::createFromFormat('!m', $data->month)->format('F') }}</td>
                                            <td><span class="badge bg-primary">{{ $data->count }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No data available.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Vehicle Categories</h5>
                </div>
                <div class="card-body">
                    @if($vehiclesByCategory->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Category</th>
                                        <th>Count</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($vehiclesByCategory as $data)
                                        <tr>
                                            <td>{{ ucfirst($data->category) }}</td>
                                            <td><span class="badge bg-success">{{ $data->count }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No data available.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Monthly Registrations Chart
@if($monthlyRegistrations->count() > 0)
const registrationsCtx = document.getElementById('registrationsChart').getContext('2d');
const registrationsChart = new Chart(registrationsCtx, {
    type: 'line',
    data: {
        labels: [
            @foreach($monthlyRegistrations as $data)
                '{{ DateTime::createFromFormat("!m", $data->month)->format("M") }}',
            @endforeach
        ],
        datasets: [{
            label: 'User Registrations',
            data: [
                @foreach($monthlyRegistrations as $data)
                    {{ $data->count }},
                @endforeach
            ],
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.1)',
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
@endif

// Vehicles by Category Chart
@if($vehiclesByCategory->count() > 0)
const vehiclesCategoryCtx = document.getElementById('vehiclesCategoryChart').getContext('2d');
const vehiclesCategoryChart = new Chart(vehiclesCategoryCtx, {
    type: 'doughnut',
    data: {
        labels: [
            @foreach($vehiclesByCategory as $data)
                '{{ ucfirst($data->category) }}',
            @endforeach
        ],
        datasets: [{
            data: [
                @foreach($vehiclesByCategory as $data)
                    {{ $data->count }},
                @endforeach
            ],
            backgroundColor: [
                '#FF6384',
                '#36A2EB',
                '#FFCE56',
                '#4BC0C0',
                '#9966FF',
                '#FF9F40'
            ]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
@endif
</script>
@endsection
