@extends('layouts.app')
@section('titulo', 'Inicio')

@section('contenido')
@php
    $u = auth()->user();
    $fmt = fn ($n) => \App\Services\InventarioService::fmt($n);

    // [etiqueta, valor, icono, color, enlace]
    $tarjetas = [];
    if ($puede['benef']) {
        $tarjetas[] = ['Beneficiarios activos', $kpi['benef'], 'bi-people', 'morado', route('beneficiarios.index', ['estado' => 'activo'])];
    }
    if ($puede['hist']) {
        $tarjetas[] = ['Ayudas este mes', $kpi['ayudasMes'], 'bi-box2-heart', 'lavanda', route('ayudas.index')];
        $tarjetas[] = ['Raciones este mes', $kpi['racionesMes'], 'bi-egg-fried', 'amarillo', route('alimentos.index')];
    }
    if ($puede['don']) {
        $tarjetas[] = ['Donaciones este mes', $kpi['donMes'], 'bi-gift', 'verde', route('donaciones.index')];
    }
    if ($puede['inv']) {
        $tarjetas[] = ['Productos en inventario', $kpi['productos'], 'bi-boxes', 'morado', route('productos.index')];
        $tarjetas[] = ['Con stock bajo', $kpi['stockBajo'], 'bi-exclamation-triangle', $kpi['stockBajo'] > 0 ? 'rojo' : 'verde', route('productos.index', ['filtro' => 'bajo'])];
    }

    // Accesos rápidos según permisos: [texto, icono, ruta, permiso]
    $accesos = collect([
        ['Nuevo beneficiario', 'bi-person-plus', route('beneficiarios.create'), 'beneficiarios.crear'],
        ['Registrar ayuda', 'bi-box2-heart', route('ayudas.create'), 'ayudas.crear'],
        ['Registrar alimentos', 'bi-egg-fried', route('alimentos.create'), 'alimentos.crear'],
        ['Registrar donación', 'bi-gift', route('donaciones.create'), 'donaciones.crear'],
        ['Entrada de producto', 'bi-box-arrow-in-down', route('movimientos.entrada'), 'inventario.entrada'],
        ['Salida de producto', 'bi-box-arrow-up', route('movimientos.salida'), 'inventario.salida'],
        ['Ver reportes', 'bi-file-earmark-text', route('reportes.index'), 'reportes.generar'],
        ['Ver estadísticas', 'bi-bar-chart', route('estadisticas.index'), 'estadisticas.ver'],
    ])->filter(fn ($a) => $u->tienePermiso($a[3]));
@endphp

{{-- Bienvenida --}}
<div class="hero-dash mb-4">
    <div>
        <div class="hero-fecha"><i class="bi bi-calendar3 me-1"></i>{{ $fecha }}</div>
        <h1>{{ $saludo }}, {{ $primerNombre }}</h1>
        <div class="hero-rol">{{ $u->rol->nombre }} · Sociedad de Apoyo Social KUSI</div>
        <p class="hero-frase">Gracias por acompañar a quienes más lo necesitan. Cada registro cuenta.</p>
    </div>
    <div class="hero-img-wrap"><img src="{{ asset('img/logo-icono.png') }}" alt="KUSI"></div>
</div>

{{-- Indicadores --}}
@if (count($tarjetas))
<div class="row row-cols-2 row-cols-lg-3 row-cols-xxl-6 g-3 mb-4">
    @foreach ($tarjetas as [$etq, $valor, $icono, $color, $enlace])
        <div class="col">
            <a href="{{ $enlace }}" class="kpi-card">
                <div class="kpi-icon {{ $color }}"><i class="bi {{ $icono }}"></i></div>
                <div class="kpi-num">{{ $valor }}</div>
                <div class="kpi-label">{{ $etq }}</div>
            </a>
        </div>
    @endforeach
</div>
@endif

{{-- Accesos rápidos --}}
@if ($accesos->isNotEmpty())
<div class="card shadow-sm mb-4">
    <div class="card-header"><i class="bi bi-lightning-charge-fill text-warning me-1"></i>Accesos rápidos</div>
    <div class="card-body">
        <div class="row row-cols-2 row-cols-sm-4 row-cols-xl-4 g-2">
            @foreach ($accesos as [$texto, $icono, $ruta])
                <div class="col"><a href="{{ $ruta }}" class="tile"><i class="bi {{ $icono }}"></i>{{ $texto }}</a></div>
            @endforeach
        </div>
    </div>
</div>
@endif

