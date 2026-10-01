<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titulo', 'Inicio') - KUSI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .sidebar { background-color: #1f6f5c !important; }
        .sidebar-fijo { min-height: 100vh; }
        @media (min-width: 768px) { .sidebar-fijo { position: sticky; top: 0; height: 100vh; overflow-y: auto; } }
        .offcanvas.sidebar { width: 280px; }
        .sidebar .nav-link { color: #d7efe8; border-radius: 8px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: rgba(255,255,255,.15); color: #fff; }
        .sidebar .seccion { color: #9fd3c4; font-size: .75rem; text-transform: uppercase; margin: 1rem 0 .3rem .5rem; }
    </style>
    @stack('estilos')
</head>
<body class="bg-light">
@php
    $u = auth()->user();
    $fijo = request()->routeIs('dashboard'); // menú fijo solo en Inicio
    // [texto, icono, ruta o '#', permiso requerido]
    // Las rutas '#' se irán activando conforme construyamos cada módulo.
    $menu = [
        'General' => [
            ['Inicio', 'bi-speedometer2', route('dashboard'), null],
        ],
        'Beneficiarios' => [
            ['Beneficiarios', 'bi-people', route('beneficiarios.index'), 'beneficiarios.ver'],
            ['Ayudas entregadas', 'bi-box2-heart', '#', 'historial.ver'],
            ['Alimentos / almuerzos', 'bi-egg-fried', '#', 'alimentos.crear'],
        ],
        'Donaciones e inventario' => [
            ['Donaciones', 'bi-gift', '#', 'donaciones.ver'],
            ['Inventario', 'bi-boxes', '#', 'inventario.ver'],
        ],
        'Análisis' => [
            ['Reportes', 'bi-file-earmark-text', '#', 'reportes.generar'],
            ['Estadísticas', 'bi-bar-chart', '#', 'estadisticas.ver'],
        ],
        'Administración' => [
            ['Usuarios', 'bi-person-gear', route('usuarios.index'), 'usuarios.gestionar'],
            ['Roles y permisos', 'bi-shield-lock', '#', 'roles.gestionar'],
        ],
    ];
@endphp

<nav class="navbar navbar-dark {{ $fijo ? 'd-md-none' : '' }}" style="background:#1f6f5c">
    <div class="container-fluid">
        <button class="btn btn-outline-light me-2" data-bs-toggle="offcanvas" data-bs-target="#menuLateral" aria-label="Abrir menú">
            <i class="bi bi-list fs-5"></i>
        </button>
        <span class="navbar-brand fw-bold me-auto">KUSI</span>
        <span class="text-white-50 small d-none d-sm-inline">{{ $u->name }}</span>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">
        <div class="{{ $fijo ? 'offcanvas-md offcanvas-start col-md-3 col-lg-2 sidebar-fijo' : 'offcanvas offcanvas-start' }} sidebar p-3" id="menuLateral" tabindex="-1">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h1 class="h4 text-white fw-bold mb-0">KUSI</h1>
                    <small class="text-white-50 d-block mb-2">Ayuda Social</small>
                </div>
                <button type="button" class="btn-close btn-close-white {{ $fijo ? 'd-md-none' : '' }}" data-bs-dismiss="offcanvas" data-bs-target="#menuLateral" aria-label="Cerrar"></button>
            </div>

            <ul class="nav flex-column">
                @foreach ($menu as $seccion => $items)
                    @php $visibles = collect($items)->filter(fn ($i) => $i[3] === null || $u->tienePermiso($i[3])); @endphp
                    @if ($visibles->isNotEmpty())
                        <li class="seccion">{{ $seccion }}</li>
                        @foreach ($visibles as [$texto, $icono, $ruta])
                            <li class="nav-item">
                                <a class="nav-link {{ $ruta !== '#' && str_starts_with(url()->current(), $ruta) ? 'active' : '' }}" href="{{ $ruta }}">
                                    <i class="bi {{ $icono }} me-2"></i>{{ $texto }}
                                </a>
                            </li>
                        @endforeach
                    @endif
                @endforeach
            </ul>

            <hr class="border-secondary">
            <div class="text-white small mb-2">
                <i class="bi bi-person-circle me-1"></i>{{ $u->name }}<br>
                <span class="text-white-50">{{ $u->rol->nombre ?? 'Sin rol' }}</span>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn btn-outline-light btn-sm w-100"><i class="bi bi-box-arrow-right me-1"></i>Cerrar sesión</button>
            </form>
        </div>

        <main class="{{ $fijo ? 'col-md-9 col-lg-10' : 'col-12' }} p-4">
            @if (session('exito'))
                <div class="alert alert-success alert-dismissible fade show">{{ session('exito') }}
                    <button class="btn-close" data-bs-dismiss="alert"></button></div>
            @endif
            @yield('contenido')
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
