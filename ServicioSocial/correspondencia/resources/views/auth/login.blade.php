@extends('layouts.guest')

@section('content')
<div class="auth-header">
    <div class="logo-badge">
        <i class="bi bi-mortarboard-fill"></i>
    </div>
    <h4 class="fw-bold mb-1">Correspondencia Universitaria</h4>
    <p class="text-white-50 small mb-0">Sistema de Control y Gestión Institucional</p>
</div>

<div class="p-4 p-md-5">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show small" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show small" role="alert">
            {{ $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('login.post') }}">
        @csrf
        <div class="mb-3">
            <label for="email" class="form-label fw-semibold text-secondary small">Correo Institucional</label>
            <div class="input-group">
                <span class="input-group-text bg-light text-muted"><i class="bi bi-envelope"></i></span>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="usuario@universidad.edu">
            </div>
            @error('email')
                <div class="invalid-feedback d-block small">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <label for="password" class="form-label fw-semibold text-secondary small mb-0">Contraseña</label>
            </div>
            <div class="input-group">
                <span class="input-group-text bg-light text-muted"><i class="bi bi-lock"></i></span>
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required placeholder="••••••••">
                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                    <i class="bi bi-eye" id="passwordEye"></i>
                </button>
            </div>
            @error('password')
                <div class="invalid-feedback d-block small">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4 form-check">
            <input type="checkbox" class="form-check-input" id="remember" name="remember">
            <label class="form-check-label text-secondary small" for="remember">Mantener sesión iniciada</label>
        </div>

        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold shadow-sm">
            <i class="bi bi-box-arrow-in-right me-1"></i> Iniciar Sesión
        </button>
    </form>
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('togglePassword')?.addEventListener('click', function() {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('passwordEye');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            passwordInput.type = 'password';
            eyeIcon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    });
</script>
@endsection
