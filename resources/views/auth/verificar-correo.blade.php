<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verifica tu correo - KUSI</title>
    <style>
        body { margin:0; min-height:100vh; display:flex; align-items:center; justify-content:center;
               background:linear-gradient(135deg,#4b2a85,#7c4dbd); font-family:'Nunito',Arial,sans-serif; }
        .card { background:#fff; border-radius:24px; padding:40px; max-width:440px; width:90%;
                box-shadow:0 20px 50px rgba(0,0,0,.25); text-align:center; }
        h2 { color:#4b2a85; margin-top:0; }
        p { color:#555; line-height:1.5; }
        .ok { background:#e8f5e9; color:#2e7d32; padding:10px; border-radius:10px; margin-bottom:16px; }
        button { width:100%; padding:14px; border:0; border-radius:12px; background:#5b2fa0;
                 color:#fff; font-size:16px; font-weight:700; cursor:pointer; }
        .link { background:none; color:#5b2fa0; margin-top:10px; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Verifica tu correo electrónico</h2>
        <p>Te enviamos un enlace de confirmación a <strong>{{ auth()->user()->email }}</strong>.
           Revisa tu bandeja de entrada (y spam) y haz clic en el enlace.</p>

        @if (session('status') === 'enlace-enviado')
            <div class="ok">Te enviamos un nuevo enlace.</div>
        @endif

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit">Reenviar enlace</button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="link">Cerrar sesión</button>
        </form>
    </div>
</body>
</html>
