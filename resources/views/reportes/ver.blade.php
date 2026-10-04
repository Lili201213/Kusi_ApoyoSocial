@extends('layouts.app')
@section('titulo', $titulo)

@push('estilos')
<style>
    .solo-print { display: none; }
    @media print {
        .sidebar, nav.navbar, .no-print, .alert { display: none !important; }
        main { flex: 0 0 100% !important; max-width: 100% !important; width: 100% !important; padding: 0 !important; }
        body { background: #fff !important; }
        .solo-print { display: block !important; }
        .table { font-size: 11px; }
        thead { display: table-header-group; }
        tr { page-break-inside: avoid; }
    }
</style>
@endpush

@section('contenido')
@php
    $q = request()->getQueryString();
    $tiene = fn ($c) => in_array($c, $campos, true);
@endphp

<div class="no-print d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
    <div>
        <a href="{{ route('reportes.index') }}" class="text-decoration-none small"><i class="bi bi-arrow-left"></i> Reportes</a>
        <h2 class="mb-0">{{ $titulo }}</h2>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <button type="button" onclick="window.print()" class="btn btn-outline-secondary"><i class="bi bi-printer me-1"></i>Imprimir / PDF</button>
        <a href="{{ route('reportes.exportar', $tipo) }}{{ $q ? '?'.$q : '' }}" class="btn btn-success"><i class="bi bi-file-earmark-excel me-1"></i>Exportar a Excel</a>
    </div>
</div>

<form method="GET" class="no-print row g-2 mb-3">
    @if ($tiene('q'))
        <div class="col-12 col-md-3"><input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="DNI o nombre"></div>
    @endif
    @if ($tiene('q_donante'))
        <div class="col-12 col-md-3"><input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Donante"></div>
    @endif
    @if ($tiene('estado'))
        <div class="col-6 col-md-2"><select name="estado" class="form-select">
            <option value="">Todos los estados</option>
            <option value="activo" @selected(request('estado') === 'activo')>Activos</option>
            <option value="inactivo" @selected(request('estado') === 'inactivo')>Inactivos</option></select></div>
    @endif
    @if ($tiene('sexo'))
        <div class="col-6 col-md-2"><select name="sexo" class="form-select">
            <option value="">Todos los sexos</option>
            <option value="M" @selected(request('sexo') === 'M')>Masculino</option>
            <option value="F" @selected(request('sexo') === 'F')>Femenino</option>
            <option value="Otro" @selected(request('sexo') === 'Otro')>Otro</option></select></div>
    @endif
    @if ($tiene('tipo_ayuda'))
        <div class="col-6 col-md-3"><select name="tipo_ayuda_id" class="form-select">
            <option value="">Todos los tipos de ayuda</option>
            @foreach ($tiposAyuda as $t)<option value="{{ $t->id }}" @selected(request('tipo_ayuda_id') == $t->id)>{{ $t->nombre }}</option>@endforeach</select></div>
    @endif
    @if ($tiene('tipo_entrega'))
        <div class="col-6 col-md-2"><select name="tipo_entrega" class="form-select">
            <option value="">Todos los tipos</option>
            @foreach ($tiposEntrega as $t)<option value="{{ $t }}" @selected(request('tipo_entrega') === $t)>{{ ucfirst($t) }}</option>@endforeach</select></div>
    @endif
    @if ($tiene('estado_don'))
        <div class="col-6 col-md-2"><select name="estado" class="form-select">
            <option value="">Todas</option>
            <option value="registrada" @selected(request('estado') === 'registrada')>Registradas</option>
            <option value="anulada" @selected(request('estado') === 'anulada')>Anuladas</option></select></div>
    @endif
    @if ($tiene('categoria'))
        <div class="col-6 col-md-3"><select name="categoria_id" class="form-select">
            <option value="">Todas las categorías</option>
            @foreach ($categorias as $c)<option value="{{ $c->id }}" @selected(request('categoria_id') == $c->id)>{{ $c->nombre }}</option>@endforeach</select></div>
    @endif
    @if ($tiene('stock'))
        <div class="col-6 col-md-3"><select name="stock" class="form-select">
            <option value="" @selected(request('stock', '') === '')>Productos activos</option>
            <option value="bajo" @selected(request('stock') === 'bajo')>Solo stock bajo</option>
            <option value="inactivos" @selected(request('stock') === 'inactivos')>Inactivos</option></select></div>
    @endif
    @if ($tiene('desde'))
        <div class="col-6 col-md-2"><input type="date" name="desde" value="{{ request('desde') }}" class="form-control" title="Desde"></div>
        <div class="col-6 col-md-2"><input type="date" name="hasta" value="{{ request('hasta') }}" class="form-control" title="Hasta"></div>
    @endif
    <div class="col-12 col-md-auto d-flex gap-1">
        <button class="btn btn-outline-success"><i class="bi bi-funnel me-1"></i>Filtrar</button>
        <a href="{{ route('reportes.ver', $tipo) }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
    </div>
</form>

{{-- Encabezado solo para impresión --}}
<div class="solo-print mb-3">
    <h3 class="mb-0">KUSI — Sistema de Gestión de Ayuda Social</h3>
    <h4 class="mb-1">{{ $titulo }}</h4>
    <div class="small">Generado el {{ now()->format('d/m/Y H:i') }} por {{ auth()->user()->name }}</div>
    @if (request('desde') || request('hasta'))
        <div class="small">Periodo: {{ request('desde') ? \Carbon\Carbon::parse(request('desde'))->format('d/m/Y') : 'inicio' }}
            al {{ request('hasta') ? \Carbon\Carbon::parse(request('hasta'))->format('d/m/Y') : 'hoy' }}</div>
    @endif
</div>

<div class="row g-2 mb-3">
    @foreach ($resumen as $etiqueta => $valor)
        <div class="col-auto"><div class="card shadow-sm"><div class="card-body py-2 px-3">
            <div class="text-muted small">{{ $etiqueta }}</div><div class="fs-5 fw-bold">{{ $valor }}</div>
        </div></div></div>
    @endforeach
</div>

@if ($truncado)
    <div class="alert alert-warning">Se muestran solo los primeros 5000 registros. Usa los filtros para acotar el reporte.</div>
@endif

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-sm table-striped align-middle mb-0">
            <thead class="table-light"><tr>@foreach ($columnas as $c)<th>{{ $c }}</th>@endforeach</tr></thead>
            <tbody>
            @forelse ($filas as $fila)
                <tr>@foreach ($fila as $celda)<td>{{ $celda }}</td>@endforeach</tr>
            @empty
                <tr><td colspan="{{ count($columnas) }}" class="text-center text-muted py-4">No hay datos con esos filtros.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="text-muted small mt-2">{{ count($filas) }} registro(s)</div>
@endsection