<div class="row g-4">
    {{-- Columna izquierda --}}
    <div class="col-xl-7">
        @if ($serie)
        <div class="card shadow-sm mb-4">
            <div class="card-header"><i class="bi bi-graph-up-arrow me-1"></i>Ayudas entregadas · últimos 6 meses</div>
            <div class="card-body"><canvas id="gInicio" height="105"></canvas></div>
        </div>
        @endif

        @if ($puede['hist'])
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-clock-history me-1"></i>Últimas ayudas entregadas</span>
                <a href="{{ route('ayudas.index') }}" class="small fw-bold text-decoration-none">Ver todas</a>
            </div>
            @forelse ($ultAyudas as $a)
                <div class="lista-item">
                    <span class="ini">{{ mb_strtoupper(mb_substr($a->beneficiario->nombres, 0, 1).mb_substr($a->beneficiario->apellidos, 0, 1)) }}</span>
                    <div class="flex-grow-1">
                        <div class="t">{{ $a->beneficiario->nombres }} {{ $a->beneficiario->apellidos }}</div>
                        <div class="s">{{ $a->tipoAyuda->nombre }}{{ $a->descripcion ? ' · '.$a->descripcion : '' }}</div>
                    </div>
                    <span class="s text-nowrap">{{ $a->fecha_entrega->format('d/m/Y') }}</span>
                </div>
            @empty
                <div class="vacio"><i class="bi bi-box2-heart"></i>Aún no hay ayudas registradas.</div>
            @endforelse
        </div>
        @endif
    </div>

    {{-- Columna derecha --}}
    <div class="col-xl-5">
        @if ($puede['benef'])
        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-hourglass-split me-1"></i>Necesidades por atender</span>
                <span class="badge text-bg-warning">{{ $kpi['pendientes'] }}</span>
            </div>
            @forelse ($necesidades as $n)
                <a href="{{ route('beneficiarios.show', $n->beneficiario_id) }}" class="lista-item text-decoration-none text-reset">
                    <span class="ini">{{ mb_strtoupper(mb_substr($n->beneficiario->nombres, 0, 1).mb_substr($n->beneficiario->apellidos, 0, 1)) }}</span>
                    <div class="flex-grow-1">
                        <div class="t">{{ $n->beneficiario->nombres }} {{ $n->beneficiario->apellidos }}</div>
                        <div class="s">Necesita: {{ $n->tipoAyuda->nombre }}</div>
                    </div>
                    <span class="s text-nowrap">{{ $n->fecha_registro->format('d/m/Y') }}</span>
                </a>
            @empty
                <div class="vacio"><i class="bi bi-emoji-smile"></i>No hay necesidades pendientes. ¡Buen trabajo!</div>
            @endforelse
        </div>
        @endif

        @if ($puede['inv'])
        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-exclamation-triangle me-1"></i>Productos con stock bajo</span>
                <a href="{{ route('productos.index', ['filtro' => 'bajo']) }}" class="small fw-bold text-decoration-none">Ver</a>
            </div>
            @forelse ($stockBajo as $p)
                <a href="{{ route('productos.show', $p) }}" class="lista-item text-decoration-none text-reset">
                    <span class="ini" style="background:#FDE7EA;color:#D6445B"><i class="bi bi-box-seam"></i></span>
                    <div class="flex-grow-1"><div class="t">{{ $p->nombre }}</div><div class="s">Mínimo: {{ $fmt($p->stock_minimo) }} {{ $p->unidad_medida }}</div></div>
                    <span class="badge text-bg-danger">{{ $fmt($p->stock_actual) }} {{ $p->unidad_medida }}</span>
                </a>
            @empty
                <div class="vacio"><i class="bi bi-check2-circle"></i>Todo el inventario está en niveles normales.</div>
            @endforelse
        </div>
        @endif

        @if ($puede['don'])
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-gift me-1"></i>Últimas donaciones</span>
                <a href="{{ route('donaciones.index') }}" class="small fw-bold text-decoration-none">Ver todas</a>
            </div>
            @forelse ($ultDonaciones as $d)
                <a href="{{ route('donaciones.show', $d) }}" class="lista-item text-decoration-none text-reset">
                    <span class="ini" style="background:#E3F6EE;color:#1E8E63"><i class="bi bi-gift"></i></span>
                    <div class="flex-grow-1">
                        <div class="t">{{ $d->donante->nombre_razon_social }}</div>
                        <div class="s">{{ $d->detalles_count }} producto(s){{ $d->estaAnulada() ? ' · Anulada' : '' }}</div>
                    </div>
                    <span class="s text-nowrap">{{ $d->fecha_donacion->format('d/m/Y') }}</span>
                </a>
            @empty
                <div class="vacio"><i class="bi bi-gift"></i>Aún no hay donaciones registradas.</div>
            @endforelse
        </div>
        @endif
    </div>
</div>
@endsection

@if ($serie)
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
    const s = @json($serie);
    const canvas = document.getElementById('gInicio');
    const grad = canvas.getContext('2d').createLinearGradient(0, 0, 0, 260);
    grad.addColorStop(0, '#8B5CC9'); grad.addColorStop(1, '#C9A6F2');
    new Chart(canvas, {
        type: 'bar',
        data: { labels: s.etiquetas, datasets: [{ data: s.valores, backgroundColor: grad, borderRadius: 10, maxBarThickness: 46 }] },
        options: { plugins: { legend: { display: false } },
                   scales: { y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#F0E8FA' } }, x: { grid: { display: false } } } }
    });
</script>
@endpush
@endif
