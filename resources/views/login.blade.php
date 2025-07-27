@extends('layouts.blade_app')

@section('title', 'Login')

@section('content')
<div class="login-page-bg d-flex align-items-center justify-content-center" style="min-height: 80vh; background: #fff;">
    <div class="login-box p-5 shadow-lg rounded-4">
        <h1 class="lux-heading text-center mb-4" style="font-size:2.5rem; color:#111;">Login</h1>
        <form method="POST" action="/login">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label lux-gold">Email address</label>
                <input type="email" class="form-control form-control-lg" id="email" name="email" required autofocus>
            </div>
            <div class="mb-4">
                <label for="password" class="form-label lux-gold">Password</label>
                <input type="password" class="form-control form-control-lg" id="password" name="password" required>
            </div>
            <button type="submit" class="btn lux-btn-gold btn-lg w-100 mb-3">Login</button>
            <div class="text-center mt-3">
                <a href="/register" class="lux-gold" style="text-decoration:underline;">Don't have an account? Register</a>
            </div>
        </form>
    </div>
</div>
<style>
.login-box {
    background: #fff;
    border: 2px solid #bfa13322;
    max-width: 400px;
    width: 100%;
}
.login-page-bg {
    background: linear-gradient(120deg, #fff 60%, #f7f7f7 100%);
}
.lux-heading {
    font-family: 'Playfair Display', serif;
    font-weight: 700;
    letter-spacing: 1px;
}
.lux-gold {
    color: #bfa133 !important;
}
</style>
@endsection
