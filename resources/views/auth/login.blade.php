<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>KUSI - Iniciar sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #1f6f5c, #2e9c81); min-height: 100vh; }
        .login-card { max-width: 400px; border: 0; border-radius: 14px; }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center p-3">
    <div class="card login-card shadow-lg w-100">
        <div class="card-body p-4">
            <h1 class="h3 text-center fw-bold mb-1">KUSI</h1>
            <p class="text-center text-muted mb-4">Sistema de Gestión de Ayuda Social</p>

            <form method="POST" action="{{ route('login.post') }}">
                @csrf
               <div class="mb-3">
    <label for="usuario" class="form-label">Usuario</label>
    <input type="text" name="usuario" id="usuario" value="{{ old('usuario') }}"
           class="form-control @error('usuario') is-invalid @enderror"
           autocomplete="username" required autofocus>
    @error('usuario') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" name="password" id="password"
                           class="form-control @error('password') is-invalid @enderror" required>
                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label" for="remember">Recordarme</label>
                </div>
                <button class="btn btn-success w-100">Ingresar</button>
            </form>
        </div>
    </div>
</body>
</html>
