@extends('layouts.blade_app')

@section('title', 'Vehicles Management')

@section('content')
<div class="container-fluid" style="font-family: Arial, sans-serif;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-gray-900">Vehicles Management</h2>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">All Vehicles</h5>
                </div>
                <div class="card-body">
                    @if($vehicles->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Make & Model</th>
                                        <th>Year</th>
                                        <th>Category</th>
                                        <th>Status</th>
                                        <th>Daily Rate</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($vehicles as $vehicle)
                                        <tr>
                                            <td>{{ $vehicle->id }}</td>
                                            <td>{{ $vehicle->make }} {{ $vehicle->model }}</td>
                                            <td>{{ $vehicle->year }}</td>
                                            <td>
                                                <span class="badge bg-primary">{{ ucfirst($vehicle->category) }}</span>
                                            </td>
                                            <td>
                                                <span class="badge {{ $vehicle->status === 'available' ? 'bg-success' : 'bg-danger' }}">
                                                    {{ ucfirst($vehicle->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $vehicle->daily_rate }} AED</td>
                                            <td>
                                                <a href="#" class="btn btn-sm btn-outline-primary">View</a>
                                                <a href="#" class="btn btn-sm btn-outline-warning">Edit</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $vehicles->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 3rem; height: 3rem;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21H5a2 2 0 01-2-2v-4a2 2 0 012-2h.586l4.707-4.707A1 1 0 0111 8h2.414l4.293 4.293A1 1 0 0119 13v6a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="mt-3 text-gray-500">No vehicles found</h3>
                            <p class="text-gray-400">No vehicles have been added to the database yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
