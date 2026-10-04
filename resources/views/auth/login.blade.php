<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar sesión - KUSI</title>
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
            <h1>¡Bienvenido/a!</h1>
            <p class="text-muted mb-4">Ingresa con tu usuario y contraseña para continuar.</p>

            <form method="POST" action="{{ route('login.post') }}">
                @csrf
                <div class="mb-3">
                    <label for="usuario" class="form-label">Usuario</label>
                    <div class="input-group has-validation">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="text" name="usuario" id="usuario" value="{{ old('usuario') }}" autocomplete="username"
                               class="form-control @error('usuario') is-invalid @enderror" placeholder="Tu usuario" required autofocus>
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

                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label" for="remember">Mantener mi sesión iniciada</label>
                </div>

                <button class="btn btn-success w-100 login-btn"><i class="bi bi-box-arrow-in-right me-1"></i>Ingresar</button>
            </form>

            <p class="text-center text-muted small mt-4 mb-0">© {{ date('Y') }} Sociedad de Apoyo Social KUSI</p>
        </div>
    </section>
</div>

<script>
    document.getElementById('verClave').addEventListener('click', function () {
        const campo = document.getElementById('password');
        const ver = campo.type === 'password';
        campo.type = ver ? 'text' : 'password';
        this.innerHTML = ver ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
    });
</script>
</body>
</html>
