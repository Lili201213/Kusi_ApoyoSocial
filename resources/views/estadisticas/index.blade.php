@extends('layouts.app')
@section('titulo', 'Estadísticas')

@section('contenido')
@php $fmt = fn ($n) => \App\Services\InventarioService::fmt($n); @endphp
<h2 class="mb-3">Estadísticas</h2>

<div class="row g-3 mb-4">
    @foreach ([
        ['Beneficiarios activos', $kpis['benefActivos'], 'bi-people', 'text-success'],
        ['Ayudas este mes', $kpis['ayudasMes'], 'bi-box2-heart', 'text-primary'],
        ['Raciones este mes', $kpis['racionesMes'], 'bi-egg-fried', 'text-warning'],
        ['Donaciones este mes', $kpis['donacionesMes'], 'bi-gift', 'text-info'],
        ['Productos activos', $kpis['productos'], 'bi-boxes', 'text-secondary'],
        ['Con stock bajo', $kpis['stockBajo'], 'bi-exclamation-triangle', $kpis['stockBajo'] > 0 ? 'text-danger' : 'text-success'],
        ['Ayudas entregadas (total)', $kpis['ayudasTotal'], 'bi-clipboard-check', 'text-dark'],
        ['Beneficiarios inactivos', $kpis['benefInactivos'], 'bi-person-dash', 'text-muted'],
    ] as [$etq, $val, $ico, $color])
        <div class="col-6 col-lg-3"><div class="card shadow-sm h-100"><div class="card-body py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div><div class="text-muted small">{{ $etq }}</div><div class="fs-3 fw-bold">{{ $val }}</div></div>
                <i class="bi {{ $ico }} fs-2 {{ $color }}"></i>
            </div>
        </div></div></div>
    @endforeach
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-8"><div class="card shadow-sm h-100"><div class="card-header fw-semibold">Ayudas entregadas por mes</div>
        <div class="card-body"><canvas id="gAyudas" height="110"></canvas></div></div></div>
    <div class="col-lg-4"><div class="card shadow-sm h-100"><div class="card-header fw-semibold">Ayudas por tipo</div>
        <div class="card-body"><canvas id="gTipos"></canvas></div></div></div>

    <div class="col-lg-6"><div class="card shadow-sm h-100"><div class="card-header fw-semibold">Raciones de alimentos por mes</div>
        <div class="card-body"><canvas id="gRaciones" height="130"></canvas></div></div></div>
    <div class="col-lg-6"><div class="card shadow-sm h-100"><div class="card-header fw-semibold">Donaciones registradas por mes</div>
        <div class="card-body"><canvas id="gDonaciones" height="130"></canvas></div></div></div>

    <div class="col-lg-8"><div class="card shadow-sm h-100"><div class="card-header fw-semibold">Nuevos beneficiarios por mes</div>
        <div class="card-body"><canvas id="gBenef" height="110"></canvas></div></div></div>
    <div class="col-lg-4"><div class="card shadow-sm h-100"><div class="card-header fw-semibold">Beneficiarios por sexo</div>
        <div class="card-body"><canvas id="gSexo"></canvas></div></div></div>
</div>

<div class="row g-3">
    <div class="col-lg-4"><div class="card shadow-sm h-100"><div class="card-header fw-semibold">Productos con stock bajo</div>
        <ul class="list-group list-group-flush">
            @forelse ($stockBajo as $p)
                <li class="list-group-item d-flex justify-content-between">
                    <a href="{{ route('productos.show', $p) }}" class="text-decoration-none">{{ $p->nombre }}</a>
                    <span class="badge text-bg-danger">{{ $fmt($p->stock_actual) }} {{ $p->unidad_medida }}</span>
                </li>
            @empty
                <li class="list-group-item text-muted">Todo el stock está en niveles normales.</li>
            @endforelse
        </ul></div></div>
    <div class="col-lg-4"><div class="card shadow-sm h-100"><div class="card-header fw-semibold">Productos más donados</div>
        <ul class="list-group list-group-flush">
            @forelse ($masDonados as $p)
                <li class="list-group-item d-flex justify-content-between">{{ $p->nombre }}<span class="text-muted">{{ $fmt($p->total) }} {{ $p->unidad }}</span></li>
            @empty
                <li class="list-group-item text-muted">Aún no hay donaciones.</li>
            @endforelse
        </ul></div></div>
    <div class="col-lg-4"><div class="card shadow-sm h-100"><div class="card-header fw-semibold">Donantes más frecuentes</div>
        <ul class="list-group list-group-flush">
            @forelse ($topDonantes as $d)
                <li class="list-group-item d-flex justify-content-between">{{ $d->nombre }}<span class="badge text-bg-success">{{ $d->total }}</span></li>
            @empty
                <li class="list-group-item text-muted">Aún no hay donaciones.</li>
            @endforelse
        </ul></div></div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
    const d = @json($datos);
    const verde = '#8B5CC9', verdeOscuro = '#6A3FA3';
    const paleta = ['#6A3FA3', '#B27EEA', '#FBC541', '#4a90d9', '#e8708a', '#2e9c81', '#8d8d99'];
    const enteros = { y: { beginAtZero: true, ticks: { precision: 0 } } };

    function barras(id, datos, color) {
        new Chart(document.getElementById(id), { type: 'bar',
            data: { labels: d.meses, datasets: [{ data: datos, backgroundColor: color, borderRadius: 4 }] },
            options: { plugins: { legend: { display: false } }, scales: enteros } });
    }
    function linea(id, datos, color) {
        new Chart(document.getElementById(id), { type: 'line',
            data: { labels: d.meses, datasets: [{ data: datos, borderColor: color, backgroundColor: color + '33', fill: true, tension: .3 }] },
            options: { plugins: { legend: { display: false } }, scales: enteros } });
    }
    function dona(id, nombres, totales) {
        new Chart(document.getElementById(id), { type: 'doughnut',
            data: { labels: nombres, datasets: [{ data: totales, backgroundColor: paleta }] },
            options: { plugins: { legend: { position: 'bottom' } } } });
    }

    barras('gAyudas', d.ayudas, verde);
    barras('gRaciones', d.raciones, '#FBC541');
    linea('gDonaciones', d.donaciones, '#B27EEA');
    linea('gBenef', d.beneficiarios, verdeOscuro);
    dona('gTipos', d.tipoNombres, d.tipoTotales);
    dona('gSexo', d.sexoNombres, d.sexoTotales);
</script>
@endpush
