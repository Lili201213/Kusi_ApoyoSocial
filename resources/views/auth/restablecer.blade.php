@extends('auth.base')
@section('titulo', 'Nueva contraseña')

@section('formulario')
    <h1>Crea tu nueva contraseña</h1>
    <p class="text-muted mb-4">Elige una contraseña segura: mínimo 8 caracteres, con letras y números.</p>

    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="mb-3">
            <label for="email" class="form-label">Correo electrónico</label>
            <div class="input-group has-validation">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input type="email" name="email" id="email" value="{{ old('email', $email) }}" readonly
                       class="form-control @error('email') is-invalid @enderror" required>
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Nueva contraseña</label>
            <div class="input-group has-validation">
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                <input type="password" name="password" id="password" autocomplete="new-password"
                       class="form-control @error('password') is-invalid @enderror" required autofocus>
                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="mb-4">
            <label for="password_confirmation" class="form-label">Confirmar contraseña</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                <input type="password" name="password_confirmation" id="password_confirmation" autocomplete="new-password" class="form-control" required>
            </div>
        </div>

        <button class="btn btn-success w-100 login-btn"><i class="bi bi-check2-circle me-1"></i>Guardar nueva contraseña</button>
    </form>

    <div class="text-center mt-4">
        <a href="{{ route('login') }}" class="text-decoration-none fw-bold"><i class="bi bi-arrow-left me-1"></i>Volver al inicio de sesión</a>
    </div>
@endsection
