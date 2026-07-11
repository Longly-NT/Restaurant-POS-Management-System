@extends('layouts.app')

@section('title', 'Login - Restaurant POS')

@section('content')
<div class="row justify-content-center align-items-center" style="min-height: 90vh;">
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h3 class="text-center mb-1"><i class="bi bi-shop"></i> Restaurant POS</h3>
                <p class="text-center text-muted mb-4">Sign in to continue</p>

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-control" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label" for="remember">Remember me</label>
                    </div>
                    <button type="submit" class="btn btn-dark w-100">Login</button>
                </form>

                <hr>
                <p class="small text-muted mb-1">Demo accounts (password: <code>password</code>):</p>
                <ul class="small text-muted mb-0">
                    <li>admin@pos.test</li>
                    <li>staff@pos.test</li>
                    <li>chef@pos.test</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
