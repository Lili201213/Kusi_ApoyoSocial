<x-auth-layout>
    <x-slot:title>Iniciar sesión</x-slot>

        <h1>¡Bienvenido/a!</h1>
        <p class="text-muted mb-4">Ingresa con tu usuario o correo electrónico y tu contraseña para continuar.</p>

        @if (session('exito'))
            <div class="alert alert-success py-2 small"><i class="bi bi-check-circle me-1"></i>{{ session('exito') }}</div>
        @endif

        <form method="POST" action="{{ route('login.post') }}">
            @csrf
            <div class="mb-3">
                <label for="usuario" class="form-label">Usuario o correo electrónico</label>
                <div class="input-group has-validation">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input type="text" name="usuario" id="usuario" value="{{ old('usuario') }}" autocomplete="username"
                           class="form-control @error('usuario') is-invalid @enderror" placeholder="admin o correo@gmail.com" required autofocus>
                    @error('usuario') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <div class="input-group has-validation">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password" id="password" autocomplete="current-password"
                           class="form-control con-boton @error('password') is-invalid @enderror" placeholder="Tu contraseña" required>
                    <button type="button" class="btn btn-ver" id="verClave" title="Mostrar u ocultar"><i class="bi bi-eye"></i></button>
                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
                <div class="form-check mb-0">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label" for="remember">Mantener mi sesión</label>
                </div>
                <a href="{{ route('password.request') }}" class="small fw-bold text-decoration-none">¿Olvidaste tu contraseña?</a>
            </div>

            <button class="btn btn-success w-100 login-btn"><i class="bi bi-box-arrow-in-right me-1"></i>Ingresar</button>
        </form>
                    <p class="text-center small mt-3 mb-0">
            ¿No tienes cuenta?
            <a href="{{ route('register') }}" class="fw-bold text-decoration-none">Crear cuenta</a>
        </p>

@push('scripts')
<script>
    document.getElementById('verClave').addEventListener('click', function () {
        const campo = document.getElementById('password');
        const ver = campo.type === 'password';
        campo.type = ver ? 'text' : 'password';
        this.innerHTML = ver ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
    });
</script>
@endpush
</x-auth-layout>
