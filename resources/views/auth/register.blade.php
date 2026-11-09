<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Crear cuenta - KUSI</title>
    <link rel="icon" type="image/png" href="{{ asset('img/favicon.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800&family=Pacifico&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/kusi.css') }}?v={{ filemtime(public_path('css/kusi.css')) }}" rel="stylesheet">
</head>
<body class="login-body">
<div class="login-wrap">

    <section class="login-hero">
        <div class="hero-logo-card"><img src="{{ asset('img/logo.png') }}" alt="KUSI - Sistema de Gestión de Apoyo Social"></div>
        <h2>Sociedad de Apoyo Social KUSI</h2>
        <p>Cada registro es una persona. Juntos llevamos esperanza a quienes más lo necesitan.</p>
    </section>

    <section class="login-side">
        <div class="login-card">
            <img src="{{ asset('img/logo-icono.png') }}" alt="KUSI" height="72" class="mb-2 d-lg-none">
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

            <p class="text-center text-muted small mt-4 mb-0">© {{ date('Y') }} Sociedad de Apoyo Social KUSI</p>
        </div>
    </section>
</div>

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
</body>
</html>
