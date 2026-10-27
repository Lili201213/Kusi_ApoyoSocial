<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titulo') - KUSI</title>
    <link rel="icon" type="image/png" href="{{ asset('img/favicon.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800&family=Pacifico&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/kusi.css') }}?v={{ filemtime(public_path('css/kusi.css')) }}" rel="stylesheet">
</head>
<body class="login-body">
<div class="login-wrap">
    <section class="login-hero">
        <div class="hero-logo-card"><img src="{{ asset('img/logo.png') }}" alt="KUSI"></div>
        <h2>Sociedad de Apoyo Social KUSI</h2>
        <p>Cada registro es una persona. Juntos llevamos esperanza a quienes más lo necesitan.</p>
    </section>
    <section class="login-side">
        <div class="login-card">
            <img src="{{ asset('img/logo-icono.png') }}" alt="KUSI" height="72" class="mb-2 d-lg-none">
            @yield('formulario')
            <p class="text-center text-muted small mt-4 mb-0">© {{ date('Y') }} Sociedad de Apoyo Social KUSI</p>
        </div>
    </section>
</div>
@stack('scripts')
</body>
</html>
