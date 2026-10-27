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
    <style>
        /* Menú de perfil (arriba a la derecha) */
        .perfil-toggle { background: transparent; border: 0; color: #fff; display: flex; align-items: center; gap: .6rem; padding: .25rem .5rem; border-radius: 14px; }
        .perfil-toggle:hover, .perfil-toggle.show { background: rgba(255, 255, 255, .12); color: #fff; }
        .topbar-avatar { width: 40px; height: 40px; border-radius: 50%; background: #b388eb; color: #fff; font-weight: 800;
                         display: inline-flex; align-items: center; justify-content: center; overflow: hidden; flex-shrink: 0; }
        .topbar-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .perfil-datos { line-height: 1.15; text-align: left; }
        .perfil-datos .nombre { font-weight: 700; font-size: .95rem; display: block; }
        .perfil-datos .rol { font-size: .75rem; color: rgba(255, 255, 255, .7); display: block; }
        .perfil-menu { border: 0; border-radius: 16px; box-shadow: 0 12px 30px rgba(46, 26, 84, .25); padding: .5rem; min-width: 230px; }
        .perfil-menu .dropdown-item { border-radius: 10px; padding: .55rem .8rem; font-weight: 600; color: #4b2a85; }
        .perfil-menu .dropdown-item:hover { background: #efe7fa; color: #4b2a85; }
        .perfil-menu .dropdown-item.salir { color: #b3261e; }
        .perfil-menu .dropdown-item.salir:hover { background: #fdecea; color: #b3261e; }
        .perfil-menu .cabecera { padding: .5rem .8rem .6rem; border-bottom: 1px solid #eee; margin-bottom: .4rem; }
        .perfil-menu .cabecera .nombre { font-weight: 800; color: #4b2a85; }
        .perfil-menu .cabecera .correo { font-size: .8rem; color: #777; word-break: break-all; }
    </style>
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
        ['Tipos de ayuda', 'bi-list-check', route('tipos_ayuda.index'), 'tipos_ayuda.crear'],
    ],
    'Donaciones e inventario' => [
        ['Donantes', 'bi-person-heart', route('donantes.index'), 'donaciones.ver'],
        ['Donaciones', 'bi-gift', route('donaciones.index'), 'donaciones.ver'],
        ['Inventario', 'bi-boxes', route('productos.index'), 'inventario.ver'],
        ['Movimientos', 'bi-arrow-left-right', route('movimientos.index'), 'inventario.ver'],
        ['Categorías', 'bi-tags', route('categorias.index'), 'productos.crear'],
    ],
    'Análisis' => [
        ['Estadísticas', 'bi-bar-chart', route('estadisticas.index'), 'estadisticas.ver'],
        ['Reportes', 'bi-file-earmark-text', route('reportes.index'), 'reportes.generar'],
    ],
    'Administración' => [
        ['Usuarios', 'bi-person-gear', route('usuarios.index'), 'usuarios.gestionar'],
        ['Roles y permisos', 'bi-shield-lock', route('roles.index'), 'roles.gestionar'],
        ['Copias de seguridad', 'bi-database', route('respaldos.index'), 'usuarios.gestionar'],
    ],
];
@endphp

<nav class="navbar kusi-topbar">
    <div class="container-fluid">
        <button class="btn btn-outline-light me-2 {{ $fijo ? 'd-md-none' : '' }}" data-bs-toggle="offcanvas" data-bs-target="#menuLateral" aria-label="Abrir menú">
            <i class="bi bi-list fs-5"></i>
        </button>
        <a href="{{ route('dashboard') }}" class="align-items-center gap-2 text-decoration-none {{ $fijo ? 'd-flex d-md-none' : 'd-flex' }}">
            <span class="brand-badge"><img src="{{ asset('img/logo-icono.png') }}" alt="KUSI"></span>
            <span class="brand-name">Kusi</span>
        </a>

        {{-- Perfil arriba a la derecha --}}
        <div class="dropdown ms-auto">
            <button class="perfil-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Menú de perfil">
                <span class="topbar-avatar">@if ($u->foto_url)<img src="{{ $u->foto_url }}" alt="">@else{{ $u->inicial }}@endif</span>
                <span class="perfil-datos d-none d-sm-block">
                    <span class="nombre">{{ $u->name }}</span>
                    <span class="rol">{{ $u->rol->nombre ?? 'Sin rol' }}</span>
                </span>
                <i class="bi bi-chevron-down small"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end perfil-menu">
                <li class="cabecera">
                    <div class="nombre">{{ $u->name }}</div>
                    <div class="correo">{{ $u->email }}</div>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('perfil.edit') }}"><i class="bi bi-person-circle me-2"></i>Mi perfil</a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item salir"><i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión</button>
                    </form>
                </li>
            </ul>
        </div>
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
