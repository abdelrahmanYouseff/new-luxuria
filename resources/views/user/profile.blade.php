@extends('layouts.blade_app')

@section('title', 'Profile Settings')

@section('content')
<div class="container-fluid" style="font-family: Arial, sans-serif;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-gray-900">Profile Settings</h2>
        <a href="{{ route('user.dashboard') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">My Profile Information</h5>
                </div>
                <div class="card-body">
                    <form>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="name" value="{{ Auth::user()->name }}" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" value="{{ Auth::user()->email }}" readonly>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="role" class="form-label">Account Type</label>
                                <input type="text" class="form-control" id="role" value="{{ ucfirst(Auth::user()->role) }}" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="joined" class="form-label">Member Since</label>
                                <input type="text" class="form-control" id="joined" value="{{ Auth::user()->created_at->format('F d, Y') }}" readonly>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="row">
                            <div class="col-12">
                                <h6 class="text-muted mb-3">Account Statistics</h6>
                                <div class="row">
                                    <div class="col-md-4 text-center">
                                        <div class="border rounded p-3">
                                            <h4 class="text-primary mb-1">{{ App\Models\Reservation::where('user_id', Auth::id())->count() }}</h4>
                                            <small class="text-muted">Total Reservations</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <div class="border rounded p-3">
                                            <h4 class="text-success mb-1">{{ App\Models\CouponInvoice::where('user_id', Auth::id())->count() }}</h4>
                                            <small class="text-muted">Coupons Purchased</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <div class="border rounded p-3">
                                            <h4 class="text-warning mb-1">{{ App\Models\Transaction::where('user_id', Auth::id())->count() }}</h4>
                                            <small class="text-muted">Total Transactions</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="text-muted mb-0">Need to update your information?</p>
                                <small class="text-muted">Contact our support team for assistance.</small>
                            </div>
                            <div>
                                <a href="/contact" class="btn btn-outline-primary">Contact Support</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
