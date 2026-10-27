@extends('auth.base')
@section('titulo', 'Recuperar contraseña')

@section('formulario')
    <h1>¿Olvidaste tu contraseña?</h1>
    <p class="text-muted mb-4">No te preocupes. Escribe el correo registrado en tu cuenta y te enviaremos un enlace para crear una contraseña nueva.</p>

    @if (session('estado'))
        <div class="alert alert-success small"><i class="bi bi-envelope-check me-1"></i>{{ session('estado') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="mb-4">
            <label for="email" class="form-label">Correo electrónico</label>
            <div class="input-group has-validation">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input type="email" name="email" id="email" value="{{ old('email') }}" autocomplete="email"
                       class="form-control @error('email') is-invalid @enderror" placeholder="tucorreo@ejemplo.com" required autofocus>
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
        <button class="btn btn-success w-100 login-btn"><i class="bi bi-send me-1"></i>Enviarme el enlace</button>
    </form>

    <div class="text-center mt-4">
        <a href="{{ route('login') }}" class="text-decoration-none fw-bold"><i class="bi bi-arrow-left me-1"></i>Volver al inicio de sesión</a>
    </div>
@endsection
