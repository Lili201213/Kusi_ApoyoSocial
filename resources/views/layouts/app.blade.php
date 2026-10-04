<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titulo', 'Inicio') - KUSI</title>
    <link rel="icon" type="image/png" href="{{ asset('img/favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800&family=Pacifico&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/kusi.css') }}?v={{ filemtime(public_path('css/kusi.css')) }}" rel="stylesheet">
    @stack('estilos')
</head>
<body>
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
            ['Ayudas entregadas', 'bi-box2-heart', route('ayudas.index'), 'historial.ver'],
            ['Alimentos / almuerzos', 'bi-egg-fried', route('alimentos.index'), 'historial.ver'],
            ['Tipos de ayuda', 'bi-tags', route('tipos_ayuda.index'), 'tipos_ayuda.crear'],
        ],
        'Donaciones e inventario' => [
            ['Donaciones', 'bi-gift', route('donaciones.index'), 'donaciones.ver'],
            ['Donantes', 'bi-person-heart', route('donantes.index'), 'donaciones.ver'],
            ['Inventario', 'bi-boxes', route('productos.index'), 'inventario.ver'],
            ['Movimientos', 'bi-arrow-left-right', route('movimientos.index'), 'inventario.ver'],
            ['Categorías', 'bi-tags', route('categorias.index'), 'productos.crear'],
        ],
        'Análisis' => [
            ['Reportes', 'bi-file-earmark-text', route('reportes.index'), 'reportes.generar'],
            ['Estadísticas', 'bi-bar-chart', route('estadisticas.index'), 'estadisticas.ver'],
        ],
        'Administración' => [
            ['Usuarios', 'bi-person-gear', route('usuarios.index'), 'usuarios.gestionar'],
            ['Roles y permisos', 'bi-shield-lock', route('roles.index'), 'roles.gestionar'],
            ['Copias de seguridad', 'bi-database', route('respaldos.index'), 'usuarios.gestionar'],
        ],
    ];
@endphp

<nav class="navbar kusi-topbar {{ $fijo ? 'd-md-none' : '' }}">
    <div class="container-fluid">
        <button class="btn btn-outline-light me-2" data-bs-toggle="offcanvas" data-bs-target="#menuLateral" aria-label="Abrir menú">
            <i class="bi bi-list fs-5"></i>
        </button>
        <a href="{{ route('dashboard') }}" class="d-flex align-items-center gap-2 text-decoration-none me-auto">
            <span class="brand-badge"><img src="{{ asset('img/logo-icono.png') }}" alt="KUSI"></span>
            <span class="brand-name">Kusi</span>
        </a>
        <span class="text-white-50 small d-none d-sm-inline">{{ $u->name }}</span>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">
        <div class="{{ $fijo ? 'offcanvas-md offcanvas-start col-md-3 col-lg-2 sidebar-fijo' : 'offcanvas offcanvas-start' }} sidebar p-3" id="menuLateral" tabindex="-1">
            <div class="sidebar-inner d-flex flex-column">
                <div class="d-flex justify-content-between align-items-start mb-1">
                    <a href="{{ route('dashboard') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                        <span class="brand-badge"><img src="{{ asset('img/logo-icono.png') }}" alt="KUSI"></span>
                        <span>
                            <span class="brand-name d-block">Kusi</span>
                            <span class="brand-sub d-block">Sociedad de Apoyo Social</span>
                        </span>
                    </a>
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
                                        <i class="bi {{ $icono }}"></i><span>{{ $texto }}</span>
                                    </a>
                                </li>
                            @endforeach
                        @endif
                    @endforeach
                </ul>

                <div class="mt-auto pt-4">
                    <div class="user-card d-flex align-items-center gap-2 mb-2">
                        <span class="avatar">{{ mb_strtoupper(mb_substr($u->name, 0, 1)) }}</span>
                        <div class="lh-sm overflow-hidden">
                            <a href="{{ route('cuenta.edit') }}" title="Mi cuenta" class="d-block text-truncate">{{ $u->name }}</a>
                            <span class="rol">{{ $u->rol->nombre ?? 'Sin rol' }}</span>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn btn-outline-light btn-sm w-100"><i class="bi bi-box-arrow-right me-1"></i>Cerrar sesión</button>
                    </form>
                </div>
            </div>
        </div>

        <main class="{{ $fijo ? 'col-md-9 col-lg-10' : 'col-12' }} p-3 p-md-4 kusi-main">
            @if (session('exito'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm"><i class="bi bi-check-circle me-1"></i>{{ session('exito') }}
                    <button class="btn-close" data-bs-dismiss="alert"></button></div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm"><i class="bi bi-exclamation-circle me-1"></i>{{ session('error') }}
                    <button class="btn-close" data-bs-dismiss="alert"></button></div>
            @endif
            @yield('contenido')
            <div class="kusi-footer no-print">© {{ date('Y') }} Sociedad de Apoyo Social KUSI · Hecho con cariño para ayudar</div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
