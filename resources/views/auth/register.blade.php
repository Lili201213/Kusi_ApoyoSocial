<x-auth-layout>
    <x-slot:title>Crear cuenta</x-slot>

        <h1>Crear cuenta</h1>
        <p class="text-muted mb-4">Completa tus datos. Te enviaremos un enlace para verificar tu correo.</p>

        @if (session('error'))
            <div class="alert alert-danger py-2 small"><i class="bi bi-exclamation-circle me-1"></i>{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('register.store') }}">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">Nombre completo</label>
                <div class="input-group has-validation">
                    <span class="input-group-text"><i class="bi bi-person-vcard"></i></span>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" autocomplete="name"
                           class="form-control @error('name') is-invalid @enderror" placeholder="Tu nombre completo" required autofocus>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="rol_id" class="form-label">Tipo de cuenta</label>
                <div class="input-group has-validation">
                    <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                    <select name="rol_id" id="rol_id" class="form-select @error('rol_id') is-invalid @enderror" required>
                        <option value="" disabled {{ old('rol_id') ? '' : 'selected' }}>Elige una opción</option>
                        @foreach ($roles as $rol)
                            <option value="{{ $rol->id }}" {{ (string) old('rol_id') === (string) $rol->id ? 'selected' : '' }}>{{ $rol->nombre }}</option>
                        @endforeach
                    </select>
                    @error('rol_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="usuario" class="form-label">Usuario</label>
                <div class="input-group has-validation">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input type="text" name="usuario" id="usuario" value="{{ old('usuario') }}" autocomplete="username"
                           class="form-control @error('usuario') is-invalid @enderror" placeholder="Ej: maria.lopez" required>
                    @error('usuario') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Correo electrónico</label>
                <div class="input-group has-validation">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" autocomplete="email"
                           class="form-control @error('email') is-invalid @enderror" placeholder="correo@gmail.com" required>
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="telefono" class="form-label">Teléfono <span class="text-muted fw-normal">(opcional)</span></label>
                <div class="input-group has-validation">
                    <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                    <input type="text" name="telefono" id="telefono" value="{{ old('telefono') }}" autocomplete="tel"
                           class="form-control @error('telefono') is-invalid @enderror" placeholder="Tu teléfono">
                    @error('telefono') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <div class="input-group has-validation">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password" id="password" autocomplete="new-password"
                           class="form-control con-boton @error('password') is-invalid @enderror" placeholder="Mínimo 8 caracteres" required>
                    <button type="button" class="btn btn-ver ver-clave" data-target="password" title="Mostrar u ocultar"><i class="bi bi-eye"></i></button>
                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="form-text">Con letras y números.</div>
            </div>

            <div class="mb-4">
                <label for="password_confirmation" class="form-label">Confirmar contraseña</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                    <input type="password" name="password_confirmation" id="password_confirmation" autocomplete="new-password"
                           class="form-control con-boton" placeholder="Repite tu contraseña" required>
                    <button type="button" class="btn btn-ver ver-clave" data-target="password_confirmation" title="Mostrar u ocultar"><i class="bi bi-eye"></i></button>
                </div>
            </div>

            <button class="btn btn-success w-100 login-btn"><i class="bi bi-person-plus me-1"></i>Crear mi cuenta</button>
        </form>

        <p class="text-center small mt-3 mb-0">
            ¿Ya tienes cuenta?
            <a href="{{ route('login') }}" class="fw-bold text-decoration-none">Inicia sesión</a>
        </p>

@push('scripts')
<script>
    document.querySelectorAll('.ver-clave').forEach(function (boton) {
        boton.addEventListener('click', function () {
            const campo = document.getElementById(this.dataset.target);
            const ver = campo.type === 'password';
            campo.type = ver ? 'text' : 'password';
            this.innerHTML = ver ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
        });
    });
</script>
@endpush
</x-auth-layout>
