<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('codigo') - KUSI</title>
    <link rel="icon" type="image/png" href="{{ asset('img/favicon.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/kusi.css') }}" rel="stylesheet">
</head>
<body class="error-body d-flex align-items-center justify-content-center p-3">
    <div class="card error-card shadow-lg text-center">
        <div class="card-body p-4 p-md-5">
            <img src="{{ asset('img/logo-icono.png') }}" alt="KUSI" height="86" class="mb-3">
            <div class="codigo">@yield('codigo')</div>
            <h1 class="h4 mt-2">@yield('titulo')</h1>
            <p class="text-muted">@yield('mensaje')</p>
            <div class="d-flex gap-2 justify-content-center">
                <a href="{{ url('/') }}" class="btn btn-success"><i class="bi bi-house me-1"></i>Ir al inicio</a>
                <a href="javascript:history.back()" class="btn btn-outline-success">Volver</a>
            </div>
        </div>
    </div>
</body>
</html>
