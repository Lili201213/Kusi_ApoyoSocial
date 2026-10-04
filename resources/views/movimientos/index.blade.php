@extends('layouts.app')
@section('titulo', 'Movimientos de inventario')

@section('contenido')
@php $u = auth()->user(); $fmt = fn ($n) => \App\Services\InventarioService::fmt($n); @endphp
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h2 class="mb-0">Movimientos de inventario</h2>
    <div class="d-flex gap-2">
        @if ($u->tienePermiso('inventario.entrada'))
            <a href="{{ route('movimientos.entrada') }}" class="btn btn-success"><i class="bi bi-box-arrow-in-down me-1"></i>Entrada</a>
        @endif
        @if ($u->tienePermiso('inventario.salida'))
            <a href="{{ route('movimientos.salida') }}" class="btn btn-warning"><i class="bi bi-box-arrow-up me-1"></i>Salida</a>
        @endif
    </div>
</div>

<form method="GET" class="row g-2 mb-3">
    <div class="col-12 col-md-4">
        <select name="producto_id" class="form-select">
            <option value="">Todos los productos</option>
            @foreach ($productos as $p)
                <option value="{{ $p->id }}" @selected(request('producto_id') == $p->id)>{{ $p->nombre }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-6 col-md-2">
        <select name="tipo" class="form-select">
            <option value="">Entradas y salidas</option>
            <option value="entrada" @selected(request('tipo') === 'entrada')>Entradas</option>
            <option value="salida" @selected(request('tipo') === 'salida')>Salidas</option>
        </select>
    </div>
    <div class="col-6 col-md-2"><input type="date" name="desde" value="{{ request('desde') }}" class="form-control" title="Desde"></div>
    <div class="col-6 col-md-2"><input type="date" name="hasta" value="{{ request('hasta') }}" class="form-control" title="Hasta"></div>
    <div class="col-6 col-md-2 d-flex gap-1">
        <button class="btn btn-outline-success flex-fill"><i class="bi bi-search"></i></button>
        <a href="{{ route('movimientos.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
    </div>
</form>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th>Fecha</th><th>Producto</th><th>Tipo</th><th class="text-end">Cantidad</th><th>Motivo</th><th>Registró</th></tr></thead>
            <tbody>
            @forelse ($movimientos as $m)
                <tr>
                    <td class="text-nowrap">{{ $m->fecha->format('d/m/Y') }}</td>
                    <td><a href="{{ route('productos.show', $m->producto_id) }}" class="text-decoration-none">{{ $m->producto->nombre }}</a></td>
                    <td><span class="badge {{ $m->tipo === 'entrada' ? 'text-bg-success' : 'text-bg-danger' }}">{{ ucfirst($m->tipo) }}</span></td>
                    <td class="text-end">{{ $m->tipo === 'entrada' ? '+' : '−' }}{{ $fmt($m->cantidad) }} {{ $m->producto->unidad_medida }}</td>
                    <td>
                        @if ($m->donacion_id)
                            <a href="{{ route('donaciones.show', $m->donacion_id) }}" class="text-decoration-none">{{ $m->motivo }}</a>
                        @else {{ $m->motivo ?: '—' }} @endif
                    </td>
                    <td>{{ $m->usuario->name }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No hay movimientos con esos filtros.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $movimientos->links('pagination::bootstrap-5') }}</div>
@endsection
