@extends('layouts.blade_app')

@section('title', 'Test Registration')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3>Test User Registration</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="Test User" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="test@example.com" required>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone (Optional)</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="0501234567">
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" value="password123" required>
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" value="password123" required>
                        </div>

                        <button type="submit" class="btn btn-primary">Register User</button>
                    </form>
                </div>
            </div>

            <div class="mt-4">
                <h4>Registration Status:</h4>
                <div id="status" class="alert alert-info">
                    Ready to test registration...
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const status = document.getElementById('status');

    form.addEventListener('submit', function(e) {
        status.innerHTML = 'Registering user...';
        status.className = 'alert alert-warning';
    });
});
</script>
@endsection
