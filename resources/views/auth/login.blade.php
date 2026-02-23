@extends('layouts.auth')

@section('content')
<div class="container d-flex justify-content-center align-items-center" style="min-height:100vh;">
    <div class="card p-4 shadow-sm" style="width: 400px; border-radius: 10px;">
        <h3 class="mb-4 text-center fw-bold">Admin Login</h3>

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

       <form method="POST" action="{{ route('admin.login') }}">

            @csrf

            <!-- Name -->
<div class="mb-3">
    <label for="email" class="form-label">Email</label>
    <input type="email" name="email" id="email" class="form-control" placeholder="Enter your email" required autofocus>
</div>


            <!-- Password with show/hide -->
            <div class="mb-3">
                <label for="passwordField" class="form-label">Password</label>
                <div class="input-group">
                    <input type="password" name="password" id="passwordField" class="form-control" placeholder="Enter your password" required>
                    <span class="input-group-text" id="togglePassword" style="cursor: pointer;">
                        <i class="bi bi-eye"></i>
                    </span>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2">Login</button>
        </form>
    </div>
</div>

<script>
    const togglePassword = document.getElementById('togglePassword');
    const passwordField = document.getElementById('passwordField');

    togglePassword.addEventListener('click', function () {
        const type = passwordField.type === 'password' ? 'text' : 'password';
        passwordField.type = type;

        const icon = this.querySelector('i');
        icon.classList.toggle('bi-eye');
        icon.classList.toggle('bi-eye-slash');
    });
</script>
@endsection
